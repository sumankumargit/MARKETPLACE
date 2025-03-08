<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

class Database {
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            
                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $username = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASSWORD'];

        
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

                $this->conn = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
                    PDO::ATTR_EMULATE_PREPARES => false,         // Use real prepared statements
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch as associative array
                ]);

            // echo "Connected successfully!";
        } catch (PDOException $e) {
            
            error_log("Database connection failed: " . $e->getMessage());
            // die("Database connection failed. Check error logs.");
        }

        return $this->conn;
    }
}

// Test connection
// $db = new Database();
// $conn = $db->connect();

?>
