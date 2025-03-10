<?php

namespace App\Libraries;

use App\Libraries\LibEnv;

class LibTelegram {

    public static function sendMessage($text) {
        $token = LibEnv::getenv('TELEGRAM_TOKEN');

        $groupId = -4621623125;

        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = [
            'chat_id' => $groupId,
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