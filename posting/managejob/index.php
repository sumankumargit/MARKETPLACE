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
    <title>Manage Jobs</title>
    <?php require_once '../../includes/headlinks.php'; ?>
</head>
<body style="background-color: #f8f9fa;">
    <?php require_once '../../includes/nav.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Manage Jobs</h2>

    <table id="jobTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Requirements</th>
                <th>Expiration</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Edit Job Modal -->
<div class="modal fade" id="editJobModal" tabindex="-1" aria-labelledby="editJobModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJobModalLabel">Edit Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editJobForm">
                    <input type="hidden" id="job_id" name="job_id">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requirements</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="expiration" class="form-label">Expiration Date</label>
                        <input type="datetime-local" class="form-control" id="expiration" name="expiration" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footerlinks.php'; ?>

<script>
$(document).ready(function() {
    loadJobs();

    function loadJobs() {
        $('#jobTable').DataTable({
            destroy: true,
            ajax: {
                url: '../../api/posting/get_jobs.php',
                type: 'GET'
            },
            columns: [
                { data: 'title' },
                { data: 'description' },
                { data: 'requirements' },
                { data: 'expiration' },
                {
                    data: null,
                    render: function(data, type, row) {
                        let now = new Date();
                        let expiration = new Date(row.expiration);
                        if (expiration > now) {
                            return `
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${row.job_id}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${row.job_id}">Delete</button>
                            `;
                        } else {
                            return `<span class="badge bg-secondary">Expired</span>`;
                        }
                    }
                }
            ]
        });
    }

    $(document).on('click', '.edit-btn', function() {
        let jobId = $(this).data('id');
        $.get('../../api/posting/get_job.php', { job_id: jobId }, function(response) {
            if (response.status === 'success') {
                $('#job_id').val(response.data.job_id);
                $('#title').val(response.data.title);
                $('#description').val(response.data.description);
                $('#requirements').val(response.data.requirements);
                $('#expiration').val(response.data.expiration.replace(' ', 'T'));
                $('#editJobModal').modal('show');
            }
        });
    });

    $('#editJobForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../api/posting/update_job.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#editJobModal').modal('hide');
                loadJobs();
                alert(response.message);
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this job?')) {
            let jobId = $(this).data('id');
            $.ajax({
                url: '../../api/posting/delete_job.php',
                type: 'POST',
                data: { job_id: jobId },
                success: function(response) {
                    loadJobs();
                    alert(response.message);
                }
            });
        }
    });
});
</script>

</body>
</html>