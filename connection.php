<?php

class Connection
{

    private $databaseFile;
    private $connection;

    public function __construct()
    {
        $this->databaseFile = realpath(__DIR__ . "/database/db.sqlite");
        $this->connect();
    }

    private function connect()
    {
        return $this->connection = new PDO("sqlite:{$this->databaseFile}");
    }

    public function getConnection()
    {
        return $this->connection ?: $this->connection = $this->connect();
    }

    public function query($query)
    {
        $result      = $this->getConnection()->query($query);

        $result->setFetchMode(PDO::FETCH_INTO, new stdClass);

        return $result;
    }

    public function isVotationTime()
    {
        $sql = "SELECT
                    date('now') BETWEEN date(start_date) AND date(end_date) as status,
                    date('now') < date(start_date) as is_before,
                    date('now') > date(end_date) as is_after
                FROM config
                WHERE id=1";
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute();
        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'status' => $row['status'],
                'is_before' => $row['is_before'],
                'is_after' => $row['is_after'],
            ));
        }
        return $result[0];
    }

    public function checkVote($cpf, $matricula)
    {
        $sql = "SELECT * FROM vote WHERE cpf = :cpf AND matricula = :matricula";
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute([
            ':cpf' => $cpf,
            ':matricula' => $matricula,
        ]);

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'matricula' => $row['matricula'],
                'cpf' => $row['cpf'],
                'created_at' => $row['created_at'],
                'vote' => $row['vote'],
                'terms' => $row['terms'],
                'unique_id' => $row['unique_id'],
            ));
        }

        return sizeof($result) > 0 ? base64_encode(json_encode($result[0])) : false;
    }

    public function authConfig($admin_user, $admin_password)
    {
        $sql = "SELECT * FROM config WHERE admin_user = :admin_user AND admin_password = :admin_password";
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute([
            ':admin_user' => $admin_user,
            ':admin_password' => $admin_password,
        ]);

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'admin_user' => $row['admin_user'],
                'admin_password' => $row['admin_password'],
                'admin_name' => $row['admin_name'],
            ));
        }

        return $result;
    }

    public function verifyUserByCode($unique_id)
    {
        $sql = "SELECT * FROM users WHERE unique_id = :unique_id";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute([
            ':unique_id' => $unique_id,
        ]);

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'cpf' => $row['cpf'],
                'matricula' => $row['matricula'],
                'email' => $row['email'],
                'password' => $row['password'],
                'unique_id' => $row['unique_id'],
                'is_active' => $row['is_active'],
            ));
        }

        return $result;
    }

    public function unlockUser($email, $password, $unique_id)
    {

        try {
            $sql = "UPDATE users SET is_active = 1 WHERE email = '" . $email . "' AND password = '" . $password  . "' AND unique_id = '" . $unique_id  . "' ";
            $stmt = $this->connection->prepare($sql);
            $execute = $stmt->execute();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function auth($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = '" . $email . "' AND password = '" . $password  . "' ";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'cpf' => $row['cpf'],
                'matricula' => $row['matricula'],
                'email' => $row['email'],
                'password' => $row['password'],
                'unique_id' => $row['unique_id'],
                'is_active' => $row['is_active'],
            ));
        }

        return $result;
    }

    public function checkAssociate($cpf, $matricula)
    {
        $sql = "SELECT * FROM associates WHERE cpf = '" . $cpf . "' AND matricula = '" . $matricula  . "' ";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'nome' => $row['nome'],
                'cpf' => $row['cpf'],
                'matricula' => $row['matricula'],
                'lota' => $row['lota'],
                'tipo' => $row['tipo'],
                'superintendencia' => $row['superintendencia'],
                'cod_sup' => $row['cod_sup'],
            ));
        }

        return $result;
    }

    public function getCandidates($cod_sup)
    {
        $sql = "SELECT * FROM candidates WHERE cod_sup = '" . $cod_sup . "' ";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'nome' => $row['nome'],
                'matricula' => $row['matricula'],
                'superintendencia' => $row['superintendencia'],
                'cod_sup' => $row['cod_sup'],
            ));
        }

        return $result;
    }

    public function getCandidateByMatricula($matricula)
    {
        $sql = "SELECT * FROM candidates WHERE matricula = '" . $matricula . "' ";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'id' => $row['id'],
                'nome' => $row['nome'],
                'matricula' => $row['matricula'],
                'superintendencia' => $row['superintendencia'],
                'cod_sup' => $row['cod_sup'],
            ));
        }

        return $result;
    }

    public function getSuperintendenciasList()
    {
        $sql = "SELECT DISTINCT superintendencia, cod_sup FROM candidates ORDER BY 2 ASC";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'superintendencia' => $row['superintendencia'],
                'cod_sup' => $row['cod_sup'],
            ));
        }

        return $result;
    }

    public function getSuperintendenciaParticipants($cod_sup)
    {
        $sql = "SELECT count() as total 
                FROM users 
                INNER JOIN associates ON associates.matricula = users.matricula
                WHERE associates.cod_sup = '" . $cod_sup . "'";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'total' => $row['total'],
            ));
        }

        return $result;
    }

    public function getVotesCounterBySuperintendencia($cod_sup)
    {
        $sql = "SELECT count() as votos, 
                (SELECT count() as total FROM associates WHERE associates.cod_sup = '" . $cod_sup . "') as total
                FROM vote
                JOIN associates on associates.matricula = vote.matricula
                WHERE associates.cod_sup = '" . $cod_sup . "' ORDER BY 2 DESC";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'votos' => $row['votos'],
                'total' => $row['total'],
            ));
        }

        return $result;
    }

    public function getVotesBySuperintendencia($cod_sup)
    {
        $sql = "SELECT DISTINCT candidates.nome as nome, candidates.matricula,
                (SELECT DISTINCT count(vote) FROM vote WHERE vote.vote = candidates.matricula) as voto
                FROM candidates 
                WHERE cod_sup = '" . $cod_sup . "' ORDER BY 3 DESC";
        $stmt = $this->connection->prepare($sql);
        $execute = $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($result, array(
                'nome' => $row['nome'],
                'matricula' => $row['matricula'],
                'voto' => $row['voto'],
            ));
        }

        return $result;
    }

    public function registerUser($email, $password, $cpf, $matricula, $unique_id, $is_active)
    {
        try {
            $sql = "INSERT INTO users (cpf, matricula, email, password, unique_id, is_active ) VALUES (:cpf, :matricula, :email, :password, :unique_id, :is_active)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':cpf' => $cpf,
                ':matricula' => $matricula,
                ':email' => $email,
                ':password' => $password,
                ':unique_id' => $unique_id,
                ':is_active' => $is_active ? 1 : 0,
            ]);

            return $this->connection->lastInsertId() > 0;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function insertVote($vote, $terms, $matricula, $cpf, $created_at, $unique_id)
    {
        $sql = "INSERT INTO vote (vote, terms, matricula, cpf, created_at, unique_id) VALUES (:vote, :terms, :matricula, :cpf, :created_at, :unique_id)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':vote' => $vote,
            ':terms' => $terms,
            ':matricula' => $matricula,
            ':cpf' => $cpf,
            ':created_at' => $created_at,
            ':unique_id' => $unique_id,
        ]);

        return $this->connection->lastInsertId() > 0;
    }

    public function insertUser($name, $email)
    {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
        ]);

        return $this->connection->lastInsertId();
    }

    public function updateDates($start_date, $end_date, $admin_user, $id)
    {
        try {
            $sql = "UPDATE config SET start_date = :start_date, end_date = :end_date WHERE admin_user = :admin_user AND id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':admin_user' => $admin_user,
                ':id' => $id,
            ]);

            return true;
        } catch (\Throwable $th) {
            print_r($th);
            return false;
        }
    }
}
