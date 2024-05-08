<?php

namespace App\Models;

use App\Controllers\Cron;
use CodeIgniter\Model;

class ProductModel extends Model
{

    public string $desc = '';
    public string $oe = '';
    public int $price;

    protected string $table = 'products';

    private function getIndexes($cols)
    {
        $result = [];

        $imports = $this->db->table('settings')
            ->select('*')
            ->like('key', 'import_')
            ->get()->getResult();

        foreach ($imports as $key => $setting){
            $index = array_search($setting->value, $cols);
            $result[$setting->key] = $index;
        }

        return $result;
    }

    public function updateProducts($data)
    {
        $indexes = $this->getIndexes($data[0]);
        unset($data[0]); //remove header

        $fileOe = [];
        foreach ($data as $item){
            $product = [
                'desc'  => $item[$indexes['import_desc']],
                'OE'    => $item[$indexes['import_oe']],
                'price' => $item[$indexes['import_price']]
            ];

            $fileOe[] = $item[2];
            $this->updateProduct($product);
        }

        $this->deleteDiffs($fileOe);
    }

    private function deleteDiffs($fileOe)
    {
        $result = $this->db->table($this->table)
            ->select('OE')->get()->getResultArray();

        $dbOe = [];
        foreach ($result as $item){
            $dbOe[] = $item['OE'];
        }

        $diff = array_diff($dbOe, $fileOe);

        if ($diff){
            $this->db->table($this->table)
                ->whereIn('OE', $diff)
                ->delete();
        }
    }

    public function updateProduct($product)
    {
        $item = $this->db->table($this->table)
            ->select('*')
            ->getWhere(['OE' => $product['OE']])
            ->getRow();

        if (!$item){
            $this->db->table($this->table)->insert($product);
            return;
        }

        //$product['updated_at'] = date('Y-m-d H:i:s', time());

        $this->db->table($this->table)
            ->where(['OE' => $product['OE']])
            ->update($product);
    }

    public function getProductForUpdate($id = null)
    {
        $query = $this->db->table($this->table)
            ->select('*')
            ->orderBy('parsed_at', 'ASC')
            //->where('error is NULL', NULL, FALSE)
            ->limit(1);

            if ($id){
                $query->where(['id' => $id]);
            }

            return $query->get()->getRow();
    }

    public function getLastUpdated()
    {
        return $this->db->table($this->table)
            ->select('COUNT(*) AS count')
            ->where('updated_at > NOW() - INTERVAL 1 DAY')
            ->get()->getRow()->count;
    }

    public function updateProductParsedAt($product)
    {
        $data = [
            'parsed_at' => date('Y-m-d H:i:s', time()),
        ];

        $this->db->table($this->table)
            ->where(['OE' => $product->OE])
            ->update($data);
    }

    public function updateProductInfo($product, $newPrice, $averagePrice, $url)
    {
        $data = [
            'newPrice' => $newPrice,
            'average' => $averagePrice,
            'url'  => $url,
            'parsed_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
            'error' => ''
        ];

        /*
        if ($product->price = '0'){
            $data['price'] = $newPrice;
        }
        */

        $this->db->table($this->table)
            ->where(['OE' => $product->OE])
            ->update($data);
    }

    public function productError($product, $error)
    {
        $data = [
            'parsed_at' => date('Y-m-d H:i:s', time()),
            'error' => $error
        ];

        $this->db->table($this->table)
            ->where(['OE' => $product->OE])
            ->update($data);

        //Cron::log($error);
        die($error);
    }

    public function clearAll()
    {
        $this->db->table($this->table)->truncate();
    }

    public function getResults()
    {
        return $this->db->table('products')
            ->select('*')
            ->where('price > newPrice', NULL, FALSE)
            ->where('newPrice <> 0')
            ->get()->getResult();
    }

    public function getCount()
    {
        return $this->db->table($this->table)
            ->select('COUNT(*) as count')
            ->get()->getRow()->count;
    }

    public function getLastParsed()
    {
        return $this->db->table($this->table)
            ->select('*')
            ->orderBy('parsed_at', 'DESC')
            ->limit(1)
            ->get()->getRow();
    }

    public function getById($productId)
    {
        return $this->db->table($this->table)
            ->select('*')
            ->where("id", $productId)
            ->get()->getRow();
    }

    public function get($page, $perPage = 100)
    {
        $page--;
        $offset = $page * $perPage;

        return $this->db->table($this->table)
            ->select('*')
            ->limit($perPage, $offset)
            ->get()->getResult();
    }

    public function getFindCount()
    {
        return $this->db->table($this->table)
            ->select('COUNT(*) as total')
            ->where('price > newPrice', NULL, FALSE)
            ->where('newPrice <> 0')
            ->get()->getRow()->total;

    }

    public function search($text)
    {
        return $this->db->table($this->table)
            ->select('*')
            ->where('OE', $text)
            ->get()->getRow();
    }

}
