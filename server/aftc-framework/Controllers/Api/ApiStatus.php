<?php

namespace AFTC\Controllers\Api;

use AFTC\Controllers\AFTCApi;
use AFTC\Utils\AFTCUtils;

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

class ApiStatus extends AFTCApi
{
    // Vars
    private mixed $model;


    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    public function __construct()
    {

        // super();
        parent::__construct();

        header('Content-type:application/json;charset=utf-8');
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



    public function get() {

        // header('Content-type:application/json;charset=utf-8');
        // echo json_encode([1,2,3]);
        // die();

        // Setup responseVo
        $this->apiResponseVo->status = 200;
        $this->apiResponseVo->data = [
            "message 1" => "Hello World 1",
            "message 2" => "Hello World 2"
        ];

        // Set http status code from ApiResponseVo
        http_response_code(200);

        // Send response
        $this->apiResponseLib->sendResponse($this->apiResponseVo);
    }
    // - - - - - - - - - - - - - - - - - - - - - - - -


}
