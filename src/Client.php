<?php

namespace Iprbooks\Vkr\Sdk;

use Firebase\JWT\JWT;

class Client
{
    const HOST = 'https://api.bse.vkr-vuz.ru/api/';
    const X_API_KEY = 'Ts1+E8*!mY94Qj!t';

    private $clientId;
    private $secretKey;


    public function __construct($clientId, $secretKey)
    {
        $this->clientId = $clientId;
        $this->secretKey = $secretKey;
    }

    private function getJwt()
    {
        $time = time();
        $data = array(
            'client_id' => $this->clientId,
            'time' => $time,
            'ip' => '192.168.0.1',
            'exp' => $time + 36000
        );

        return JWT::encode($data, $this->secretKey);
//        var_dump((array)JWT::decode($this->token, $this->secretKey, ['HS256']));
    }

//Route::post('/create', [App\Http\Controllers\QueueController::class, 'create']);
//Route::post('{id}/status', [App\Http\Controllers\QueueController::class, 'status']);
//Route::post('{id}/report', [App\Http\Controllers\QueueController::class, 'report']);

    public final function create($docId, $docType, $url)
    {
        $curl = curl_init();
        $url = self::HOST . 'doc/create?client_id=' . $this->clientId
            . '&doc_id=' . $docId
            . '&doc_type=' . $docType
            . '&url=' . $url;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->getJwt(),
                "X-APIKey: " . self::X_API_KEY
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public final function getReportStatus($id)
    {
        $curl = curl_init();
        $url = self::HOST . 'doc/' . $id . '/status?client_id=' . $this->clientId;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->getJwt(),
                "X-APIKey: " . self::X_API_KEY
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public final function getReportJson($id)
    {
        $curl = curl_init();
        $url = self::HOST . 'doc/' . $id . '/report?client_id=' . $this->clientId;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->getJwt(),
                "X-APIKey: " . self::X_API_KEY
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

}