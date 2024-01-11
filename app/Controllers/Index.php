<?php

namespace App\Controllers;

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
            $result = filectime($this->filePath);
            $data['updated_data'] = date('d-m-Y H:i:s', $result);
        }

        $data['find'] = $productModel->getFindCount();
        $data['count'] = $productModel->getCount();
        $data['last_parsed'] = $productModel->getLastParsed();
        $data['updated_products'] = $productModel->getLastUpdated();

        return view('content', ['data' => $data]);
    }

    public function upload()
    {
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

    /*
    public function clear()
    {
        $productModel = new ProductModel();
        $productModel->clearAll();

        return redirect()->to('/');
    }
    */

    public function result()
    {
        $productModel = new ProductModel();
        $result = $productModel->getResults();

        return view('results', ['items' => $result]);
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
            echo str_replace('.' ,',', $item->newPrice);
            echo $item->average . "\t";

            echo "\n";
        }

        die();
    }

    public function test()
    {

    }

}
