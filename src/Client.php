<?php

class Client
{
    const HOST = 'http://192.168.0.200/queue/public/index.php';
    const LOGIN = self::HOST . '/auth/login';
    const UPLOAD = self::HOST . '/upload';
    const STATUS = self::HOST . '/{id}/status?token={token}';
    const REPORT = self::HOST . '/{id}/report?token={token}';

    private $token;
    private $curl;


    public function __construct($email, $password)
    {
        $this->curl = curl_init();

        $post = array('email' => $email, 'password' => $password);
        curl_setopt($this->curl, CURLOPT_URL, self::LOGIN);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($this->curl);
        $json = json_decode($result, true);
        $this->token = $json['token'];

        if (!$this->token) {
            throw new Exception($json['error']);
        }
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    public final function uploadReport($id, $type, $path)
    {
        if (function_exists('curl_file_create')) {
            $json = curl_file_create($path);
        } else {
            $json = '@' . realpath($path);
        }

        $post = array(
            'document_id' => $id,
            'document_type' => $type,
            'token' => $this->token,
            'json' => $json
        );

        curl_setopt($this->curl, CURLOPT_URL, self::UPLOAD);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

        return curl_exec($this->curl);
    }

    public final function getReportStatus($id)
    {
        $url = str_replace('{id}', $id, self::STATUS);
        $url = str_replace('{token}', $this->token, $url);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($this->curl);
    }

    public final function getReportJson($id)
    {
        $url = str_replace('{id}', $id, self::REPORT);
        $url = str_replace('{token}', $this->token, $url);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($this->curl);
    }

}