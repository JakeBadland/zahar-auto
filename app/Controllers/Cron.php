<?php

namespace App\Controllers;

use App\Libraries\LibAutopro;
use App\Libraries\libCsv;
use App\Libraries\LibDella;
use App\Models\ProductModel;
use CodeIgniter\Model;


class Cron extends BaseController
{

    public function c24hour()
    {
        set_time_limit(0);

        $link = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTzDZhMwu8ZWd0rnscHzMtz73qYv5myobbbH5kQsDWJTv84zXpVxLXLlTbFQPEjW6iQsDzy4pjNPjux/pub?gid=1464938412&single=true&output=csv';

        $data = file_get_contents($link);

        if (!$data){
            die('Cant get file!');
        }

        $fileName = WRITEPATH . 'uploads/datafile.csv';

        if (is_file($fileName)){
            unlink($fileName);
        }

        file_put_contents($fileName, $data);

        $items = libCsv::parseFile($fileName);
        $productModel = new ProductModel();
        $productModel->updateProducts($items);
    }

    public function c2min()
    {
        $della = new LibDella();
        $della->run();
    }

    public function c1min()
    {
        /*
        echo "<script>setTimeout(function(){
            location.reload();
        }, 10000)</script>";
        */

        //CLEAR writable/session files!
        Cron::clearRoot();
        Cron::clearSessions();

        self::log('Started at : ' . date('Y-m-d H:i:s') . ' ');

        $auto = new LibAutopro();
        $auto->run();

        self::log('Finished at : ' . date('Y-m-d H:i:s') . "\r\n");

        die('ALL DONE');
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
