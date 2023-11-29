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
            'find'      => 0
        ];

        if (is_file($this->filePath)){
            $result = filectime($this->filePath);
            $data['updated'] = date('d-m-Y H:i:s', $result);
        }

        $data['find'] = $productModel->getFindCount();
        $data['count'] = $productModel->getCount();

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

    public function clear()
    {
        $productModel = new ProductModel();
        $productModel->clearAll();

        return redirect()->to('/');
    }

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

    public function test()
    {

    }

}
