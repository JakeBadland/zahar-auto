<?php

namespace App\Controllers;

use App\Helpers\PagerHelper;
use App\Libraries\libCsv;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Model;


class Index extends BaseController
{

    private string $filePath = WRITEPATH . 'uploads/datafile.csv';

    public function index() : string
    {
        $productModel = new ProductModel();
        $data = [
            'count'     => 0,
            'updated'   => '',
        ];

        if (is_file($this->filePath)){
            $result = filemtime($this->filePath);
            $data['updated_data'] = date('d-m-Y H:i:s', $result);
        }

        $data['find'] = $productModel->getFindCount();
        $data['count'] = $productModel->getCount();
        $data['errors_count'] = $productModel->getErrorsCount();
        $data['last_parsed'] = $productModel->getLastParsed();
        $data['updated_products'] = $productModel->getLastUpdated();

        return view('content', ['data' => $data]);
    }

    public function errorProducts(): string
    {
        $productModel = new ProductModel();

        $products = $productModel->getErrorProducts();

        return view('error_products', ['products' => $products]);
    }

    public function settings()
    {
        $db = db_connect();

        $data = $this->request->getPost();

        if ($data){
            foreach ($data as $key => $value){

                $db->table('settings')
                    ->where(['key' => $key])
                    ->update(['value' => trim($value)]);
            }

            return redirect()->to('/settings');
        }

        $settings = $db->table('settings')
            ->select('*')
            ->like('key', 'import_')
            ->get()->getResult();

        $items = [];
        foreach ($settings as $line){
            $items[$line->key] = $line->value;
        }

        return view('settings', ['items' => $items]);
    }

    public function upload()
    {
        set_time_limit(0);

        $file = $this->request->getFile('datafile');

        if ($file && !$file->hasMoved()){
            if (is_file($this->filePath)){
                unlink($this->filePath);
            }

            $filepath = WRITEPATH . 'uploads' . $file->store('', 'datafile.csv');

            $items = libCsv::parseFile($filepath);
            $productModel = new ProductModel();
            $productModel->updateProducts($items);

            return redirect()->to('/');
        }

        return view('upload');
    }

    public function doubles()
    {
        $file = $this->request->getFile('testfile');

        if ($file && !$file->hasMoved()){
            $db = db_connect();

            if (is_file($this->filePath)){
                unlink($this->filePath);
            }

            $filepath = WRITEPATH . 'uploads' . $file->store('', 'testfile.csv');

            $items = libCsv::parseFile($filepath);

            unset($items[0]); //header

            $fileOe = [];
            foreach ($items as $item){
                $fileOe[] = $item[2];
            }

            $dubs = [];
            foreach(array_count_values($fileOe) as $val => $c){
                if($c > 1){
                    $dubs[] = $val;
                }
            }

            if ($dubs){
                $products = $db->table('products')
                    ->select('*')
                    ->whereIn('OE', $dubs)
                    ->get()->getResult();
            }

            return view('doubles', ['items' => $products]);
        }

        return view('upload_d');
    }


    public function clear()
    {
        $productModel = new ProductModel();
        $productModel->clearAll();

        return redirect()->to('/');
    }


    public function result() : string
    {
        $productModel = new ProductModel();
        $result = $productModel->getResults();

        foreach ($result as $key => $item){
            $result[$key]->recommended_price = 0;
            if ($item->average){
                $result[$key]->recommended_price = $item->average - ($item->average * 0.2);
                $result[$key]->recommended_price = round($result[$key]->recommended_price, 2);
            }
        }

        return view('results', ['items' => $result]);
    }

    public function listProducts($page = null) : string
    {
        $perPage = 100;

        $productModel = new ProductModel();
        $pager = new PagerHelper();

        $total = $productModel->getCount();

        $products = $productModel->get($page, $perPage);

        $pager = $pager->calc($total, $page, $perPage);

        $paginator = view('paginator', ['pager' => $pager]);

        return view('products', [
            'products' => $products,
            'paginator' => $paginator
        ]);
    }

    public function searchProducts()
    {
        $productModel = new ProductModel();

        $data = $this->request->getPost();

        if (isset($data['text'])){
            $product = $productModel->search($data['text']);

            if ($product){
                return view('edit_product', ['product' => $product]);
            }

            die('Products not found');
        }

    }

    public function editProduct($productId = null)
    {
        $db = db_connect();

        $productModel = new ProductModel();

        $data = $this->request->getPost();

        if ($data){
            $productId = $data['id'];
            unset($data['id']);
            $data['is_ignored'] = (isset($data['is_ignored']))? 1 : 0;

            $db->table('products')
                ->where(['id' => $productId])
                ->update($data);

            return redirect()->to('/edit-product/' . $productId);
        }

        if ($productId){
            $product = $productModel->getById($productId);

            return view('edit_product', ['product' => $product]);
        }

    }

    public function login()
    {
        $user = new UserModel();

        $data = $this->request->getPost();

        if ($data){
            $result = $user->auth($data);

            if ($result){
                return redirect()->to('/');
            }
        }

        return view('login');
    }

    public function logout()
    {
        $user = new UserModel();
        $user->logout();

        header('Location: /login');
        die;
    }

    public function export()
    {
        $model = new ProductModel();
        $results = $model->getResults();
        $filename = 'export_' . date('Y-m-d_H.i.s');

        header('charset=utf-8');
        header("Content-Disposition: attachment; filename=$filename.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        foreach ($results as $item){
            echo $item->desc . "\t";
            echo $item->OE . "\t";
            echo $item->price . "\t";
            echo str_replace('.' ,',', $item->newPrice) . "\t";
            echo str_replace('.' ,',', $item->average) . "\t";

            echo "\n";
        }

        die();
    }

    //della.ua
    public function cargoFilter()
    {
        $fileName = WRITEPATH . 'delta-filters.txt';

        $data = $this->request->getPost();

        if ($data){
            $filters = trim($data['filters']);
            $filters = explode("\r\n", $filters);

            foreach ($filters as $key => $line){
                $filters[$key] = trim($line);
            }

            $filters = implode("\r\n", $filters);

            file_put_contents($fileName, $filters);
        }

        $filters = file_get_contents($fileName);

        return view('cargo-filter', ['filters' => $filters]);
    }

    public function test()
    {
        /*
        $fileName = WRITEPATH . 'uploads/headers.txt';

        $data = json_encode($_SERVER);

        echo "<PRE>";
        var_dump($data);
        var_dump('Okey!)');
        echo "</PRE>";

        file_put_contents($fileName, $data);
        */

        /*
        $link = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTzDZhMwu8ZWd0rnscHzMtz73qYv5myobbbH5kQsDWJTv84zXpVxLXLlTbFQPEjW6iQsDzy4pjNPjux/pub?gid=1464938412&single=true&output=csv';

        $data = file_get_contents($link);

        $fileName = WRITEPATH . 'uploads/test.csv';

        file_put_contents($fileName, $data);

        /*
        $model = new ProductModel();
        $list = $model->getListOEFromFile();

        echo "<PRE>";
        var_dump($list);
        echo "</PRE>";
        */
    }

}
