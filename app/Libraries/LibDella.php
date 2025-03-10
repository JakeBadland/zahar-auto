<?php

namespace App\Libraries;

use App\Models\DellaItemModel;

class LibDella extends libMain
{
    private $result = null;
    private $partUrl = null;

    private $mailTo = 'z.klymus@gmail.com, badland@ukr.net';
    private $subject = 'Новые заказы на Della.ua';

    private $defaultHeaders = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        //'Accept-Encoding: gzip, deflate, br, zstd',
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
        'Connection: keep-alive',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0',
        'Host: della.ua',
        'Referer: https://della.ua/',
    ];

    private $mailHeaders = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8";

    private string $url = 'https://della.ua';

    private string $headersFile = APPPATH . '../writable/headers-della.txt';
    private string $cookiesFile = APPPATH . '../writable/cookies-della.txt';

    private $headers = null;
    private $cookies = null;

    public function run()
    {
        $html = $this->getHtml();

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $finder = new \DomXPath($dom);
        $container = $finder->query('//div[@id="request_list_main"]');

        if (!$container){
            die('Items container not found!');
        }

        $container = $container->item(0);

        $items = $finder->query('//div[@request_id]', $container);

        foreach ($items as $item){
            $dellaItem = $this->parseItem($item, $finder);

            $dellaItem->saveItem();
        }

        $this->sendItems();

        echo "Della done at: " . date('Y-m-d H:i:s') . "<br/>";
    }

    private function getHtml()
    {

        $curl = new LibCurl();

        $searchUrl = 'https://della.ua/search/a204bd158eflolh0ilk0m1.html';

        $result = $curl->execute($searchUrl, $this->defaultHeaders, NULL, 'GET', NULL);

        echo "<PRE>";
        var_dump($result->code);
        echo "</PRE>";

        if (!$result){
            die('Can`t get search result!');
        }

        return $result->body;
    }

    private function sendItems()
    {
        $dellaModel = new DellaItemModel();

        $dellaItems = $dellaModel->getNewItems();

        if (!$dellaItems) return;

        //$template = '<div class="container" style="width: 600px; border: 13px solid #787878; border-radius: 4px; background: rgba(173,173,240,0.48); margin: auto">';

        $template = "";
        $ids = [];

        foreach ($dellaItems as $item){
            $template .= $this->genHtml($item);
            $ids[] = $item->inner_id;
        }

        //$template .= '</div>';

        if (!$this->isLocal()){
            $this->sendEmail($template);

            LibTelegram::sendMessage($template);
        }

        $dellaModel->markAsSent($ids);
    }

    private function sendEmail($html)
    {
        if (mail($this->mailTo, $this->subject, $html, $this->mailHeaders)) {
            return true;
        }

        return false;
    }

    private function genHtml($item) : string
    {
        $link = 'https://della.ua';

        /*
        $html = '<div style="margin-top: 4px">';
        $html .= "<div style='margin-left: 10px'><a target='_blank' href='{$link}{$item->href}'>Ссылка</a></div>";
        $html .= "<div style='margin-left: 10px'>Направление: {$item->direction} [{$item->distance}]</div>";
        $html .= "<div style='margin-left: 10px'>Тип груза: {$item->cargo}</div>";
        $html .= '</div><hr>';
        */

        $html = "Направление: {$item->direction} [{$item->distance}] \r\n";
        $html .= "Ссылка: {$link}{$item->href}\r\n";
        $html .= "Тип груза: {$item->cargo}\r\n\r\n";

        return $html;
    }

    private function parseItem($item, $finder): DellaItemModel
    {
        $dellaItem = new DellaItemModel();
        $dellaItem->inner_id = $item->getAttribute('request_id');

        $reqDist = $finder->query('.//a[@class="request_distance"]', $item);
        $dellaItem->href = $reqDist->item(0)->getAttribute('href');

        $loc = $finder->query('.//a[@class="request_distance"]', $item);
        $loc = str_replace("\n", " ", $loc->item(0)->nodeValue);
        $loc = trim($loc);
        $loc = preg_replace('/^ +| +$|( ) +/m', '$1', $loc);
        $dellaItem->direction = $loc;

        $dist = $finder->query('.//a[@class="distance"]', $item);
        $dellaItem->distance = (isset($dist->item(0)->nodeValue))? $dist->item(0)->nodeValue: '0';

        $cargo = $finder->query('.//span[@class="cargo_type"]', $item);
        $dellaItem->cargo = $cargo->item(0)->nodeValue;

        return $dellaItem;
    }

    private function getSettings()
    {
        $db = db_connect();

        $result = $db->table('settings')
            ->select('*')
            ->like('key', 'della_')
            ->get()->getResult();

        return $result;
    }

    private function checkHeaders()
    {
        if (!is_file($this->headersFile)){
            file_put_contents($this->headersFile, json_encode($this->defaultHeaders));
            $this->headers = $this->defaultHeaders;
            return;
        }

        $this->headers = file_get_contents($this->headersFile);
        $this->headers = json_decode($this->headers);
    }

    private function login()
    {
        /*
        $settings = $this->getSettings();

        $body = [];
        foreach ($settings as $item){
            $body[$item->key] = $item->value;
        }

        $encodedData = http_build_query($body);
        $this->headers = $this->defaultHeaders;
        $this->headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $this->headers[] = 'Content-Length: ' . strlen($encodedData);
        */

        /*
        //login to della.ua
        $result = $curl->execute($this->url, $this->headers, null, 'POST', $body);

        if ($result->code != 200){
            die('Login failed!');
        }
        */
    }

}