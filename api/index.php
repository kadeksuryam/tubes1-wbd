<?php

use API\Controllers\UserController;
use API\DB\Database;

require_once("./controllers/UserController.php");
require_once("./controllers/LoginController.php");
require_once("./controllers/RegisterController.php");
require_once("./controllers/DorayakiController.php");
require_once("./utils/AuthorizationUtil.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);


$requestMethod = $_SERVER["REQUEST_METHOD"];
$authenticationUtil = new AuthenticationUtil(Database::getInstance()->getDbConnection());
$authorizationUtil = new AuthorizationUtil();

if($uri[1] == "api") {
    if($uri[2] != "login" || $uri[2] != "register") {
        if($authenticationUtil->isCookieMalformed()) {
            malformedCookieResponse();
            exit();
        }
        if(!$authenticationUtil->isCookieStillValid()) {
            cookieInvalidResponse();
            exit();
        }
    }
    $authorizationUtil->authorizeRequest($requestMethod, $uri);
    switch ($uri[2]) {
        case "users":
            $userId = null;
            if(isset($uri[3])) {
                $userId = $uri[3];
            }
            $controller = new UserController($userId);
            break;
        case "login":
            $controller = new LoginController();
            break;
        case "register":
            $controller = new RegisterController();
            break;
        case "dorayakis":
            $dorayakiId = null;
            if(isset($uri[3])) {
                $dorayakiId = $uri[3];
            }
            $controller = new DorayakiController($dorayakiId);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["message" => "resources not found"]);
            exit();
    }
    
    $controller->processRequest();
}
else {
    echo $_SERVER['REQUEST_URI'];
    echo file_get_contents($_SERVER['REQUEST_URI']);
}

function malformedCookieResponse() {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "malformed cookies"]);
}

function cookieInvalidResponse() {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "session expired"]);
}


