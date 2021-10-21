<?php
use API\DB\Database;
require_once(getcwd()."/db/Database.php");

class SearchGateway {
    private $dbConnection;

    function __construct()
    {
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function findDorayakiByName($name)
    {
        $stmt_all = <<<EOS
            SELECT * FROM dorayakis WHERE nama like :name
        EOS;
    
        $countAllMatch = count($this->dbConnection->query($stmt_all));
        $num_page = ceil($countAllDorayaki/$size);
        $start = $size * ($page - 1);

        $stmt = <<<EOS
            SELECT * FROM dorayakis WHERE nama like :name ORDER BY terjual LIMIT :size OFFSET :start
        EOS;
    
        $stmt = $this->dbConnection->query($stmt);
        $dorayakiPerPage = count($this->dbConnection->query($stmt));
        $allDorayaki = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $retPage = [$num_page, $page, $dorayakiPerPage];
        $result = ["page" => $retPage, "payload" => $allDorayaki];
        return $result;
    }
}