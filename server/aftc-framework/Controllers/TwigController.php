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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigController
{
    protected SessionLib $sessionLib;
    protected PasswordLib $passwordLib;
    protected JwtLib $jwtLib;
    protected SecurityController $securityController;
    protected DatabaseLib $db;
    protected SendSmtpMailLib $mail;
    private Environment $twig;
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



        // Twig
        $loader = new FilesystemLoader(Config::$viewFolder);

        if (Config::$twigDebug){
            $this->twig = new Environment($loader, [
                'debug' => true,
            ]);

            $this->twig->addExtension(new DebugExtension());
        } else {
            if (Config::$twigEnableCache){
                $this->twig = new Environment($loader, [
                    'cache' => Config::$twigCacheFolder,
                ]);
            } else {
                $this->twig = new Environment($loader);
            }
        }

    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



    protected function render(string $twigFile, array $pageData = null):void
    {
        $data = [
            'dev' => Config::$dev,
            'user_type' => Vars::$currentUserType
        ];

        if ($pageData){
            $data = array_merge($data,$pageData);
        }

        // Render the template with the provided data
        try {
            echo($this->twig->render($twigFile, $data));
        } catch (LoaderError $e) {
            AFTCUtils::writeToLog("Twig can't find file error '{$twigFile}'");
            AFTCUtils::redirect("/500.html");
        } catch (RuntimeError $e) {
            AFTCUtils::writeToLog("Twig compile error '{$twigFile}'");
            AFTCUtils::redirect("/500.html");
        } catch (SyntaxError $e) {
            AFTCUtils::writeToLog("Twig syntax error '{$twigFile}'");
            AFTCUtils::redirect("/500.html");
        }
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


}