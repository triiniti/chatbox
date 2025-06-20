<?php
namespace ChatBox\App;
use Dotenv\Dotenv;
use PDO;
use PDOException;


class Database
{

	public $pdo;
	private $host;
	private $user;
	private $pass;
	private $dbname;

	private $error;
	public $stmt;

	public function __construct()
	{
		// Load environment variables from .env file
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
		$dotenv->load();
		// Use the environment variables
		$this->host = $_ENV['DB_HOST'];
		$this->dbname = $_ENV['DB_NAME'];
		$this->user = $_ENV['DB_USER'];
		$this->pass = $_ENV['DB_PASSWORD'];
		$this->port = $_ENV['DB_PORT'];
		// Set DSN
		$dsn = 'pgsql:host=' . $this->host . ';dbname=' . $this->dbname;
		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::MYSQL_ATTR_FOUND_ROWS => TRUE
		);

		// Create a new PDO instanace
		try {
			$this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
		}		// Catch any errors
		catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}
}