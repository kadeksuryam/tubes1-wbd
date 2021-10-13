<?php

namespace API\Controllers;

require_once("../db/dbConfig.php");

if(isset($_POST["username"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmtFindRequestedUser = $db_connection->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmtFindRequestedUser->bindParam(1, $username);

    $findRequestedUserRes = $stmtFindRequestedUser->execute();
    if(!$findRequestedUserRes) {
        $responseBody = ["status" => "error", "description" => $stmtFindRequestedUser->error];
        http_response_code(500);
        exit(json_encode($responseBody));
    }

    echo $findRequestedUserRes;

}