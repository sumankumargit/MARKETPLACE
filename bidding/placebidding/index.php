<?php require_once '../../config/config.php'; ?>
<?php require_once '../../config/db.php'; ?>

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "auth/login/");
    exit();
}

if ($_SESSION['user_type'] !== 'bidder') {
    header("Location: " . $base_url);
    exit();
}

$job_id = $_POST['job_id'] ?? null;
$bidder_id = $_SESSION['user_id'];

if (!$job_id) {
    echo "Invalid Job ID";
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = :job_id AND expiration > NOW() AND is_closed = FALSE");
$stmt->execute(['job_id' => $job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "Job not found or expired.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Your Bid</title>
    <?php require_once '../../includes/headlinks.php'; ?>
</head>
<body style="background-color: #f8f9fa;">
<?php require_once '../../includes/nav.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Place a Bid for: <span class="text-primary"><?php echo htmlspecialchars($job['title']); ?></span></h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Job Details</h5>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
            <p><strong>Requirements:</strong> <?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
            <p><strong>Deadline:</strong> <?php echo date('F j, Y, g:i a', strtotime($job['expiration'])); ?></p>
        </div>
    </div>

    <h3 class="mt-4">Your Bid</h3>
    <form id="bidForm">
        <div class="form-group">
            <label for="bid_amount">Bid Amount</label>
            <input type="number" step="0.01" class="form-control" id="bid_amount" name="bid_amount" required>
        </div>
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        <button type="submit" class="btn btn-success">
            <span id="btnText">Place Bid</span>
            <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </form>
</div>

<?php require_once '../../includes/footerlinks.php'; ?>
<script>
$(document).ready(function() {
    $('#bidForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#btnText').text('Placing...');
        $('#btnLoader').removeClass('d-none');
        
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: '../../api/bidding/place_bid.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#btnText').text('Place Bid');
                $('#btnLoader').addClass('d-none');
                
                if (response.status === 'success') {
                    Swal.fire('Success!', response.message, 'success').then(() => {
                window.location.href = '<?php echo $base_url; ?>bidding/';
            });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                $('#btnText').text('Place Bid');
                $('#btnLoader').addClass('d-none');
                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
            }
        });
    });
});
</script>
</body>
</html>
