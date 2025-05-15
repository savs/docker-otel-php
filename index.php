<?php
// Connect to MySQL database using environment variables
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'testdb';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query('SELECT id, name FROM people');
    echo "<h1>People Table</h1>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
    foreach ($stmt as $row) {
        $id = htmlspecialchars($row['id']);
        $name = htmlspecialchars($row['name']);
        echo "<tr><td>$id</td><td><a href='person.php?id=$id'>$name</a></td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<h1>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
