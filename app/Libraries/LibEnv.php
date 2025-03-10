<?php

namespace App\Libraries;

class LibEnv {

    public static function getEnv($envKey) : ?string
    {
        $envFile = FCPATH . '../.JSON_ENV';

        if (!is_file($envFile)){
            return null;
        }

        $env = file_get_contents($envFile);
        $env = json_decode($env);

        foreach ($env as $key => $line){
            if ($envKey == $key){
                return $line;
            }
        }

        return null;
    }

}