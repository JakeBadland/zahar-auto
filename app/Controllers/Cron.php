<?php

namespace App\Controllers;

use App\Libraries\LibAutopro;


class Cron extends BaseController
{

    public function c24hour()
    {

    }

    public function c2min()
    {

    }

    public function c1min()
    {
        self::log('Started at : ' . date('Y-m-d H:i:s') . ' ');

        $auto = new LibAutopro();
        $auto->run();

        self::log('Finished at : ' . date('Y-m-d H:i:s') . "\r\n");

        /*
        echo "<script>setTimeout(function(){
            location.reload();
        }, 1000)</script>";
        */

        die();
    }

    public static function log($message)
    {
        //$message .= "\r\n";
        $fileName = 'log_' . date('Y-m-d') . '.log';
        file_put_contents($fileName, $message, FILE_APPEND);
    }



}
