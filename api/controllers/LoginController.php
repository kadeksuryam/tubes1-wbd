<?php

use API\TableGateways\UserGateway;
use API\TableGateways\UserSessionGateway;
require_once("../tableGateways/UserGateway.php");
require_once("../tableGateways/UserSessionGateway.php");
require_once("Auth.php");

class LoginController {
    private $dbCon;
    private $requestMethod;
    private $userGateway;
    private $userSessionGateway;
    private $auth;

    public function __construct($dbCon, $requestMethod)
    {
        $this->dbCon = $dbCon;
        $this->requestMethod = $requestMethod;
        $this->userGateway = new UserGateway($dbCon);
        $this->userSessionGateway = new UserSessionGateway($dbCon);
        $this->auth = new Auth($dbCon);
    }

    public function processRequest()
    {
        //$response = "";
        if($this->requestMethod != 'POST') {
            $response = $this->methodNotFoundResponse();
        }
        else {
            if($this->auth->isLoggedIn()) {
                $response["status_code_header"] = "HTTP/1.1 200 OK";
                $response["body"] = ["message" => "successfully logged in: already loggedin"];
            }
            else {
                //business logic login
                $reqEmail = $_POST["email"];
                $reqPassword = $_POST["password"];

                $reqUser = $this->userGateway->findByEmail($reqEmail);

                if(empty($reqUser)) {
                    $response = $this->userNotFoundResponse();
                }
                else {
                    $dbUserId = $reqUser[0]["id"];
                    $dbPassword = $reqUser[0]["password"];
                    $dbIsAdmin = $reqUser[0]["is_admin"];
                    $isPasswordMatch = password_verify($reqPassword, $dbPassword);
                    $session_id  = uniqid();

                    if($isPasswordMatch) {
                        $input = ["user_id" => $dbUserId, "session_id" => $session_id, "is_admin" => $dbIsAdmin];
                        $this->userSessionGateway->insert($input);
                        $response["status_code_header"] = "HTTP/1.1 200 OK";
                        $response["body"] = ["message" => "successfully logged in"];

                        setcookie("user_id", $input["user_id"], time() + (86400 * 30), "/");
                        setcookie("session_id", $input["session_id"], time() + (86400 * 30), "/");
                        setcookie("is_admin", $input["is_admin"], time() + (86400 * 30), "/");
                    }
                }
            }
        }

        header($response["status_code_header"]);
        if ($response["body"]) {
            echo json_encode($response["body"]);
        }
    }

    private function methodNotFoundResponse()
    {
        $response["status_code_header"] = "HTTP/1.1 404 Not Found";
        $response["body"] = ["error" => "Method not found"];
        return $response;
    }

    private function userNotFoundResponse()
    {
        $response["status_code_header"] = "HTTP/1.1 404 Not Found";
        $response["body"] = ["error" => "User not found"];
        return $response;
    }
}