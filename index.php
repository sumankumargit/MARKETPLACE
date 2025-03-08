<?php
// session_start();
require_once 'config/config.php';
require_once 'config/db.php';


$db = new Database();
$conn = $db->connect();

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Filtering settings
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$whereClause = "WHERE j.is_closed = FALSE AND j.expiration > NOW()";
$orderBy = ($filter === 'active') ? "ORDER BY bid_count DESC" : "ORDER BY j.created_at DESC";

// Fetch jobs with pagination and filtering
$jobsQuery = "
    SELECT j.job_id, j.title, j.description, j.expiration, j.created_at,
           COUNT(b.bid_id) AS bid_count
    FROM jobs j
    LEFT JOIN bids b ON j.job_id = b.job_id
    $whereClause
    GROUP BY j.job_id
    $orderBy
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($jobsQuery);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$jobs = $stmt->fetchAll();

// Count total jobs for pagination
$totalJobsQuery = "SELECT COUNT(*) as total FROM jobs j $whereClause";
$totalJobs = $conn->query($totalJobsQuery)->fetch()['total'];
$totalPages = ceil($totalJobs / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Marketplace</title>
    <?php require_once 'includes/headlinks.php'; ?>
</head>
<body class="bg-light">
    <?php require_once 'includes/nav.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-5">Job Marketplace</h1>

        <form method="GET" class="mb-4">
            <div class="input-group">
                <select name="filter" class="form-select">
                    <option value="">All Jobs</option>
                    <option value="recent" <?= $filter === 'recent' ? 'selected' : '' ?>>Recent Jobs</option>
                    <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Most Active</option>
                </select>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </div>
        </form>

        <div class="list-group">
            <?php foreach ($jobs as $job): ?>
                <div class="list-group-item">
                    <h5><?= htmlspecialchars($job['title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    <small>Posted on: <?= $job['created_at'] ?></small><br>
                    <small>Bids: <?= $job['bid_count'] ?></small>
                    <div id="timer-<?= $job['job_id'] ?>" class="mt-2"></div>
                    
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'bidder'): ?>
    <p>Expiration: <?= $job['expiration'] ?></p>
    <p>Current Time: <?= date('Y-m-d H:i:s') ?></p>

    <?php if (strtotime($job['expiration']) > time()): ?>
        <form action="<?php echo $base_url?>bidding/placebidding/" method="POST" class="mt-3">
            <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
            <button type="submit" class="btn btn-success">Bid</button>
        </form>
    <?php else: ?>
        <span class="badge bg-danger">Bidding Closed</span>
    <?php endif; ?>
<?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>"> <?= $i ?> </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <?php require_once 'includes/footerlinks.php'; ?>

    <script>
        function startCountdown(expiration, elementId) {
            const countDownDate = new Date(expiration).getTime();
            const timerElement = document.getElementById(elementId);

            const timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                if (distance <= 0) {
                    clearInterval(timerInterval);
                    timerElement.innerHTML = '<span class="badge bg-danger">Expired</span>';
                } else {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60)) / (1000));
                    const seconds = Math.floor((distance % (1000)) / 1000);
                    timerElement.innerHTML = `<span class="badge bg-success">${days}d ${hours}h ${minutes}m ${seconds}s</span>`;
                }
            }, 1000);
        }

        <?php foreach ($jobs as $job): ?>
            startCountdown("<?= $job['expiration'] ?>", "timer-<?= $job['job_id'] ?>");
        <?php endforeach; ?>
    </script>


<script>
    $(document).ready(function () {
    $.ajax({
        url: "api/automation/update.php", // Update the path if needed
        method: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response.message);
        },
        error: function () {
            console.error("Error updating jobs.");
        }
    });
});

</script>
</body>
</html>
