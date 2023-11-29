<?php


namespace App\Libraries;

use \App\Models\CurlResponse;

class LibCurl
{

    public function execute($url, $headers = null, $cookies = null, $method = 'GET', $body = null) : CurlResponse
    {
        $curl = $this->getCurl($url, $method, $body);

        if ($headers){
            $this->setHeaders($curl, $headers);
        }

        if ($cookies){
            $this->setCookies($curl, $cookies);
        }

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerStr = substr($response, 0, $headerSize);
        $bodyStr = substr($response, $headerSize );

        if ($httpCode != 200){
            //die("http code != 200");
        }

        $result = new CurlResponse($httpCode, $headerStr, $bodyStr);

        curl_close($curl);

        return $result;
    }

    private function getCurl($url, $method = 'GET', $body = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 12,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HEADER  => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ));

        if ($body) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        }

        return $curl;
    }

    private function setHeaders($curl, $headers)
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    private function setCookies($curl, $cookies)
    {
        foreach ($cookies as $line){
            curl_setopt($curl, CURLOPT_COOKIE , $line);
        }
    }

    public function getCookiesFromResponse(string $header)
    {
        $result = [];
        $cookieLine = 'Set-Cookie: ';

        $header = explode("\r", $header);

        foreach ($header as $line){
            if (!strpos($line, $cookieLine)){
                continue;
            }

            $line = str_replace($cookieLine, '', $line);
            $result[] = $line;
        }

        return $result;
    }

}