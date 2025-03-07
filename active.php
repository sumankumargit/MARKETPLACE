<?php
require('config/config.php');

require('config/db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARKET PLACE</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css" integrity="sha512-BMbq2It2D3J17/C7aRklzOODG1IQ3+MHw3ifzBHMBwGO/0yUqYmsStgBjI0z5EYlaDEFnvYV7gNYdD3vFLRKsA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<?php include('includes/nav.php'); ?>

    <h1 class="m-5">MARKET PLACE</h1>

    <h2 style="text-align:center;" class="m-5">ACTIVE JOBS</h2>
    

    <table id="jobs_table" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>id</th> 
            <th>description</th>
             <th>requirements</th>
             <th>poster_name</th>
             <th>contact_info</th>
             <th>lowest_bid</th>
             <th>bid_count</th>
             <th>expiration</th>
             <th>status</th>
             <th>BID</th>


        </tr>
        </thead>
        <tbody>
        <?php
        $query = 'SELECT * FROM jobs ORDER BY bid_count DESC LIMIT 10'; 
        $statement = $db->prepare($query);
        $statement->execute();
        $jobs = $statement->fetchAll();
        $statement->closeCursor();
        foreach ($jobs as $job) { ?>
            <tr>
                <td><?php echo $job['id']; ?></td>
                <td><?php echo $job['description']; ?></td>
                <td><?php echo $job['requirements']; ?></td>
                <td><?php echo $job['poster_name']; ?></td>
                <td><?php echo $job['contact_info']; ?></td>
                <td><?php echo $job['lowest_bid']; ?></td>
                <td><?php echo $job['bid_count']; ?></td>
                <td><?php echo $job['expiration']; ?></td>
                <td><?php echo $job['status']; ?></td>
                <td><a href="bidding/index.php?id=<?php echo $job['id']; ?>">BID</a></td>
            </tr>

           
       <?php }
        ?>
        </tbody>
    </table>

        <a class="btn btn-primary" href="post_job.php">POST JOB</a>

        <a class="btn btn-success" href="/">RECENT JOBS</a>
<script src="assets/js/jquery.js"></script>
   
<script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>