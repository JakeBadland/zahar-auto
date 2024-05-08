<?php

namespace App\Helpers;

use CodeIgniter\CodeIgniter;

class PagerHelper{

    public function calc($totalItems, $currentPage, $perPage) : array
    {
        $data = [];

        $data['first'] = 1;
        $data['current'] = $currentPage;
        $data['left'] = ($currentPage > 1)? $currentPage - 1: 1;
        $data['last'] = floor($totalItems / $perPage);
        $data['right'] = ($currentPage < $data['last'])? $currentPage + 1: $data['last'];

        return $data;
    }

}
