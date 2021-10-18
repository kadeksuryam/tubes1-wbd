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
                (state_user, action_type, state_before, state_after) 
            VALUES 
                (:stateUser, :actionType, :stateBefore, :stateAfter)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "stateUser" => $input["stateUser"],
            "actionType" => $input["actionType"],
            "stateBefore" => $input["stateBefore"],
            "stateAfter" => $input["stateAfter"],
        ));
        return $stmt->rowCount();
    }
}