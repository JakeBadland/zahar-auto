<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{

    public string $desc = '';
    public string $oe = '';
    public int $price;

    protected string $table = 'products';

    public function updateProducts($data)
    {
        unset($data[0]); //remove header

        foreach ($data as $item){
            $product = [
                'desc'  => $item[1],
                'OE'    => $item[2],
                'price' => $item[8]
            ];

            $this->updateProduct($product);
        }

    }

    public function getProductForUpdate()
    {
        return $this->db->table($this->table)
            ->select('*')
            ->orderBy('parsed_at', 'ASC')
            ->where('error is NULL', NULL, FALSE)
            //->orderBy('id', 'ASC')
            ->get()->getRow();
    }

    public function updateProductInfo($product, $newPrice, $url)
    {
        $data = [
            'newPrice' => $newPrice,
            'url'  => $url,
            'parsed_at' => date('Y-m-d H:i:s', time()),
        ];

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

        $this->db->table($this->table)
            ->where(['OE' => $product['OE']])
            ->update($product);
    }

    public function clearAll()
    {
        $this->db->table($this->table)->truncate();
    }

    public function getResults()
    {
        return $this->db->table('products')
            ->select('*')
            ->where('find is NOT NULL', NULL, FALSE)
            ->get()->getResult();
    }

    public function getCount()
    {
        return $this->db->table($this->table)
            ->select('COUNT(*) as count')
            ->get()->getRow()->count;
    }

    public function getFindCount()
    {
        return $this->db->table('products')
            ->select('COUNT(*) as count')
            ->where('find is NOT NULL', NULL, FALSE)
            ->get()->getRow()->count;
    }
}
