<?php

use API\DB\Database;

class AuthorizationUtil {
    //private $dbConnection;
    private $isAdmin;
    private $userId;

    public function __construct()
    {
       // $this->dbConnection = Database::getInstance()->getInstance();
       $this->isAdmin = $_COOKIE["is_admin"];
       $this->userId = $_COOKIE["user_id"];
    }

    public function authorizeRequest($requestMethod, $uri) {
        switch($uri[2]) {
            case "users":
                if($this->isAdmin) break;
                switch($requestMethod) {
                    case "GET":
                        if(!isset($uri[3])) $this->forbiddenResponse();
                        if(isset($uri[3]) && $uri[3] != $this->userId) $this->forbiddenResponse();
                        break;
                    case "UPDATE":
                        $this->forbiddenResponse();
                        break;
                    case "DETELE":
                        if(isset($uri[3]) && $uri[3] != $this->userId) $this->forbiddenResponse();
                        break;
                }
                break;
            case "dorayakis":
                if($this->isAdmin) break;
                switch($requestMethod) {
                    case "POST":
                        if($_GET["type"] !== "update")
                            $this->forbiddenResponse();
                        break;
                    case "DELETE":
                        $this->forbiddenResponse();
                        break;
                }
        }
    }

    private function forbiddenResponse() {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(["message" => "admin only operation"]);
        exit();
    }
}