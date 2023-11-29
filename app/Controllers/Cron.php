<?php

namespace App\Controllers;

use App\Libraries\libAutopro;
use App\Libraries\LibCurl;
use App\Libraries\LibCurrencies;


class Cron extends BaseController
{

    public function c24hour()
    {

    }

    public function c2min()
    {

    }

    public function c1min()
    {
        $auto = new libAutopro();
        $auto->run();

        die;



        /*
        $libCurl = new LibCurl();

        $auto->prepare();
        $result = $auto->getResult();
        $cookies = $libCurl->getCookiesFromResponse($result->headers);
        $auto->updateCookies($cookies);

        echo "<PRE>";
        var_dump($cookies);
        echo "</PRE>";
        die;
        */

        /*
        $curl = new LibCurl();

        $headers = [
            'Accept: text/plain',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0',
            'X-Requested-With: XMLHttpRequest'
        ];

        $cookies = [
            'APD=ID%3DK4y98HcAtUOct7D3EedPgg%3ATM%3D1700832197%3AS%3DRL5wcu5BR1Hlm81lLGun3w; expires=Thu, 24 Nov 2033 13:23:17 GMT; domain=.avto.pro; path=/',
            '_BpanelUser=9DC332F9ED6BE7172305D3D504C50D410634ABD2052D008214FD2FDB3CD065DC9123572942011F9FB31E3B3953482199FB673F75593F3878EC7FAF21377C19FC214FA79D0E93EF7D4E5139DE70000E1CEC28DDD9D24B80B540E33DF755E1FDFBF60E710961D16F876F41CC2658EA26C0B6EC90227D8C908A5DB88C8F0330EA48E04964414EDA820E6F139D2D04B828956A684530; expires=Fri, 24 Nov 2023 13:43:17 GMT; domain=.avto.pro; path=/',
            '_bpuexp=BDA59FAD497490B2CB66B207CC74D0983BCF91EEB0A5B3E2E6D7A126BA4EE6B80F7AD35EE1E2BAC97357636AB9C3B07B88CC7A7E0EC4BBE89BEF7A4D56092A433A23644FF3A69EB12C7032D0EA7085C51B132369628AFB2400F583E8C9BCDEADCFFC8F6DE0BDE5544DB3DE2E4285570AD3FFE589; expires=Thu, 24 Nov 2033 13:23:17 GMT; domain=.avto.pro; path=/',
        ];

        $body = [
            'Query'     => '06A905433G',
            'RegionId'  => 1,
            'SuggestionType'    => 'Regular'
        ];

        //$result = $curl->execute('https://avto.pro/api/v1/search/query', $headers, null, 'PUT', $body);
        $result = $curl->execute('https://avto.pro/zapchasti-06A905433G-VAG/', $headers, null, 'GET', NULL);

        echo "<PRE>";
        //var_dump($result);
        //var_dump(json_decode($result->body));
        echo "</PRE>";
        */
    }

    private static function log($message)
    {
        $fileName = 'log_' . date('Y-m-d') . '.log';
        file_put_contents($fileName, $message);
    }



}
