<?php
namespace ChatBox\App;
use ChatBox\App\Database;
use PDO;
use PDOException;

class User
{
    public $github_id;
    public $username;
    public $email;
    public $password;
    public $avatar;
    public $created_at;
    public $ip;
    public $state;

    public function __construct($github_id = null, $password = null, $username = null, $email = null, $avatar = null, $ip = null)
    {
        $this->github_id = $github_id;
        $this->username = $username;
        $this->email = $email;
        $this->avatar = $avatar;
        $this->ip = $ip;
        $this->password = hash('sha256', $password ?? '');
        $this->created_at = date('Y-m-d H:i:s');
        $this->state = bin2hex(random_bytes(16));
    }

    public function save()
    {
        $db = new Database();
        if (empty($this->github_id)) {
            echo "GitHub ID is required.";
            return false; // GitHub ID is required
        }
        try {
            $data = [
                'github_id' => $this->github_id,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'avatar' => $this->avatar,
                'created_at' => $this->created_at,
                'ip' => $this->ip,
                'state' => $this->state,
                'token' => bin2hex(random_bytes(10)),
            ];
            $sql = "INSERT INTO users (github_id, username, email, password, avatar, created_at, state, token, ip) VALUES (:github_id, :username, :email, :password, :avatar, :created_at, :state, :token, :ip)";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute($data);

            //$lastId = $stmt->fetchColumn();
            return $db->lastInsertId();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function loadById($id)
    {
        $db = new Database();
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $this->github_id = $user['github_id'];
                $this->username = $user['username'];
                $this->email = $user['email'];
                $this->ip = $user['ip'];
                $this->avatar = $user['avatar'];
                $this->created_at = $user['created_at'];
                return $user;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function loadByGitHubId($github_id)
    {
        $db = new Database();
        try {
            $sql = "SELECT * FROM users WHERE github_id = :github_id";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute([':github_id' => $github_id]);
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function addParticipant($id)
    {
        $db = new Database();
        try {
            $sql = "INSERT INTO participants (user_id, created_at) VALUES(:id, NOW())";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function removeParticipant($id)
    {
        $db = new Database();
        try {
            $sql = "DELETE FROM participants WHERE user_id = :id";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public static function getParticipants()
    {
        $db = new Database();
        try {
            $sql = "SELECT user_id FROM participants";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}