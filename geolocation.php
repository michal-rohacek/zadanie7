<?php
require("session_init.php");

function echo_user_info_table() {
    $data = $_SESSION['user_info'];

    $city = ((($data['city'] == "") || ($data['city'] == null))
        ? "Mesto sa nedá lokalizovať, alebo sa nachádzate na vidieku"
        : $data['city']);

    echo "
        <table>
            <tr><td>IP adresa</td><td>$data[ip]</td></tr>
            <tr><td>GPS Latitude</td><td>$data[latitude]</td></tr>
            <tr><td>GPS Longitude</td><td>$data[longitude]</td></tr>
            <tr><td>Miesto</td><td>$city</td></tr>
            <tr><td>Štát</td><td>$data[country]</td></tr>
            <tr><td>Hlavné mesto</td><td>$data[capital]</td></tr>
        </table>";

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

<div id="width_limit">
    <?php echo_user_info_table(); ?>
</div>

<script>
    logUserData();
    incrementCounter();
</script>

<a class=velky href=index.php>BACK</a>
</body>
</html>









