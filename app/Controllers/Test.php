<?php

namespace App\Controllers;

use App\Libraries\LibTelegram;
use App\Libraries\LibEnv;


class Test extends BaseController
{

    private $mailTo = 'badland@ukr.net';
    private $subject = 'HTML test';

    private $mailHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8";

    public function index()
    {
        //LibTelegram::sendMessage('Token test');

        die('test/index');

        /*
        $html = '<div>';
        $html .= '<a href="https://pandowoz.com">link</a>';
        $html .= '</div>';

        $result = mail($this->mailTo, $this->subject, $html, $this->mailHeaders);

        echo "<PRE>";
        var_dump($result);
        echo "</PRE>";
        */
    }


}
