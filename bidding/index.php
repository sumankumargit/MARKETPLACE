<?php

require('../config/config.php');


if($_GET["id"]) {
    $job_id = $_GET["id"];

} else {
    echo "Invalid job id";


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">

</head>
<body>
    <?php include('../includes/nav.php'); ?>
    <h1>BIDDING FORM</h1>
    <center><form id="bid_form" class="m-5 bg-gray" action="">
    <div class="mb-3">

        <label  class="form-label" for="job_id">Job ID:</label><br>

        <input type="text" id="job_id" value="<?php echo $job_id ?>" name="job_id" readonly><br>
        </div>
        <div class="mb-3">
        <label  class="form-label" for="bidder_name">Bidder Name:</label><br>
        <input type="text" id="bidder_name" name="bidder_name"><br>
        </div>
        <div class="mb-3">
        <label  class="form-label" for="bid_amount">Bid Amount:</label><br>
        <input type="text" id="bid_amount" name="bid_amount"><br>
        </div>
        <input class="btn btn-success" type="submit" value="Submit">

    </form></center>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            $('#bid_form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'bidding_api.php',
                    data: $('#bid_form').serialize(),
                    success: function(response) {
                        // console.log(response);
                        Swal.fire({
                            title: 'Success',
                            text: 'Bid submitted successfully',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then(function() {
                            window.location = 'biddings.php';
                        });
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });
        });
    </script>

</body>
</html>