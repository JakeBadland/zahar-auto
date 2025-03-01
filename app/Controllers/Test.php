<?php

namespace App\Controllers;

use App\Libraries\LibTelegram;


class Test extends BaseController
{

    private $mailTo = 'badland@ukr.net';
    private $subject = 'HTML test';

    private $mailHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8";

    private $token = '7565495903:AAHBIxyV9v_CZrwkEehjobqa_9w9-MIRGhE';

    public function index()
    {
        LibTelegram::sendMessage('test');
        //$this->sendMessage(-4621623125, $this->token,"Hellokitty");

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

    // Функция отправки сообщения в чат
    function sendMessage($chatId, $token, $text) {

        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query($data),
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            ],
        ];
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    }

}
