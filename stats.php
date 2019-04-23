<?php
require("session_init.php");

require("config.php");
require("lib/adodb5/adodb.inc.php");

    /*
     * hlavna tabulka
     */

    $db = NewADOConnection('mysqli');
    $db->Connect($hostname, $username, $password, $dbname);
    $db->SetCharSet('utf8');

    $query_all_rows = "SELECT country, country_code, count(*) as count FROM `users` group by country, country_code";
    $all_rows_data = $db->Execute($query_all_rows) or die ("Chyba: " . $db->ErrorMsg());


    /*
     * miesta na mape
     */

    $query_map_locations = "SELECT city, latitude, longitude, count(*) as count FROM `users` group by latitude, longitude, city";
    $map_locations_data = $db->Execute($query_map_locations) or die ("Chyba: " . $db->ErrorMsg());

    $locations_result = array();
    while($row = $map_locations_data->FetchRow()) {
        $city = ($row['city'] == "unknown") ? "Nelokalizované mesto" : $row['city'];
        $locations_result[] = array(
                'city' => $city,
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'count' => $row['count']
        );
    }
    $json_locations_result = json_encode($locations_result);

    /*
     * casy
     */

    $query_visit_times = "
        SELECT 
            CASE 
                WHEN time(login_time) >= '06:00:00' and time(login_time) < '15:00:00' THEN '06:00-15:00' 
                WHEN time(login_time) >= '15:00:00' and time(login_time) < '21:00:00' THEN '15:00-21:00' 
                WHEN time(login_time) >= '21:00:00' and time(login_time) < '24:00:00' THEN '21:00-24:00' 
                WHEN time(login_time) >= '00:00:00' and time(login_time) < '06:00:00' THEN '00:00-6:00' 
            END AS category, count(*) AS count 
        FROM users 
        GROUP BY category";

    $visit_times_data = $db->Execute($query_visit_times) or die ("Chyba: " . $db->ErrorMsg());


    /*
     * pocty navstev jednotlivych stranok
     */

    $query_pages_visits = "SELECT * FROM `counters` ORDER BY counter DESC";
    $pages_visits_data = $db->Execute($query_pages_visits) or die ("Chyba: " . $db->ErrorMsg());

    function echo_main_table($data) {
        echo "<table>
                <tr>
                    <th>Vlajka</th>
                    <th>Štát</th>
                    <th>Počet návštev</th>
                </tr>";

        while ($row = $data->FetchRow()) {
            $country_code = $row['country_code'];

            $o_country = $row['country'];
            $o_count = $row['count'];
            $o_country_code_link = 'http://www.geonames.org/flags/x/' . strtolower($country_code) . '.gif';

            echo "
            <tr id='clickable_$country_code'>
                <td><img alt=country_flag src=$o_country_code_link></td>
                <td>$o_country</td>
                <td>$o_count</td>
            </tr>
            ";
        }
        echo "</table>";
    }

    function echo_time_stats_table($data) {
        echo "<table>
                <tr>
                    <th>Časový interval</th>
                    <th>Počet návštev</th>
                </tr>";
        while($row = $data->FetchRow()) {
            $o_category = $row['category'];
            $o_count = $row['count'];
            echo "
            <tr>
                <td>$o_category</td>
                <td>$o_count</td>
            </tr>
            ";
        }

        echo "</table>";
    }

    function echo_counters_table($data) {
        echo "<table>
                <tr>
                    <th>Názov podstránky</th>
                    <th>Počet kliknutí</th>
                </tr>";
        while($row = $data->FetchRow()) {
            $o_page_name = $row['page_name'];
            $o_counter = $row['counter'];

            echo "
            <tr>
                <td>$o_page_name</td>
                <td>$o_counter</td>
            </tr>
            ";
        }

        echo "</table>";
    }

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Zadanie 7</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/func.js"></script>
    <script src='http://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=AtDxCI05N6CVUwNCvfkqkfQEpEs1eIiIrseigcRoXi2pimCBCGClRmOT0fwN-uFA' async defer></script>
</head>
<body>
<div id="width_limit">
    <?php echo_main_table($all_rows_data); ?>
    <div id="myMap" style='position:relative;width:600px;height:400px;margin:10px;'></div>
    <?php echo_time_stats_table($visit_times_data); ?>
    <?php echo_counters_table($pages_visits_data); ?>
</div>
    <div id="dialog" title="Počet návštev podľa mesta"></div>

    <br><br>
    <a class=velky href=index.php>BACK</a>

    <script>
        logUserData();
        incrementCounter();

        $("tr[id^='clickable_']").click(function() {
            var thisId = $(this).attr('id');
            var countryCode = thisId.split("_").pop();
            showDialogTable(countryCode);
        });

        var locationsData = <?php echo $json_locations_result; ?>;
    </script>
</body>
</html>









