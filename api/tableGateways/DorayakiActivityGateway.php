<?php
use API\DB\Database;
require_once(getcwd()."/db/Database.php");

class DorayakiActivityGateway {
    private $dbConnection;

    function __construct()
    {
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO dorayaki_activities
                (dorayaki_id, user_id, action_type, state_before, state_after) 
            VALUES 
                (:dorayakiId, :userId, :actionType, :stateBefore, :stateAfter)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "dorayakiId" => $input["dorayakiId"],
            "userId" => $input["userId"],
            "actionType" => $input["actionType"],
            "stateBefore" => $input["stateBefore"],
            "stateAfter" => $input["stateAfter"],
        ));
        return $stmt->rowCount();
    }
}