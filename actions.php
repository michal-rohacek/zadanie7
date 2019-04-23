<?php
session_start();
switch($_POST['action']) {
    case 'increment_counter':
        increment_counter();
        break;
    case 'get_cities':
        get_cities();
        break;
    case 'process_visit':
        process_visit();
        break;
    default:
        destroy_session();
}

function increment_counter() {
    if(isset($_POST['page_name'])) {

        $db = initDBConnection();

        $page_name = $_POST['page_name'];

        $query_insert_visit = "UPDATE counters SET counter = counter + 1 where page_name = '$page_name'";
        $result = $db->Execute($query_insert_visit)  or die ("Chyba v query: $query_insert_visit " . $db->ErrorMsg());
    }
}

function get_cities()  {
    if (isset($_POST['country_code'])) {

        $db = initDBConnection();

        $country_code = $_POST['country_code'];

        $query_visits_by_city = "SELECT city, count(*) as count FROM `users` WHERE country_code = '$country_code' group by city";
        $query_result_set = $db->Execute($query_visits_by_city) or die ("Chyba v query: $query_visits_by_city " . $db->ErrorMsg());

        $result = array();
        while ($row = $query_result_set->FetchRow()) {
            $city = ($row['city'] == "unknown") ? "Nelokalizované mesto" : $row['city'];
            $result[$city] = $row["count"];
        }
        echo json_encode($result);
    }
}
function process_visit() {

    $db = initDBConnection();

    $user_info = $_SESSION['user_info'];

    $ip = $user_info['ip'];
    $country = $user_info['country'];
    $country_code = $user_info['country_code'];
    $city = $user_info['city'];
    $time_offset = $user_info['time_offset'];
    $offset_in_seconds = $time_offset * 3600;

    $latitude = $user_info['latitude'];
    $longitude = $user_info['longitude'];

    $last_visit = getLastVisitDatetime_DB($db, $ip);
    $current_time = date("Y-m-d H:i:s",time() + $offset_in_seconds);

    if(isVisitingAfter24Hours($current_time, $last_visit)) {
        $query_insert_visit = "INSERT INTO users (ip, login_time, country, country_code, city, latitude, longitude) 
                              VALUES ('$ip', '$current_time', '$country', '$country_code', '$city', '$latitude', '$longitude')";
        $result = $db->Execute($query_insert_visit)  or die ("Chyba v query: $query_insert_visit " . $db->ErrorMsg());
        echo "visit pridany";
    } else {
        echo "visit v ramci 24h";
    }
}

function isVisitingAfter24Hours($current, $last) {
    $SECONDS_IN_24_HOURS = 24 * 60 * 60;
    $diff = strtotime($current) - strtotime($last);
    return $SECONDS_IN_24_HOURS < $diff;
}

function getLastVisitDatetime_DB($conn, $ip) {
    $query_get_last_login_time = "SELECT login_time FROM users WHERE ip = '$ip' ORDER BY login_time DESC limit 1";
    $last_visit = $conn->GetOne($query_get_last_login_time);
    return $last_visit;
}

function destroy_session() {
    session_start();
    session_destroy();
    header("Location:index.php");
}

function initDBConnection() {
    require_once("config.php");
    require_once("lib/adodb5/adodb.inc.php");

    $db = NewADOConnection('mysqli');
    $db->Connect($hostname, $username, $password, $dbname);
    $db->SetCharSet('utf8');

    return $db;
}







