<?php
namespace API\TableGateways;
use API\DB\Database;
require_once("../db/Database.php");

class UserSessionGateway {
    private $dbCon = null;
    
    public function __construct()
    {
        $this->dbCon = Database::getInstance()->getDbConnection();
    }

    public function findAll()
    {
        $stmt = <<<EOS
            SELECT * FROM user_sessions;
        EOS;

        $stmt = $this->dbCon->query($stmt);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function find($id)
    {
        $stmt = <<<EOS
            SELECT * FROM user_sessions WHERE id = :id;
        EOS;

        $stmt = $this->dbCon->prepare($stmt);
        $stmt->execute(array("id" => $id));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function findBySessionId($sessionId)
    {
        $stmt = <<<EOS
            SELECT * FROM user_sessions WHERE session_id = :sessionId;
        EOS;
        
        $stmt = $this->dbCon->prepare($stmt);
        $stmt->execute(array("sessionId" => $sessionId));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO user_sessions 
                (user_id, session_id, is_admin) 
            VALUES 
                (:user_id, :session_id, :is_admin)
        EOS;

        $stmt = $this->dbCon->prepare($stmt);
        $stmt->execute(array(
            "user_id" => $input["user_id"],
            "session_id" => $input["session_id"],
            "is_admin" => $input["is_admin"],
        ));
        return $stmt->rowCount();
    }

}