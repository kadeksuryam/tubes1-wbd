<?php

use API\TableGateways\UserSessionGateway;
require_once("../tableGateways/UserSessionGateway.php");

class Auth {
    private $dbCon;
    private $userSessionGateway;

    public function __construct($dbCon)
    {
        $this->dbCon = $dbCon;
        $this->userSessionGateway = new UserSessionGateway($dbCon);
    }

    public function isLoggedIn() {
        $sessionIdCookie = $_COOKIE["session_id"];
        $userIdCookie = $_COOKIE["user_id"];
        $isAdminCookie = $_COOKIE["is_admin"];

        if(!(isset($sessionIdCookie) && isset($userIdCookie) && isset($isAdminCookie))) return false;

        $reqUser = $this->userSessionGateway->findBySessionId($sessionIdCookie);
        $dbSessionId = $reqUser[0]["session_id"];
        $dbUserId = $reqUser[0]["user_id"];
        $dbIsAdmin = $reqUser[0]["is_admin"];
        $dbUpdatedAt = $reqUser[0]["updated_at"];

        $isCookieValid = ($dbSessionId === $sessionIdCookie) && 
            ($dbUserId === $userIdCookie) && ($dbIsAdmin === $isAdminCookie);
        
        if(!$isCookieValid) return false;

        $currDate = new DateTime(date("Y-m-d H:i:s"));
        $dbDate = new DateTime($dbUpdatedAt);

        $diffDate = $currDate->getTimestamp() - $dbDate->getTimestamp();

        //session life time: 1 hour
        if($diffDate > 3600) return false;

        return true;
    }

}
