<?php

class Clima_tempo_API
{

    private $token = null;
    protected $urlResgiter = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/';
    protected $urlApi = 'http://apiadvisor.climatempo.com.br/api/v1/';

    function __construct($token)
    {
        if (!empty($token)) {
            $this->token = $token;
        }
    }

    function request($endpoint = '', $params = array())
    {
        $url = $this->urlApi . $endpoint . '?token=' . $this->token . '&format=json';
        if (is_array($params)) {
            $url .= '&' . http_build_query($params);
            $url = substr($url, 0, -1);
            $response = $this->file_g_contents($url);
            return json_decode($response);
        } else {
            return false;
        }
    }

    /**
     * Todas as cidades.
     * @return array
     */
    function all_cities()
    {
        return $this->request('locale/city');
    }

    function register_a_city($city_id)
    {
        $url = $this->urlResgiter . $this->token . '/locales';
        $response = $this->file_g_contents($url, "PUT",  array("localeId[]" => $city_id));
        return json_decode($response);
    }

    /**
     * Cidade já cadastrada.
     * @return object
     */
    function city_already_registered()
    {
        $url = $this->urlResgiter . $this->token . '/locales';
        $response = $this->file_g_contents($url);
        return json_decode($response);
    }

    /**
     * Informa o clima atual da cidade.
     * @param int $city_id
     * @return object
     * 
     */
    function current_weather(int $city_id)
    {
        return $this->request('weather/locale/' . $city_id . '/current');
    }

    /**
     * Informa chuva climáticas da cidade.
     * @param int $city_id
     * @return object
     */
    function climate_rain(int $city_id)
    {
        return $this->request('climate/rain/locale/' . $city_id);
    }

    /**
     * Procura o id cidade.
     * @param int $city_id
     * @return object
     */
    function search_city_id(int $city_id)
    {
        return $this->request('locale/city/' . $city_id);
    }

    /**
     * curl_file_get_contents
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $fields
     * @return string
     */
    protected function file_g_contents($url, $method = 'GET', $dados = null , $header = array('Content-Type: application/x-www-form-urlencoded'))
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clima Tempo API');
        curl_setopt($curl_handle, CURLOPT_POST, true);
        if (is_array($dados)) {
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($dados));
        }
        // curl_setopt($curl_handle, CURLOPT_POSTFIELDS, 'localeId[]=5959'); esse funcionou!!!
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
}
