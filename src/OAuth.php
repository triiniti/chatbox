<?php
namespace ChatBox\App;

use Dotenv\Dotenv;

class OAuth
{

    public static function authenticate($token)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $params = [
            'client_id' => $_ENV['CLIENT_ID'],
            'redirect_uri' => sprintf('%s/authenticate', $_ENV['APP_URL']),
            'state' => $token,
            'scope' => 'repo:invite user user:email',
        ];
        $url = 'https://github.com/login/oauth/authorize?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            return $response;
        }
        curl_close($ch);
    }

    public static function get_user_code($code)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $post_data = [
            'client_id' => $_ENV['CLIENT_ID'],
            'client_secret' => $_ENV['CLIENT_SECRET'],
            'code' => $code,
            'redirect_uri' => sprintf('%s/authenticate', $_ENV['APP_URL']),
        ];
        $url = 'https://github.com/login/oauth/access_token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
        ]);
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            return $json['access_token'] ?? null;
        }
        curl_close($ch);
    }

    public static function get_user_data($code)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $url = 'https://api.github.com/user';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github+json',
            'User-Agent: ChatBox-App',
            'X-GitHub-Api-Version:2022-11-28',
            'Authorization: Bearer ' . $code
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            return json_decode($response, true);
        }
        curl_close($ch);
    }

    public static function get_user_email($code)
    {
        $url = 'https://api.github.com/user/emails';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github+json',
            'User-Agent: ChatBox-App',
            'X-GitHub-Api-Version:2022-11-28',
            'Authorization: Bearer ' . $code
        ]);
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            return $response[0]['email'] ?? null; // Return the first email
        }
        curl_close($ch);
    }
}