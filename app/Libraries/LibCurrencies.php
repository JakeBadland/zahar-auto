<?php


namespace App\Libraries;

class LibCurrencies
{

    public static string $url = "https://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11";

    public static function getCurrencies()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, self::$url);
        $response = curl_exec($ch);
        return json_decode($response);
    }

    public static function updateCurrencies()
    {
        $db = db_connect();

        $result = $db->table('settings')
            ->select('*')
            ->where("(updated_at >= NOW() - INTERVAL 1 Minute) AND (key = 'currency')" )
            ->get()->getRow();

        if (!$result){
            $currency = self::getCurrencies();

            if (isset($currency[1]->buy)){
                $curr = $currency[1]->sale;

                $db->table('settings')
                    ->where(['key' => 'currency'])
                    ->update([
                        'value' => $curr,
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ]);
                return $curr;
            }
        }

        return $result->value;
    }

}