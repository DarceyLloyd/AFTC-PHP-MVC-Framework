<?php

namespace AFTC\Controllers;

use AFTC\Config\Config;
use AFTC\Config\Vars;
use AFTC\Libs\ApiResponseLib;
use AFTC\Libs\CookieLib;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\PasswordLib;
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
    public SecurityController $securityController;
    public ApiResponseLib $apiResponseLib;
    public ApiResponseVo $apiResponseVo;
    public DatabaseLib $db;
    public SendSmtpMailLib $mail;

    // NOTE: Do not add ModelResponseVo above, let each method have their own
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function __construct()
    {
        // Instantiations
        $this->sessionLib = new SessionLib();
        $this->cookieLib = new CookieLib();
        $this->passwordLib = new PasswordLib();
        $this->jwtLib = new JwtLib();
        $this->securityController = new SecurityController();
        $this->apiResponseLib = new ApiResponseLib();
        $this->apiResponseVo = new ApiResponseVo();
        $this->mail = new SendSmtpMailLib();

        // NOTE: AFTCApi use only, allows multiple requests without re-opening &  closing the db con via models
        $this->db = DatabaseLib::getInstance();
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    // Done here as controllers may use multiple models.
    protected function closeDb():void
    {
        $this->db->close();
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


}
