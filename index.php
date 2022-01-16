<?php
require_once("app/config/config.php");
require_once("app/modules/api.php");

$clima_api = new Clima_tempo_API(CLIMA_TEMPO_API);

$all_cities = $clima_api->all_cities();
$city_registered = $clima_api->city_already_registered();
foreach ($all_cities as $city) {
    if ($city->id == $city_registered->locales[0]) {
        $current_city = $city->name;
    }
}
$idCity = $_POST['idcity'] ?? $city_registered->locales[0];
if (!empty($idCity)) {
    $clima = $clima_api->current_weather($idCity);
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
                <h4>Registered city: <?php echo $current_city; ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form action="index.php" method="POST">
                    <select name="idcity" id="idcity">
                        <?php foreach ($all_cities as $key => $values) { ?>
                            <option value="<?php echo $values->id; ?>"><?php echo $values->name; ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="resultado">
                    <?php if ($clima->error != true) { ?>
                        <h2>Weather Forecast for <?php echo $current_city; ?></h2>
                        <div class="row">
                            <label for="Temperature">
                                <strong>Temperature:</strong> <span class="badge bg-info"><?php echo $clima->data->temperature; ?>ºC</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Humidity">
                                <strong>Humidity:</strong> <span class="badge bg-info"><?php echo $clima->data->humidity; ?>%</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Pressure">
                                <strong>Pressure:</strong> <span class="badge bg-info"><?php echo $clima->data->pressure; ?>hPa</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Wind Velocity">
                                <strong>Wind Velocity:</strong> <span class="badge bg-info"><?php echo $clima->data->wind_velocity; ?>km/h</span>
                            </label>
                        </div>
                        <div class="row">
                            <label for="Date">
                                <strong>Date:</strong> <span class="badge bg-info"><?php echo $clima->data->date; ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="alert alert-danger" role="alert">
                                Erro: <?php echo $clima->detail; ?>
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

        $("#idcity").change(function() {
            sessionStorage.setItem('id', $('#idcity').val())
            sessionStorage.setItem('city', $('#idcity option:selected').text())
        });
        if (id) {
            newOption = new Option(city, id, true, true);
            $('#idcity').append(newOption).trigger('change');
        } else {
            $('#idcity').val('').trigger('change');
        }

        $('#idcity').select2({
            placeholder: 'Select a city',
            allowClear: true
        });

    });
</script>

</html>