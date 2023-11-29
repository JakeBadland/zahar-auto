<?php

namespace App\Libraries;

class libCsv {

    public static function parseFile($path) : array
    {
        if (!is_file($path)){
            return [];
        }

        $result = [];
        $row = 1;
        if (($handle = fopen($path, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $result[] = $data;
            }
            fclose($handle);
        }

        return $result;
    }

}