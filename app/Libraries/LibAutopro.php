<?php

namespace App\Libraries;

use App\Models\ProductModel;

class LibAutopro {
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

        $model = new ProductModel();

        $this->checkHeaders();
        //$this->checkCookies();

        $product = $model->getProductForUpdate();
        $html = $this->getProductInfo($product);

        if ($html->code != 200){
            $model->productError($product, 'Part not found');
        }

        $price = $this->getPrice($product, $html->body);

        if (!$price){
            $model->productError($product, 'Can`t get price');
        }

        $model->updateProductInfo($product, $price, $this->partUrl);
    }

    private function getPrice($product ,$htmlBody)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($htmlBody);

        $finder = new \DomXPath($dom);
        $node = $finder->query("//*[contains(@class, 'pro-card__price__value')]");

        if ($node->length){
            $price = $this->parseSingle($product, $node);
        } else {
            $price = $this->parseMulti($product, $finder);
        }

        if (!$price){
            return false;
        }

        return $price;
    }

    private function parseMulti($product, $finder)
    {
        $currency = floatval(LibCurrencies::updateCurrencies());

        $rows = $finder->query('//table[@id="js-partslist-primary"]/tbody/tr');

        $prices = [];
        $price = null;

        foreach ($rows as $tr){
            $cols = $tr->getElementsByTagName('td');

            $productOE = trim($cols[2]->nodeValue);
            if ($productOE == $product->OE){
                $price = trim($cols[5]->nodeValue);
                $price = str_replace("\r\n", '', $price);
                $price = str_replace(' ', '', $price);
                $price = str_replace(',', '.', $price);
                $price = trim(str_replace('грн', '', $price));
                $price = floatval($price);
                if ($price){
                    $prices[] = $price;
                }
            }
        }

        if (!$price){
            return false;
        }

        $price = min($prices);
        return round($price / $currency, 2);
    }

    private function parseSingle($product, $node)
    {
        $model = new ProductModel();
        $currency = floatval(LibCurrencies::updateCurrencies());

        $node = $node->item(0)->nodeValue;

        $price = null;
        if (strpos($node, 'грн') !== false){
            $node = str_replace('грн', '', $node);
            $node = trim($node);
            $price = floatval($node);
            $price = round($price / $currency, 2);
        } else {
            $model->productError($product, 'Can`t find price');
            die('Error: Can`t find price');
        }

        if (!$price){
            $model->productError($product, 'Can`t get price');
            die('Error: Can`t get price');
        }

        return $price;
    }

    private function getProductInfo($product)
    {
        $model = new ProductModel();

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

        if (!isset($result->Suggestions[0]->FoundPart->Part)){
            $model->productError($product, 'Part suggestion not found');
            die('Error: Part suggestion not found');
        }

        /*
        if (count($result->Suggestions) > 1){
            $model->productError($product, 'Suggestions count > 1');
            die('Error: Suggestions count > 1');
        }
        */

        $params = $result->Suggestions[0]->Uri;
        $params = explode('&', $params);
        foreach ($params as $param){
            if (strpos($param, 'uri=') !== false){
                $params = $param;
                break;
            }
        }
        $params = str_replace('uri=', '', $params);
        $params = str_replace('%2F', '', $params);
        $this->partUrl = $this->url . '/' . $params;

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