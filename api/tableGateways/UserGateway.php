<?php
namespace API\TableGateways;

use API\DB\Database;
require_once("../db/Database.php");

class UserGateway {
    private $dbCon = null;

    public function __construct()
    {
        $this->dbCon = Database::getInstance()->getDbConnection();
    }

    public function findAll()
    {
        $stmt = <<<EOS
            SELECT * FROM users;
        EOS;

        try {
            $stmt = $this->dbCon->query($stmt);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $stmt = <<<EOS
            SELECT * FROM users WHERE id = :id;
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array("id" => $id));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findByEmail($email)
    {
        $stmt = <<<EOS
            SELECT * FROM users WHERE email = :email;
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array("email" => $email));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findByUsername($username)
    {
        $stmt = <<<EOS
            SELECT * FROM users WHERE username = :username;
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array("username" => $username));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO users 
                (email, username, password, is_admin) 
            VALUES 
                (:email, :username, :password, :is_admin)
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array(
                "email" => $input["email"],
                "username" => $input["username"],
                "password" => password_hash($input["password"], PASSWORD_DEFAULT),
                "is_admin" => 0
            ));
            return $stmt->rowCount();
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update($id, Array $input)
    {
        $stmt = <<<EOS
            UPDATE users
            SET
                email = :email,
                username = :username
            WHERE id = :id;
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array(
                "id" => (int)$id,
                "email" => $input["email"],
                "username" => $input["username"],
            ));
            return $stmt->rowCount();
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $stmt = <<<EOS
            DELETE FROM users
            WHERE id = :id;
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array("id" => (int)$id));
            
            return $stmt->rowCount();
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
        
    }
}