<?php
// person.php?id=1
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

if (!isset($_GET['id'])) {
    echo '<h1>Error</h1><p>No person ID specified.</p>';
    exit;
}

$id = (int) $_GET['id'];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('SELECT id, name FROM people WHERE id = ?');
    $stmt->execute([$id]);
    $person = $stmt->fetch();
    if ($person) {
        echo "<h1>Person Details</h1>";
        echo "<p><strong>ID:</strong> " . htmlspecialchars($person['id']) . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($person['name']) . "</p>";
        echo '<p><a href="index.php">Back to list</a></p>';
    } else {
        echo '<h1>Not Found</h1><p>No person found with that ID.</p>';
        echo '<p><a href="index.php">Back to list</a></p>';
    }
} catch (PDOException $e) {
    echo "<h1>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
