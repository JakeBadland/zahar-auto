<?php

namespace App\Models;

class CurlResponse {

    public $code;
    public $body;
    public $headers;

    public function __construct($code, $headers, $body){
        $this->code = $code;
        $this->headers = $headers;
        $this->body = $body;
    }

}