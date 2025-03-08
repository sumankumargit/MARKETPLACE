<?php require_once '../../config/config.php'; ?>
<?php


// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "auth/login/");
    exit();
}

// Check if the user type is not 'poster'
if ($_SESSION['user_type'] !== 'poster') {
    header("Location: " . $base_url);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POST JOB</title>
    <?php require_once '../../includes/headlinks.php'; ?>
</head>
<body style="background-color: #f8f9fa;">
    <?php require_once '../../includes/nav.php'; ?>

<div class="container mt-5">
        <h2 class="mb-4">Post a New Job</h2>
        <form id="jobPostForm">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="requirements">Requirements</label>
                <textarea class="form-control" id="requirements" name="requirements" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="expiration">Application Deadline</label>
                <input type="datetime-local" class="form-control" id="expiration" name="expiration" required>
            </div>
            <button type="submit" class="btn btn-primary">Post Job</button>
        </form>
    </div>
<?php require_once '../../includes/footerlinks.php'; ?>
<script>
// Add loading animation to the 'Post Job' button
$(document).ready(function() {
    $('#jobPostForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var postButton = $('button[type="submit"]');

        // Show loading state
        postButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Posting...');

        console.log('Serialized form data:', formData);

        $.ajax({
            type: 'POST',
            url: '../../api/posting/post_job.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Job Posted!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '<?php echo $base_url?>';
                    });
                } else {
                    Toastify({
                        text: response.message || 'Failed to post the job. Please try again.',
                        backgroundColor: '#dc3545',
                        duration: 3000
                    }).showToast();
                    
                    // Reset button state on failure
                    postButton.prop('disabled', false).html('Post Job');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', xhr.responseText);
                Toastify({
                    text: 'An error occurred. Please check the console for details.',
                    backgroundColor: '#dc3545',
                    duration: 3000
                }).showToast();
                
                // Reset button state on error
                postButton.prop('disabled', false).html('Post Job');
            }
        });
    });
});
</script>


</body>
</html>