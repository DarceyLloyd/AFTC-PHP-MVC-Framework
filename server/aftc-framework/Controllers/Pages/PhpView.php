<?php

namespace AFTC\Controllers\Pages;

use AFTC\Controllers\AFTCPhpView;

class PhpView extends AFTCPhpView
{

    public function __construct()
    {
        parent::__construct();
    }


    public function get()
    {
        $browser_title = "PHP View - AFTC PHP MVC Framework";


        // Output view
        require_once(VIEWS_PATH . "/php/html_top.php");
        require_once(VIEWS_PATH . "/php/header.php");
        require_once(VIEWS_PATH . "/php/page.php");
        require_once(VIEWS_PATH . "/php/footer.php");
        require_once(VIEWS_PATH . "/php/html_btm.php");
    }


    public function post()
    {
        $browser_title = "PHP View Post Response - AFTC PHP MVC Framework";


        // Output view
        require_once(VIEWS_PATH . "/php/html_top.php");
        require_once(VIEWS_PATH . "/php/header.php");
        require_once(VIEWS_PATH . "/php/page_post.php");
        require_once(VIEWS_PATH . "/php/footer.php");
        require_once(VIEWS_PATH . "/php/html_btm.php");
    }


}