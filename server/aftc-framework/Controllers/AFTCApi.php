<?php

namespace AFTC\Controllers;

use AFTC\Config\Config;
use AFTC\Config\Vars;
use AFTC\Libs\ApiResponseLib;
use AFTC\Libs\CookieLib;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\PasswordLib;
use AFTC\Libs\SecurityLib;
use AFTC\Libs\SendSmtpMailLib;
use AFTC\Libs\SessionLib;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\ApiResponseVo;
use Exception;

class AFTCApi
{
    public SessionLib $sessionLib;
    public CookieLib $cookieLib;
    public PasswordLib $passwordLib;
    public JwtLib $jwtLib;
    public SecurityLib $securityLib;
    public ApiResponseLib $apiResponseLib;
    public ApiResponseVo $apiResponseVo;
    public DatabaseLib $db;
    public SendSmtpMailLib $mail;

    public function __construct()
    {
        $this->sessionLib = new SessionLib();
        $this->cookieLib = new CookieLib();
        $this->passwordLib = new PasswordLib();
        $this->jwtLib = new JwtLib();
        $this->securityLib = new SecurityLib();
        $this->apiResponseLib = new ApiResponseLib();
        $this->apiResponseVo = new ApiResponseVo();
        $this->mail = new SendSmtpMailLib();
        $this->db = DatabaseLib::getInstance();
    }

    protected function closeDb(): void
    {
        $this->db->close();
    }

}
