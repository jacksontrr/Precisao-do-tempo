<?php

class Clima_tempo_API
{

    private $token = null;
    protected $urlResgiter = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/';

    function __construct($token)
    {
        if (!empty($token)) {
            $this->token = $token;
        }
    }

    function request($endpoint = '', $params = array())
    {
        $url = 'http://apiadvisor.climatempo.com.br/api/v1/' . $endpoint . '?token=' . $this->token . '&format=json';
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (empty($value)) continue;
                $url .= $key . '=' . urlencode($value) . '&';
            }
            $url = substr($url, 0, -1);
            $response = $this->file_g_contents($url);
            return json_decode($response);
        } else {
            return false;
        }
    }

    function all_cities()
    {
        return $this->request('locale/city');
    }

    function register_a_city($city_id)
    {
        $url = $this->urlResgiter . $this->token . '/locales';
        $response = $this->file_g_contents($url, "PUT", array('Content-Type: application/x-www-form-urlencoded', array("localeId[]" => $city_id)));
        return json_decode($response);
    }

    function city_already_registered()
    {
        $url = $this->urlResgiter . $this->token . '/locales';
        $response = $this->file_g_contents($url);
        return json_decode($response);
    }

    function current_weather($city_id)
    {
        return $this->request('weather/locale/' . $city_id . '/current');
    }

    function climate_rain($city_id)
    {
        return $this->request('climate/rain/locale/' . $city_id);
    }

    function search_city_id($city_id)
    {
        return $this->request('locale/city/' . $city_id);
    }

    protected function file_g_contents ($url, $method = 'GET', $header = array('Content-Type: application/x-www-form-urlencoded'), $dados = null) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clima Tempo API');
        if ($dados) {
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $dados);
        }
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
    
}
