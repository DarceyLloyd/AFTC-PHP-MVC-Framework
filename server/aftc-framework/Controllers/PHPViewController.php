<?php

namespace AFTC\Controllers;

use AFTC\Config\Config;
use AFTC\Libs\ApiResponseLib;
use AFTC\Libs\CookieLib;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\PasswordLib;
use AFTC\Libs\SecurityLib;
use AFTC\Libs\SendSmtpMailLib;
use AFTC\Libs\SessionLib;
use AFTC\VOs\ApiResponseVo;

class PHPViewController
{
    protected SessionLib $sessionLib;
    protected PasswordLib $passwordLib;
    protected JwtLib $jwtLib;
    protected SecurityController $securityController;
    protected DatabaseLib $db;
    protected SendSmtpMailLib $mail;
    protected CookieLib $cookieLib;
    protected SecurityLib $securityLib;
    protected ApiResponseLib $apiResponseLib;
    protected ApiResponseVo $apiResponseVo;

    public function __construct()
    {
        $this->sessionLib = new SessionLib();
        $this->cookieLib = new CookieLib();
        $this->passwordLib = new PasswordLib();
        $this->jwtLib = new JwtLib();
        $this->securityLib = new SecurityLib();
        $this->mail = new SendSmtpMailLib();
        $this->db = DatabaseLib::getInstance();


    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


}