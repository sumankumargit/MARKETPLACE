<?php require_once '../config/config.php'; ?>
<?php require_once '../config/db.php'; ?>

<?php
// session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "auth/login/");
    exit();
}


$bidder_id = $_SESSION['user_id'];

// Fetch job wins for the logged-in bidder
$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT jw.job_id, j.title, jw.winning_bid, jw.assigned_at
                        FROM job_winners jw
                        JOIN jobs j ON jw.job_id = j.job_id
                        WHERE jw.winner_id = :bidder_id");
$stmt->execute(['bidder_id' => $bidder_id]);
$wins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Job Wins</title>
    <?php require_once '../includes/headlinks.php'; ?>
</head>
<body>
    <?php require_once '../includes/nav.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">My Job Wins</h2>

        <?php if ($wins): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Winning Bid</th>
                        <th>Assigned On</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wins as $win): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($win['title']); ?></td>
                            <td>₹ <?php echo number_format($win['winning_bid'], 2); ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($win['assigned_at'])); ?></td>
                            <!-- <td>
                                <a href="<?php echo $base_url . 'jobs/view.php?job_id=' . $win['job_id']; ?>" class="btn btn-primary">View Job</a>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No job wins found.</p>
        <?php endif; ?>
    </div>

    <?php require_once '../includes/footerlinks.php'; ?>
</body>
</html>
