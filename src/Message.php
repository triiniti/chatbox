<?php
namespace ChatBox\App;
use ChatBox\App\Database;
use PDO;
use PDOException;

class Message
{
  public $message;
  public $sender_id;
  public $pos;
  public $created_at;
  public $state;
  public function __construct($message = null, $sender_id = null, $created_at = null, $pos = null)
  {
    $this->message = $message;
    $this->sender_id = $sender_id;
    $this->pos = $pos;
    $this->created_at = $created_at;
  }
  public function save()
  {
    $db = new Database();
    try {
      $data = [
        'message' => $this->message,
        'sender_id' => $this->sender_id,
        'position' => $this->pos,
        'created_at' => $this->created_at,
      ];
      $sql = "INSERT INTO messages (content, sender_id, position, created_at) VALUES (:message, :sender_id, :position, :created_at)";
      $stmt = $db->pdo->prepare($sql);
      $stmt->execute($data);
      return $db->lastInsertId();
    } catch (PDOException $e) {
      return false;
    }
  }
  public function loadById($id)
  {
    $db = new Database();
    try {
      $sql = "SELECT * FROM messages WHERE id = :id";
      $stmt = $db->pdo->prepare($sql);
      $stmt->execute([':id' => $id]);
      if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
      } else {
        return null;
      }
    } catch (PDOException $e) {
      return false;
    }
  }

  public function loadAll(array $where = [])
  {
    $db = new Database();
    try {
      $sql = "SELECT u.avatar, m.* FROM messages m INNER JOIN users u ON u.id=m.sender_id ORDER BY id";
      if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', array_map(function ($key) {
          return "$key = :$key";
        }, array_keys($where)));
      }
      $stmt = $db->pdo->prepare($sql);
      $stmt->execute($where);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return false;
    }
  }

  public function count()
  {
    $db = new Database();
    try {
      $sql = "SELECT COUNT(*) as cnt FROM messages";
      $stmt = $db->pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      return false;
    }
  }

  public function lastSenderId()
  {
    $db = new Database();
    try {
      $sql = "SELECT sender_id FROM messages ORDER BY id DESC LIMIT 1";
      $stmt = $db->pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      return false;
    }
  }
}
