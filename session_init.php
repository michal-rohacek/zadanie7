<?php
session_start();

if($_SESSION['allowedIP'] != $_SERVER['REMOTE_ADDR']) {
    $_SESSION['agreement'] = "changedIP";
}

if($_SESSION['agreement'] != "accepted") {
    header("Location:privacy_confirm.php");
}
else {
    if((!isset($_SESSION['user_info'])) || ($_SESSION['user_info']['ip'] != $_SERVER['REMOTE_ADDR'])) {
        fetch_and_save_geolocation_into_session();
    }
}

function fetch_and_save_geolocation_into_session() {
    $apiKey = "a8588fd2d79043709c6c3dd53f7fbb36";
    $ip = $_SERVER['REMOTE_ADDR'];

    $ipgeolocationUrl = "https://api.ipgeolocation.io/ipgeo?apiKey=" . $apiKey . "&ip=" . $ip;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $ipgeolocationUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);
    $data = json_decode($response, true);

    $user_info = array();

    $user_info['ip'] = $data['ip'];
    $user_info['latitude'] = $data['latitude'];
    $user_info['longitude'] = $data['longitude'];
    $user_info['city'] = $data['city'];
    $user_info['country'] = $data['country_name'];
    $user_info['country_code'] = $data['country_code2'];
    $user_info['capital'] = $data['country_capital'];
    $user_info['time_offset'] = $data['time_zone']['offset'] + $data['time_zone']['dst_savings'];
    
    $_SESSION['user_info'] = $user_info;
}

?>