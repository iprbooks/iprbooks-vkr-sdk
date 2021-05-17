<?php

namespace Iprbooks\Vkr\Sdk;

//require '/var/www/html/vkr-sdk-test/iprbooks-vkr-sdk/vendor/autoload.php';
require '/var/www/html/vkr-sdk-test/iprbooks-vkr-sdk/vendor/autoload.php';


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
    }

    public final function create($docId, $docType, $filePath)
    {
        $fileName = basename($filePath);

        $params = array(
            'file_name' => $fileName,
            'doc_id' => $docId,
            'doc_type' => $docType,
            'client_id' => $this->clientId
        );


        $apiMethod = sprintf("%s?%s", "doc/create", http_build_query($params, '', '&'));

        $headers = array(
            'Authorization: Bearer ' . $this->getJwt(),
            'X-APIKey: ' . self::X_API_KEY,
            'Content-Type: multipart/form-data',
            'Accept: application/json'
        );


        if (function_exists('curl_file_create')) {
            $cFile = curl_file_create($filePath);
        } else {
            $cFile = '@' . realpath($filePath);
        }
        $post = array('file' => $cFile);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_URL, self::HOST . $apiMethod);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);

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