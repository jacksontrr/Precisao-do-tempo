<?php
require_once("./app/api.php");
require_once("./vendor/autoload.php");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();
$token = $_ENV['TOKEN'] ?? null;

$clima_api = new Clima_tempo_API($token);

$all_cities = $clima_api->all_cities();
if ( !empty($all_cities)) {
    $city_registered = $clima_api->city_already_registered();
    foreach ($all_cities as $city) {
        if ($city->id == $city_registered->locales[0]) {
            $current_city = $city->name;
        }
    }
    $idCity = $_POST['idcity'] ?? $city_registered->locales[0];
    if(!empty($_POST['idcity'])){
        $clima_api->register_a_city($idCity);
    }
    if (!empty($idCity)) {
        $clima = $clima_api->current_weather($idCity);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather forecast</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <section class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Weather forecast</h1>
            </div>
            <div class="col-md-6">
                <h4>Registered city: <span id="current_city"><?php echo $current_city; ?></span></h4>
                <span class="error_alert"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form action="index.php" method="POST">
                    <div class="row">
                        <div class="col-md-8">
                            <select name="idcity" id="idcity" class="form-select">
                                <?php foreach ($all_cities as $key => $values) { ?>
                                    <option value="<?php echo $values->id; ?>"><?php echo $values->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                            <button type="submit" class="btn btn-success btn-sm" id="registerCity">register city</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="resultado" style="margin-top: 10px;">
                    <?php if ($clima->error != true && !empty($clima)) { ?>
                        <h2>Weather Forecast for <?php echo $current_city; ?></h2>
                        <div class="row">
                            <label for="Temperature">
                                <strong>Temperature:</strong> <span class="badge bg-info" id='temperature'><?php echo $clima->data->temperature; ?>ºC</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Humidity">
                                <strong>Humidity:</strong> <span class="badge bg-info" id="humidity"><?php echo $clima->data->humidity; ?>%</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Pressure">
                                <strong>Pressure:</strong> <span class="badge bg-info" id="pressure"><?php echo $clima->data->pressure; ?>hPa</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Wind Velocity">
                                <strong>Wind Velocity:</strong> <span class="badge bg-info" id="wind_velocity"><?php echo $clima->data->wind_velocity; ?>km/h</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Date">
                                <strong>Date:</strong> <span class="badge bg-info" id="date"><?php echo $clima->data->date; ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="alert alert-danger" role="alert">
                                Error: <span class="error_alert"><?php echo $clima->detail; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </section>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {

        const id = sessionStorage.getItem('id');
        const city = sessionStorage.getItem('city');
        if(city == ''){ sessionStorage.clear(); }

        $("#idcity").change(function() {
            sessionStorage.setItem('id', $('#idcity').val())
            sessionStorage.setItem('city', $('#idcity option:selected').text())
        });
        if (city) {
            newOption = new Option(city, id, true, true);
            $('#idcity').append(newOption).trigger('change');
        } else {
            $('#idcity').val('').trigger('change');
            sessionStorage.clear();
        }

        $('#idcity').select2({
            placeholder: 'Select a city',
            allowClear: true
        });

        $('#registerCity').click(function(){
            $.ajax({
                url: 'registerCity.php',
                type: 'POST',
                data: {
                    idcity: $('#idcity').val()
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('.error_alert').html('');
                    if (response[1] === true) {
                        $('.error_alert').html(response.detail);
                        return false;
                    }else if(response.status === true){
                        $.ajax({
                            url: 'getWeather.php',
                            type: 'POST',
                            data: {
                                idcity: $('#idcity').val()
                            },
                            success: function(dados) {
                                dados = JSON.parse(dados);
                                $('.error_alert').html('');
                                if (dados.error === true) {
                                    $('.error_alert').html(dados.detail);
                                } else {
                                    $('#current_city').html(dados.name);
                                    $('#temperature').html(dados.data.temperature);
                                    $('#humidity').html(dados.data.humidity);
                                    $('#pressure').html(dados.data.pressure);
                                    $('#wind_velocity').html(dados.data.wind_velocity);
                                    $('#date').html(dados.data.date);
                                }
                            }
                        })
                        return false;
                    }
                    console.log(response);
                }
            });
            return false;
        });
        
    });
</script>

</html>