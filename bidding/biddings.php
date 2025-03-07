<?php
require('../config/config.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIDDINGS</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/nav.php'); ?>

    <h1>ALL BIDDINGS </h1>

    <table class="table table-striped table-bordered">  
        <thead>
        <tr>
            <th>id</th> 
            <th>job_id</th>
            <th>bidder_name</th>
            <th>bid_amount</th>

           
        </tr>
        </thead>
        <tbody>
        <?php
        require('../config/db.php');
        $query = 'SELECT * FROM bids ORDER BY id DESC LIMIT 10'; 
        $statement = $db->prepare($query);
        $statement->execute();
        $biddings = $statement->fetchAll();
        $statement->closeCursor();
        foreach ($biddings as $bidding) {
            echo "<tr>";
            echo "<td>" . $bidding['id'] . "</td>";
            echo "<td>" . $bidding['job_id'] . "</td>";
            echo "<td>" . $bidding['bidder_name'] . "</td>";
            echo "<td>" . $bidding['bid_amount'] . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

<script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>