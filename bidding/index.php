<?php require_once '../config/config.php'; ?>
<?php require_once '../config/db.php'; ?>

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "auth/login/");
    exit();
}

if ($_SESSION['user_type'] !== 'bidder') {
    header("Location: " . $base_url);
    exit();
}

$bidder_id = $_SESSION['user_id'];

// Fetch user's bids
$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT b.bid_id, b.bid_amount, b.created_at, j.title, j.job_id
                        FROM bids b
                        JOIN jobs j ON b.job_id = j.job_id
                        WHERE b.bidder_id = :bidder_id");
$stmt->execute(['bidder_id' => $bidder_id]);
$bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids</title>
    <?php require_once '../includes/headlinks.php'; ?>
</head>
<body>
    <?php require_once '../includes/nav.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">My Bids</h2>

        <?php if ($bids): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Bid Amount</th>
                        <th>Placed On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bids as $bid): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bid['title']); ?></td>
                            <td>₹ <?php echo number_format($bid['bid_amount'], 2); ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($bid['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-danger delete-btn" data-bid-id="<?php echo $bid['bid_id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bids placed yet.</p>
        <?php endif; ?>
    </div>

    <?php require_once '../includes/footerlinks.php'; ?>

<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        const bidId = $(this).data('bid-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '../api/bidding/delete_bid.php',
                    data: { bid_id: bidId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                    }
                });
            }
        });
    });
});
</script>

</body>
</html>
