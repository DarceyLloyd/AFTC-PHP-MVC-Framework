<?php

namespace AFTC\Controllers;

use AFTC\Config\Config;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\PasswordLib;
use AFTC\Libs\SendSmtpMailLib;
use AFTC\Libs\SessionLib;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class AFTCTwigView
{
    protected SessionLib $sessionLib;
    protected PasswordLib $passwordLib;
    protected JwtLib $jwtLib;
    protected SecurityController $securityController;
    protected DatabaseLib $db;
    protected SendSmtpMailLib $mail;
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(Config::$viewFolder);

        if (Config::$twigEnableCache){
            $this->twig = new Environment($loader, [
                'cache' => Config::$twigCacheFolder,
            ]);
        } else {
            $this->twig = new Environment($loader);
        }

        if (Config::$twigDebug){
            $this->twig->addExtension(new DebugExtension());
        }

    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


}