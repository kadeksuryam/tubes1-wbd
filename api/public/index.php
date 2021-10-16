<?php

use API\Controllers\UserController;

require_once("../controllers/UserController.php");
require_once("../controllers/LoginController.php");
require_once("../controllers/RegisterController.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$authUtil = new AuthUtil($dbConnection);

switch ($uri[1]) {
    case "users":
        if($authUtil->isCookieMalformed()) {
            malformedCookieResponse();
            exit();
        }
        if(!$authUtil->isCookieStillValid()) {
            cookieInvalidResponse();
            exit();
        }
        $userId = null;
        if(isset($uri[2])) {
            $userId = (int)$uri[2];
        }
        $controller = new UserController($userId);
        break;
    case "login":
        $controller = new LoginController();
        break;
    case "register":
        $controller = new RegisterController();
        break;

    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "resources not found"]);
        exit();
}

function malformedCookieResponse() {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "malformed cookies"]);
}

function cookieInvalidResponse() {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "session expired"]);
}

$controller->processRequest();

