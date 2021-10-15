<?php

use API\Controllers\UserController;

require "../db/bootstrap.php";
require "../controllers/UserController.php";
require "../controllers/LoginController.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if($uri[1] !== "users" && $uri[1] !== "login") {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($uri[1]) {
    case "users":
        $userId = null;
        if(isset($uri[2])) {
            $userId = (int)$uri[2];
        }

        $controller = new UserController($dbConnection, $requestMethod, $userId);
        break;
    case "login":
        $controller = new LoginController($dbConnection, $requestMethod);
        break;
}
$controller->processRequest();


