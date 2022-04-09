<?php 

include_once("app/api.php");
include_once("vendor/autoload.php");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();

$idCity = $_POST['idcity'] ?? null;
$token = $_ENV['TOKEN'];

$climaTempo = new Clima_tempo_API($token);

$response = $climaTempo->current_weather($idCity);

print json_encode($response);


?>