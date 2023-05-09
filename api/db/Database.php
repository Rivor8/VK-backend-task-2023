<?php

class Database
{

    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = $_ENV["DB_HOST"];
        $this->db_name = $_ENV["DB_NAME"];
        $this->username = $_ENV["DB_USER"];
        $this->password = $_ENV["DB_PASSWORD"];
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Ошибка подключения __construct: " . $exception->getMessage() . "\n";
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function createEventsTable()
    {
        if ($this->conn) {
            try {
                $this->conn->exec("
                CREATE TABLE IF NOT EXISTS events (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    name_event VARCHAR(256) NOT NULL,
                    is_auth BOOL NOT NULL,
                    user_ip VARCHAR(15) NOT NULL,
                    event_date DATE NOT NULL
                );
                ");
                return "success";
            } catch (PDOException $exception) {
                return "Ошибка createEventsTable: " . $exception->getMessage();
            }

        } else {
            return "Соединение не установлено.";
        }
    }

    public function addEvent($event)
    {
        if ($this->conn) {
            try {
                $sql = "INSERT INTO events (name_event, is_auth, user_ip, event_date) VALUES (?,?,?,?)";
                return $this->conn->prepare($sql)->execute([$event->name, $event->auth, $event->userIp, $event->eventDate]);
            } catch (PDOException $exception) {
                return false;
            }
        } else 
            return false;
    }
    public function getStatistics($stat)
    {
        if ($this->conn) {
            try {
                // Тут писать
                // $filter = "SELECT * FROM events WHERE ";
            } catch (PDOException $exception) {
                return false;
            }
        } else 
            return false;
    }
}