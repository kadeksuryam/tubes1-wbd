<?php

use API\TableGateways\UserGateway;
use API\TableGateways\UserSessionGateway;
require_once("../tableGateways/UserGateway.php");
require_once("../tableGateways/UserSessionGateway.php");
require_once("../utils/AuthUtil.php");
require_once("Controller.php");

class LoginController implements Controller {
    private $requestMethod;
    private $requestBody;

    private $userGateway;
    private $userSessionGateway;
    private $auth;

    public function __construct()
    {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestBody = $_POST;

        $this->userGateway = new UserGateway();
        $this->userSessionGateway = new UserSessionGateway();
        $this->auth = new AuthUtil();
    }

    public function processRequest()
    {
        try {
            if($this->requestMethod != 'POST') {
                $response["status_code_header"] = "HTTP/1.1 404 Not Found";
                $response["body"] = ["message" => "Method not found"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
            }

            if($this->auth->isCookieStillValid()) {
                $response["status_code_header"] = "HTTP/1.1 200 OK";
                $response["body"] = ["message" => "successfully logged in: already loggedin"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
            }
        
            //business logic login
            $reqUsername = $this->requestBody["username"];
            $reqPassword = $this->requestBody["password"];
            $dbUser = $this->userGateway->findByUsername($reqUsername);

            if(empty($dbUser)) {
                $response["status_code_header"] = "HTTP/1.1 400 Bad Request";
                $response["body"] = ["message" => "Invalid username or password"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
            }

            $dbUserId = $dbUser[0]["id"];
            $dbPassword = $dbUser[0]["password"];
            $dbIsAdmin = $dbUser[0]["is_admin"];
            $isPasswordMatch = password_verify($reqPassword, $dbPassword);

            if($isPasswordMatch) {
                $session_id  = uniqid();
                $input = ["user_id" => $dbUserId, "session_id" => $session_id, "is_admin" => $dbIsAdmin];
                $this->userSessionGateway->insert($input);
                $response["status_code_header"] = "HTTP/1.1 200 OK";
                $response["body"] = ["message" => "successfully logged in"];

                setcookie("user_id", $input["user_id"], time() + 86400, "/");
                setcookie("session_id", $input["session_id"], time() + 86400, "/");
                setcookie("is_admin", $input["is_admin"], time() + 86400, "/");
            }
            else {
                $response["status_code_header"] = "HTTP/1.1 400 Bad Request";
                $response["body"] = ["message" => "Invalid username or password"];
            }

            header($response["status_code_header"]);
            echo json_encode($response["body"]);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
        }

    }
}
