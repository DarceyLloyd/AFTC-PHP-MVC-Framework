<?php

namespace AFTC\Controllers\Views;

use AFTC\Controllers\TwigController;
use AFTC\Utils\GetRouteData;

class TwigViewTest extends TwigController
{

    public function __construct()
    {
        parent::__construct();
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function get() {

        // Define some data to pass to the template
        $data = [
            'title' => 'Twig Example',
            'message' => 'Hello, Twig!',
            'myVariable' => 'Some value',
            'arrayVariable' => ['item1', 'item2', 'item3']
        ];

        // Render the template with the provided data
        // echo($this->twig->render('./twig-demo/base.twig',$data));
        echo($this->twig->render('./twig-demo/test.twig',$data));
    }
    // - - - - - - - - - - - - - - - - - - - - - - - -
}