<?php
// process_winners.php
header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../../config/db.php';

$db = new Database();
$pdo = $db->connect();

$response = [
    'success' => false,
    'message' => 'Initializing processing',
    'processed_jobs' => [],
    'errors' => []
];

try {
    // Begin transaction on the new connection
    $pdo->beginTransaction();

    // Get all closed jobs without existing winners
    $closedJobsQuery = "
        SELECT j.job_id 
        FROM jobs j
        LEFT JOIN job_winners jw ON j.job_id = jw.job_id
        WHERE j.is_closed = TRUE 
        AND jw.job_id IS NULL
    ";
    $stmt = $pdo->query($closedJobsQuery);
    $closedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($closedJobs)) {
        $response['message'] = 'No closed jobs without winners found';
        $pdo->commit();
        echo json_encode($response);
        exit;
    }

    foreach ($closedJobs as $job) {
        $jobId = $job['job_id'];
        $processingResult = [
            'job_id' => $jobId,
            'status' => 'processed',
            'winner_id' => null,
            'winning_bid' => null,
            'error' => null
        ];

        try {
            // Get lowest valid bid with tiebreaker (earliest bid)
            $bidsQuery = "
                SELECT b.bidder_id, b.bid_amount 
                FROM bids b
                WHERE b.job_id = :job_id
                ORDER BY b.bid_amount ASC, b.created_at ASC
                LIMIT 1
            ";
            $stmt = $pdo->prepare($bidsQuery);
            $stmt->execute([':job_id' => $jobId]);
            $winningBid = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$winningBid) {
                $processingResult['status'] = 'skipped';
                $processingResult['error'] = 'No valid bids found';
                $response['errors'][] = "Job $jobId: No bids found";
                continue;
            }

            // Insert into job_winners
            $insertQuery = "
                INSERT INTO job_winners (job_id, winner_id, winning_bid, assigned_at)
                VALUES (:job_id, :winner_id, :winning_bid, NOW())
                ON CONFLICT (job_id) DO NOTHING
            ";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([
                ':job_id' => $jobId,
                ':winner_id' => $winningBid['bidder_id'],
                ':winning_bid' => $winningBid['bid_amount']
            ]);

            if ($stmt->rowCount() > 0) {
                $processingResult['winner_id'] = $winningBid['bidder_id'];
                $processingResult['winning_bid'] = $winningBid['bid_amount'];
                $response['processed_jobs'][] = $processingResult;
            } else {
                $processingResult['status'] = 'skipped';
                $processingResult['error'] = 'Winner already exists or conflict occurred';
                $response['errors'][] = "Job $jobId: Failed to insert winner";
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $processingResult['status'] = 'failed';
            $processingResult['error'] = $e->getMessage();
            $response['errors'][] = "Job $jobId: " . $e->getMessage();
        }
    }

    $pdo->commit();
    $response['success'] = count($response['errors']) === 0;
    $response['message'] = count($response['processed_jobs']) . ' jobs processed successfully';

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['success'] = false;
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'System error: ' . $e->getMessage();
}

echo json_encode($response);
?>
