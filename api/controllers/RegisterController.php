<?php

use API\TableGateways\UserGateway;
use API\TableGateways\UserSessionGateway;

require_once("Controller.php");
require_once("../utils/validationUtil.php");

class RegisterController implements Controller {
    private $userGateway;
    private $userSessionGateway;
    private $requestMethod;
    private $requestParam;
    private $requestBody;
    
    public function __construct()
    {
        $this->userGateway = new UserGateway();
        $this->userSessionGateway = new UserSessionGateway();
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestParam = $_GET;
        $this->requestBody = $_POST;
    }

    public function processRequest()
    {
        if($this->requestMethod != 'POST') {
            $response["status_code_header"] = "HTTP/1.1 404 Not Found";
            $response["body"] = ["message" => "Method not found"];
            header($response["status_code_header"]);
            echo json_encode($response["body"]);
            exit();
        }

        $validationErrorResponse = $this->validateRequest();
        if(!empty($validationErrorResponse)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode($validationErrorResponse);
            exit();
        }

        if($this->requestParam["type"] == "validationOnly") {
            $response["status_code_header"] = "HTTP/1.1 200 OK";
            $response["body"] = ["message" => "All validation success"];
            header($response["status_code_header"]);
            echo json_encode($response["body"]);
            exit();
        }

        $reqUsername = $this->requestBody["username"];
        $reqEmail = $this->requestBody["email"];
        $reqPassword = $this->requestBody["password"];
        
        $inputUser = ["username" => $reqUsername, 
            "email" => $reqEmail, "password" => password_hash($reqPassword, PASSWORD_DEFAULT)];

        $this->userGateway->insert($inputUser);
        $sessionId = uniqid();
        
        $dbUser = $this->userGateway->findByUsername($reqUsername);
        $dbUserId = $dbUser[0]["id"];
        $dbIsAdmin = $dbUser[0]["is_admin"];

        $inputSession = ["user_id" => $dbUserId, "session_id" => $sessionId, "is_admin" => $dbIsAdmin];
        $this->userSessionGateway->insert($inputSession);

        setcookie("user_id", $inputSession["user_id"], time() + 86400, "/");
        setcookie("session_id", $inputSession["session_id"], time() + 86400, "/");
        setcookie("is_admin", $inputSession["is_admin"], time() + 86400, "/");

        $response["status_code_header"] = "HTTP/1.1 200 OK";
        $response["body"] = ["message" => "successfully registered"];

        header($response["status_code_header"]);
        echo json_encode($response["body"]);
    }

    private function validateRequest() {
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
        
        return $errorMsg;
    }

    private function validateUsername(&$errorMsg) {
        $reqUsername = $this->requestBody["username"];
        
        if(strlen($reqUsername) == 0) {
            array_push($errorMsg, ["username" => "Username can't be empty"]);
            return;
        }
        //check username uniqueness
        $res = $this->userGateway->findByUsername($reqUsername);
        if(!empty($res)) {
            array_push($errorMsg, ["username" => "Username must be unique, please pick another username"]);
        }
    }
}