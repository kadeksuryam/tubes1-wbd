<?php

use API\TableGateways\UserSessionGateway;
require_once(getcwd()."/tableGateways/UserSessionGateway.php");

class AuthenticationUtil {
    private $userSessionGateway;
    private $sessionIdCookie;
    private $userIdCookie;
    private $isAdminCookie;
    private $dbUserSession;

    public function __construct()
    {
        $this->userSessionGateway = new UserSessionGateway();
        $this->sessionIdCookie = $_COOKIE["session_id"];
        $this->userIdCookie = $_COOKIE["user_id"];
        $this->isAdminCookie = $_COOKIE["is_admin"];
        $this->dbUserSession = $this->userSessionGateway->findBySessionId($this->sessionIdCookie);
    }

    public function isCookieMalformed() {
        if(!(isset($this->sessionIdCookie) && 
            isset($this->userIdCookie) && 
            isset($this->isAdminCookie)) || empty($this->dbUserSession)) return true;

        $dbSessionId = $this->dbUserSession[0]["session_id"];
        $dbUserId = $this->dbUserSession[0]["user_id"];
        $dbIsAdmin = $this->dbUserSession[0]["is_admin"];

        $isCookieMalformed = !($dbSessionId == $this->sessionIdCookie && 
            $dbUserId == $this->userIdCookie && $dbIsAdmin == $this->isAdminCookie);
        
        return $isCookieMalformed;
    }

    public function isCookieStillValid() {
        if($this->isCookieMalformed()) return false;

        $dbUpdatedAt = $this->dbUserSession[0]["updated_at"];

        $currDate = new DateTime(date("Y-m-d H:i:s"));
        $dbDate = new DateTime($dbUpdatedAt);

        $diffDate = $currDate->getTimestamp() - $dbDate->getTimestamp();

        //session life time: 1 hour
        if($diffDate > 3600) return false;

        return true;
    }
}
