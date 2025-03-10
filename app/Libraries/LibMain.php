<?php

namespace App\Libraries;

class LibMain
{

    public function isLocal() : bool
    {
        if (stripos(PHP_OS, 'WIN') === 0) {
            return true;
        }

        return false;
    }

}