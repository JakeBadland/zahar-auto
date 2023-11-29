<?php

namespace App\Libraries;

use App\Models\ProductModel;
use CodeIgniter\Model;

class libAutopro {
    private $result = null;
    private $partUrl = null;

    private $defaultHeaders = [
        'Accept: text/plain',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0',
        'X-Requested-With: XMLHttpRequest'
    ];

    private string $url = 'https://avto.pro';
    private string $headersFile = APPPATH . '../writable/headers-avto-pro.txt';
    private string $cookiesFile = APPPATH . '../writable/cookies-avto-pro.txt';

    private $headers = null;
    private $cookies = null;

    public function run()
    {
        $currency = LibCurrencies::updateCurrencies();
        $model = new ProductModel();

        $this->checkHeaders();
        //$this->checkCookies();

        $product = $model->getProductForUpdate();
        $html = $this->getProductInfo($product);

        if ($html->code != 200){
            $model->productError($product, 'Part not found');
            die;
        }

        $html = json_decode($html->body);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $finder = new \DomXPath($dom);
        $element = $finder->query("//*[contains(@class, 'pro-card__price__value')]");
        $htmlString = $dom->saveHTML($element->item(0));
        //$htmlString = $element->item(0)->nodeValue;

        if (strpos($htmlString, 'грн') != false){
            $price = floatval($htmlString);
            $price = $price / $currency;
        }

        if ($price < $product->price){
            $model->updateProductInfo($product, $price, $this->partUrl);
        }
    }

    private function getProductInfo($product)
    {
        $body = [
            'Query' => $product->OE,
            'RegionId' =>	1,
            'SuggestionType' =>	'Regular'
        ];

        $url = $this->url . '/api/v1/search/query';

        $curl = new LibCurl();

        //get sellers list
        $result = $curl->execute($url, $this->headers, null, 'PUT', $body);
//        $this->updateCookies($result->headers);

        $result = json_decode($result->body);

        $model = new ProductModel();

        if (!isset($result->Suggestions[0]->FoundPart->Part)){
            $model->productError($product, 'Part suggestion not found');
            die();
        }

        if (count($result->Suggestions) > 1){
            $model->productError($product, 'Suggestions count > 1');
            die();
        }

        $part = $result->Suggestions[0]->FoundPart->Part;
        $this->partUrl = $this->url . '/zapchasti-' . $part->ShortNr . '-' . $part->Brand->Name;

        return $curl->execute($this->partUrl, $this->headers, null, 'GET', null);
    }

    private function checkHeaders()
    {
        if (!is_file($this->headersFile)){
            file_put_contents($this->headersFile, json_encode($this->defaultHeaders));
        }

        $this->headers = file_get_contents($this->headersFile);
        $this->headers = json_decode($this->headers);
    }

    private function checkCookies()
    {
        if (!is_file($this->cookiesFile)){
            $curl = new LibCurl();

            $result = $curl->execute($this->url, $this->headers, null, 'GET', NULL);
            $cookies = $curl->getCookiesFromResponse($result->headers);

            file_put_contents($this->cookiesFile, json_encode($cookies));
        }

        $this->cookies = file_get_contents($this->cookiesFile);
        $this->cookies = json_decode($this->cookies);
    }

    private function updateCookies($headers)
    {
        $curl = new LibCurl();
        $cookies = $curl->getCookiesFromResponse($headers);
        file_put_contents($this->cookiesFile, json_encode($cookies));
    }

}