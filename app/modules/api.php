<?php

class Clima_tempo_API
{

    private $token = null;

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
        header('Content-Type: application/x-www-form-urlencoded;');
        $url = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/' . $this->token . '/locales?localeId[]=' . $city_id;
        $response = $this->file_g_contents($url);
        return json_decode($response);
    }

    function city_already_registered()
    {
        $url = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/' . $this->token . '/locales';
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

    function file_g_contents ($url){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clima Tempo API');
        return curl_exec($curl_handle);
    }
}
