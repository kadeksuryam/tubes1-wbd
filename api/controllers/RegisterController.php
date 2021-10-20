<?php

use API\DB\Database;
use API\TableGateways\UserGateway;
use API\TableGateways\UserSessionGateway;

require_once("Controller.php");
require_once(getcwd()."/utils/validationUtil.php");
require_once(getcwd()."/db/Database.php");

class RegisterController implements Controller {
    private $userGateway;
    private $userSessionGateway;
    private $requestMethod;
    private $requestParam;
    private $requestBody;
    private $dbConnection;

    public function __construct()
    {
        $this->userGateway = new UserGateway();
        $this->userSessionGateway = new UserSessionGateway();
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestParam = $_GET;
        $this->requestBody = $_POST;
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function processRequest()
    {   
        try{
            if($this->requestMethod != 'POST') {
                $response["status_code_header"] = "HTTP/1.1 404 Not Found";
                $response["body"] = ["message" => "Method not found"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
            }

            $this->validateRequest();

            if(array_key_exists("req_type", $this->requestParam)) {
                if($this->requestParam["req_type"] == "validationOnly") {
                    $response["status_code_header"] = "HTTP/1.1 200 OK";
                    $response["body"] = ["message" => "All validation success"];
                    header($response["status_code_header"]);
                    echo json_encode($response["body"]);
                    exit();
                }
            }
        }
        catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        try{
            // register business logic
            $this->dbConnection->beginTransaction();

            $reqUsername = $this->requestBody["username"];
            $reqEmail = $this->requestBody["email"];
            $reqPassword = $this->requestBody["password"];
            $inputUser = ["username" => $reqUsername, 
                "email" => $reqEmail, "password" => $reqPassword];

            $this->userGateway->insert($inputUser);

            $sessionId = uniqid();
            $dbUser = $this->userGateway->findByUsername($reqUsername);
            $dbUserId = $dbUser[0]["id"];
            $dbIsAdmin = $dbUser[0]["is_admin"];
            $inputSession = ["user_id" => $dbUserId, "session_id" => $sessionId, "is_admin" => $dbIsAdmin];

            $this->userSessionGateway->insert($inputSession);

            $this->dbConnection->commit();

            setcookie("user_id", $inputSession["user_id"], time() + 86400, "/");
            setcookie("session_id", $inputSession["session_id"], time() + 86400, "/", "", false, true);
            setcookie("is_admin", $inputSession["is_admin"], time() + 86400, "/");

            $response["status_code_header"] = "HTTP/1.1 200 OK";
            $response["body"] = ["message" => "successfully registered"];

            header($response["status_code_header"]);
            echo json_encode($response["body"]);
        }
        catch(\Exception $e) {
            $this->dbConnection->rollBack();
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    private function validateRequest() {
        if(!array_key_exists("email", $this->requestBody)) {
            $this->badRequestResponse(["email" => "Email field is required"]);
        }
        if(!array_key_exists("password", $this->requestBody)) {
            $this->badRequestResponse(["password" => "Password field is required"]);
        }
        if(!array_key_exists("username", $this->requestBody)) {
            $this->badRequestResponse(["username" => "Username field is required"]);
        }

        $reqEmail = $this->requestBody["email"];
        $reqPassword = $this->requestBody["password"];
        $errorMsg = [];
        
        //validate username
        $this->validateUsername($errorMsg);
        if(!isValidEmail($reqEmail)) {
            array_push($errorMsg, ["email" => "Invalid email format"]);
        }
        if(!isValidPassword($reqPassword)) {
            array_push($errorMsg, ["password" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character."]);
        }     
        if(!empty($errorMsg)) {
            $this->badRequestResponse($errorMsg);
        }
    }

    private function validateUsername(&$errorMsg) {
        $reqUsername = $this->requestBody["username"];
        
        if(strlen($reqUsername) == 0) {
            array_push($errorMsg, ["username" => "Username can't be empty"]);
            return;
        }
        if(!preg_match("/^[a-zA-Z0-9_]+$/", $reqUsername)) {
            array_push($errorMsg, ["username" => "Username not valid, only alphanumeric and underscore are allowed"]);
            return;
        }
        //check username uniqueness
        $res = $this->userGateway->findByUsername($reqUsername);
        if(!empty($res)) {
            array_push($errorMsg, ["username" => "Username must be unique, please pick another username"]);
        }
    }

    private function badRequestResponse($errorMsg) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["message" => $errorMsg]);
        exit();
    }
}