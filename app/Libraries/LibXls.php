<?php

namespace App\Libraries;

class libXls {

    public function readFile()
    {

    }

    public function parseFile()
    {

    }

    public function test(){
        $dir = Config::get('settings', 'xls_dir');

        $files = scandir($dir);

        foreach ($files as $file){
            if ($file == '.' || $file == '..') continue;

            $fileName = realpath($dir . DS . $file);

            $xml = simplexml_load_file($fileName);

            $out = [];
            $row = 0;

            foreach ($xml->sheetData->row as $item) {

                $cell = 0;
                foreach ($item as $child) {

                    echo "<pre>";
                    var_dump($child);
                    echo "</pre>";
                    $cell++;

                    if ($cell > 5) die;

                    /*
                    $attr = $child->attributes();
                    $value = isset($child->v)? (string)$child->v:false;
                    $out[$file][$row][$cell] = isset($attr['t']) ? $sharedStringsArr[$value] : $value;
                    $cell++;
                    */
                }

                /*
                $out[$file][$row] = array();
                //по каждой ячейке строки
                $cell = 0;
                foreach ($item as $child) {
                    $attr = $child->attributes();
                    $value = isset($child->v)? (string)$child->v:false;
                    $out[$file][$row][$cell] = isset($attr['t']) ? $sharedStringsArr[$value] : $value;
                    $cell++;
                }
                $row++;
                */
            }

            die;

            /*
            $xmlStr = file_get_contents($fileName);
            $xml = new SimpleXMLElement($xmlStr);
            foreach ($xml->children() as $item) {
                echo "<pre>";
                var_dump($item->sheetData);
                echo "</pre>";
                die;
                //$sharedStringsArr[] = (string)$item->t;
            }
            */


            /*
            $xml = simplexml_load_file($file);
            echo "<pre>";
            var_dump($xml);
            echo "</pre>";
            die;
            */

        }

        die;

        /*
        $fileName = $settings['xls_file'];

        if (!file_exists($fileName)) return null;

        $sharedStringsArr = [];
        foreach ($xml->children() as $item) {
            $sharedStringsArr[] = (string)$item->t;
        }
        */

    }

}