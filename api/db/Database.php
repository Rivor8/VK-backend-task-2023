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
                $query = null;
                switch ($stat->aggregation) {
                    case "byname":
                        $agreg = "name_event";
                        break;
                    case "byuserip":
                        $agreg = "user_ip";
                        break;
                    case "bystatus":
                        $agreg = "is_auth";
                        break;
                    default:
                        return false;
                }
                $sql = "SELECT count(*) AS count, " . $agreg . " FROM events ";
                $params = array();
                if ($stat->daterange != null or $stat->names != null) {
                    $sql .= "WHERE ";
                    if ($stat->daterange != null) {
                        $sql .= "event_date >= ? AND event_date <= ? ";
                        $params = array_merge($params, [$stat->daterange[0], $stat->daterange[1]]);
                    }
                    if ($stat->names != null) {
                        if ($stat->daterange != null) {
                            $sql .= "AND ";
                        }
                        $sql .= "(name_event = ?";
                        for ($i = 0; $i < count($stat->names) - 1; $i++) {
                            $sql .= " OR name_event = ? ";
                        }
                        $sql .= ") ";
                        $params = array_merge($params, $stat->names);
                    }
                }

                $sql .= "GROUP BY " . $agreg . ";";

                $query = $this->conn->prepare($sql);
                if (!$query->execute(array_merge($params))) {
                    return false;
                }
                return $query->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $exception) {
                return false;
            }
        } else
            return false;
    }
}