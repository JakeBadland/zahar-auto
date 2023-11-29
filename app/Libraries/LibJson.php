<?php

namespace App\Libraries;

class libJson{

    public function getData($fileName)
    {
        if (!is_file($fileName)){
            return false;
        }

        $data = file_get_contents($fileName);

        return json_decode($data);
    }

    public function setData($fileName, $data)
    {
        if(is_array($data)){
            $data = json_encode($data);
        }

        file_put_contents($fileName, $data);
    }

}

