<?php
namespace API\TableGateways;

class UserSessionGateway {
    private $dbCon = null;
    
    public function __construct($dbCon)
    {
        $this->dbCon = $dbCon;
    }

    public function findAll()
    {
        $stmt = <<<EOS
            SELECT * FROM user_sessions;
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
            SELECT * FROM user_sessions WHERE id = :id;
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

    public function findBySessionId($sessionId)
    {
        $stmt = <<<EOS
            SELECT * FROM user_sessions WHERE session_id = :sessionId;
        EOS;
        
        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array("sessionId" => $sessionId));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO user_sessions 
                (user_id, session_id, is_admin) 
            VALUES 
                (:user_id, :session_id, :is_admin)
        EOS;

        try {
            $stmt = $this->dbCon->prepare($stmt);
            $stmt->execute(array(
                "user_id" => $input["user_id"],
                "session_id" => $input["session_id"],
                "is_admin" => $input["is_admin"],
            ));
            return $stmt->rowCount();
        } catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }

}