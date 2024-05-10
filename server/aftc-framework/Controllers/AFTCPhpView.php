<?php

namespace AFTC\Controllers;

use AFTC\Config\Config;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\PasswordLib;
use AFTC\Libs\SendSmtpMailLib;
use AFTC\Libs\SessionLib;

class AFTCPhpView
{

    protected SessionLib $sessionLib;
    protected PasswordLib $passwordLib;
    protected JwtLib $jwtLib;
    protected SecurityController $securityController;

    protected DatabaseLib $db;

    public function __construct()
    {


    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


}