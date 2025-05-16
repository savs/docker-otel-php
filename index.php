<?php

use Monolog\Logger;
use OpenTelemetry\Contrib\Logs\Monolog\Handler;
use OpenTelemetry\API\Globals;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LogLevel;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';

$loggerProvider = Globals::loggerProvider();
$handler = new Handler(
    $loggerProvider,
    LogLevel::INFO
);
$monolog = new Logger('otel-php-monolog', [$handler]);

$tracer = Globals::tracerProvider()->getTracer('phpdemo');

$app = AppFactory::create();

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


$app->get('/', function (Request $request, Response $response) use ($dsn, $user, $pass, $options, $tracer, $monolog) {
  $monolog->info('index page served');

  $span = $tracer->spanBuilder('index')->startSpan();
  try {
      $pdo = new PDO($dsn, $user, $pass, $options);
      $stmt = $pdo->query('SELECT id, name FROM people');
      $span->addEvent('Querying database');
      $span->setAttribute('db.system', 'mysql');
      $span->setAttribute('db.name', 'testdb');
      $span->setAttribute('db.statement', 'SELECT id, name FROM people');
      $span->setAttribute('net.peer.name', 'localhost');
      $span->setAttribute('db.user', 'user');
      $response->getBody()->write("<h1>People Table</h1>");
      $response->getBody()->write("<table border='1'><tr><th>ID</th><th>Name</th></tr>");
      foreach ($stmt as $row) {
          $id = htmlspecialchars($row['id']);
          $name = htmlspecialchars($row['name']);
          $response->getBody()->write("<tr><td>$id</td><td><a href='/person/$id'>$name</a></td></tr>");
      }
      $response->getBody()->write("</table>");
  } catch (PDOException $e) {
      $response->getBody()->write("<h1>Database Error</h1>");
      $response->getBody()->write("<p>" . htmlspecialchars($e->getMessage()) . "</p>");
  }
  $span->end();
  return $response;
});

$app->get('/person/{id}', function (Request $request, Response $response, $args) use ($dsn, $user, $pass, $options, $tracer, $monolog) {
    $id = $args['id'];
$monolog->info('person page served', ['id' => $id]);
    $span = $tracer->spanBuilder('person')->startSpan();
    try {
      $pdo = new PDO($dsn, $user, $pass, $options);
      $stmt = $pdo->prepare('SELECT id, name FROM people WHERE id = ?');
      $stmt->execute([$id]);
      $person = $stmt->fetch();
      $span->addEvent('Querying database');
      $span->setAttribute('db.system', 'mysql');
      $span->setAttribute('db.name', 'testdb');
      $span->setAttribute('db.statement', 'SELECT id, name FROM people WHERE id = ?');
      $span->setAttribute('net.peer.name', 'localhost');
      $span->setAttribute('db.user', 'user');
      if ($person) {
          $response->getBody()->write("<h1>Person Details</h1>");
          $response->getBody()->write("<p><strong>ID:</strong> " . htmlspecialchars($person['id']) . "</p>");
          $response->getBody()->write("<p><strong>Name:</strong> " . htmlspecialchars($person['name']) . "</p>");
          $response->getBody()->write('<p><a href="/">Back to list</a></p>');
      } else {
          $response->getBody()->write('<h1>Not Found</h1><p>No person found with that ID.</p>');
          $response->getBody()->write('<p><a href="/">Back to list</a></p>');
      }
  } catch (PDOException $e) {
      $response->getBody()->write("<h1>Database Error</h1>");
      $response->getBody()->write("<p>" . htmlspecialchars($e->getMessage()) . "</p>");
  }
  $span->end();
  return $response;
});

$app->run();

?>
