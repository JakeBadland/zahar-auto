<?php

namespace App\Models;

use App\Libraries\libCsv;
use CodeIgniter\Model;

class DellaItemModel extends Model
{

    public int $id = 0;
    public $inner_id = '';
    public $distance = '';
    public $direction = '';
    public $href = '';
    public $cargo = '';

    protected string $table = 'della_items';

    public function saveItem()
    {
        $filters = $this->getCargoFilers();

        if (!$this->isExist()){

            $item = [
                'inner_id' => $this->inner_id,
                'distance' => $this->distance,
                'direction' => $this->direction,
                'href' => $this->href,
                'cargo' => $this->cargo,
                'is_sent' => 0
            ];

            if (in_array($this->cargo, $filters)){
                $item['is_sent'] = 1;
            }

            $this->db->table($this->table)->insert($item);
        }

    }

    private function getCargoFilers()
    {
        $fileName = WRITEPATH . 'delta-filters.txt';

        $filters = file_get_contents($fileName);
        return explode("\r\n", $filters);
    }

    public function isExist()
    {
        $result = $this->db->table($this->table)
            ->select('*')
            ->where('inner_id', (int)$this->inner_id)
            ->get()->getResult();

        return (bool)$result;
    }

    public function getNewItems()
    {
        $result = $this->db->table($this->table)
            ->select('*')
            ->where('is_sent', 0)
            ->get()->getResult();

        return $result;
    }

    public function markAsSent($ids)
    {
        $this->db->table($this->table)
            ->whereIn('inner_id', $ids)
            ->update(['is_sent' => 1]);
    }

}