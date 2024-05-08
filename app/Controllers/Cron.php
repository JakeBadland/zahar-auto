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
        //CLEAR writable/session files!
        Cron::clearRoot();
        Cron::clearSessions();

        self::log('Started at : ' . date('Y-m-d H:i:s') . ' ');

        $auto = new LibAutopro();
        $auto->run();

        self::log('Finished at : ' . date('Y-m-d H:i:s') . "\r\n");

        /*
        echo "<script>setTimeout(function(){
            location.reload();
        }, 1000)</script>";
        */

        die('DONE');
    }

    public static function clearRoot()
    {
        //take a look for c1min.* files in root dir
        $rootPath = APPPATH . '..' . DS . '..' . DS;

        $files = scandir($rootPath);

        foreach ($files as $file){
            $match = strpos($file, 'c1min.');
            if ($match !== false){
                unlink($rootPath . $file);
            }
        }

    }

    public static function clearSessions()
    {
        $sessionsPath = APPPATH . '..' . DS . 'writable' . DS . 'session' . DS;

        $files = scandir($sessionsPath);
        $ago = time() - (3 * 24 * 60 * 60);

        foreach ($files as $file){

            if ($file == '.' || $file == '..' || $file == 'index.html'){
                continue;
            }

            if (is_file($sessionsPath . $file)){

                $fTime = filectime($sessionsPath . $file);

                if ($fTime < $ago){
                    unlink($sessionsPath . $file);
                }
            }
        }

    }

    public static function log($message)
    {
        //$message .= "\r\n";
        $fileName = 'log_' . date('Y-m-d') . '.log';
        file_put_contents($fileName, $message, FILE_APPEND);
    }



}
