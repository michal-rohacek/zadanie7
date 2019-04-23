<?php
require("session_init.php");

$data = getWeatherByCity($_SESSION['user_info']['city']);

if($data->cod != 200) {
    $latitude = $_SESSION['user_info']['latitude'];
    $longitude = $_SESSION['user_info']['longitude'];
    $nearest_city = getNearestCity_cURL($latitude, $longitude);
    $data = getWeatherByCity($nearest_city);
}

$currentTime = time() + $_SESSION['user_info']['time_offset'] * 3600;

function getWeatherByCity($cityName) {
    $apiKey = "1066a3c9570df6002499c1793ac933cd";
    $currentCityWeatherAPI_URL = "http://api.openweathermap.org/data/2.5/weather?q=" . $cityName . "&lang=en&units=metric&APPID=" . $apiKey;
    return process_cURL_API_request($currentCityWeatherAPI_URL);
}

function getNearestCity_cURL($latitude, $longitude) {
    $nearestCityAPI_URL = "http://api.geonames.org/findNearbyPlaceNameJSON?lat=" . $latitude . "&lng=" . $longitude . "&username=michal.r.1024&cities=cities15000";
    $data = process_cURL_API_request($nearestCityAPI_URL);
    return $data->geonames[0]->toponymName;
}

function process_cURL_API_request($apiUrl) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);
    $data = json_decode($response);
    return $data;
}


?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Zadanie 7</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/func.js"></script>
</head>
<body>
    <div class="report-container">
        <h2><?php echo $data->name; ?> Weather Status</h2>
        <div class="time">
            <div><?php echo date("l g:i a", $currentTime); ?></div>
            <div><?php echo date("jS F, Y",$currentTime); ?></div>
            <div><?php echo ucwords($data->weather[0]->description); ?></div>
        </div>
        <div class="weather-forecast">
            <img
                    alt="weather-icon"
                    src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png"
                    class="weather-icon" /> <?php echo $data->main->temp_max; ?>°C<span
                    class="min-temperature"><?php echo $data->main->temp_min; ?>°C</span>
        </div>
        <div class="time">
            <div>Humidity: <?php echo $data->main->humidity; ?> %</div>
            <div>Wind: <?php echo $data->wind->speed; ?> km/h</div>
        </div>
    </div>

    <a class=velky href=index.php>BACK</a>

    <script>
        logUserData();
        incrementCounter();
    </script>
</body>
</html>









