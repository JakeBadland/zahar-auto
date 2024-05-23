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
        $currency = floatval(LibCurrencies::updateCurrencies());

        $model = new ProductModel();

        $this->checkHeaders();
        //$this->checkCookies();

        $product = $model->getProductForUpdate();

        //for testing
        //$product = $model->getProductForUpdate(13047); //multi
        //$product = $model->getProductForUpdate(13036); //single
        //$product = $model->getProductForUpdate(13569); //can`t get price

        $html = $this->getProductInfo($product);

        if ($html->code != 200){
            $model->productError($product, 'Part not found');
        }

        $price = $this->getPrice($product, $html->body, $currency);

        $averagePrice = $this->getAveragePrice($product, $html->body, $currency);

        if (!$price && $price !== 0){
            $model->productError($product, 'Can`t get price');
        }

        $model->updateProductInfo($product, $price, $averagePrice, $this->partUrl);
    }

    private function getPrice($product ,$htmlBody, $currency)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($htmlBody);

        $finder = new \DomXPath($dom);
        $node = $finder->query("//*[contains(@class, 'pro-card__price__value')]");

        if ($node->length){
            $price = $this->parseSingle($product, $node, $currency);
        } else {
            $price = $this->parseMulti($product, $finder, $currency);
        }

        if (!$price){
            return false;
        }

        //if price less then a 10% price - (new price - 10%)
        $percents = ($product->price / $price) * 100;
        if ($percents < 90){
            return 0;
        }

        return $price;
    }

    private function isUsed($td, $finder) : bool
    {
        $spans = $finder->query('.//span/span', $td);

        if ($spans->length > 0){
            $priceSpan = $spans->item(0);

            return $priceSpan->hasAttribute('data-sub-title');
        }

        return false;
    }

    private function parseMulti($product, $finder, $currency)
    {
        $rows = $finder->query('//table[@id="js-partslist-primary"]/tbody/tr');

        $prices = [];
        $price = null;

        foreach ($rows as $tr){
            $cols = $tr->getElementsByTagName('td');

            $productOE = trim($cols[2]->nodeValue);
            if ($productOE == $product->OE){
                $price = 0;

                //use only "Б/У"
                if  ($this->isUsed($cols[5], $finder)){
                    $price = $this->clearPrice($cols[5]->nodeValue);
                }

                if ($price){
                    $prices[] = $price;
                }
            }
        }

        if (!$prices){
            return false;
        }

        $price = min($prices);

        return round($price / $currency, 2);
    }

    private function getAveragePrice($product, $htmlBody, $currency)
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($htmlBody);

        $finder = new \DomXPath($dom);
        $node = $finder->query("//*[contains(@class, 'pro-card__price__value')]");

        if ($node->length){
            return $this->parseSingle($product, $node, $currency);
        }

        $rows = $finder->query('//table[@id="js-partslist-primary"]/tbody/tr');

        $prices = [];
        $sum = 0;

        foreach ($rows as $tr){
            $cols = $tr->getElementsByTagName('td');

            $productOE = trim($cols[2]->nodeValue);
            if ($productOE == $product->OE){
                $price = 0;

                //use only "Б/У"
                if  ($this->isUsed($cols[5], $finder)){
                    $price = $this->clearPrice($cols[5]->nodeValue);
                }

                if ($price){
                    $prices[] = $price;
                    $sum += $price;
                }
            }
        }

        if (count($prices)){
            $average = $sum / count($prices);
            $average = $average / $currency;
            $average = round($average, 2);
        } else {
            return 0;
        }

        return $average;
    }

    private function parseSingle($product, $node, $currency)
    {
        $model = new ProductModel();

        $node = $node->item(0)->nodeValue;

        $price = null;
        if (strpos($node, 'грн') !== false){
            $node = str_replace('грн', '', $node);
            $node = trim($node);
            $price = floatval($node);
            $price = round($price / $currency, 2);
        } else {
            $model->productError($product, 'Can`t find price');
        }

        if (!$price){
            $model->productError($product, 'Can`t get price');
        }

        return $price;
    }

    private function clearPrice($price)
    {
        $price = trim($price);
        $price = str_replace("\r\n", '', $price);
        $price = str_replace(' ', '', $price);
        $price = str_replace(',', '.', $price);
        $price = trim(str_replace('грн', '', $price));
        $price = floatval($price);

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
        }

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