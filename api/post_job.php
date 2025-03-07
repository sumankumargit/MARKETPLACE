<?php
require('../config/db.php');

if ($_POST) {
    $description = $_POST['description'];
    $requirements = $_POST['requirements']; 
    $poster_name = $_POST['poster_name'];
    $contact_info = $_POST['contact_info'];
    $lowest_bid = $_POST['lowest_bid'];
    $bid_count = $_POST['bid_count'];
    $expiration = $_POST['expiration'];
    $status = $_POST['status'];
    
    $query = 'INSERT INTO jobs (description, requirements, poster_name, contact_info, lowest_bid, bid_count, expiration, status) VALUES (:description, :requirements, :poster_name, :contact_info, :lowest_bid, :bid_count, :expiration, :status)';
    $statement = $db->prepare($query);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':requirements', $requirements);
    $statement->bindValue(':poster_name', $poster_name);
    $statement->bindValue(':contact_info', $contact_info);
    $statement->bindValue(':lowest_bid', $lowest_bid);
    $statement->bindValue(':bid_count', $bid_count);
    $statement->bindValue(':expiration', $expiration);
    $statement->bindValue(':status', $status);
    $statement->execute();
    $statement->closeCursor();
    echo "Job posted successfully";
}

?>