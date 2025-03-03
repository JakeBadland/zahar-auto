<?php

namespace App\Models;

use App\Libraries\libCsv;
use CodeIgniter\Model;

class DellaItemModel extends Model
{
    protected string $table = 'della_items';

    private string $allowAll = 'all';

    public int $id = 0;
    public string $inner_id = '';
    public string $distance = '';
    public string $direction = '';
    public string $href = '';
    public string $cargo = '';
    public string $is_sent = '0';

    public function saveItem()
    {
        if ($this->isExist()){
            return;
        }

        $item = [
            'inner_id' => $this->inner_id,
            'distance' => $this->distance,
            'direction' => $this->direction,
            'href' => $this->href,
            'cargo' => $this->cargo,
            'is_sent' => 0
        ];

        //ignore if not in filters
        if (!$this->isInList($this->cargo)){
            $item['is_sent'] = 1;
        }

        $this->db->table($this->table)->insert($item);
    }

    private function isInList($cargo) : bool
    {
        $filters = $this->getCargoFilters();

        foreach ($filters as $cargoType){
            if ($cargoType == $this->allowAll){
                return true;
            }

            if ($cargoType == $cargo){
                return true;
            }
        }

        return false;
    }

    private function getCargoFilters()
    {
        $fileName = WRITEPATH . 'delta-filters.txt';

        $filters = file_get_contents($fileName);

        return explode("\r\n", $filters);
    }

    public function isExist()
    {
        $result = $this->db->table($this->table)
            ->select('*')
            ->where('inner_id', (string) $this->inner_id)
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