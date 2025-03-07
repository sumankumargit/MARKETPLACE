<?php

// id INT AUTO_INCREMENT PRIMARY KEY,
// job_id INT NOT NULL,
// bidder_name VARCHAR(255) NOT NULL,
// bid_amount DECIMAL(10,2) NOT NULL,
// FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE

require('../config/db.php');

if ($_POST) {
    $job_id = $_POST['job_id'];
    $bidder_name = $_POST['bidder_name'];
    $bid_amount = $_POST['bid_amount']; 

    $query = 'INSERT INTO bids (job_id, bidder_name, bid_amount) VALUES (:job_id, :bidder_name, :bid_amount)';
    $statement = $db->prepare($query);
    $statement->bindValue(':job_id', $job_id);
    $statement->bindValue(':bidder_name', $bidder_name);
    $statement->bindValue(':bid_amount', $bid_amount);
    $statement->execute();
    $statement->closeCursor();
    echo "Bid posted successfully";
}

?>