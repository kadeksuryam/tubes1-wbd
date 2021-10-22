<?php

use API\Controllers\UserController;
use API\DB\Database;
error_reporting(0);
require_once("./controllers/UserController.php");
require_once("./controllers/LoginController.php");
require_once("./controllers/RegisterController.php");
require_once("./controllers/DorayakiController.php");
require_once("./controllers/RiwayatDorayakiController.php");
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
    switch ($uri[2]) {
        case "users":
            verifyCookie($authenticationUtil);
            $authorizationUtil->authorizeRequest($requestMethod, $uri);
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
            verifyCookie($authenticationUtil);
            $authorizationUtil->authorizeRequest($requestMethod, $uri);
            $dorayakiId = null;
            if(isset($uri[3])) {
                $dorayakiId = $uri[3];
            }
            $controller = new DorayakiController($dorayakiId);
            break;
        case "riwayat":
            verifyCookie($authenticationUtil);
            $authorizationUtil->authorizeRequest($requestMethod, $uri);
            if($uri[3] == "dorayaki") {
                $controller = new RiwayatDorayakiController();
                break;
            }
        case "auth":
            if(isset($uri[3])) {
                if($uri[3] == "verify-cookie") {
                    verifyCookie($authenticationUtil);
                    header("HTTP/1.1 200 OK");
                    echo json_encode(["message" => "Cookie valid"]);
                    exit();
                }
            }
            break;
        case "logout":
            foreach($_COOKIE as $key => $val) {
                unset($_COOKIE[$key]);
                setcookie($key, "", time()-3600, "/");
            }
            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "logout success"]);
            exit();
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["message" => "resources not found"]);
            exit();
    }
    
    $controller->processRequest();
}
else {
    // echo $_SERVER['REQUEST_URI'];
    $uriGet = "/".$uri[1]."/".$uri[2]; 
    if(!file_get_contents($uriGet)) echo "failed";
    // echo $uriGet;
   // echo file_get_contents($uriGet);
}

function verifyCookie($authenticationUtil) {
    if($authenticationUtil->isCookieMalformed()) {
        malformedCookieResponse();
    }
    if(!$authenticationUtil->isCookieStillValid()) {
        cookieInvalidResponse();
    }
}

function malformedCookieResponse() {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "malformed cookies"]);
    exit();
}

function cookieInvalidResponse() {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "session expired"]);
    exit();
}

