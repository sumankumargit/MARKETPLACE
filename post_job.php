<?php
require('config/config.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">

</head>
<body>
<?php include('includes/nav.php'); ?>

    <br>
    <form id="job_post_form" action="" class="m-5 px-5">
        <h1>POST JOB</h1>
    <div class="mb-3">
  <label for="description" class="form-label">Description</label>
  <input type="text" class="form-control" id="description" name="description"  placeholder="Write job description..">
</div>
<div class="mb-3">
  <label for="requirements" class="form-label">Requirements</label>     
    <input type="text" class="form-control" id="requirements" name="requirements" placeholder="Write job requirements..">   
</div>
<div class="mb-3">
    <label for="poster_name" class="form-label">Poster Name</label>
    <input type="text" class="form-control" id="poster_name" name="poster_name" placeholder="Write your name..">
</div>
<div class="mb-3">
    <label for="contact_info" class="form-label">Contact Info</label>   
    <input type="text" class="form-control" id="contact_info" name="contact_info" placeholder="Write your contact info..">
</div>
<div class="mb-3">
    <label for="lowest_bid" class="form-label">Lowest Bid</label>
    <input type="text" class="form-control" id="lowest_bid" name="lowest_bid" placeholder="Write the lowest bid..">
</div>
<div class="mb-3">
    <label for="bid_count" class="form-label">Bid Count</label>
    <input type="text" class="form-control" id="bid_count" name="bid_count" placeholder="Write the bid count..">
</div>
<div class="mb-3">
    <label for="expiration" class="form-label">Expiration</label>

    <input type="date" class="form-control" id="expiration" name="expiration" placeholder="Write the expiration date..">
</div>
<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select name="status" id="stuat" class="form-select">
    <option value="open">Open</option>
    <option value="closed">Closed</option>
</select>
</div>
        <input class="btn btn-success" type="submit" value="Submit">

    </form>
<br>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#job_post_form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'api/post_job.php',
                    data: $('#job_post_form').serialize(),
                    success: function(response) {
                        // console.log(response);
                        Swal.fire({
                            title: 'Success',
                            text: 'Job posted successfully',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        });
                    },
                    error: function(response) {
                        // console.log(response);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>