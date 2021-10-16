<?php
namespace API\Controllers;

use API\TableGateways\UserGateway;
use Controller;

require("../tableGateways/UserGateway.php");
require("Controller.php");

class UserController implements Controller {
    private $requestMethod;
    private $userId;

    private $userGateway;

    public function __construct($userId)
    {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->userId = $userId;

        $this->userGateway = new UserGateway();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->userId) {
                    $response = $this->getUser((int)$this->userId);
                } else {
                    $response = $this->getAllUsers();
                };
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response["body"]) {
            echo $response["body"];
        }
    }

    private function getAllUsers()
    {
        $result = $this->userGateway->findAll();
        $response["status_code_header"] = "HTTP/1.1 200 OK";
        $response["body"] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->userGateway->find($id);
        $response["status_code_header"] = "HTTP/1.1 200 OK";
        $response["body"] = json_encode($result);
        return $response;
    }

    private function notFoundResponse()
    {
        $response["status_code_header"] = "HTTP/1.1 404 Not Found";
        $response["body"] = null;
        return $response;
    }
}