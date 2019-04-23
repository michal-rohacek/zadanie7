<?php
    session_start();

    if(isset($_POST['agreement'])) {

        if($_POST['agreement'] == 'changeValue') {
            $_SESSION['agreement'] = "";
            $_SESSION['message'] = "";
            header("Location:privacy_confirm.php");
        }

        $_SESSION['agreement'] = $_POST['agreement'];

        if($_POST['agreement'] == 'accepted') {
            $_SESSION['message'] = "";
            $_SESSION['allowedIP'] = $_SERVER['REMOTE_ADDR'];
        }

        elseif($_POST['agreement'] == 'declined') {
            $_SESSION['message'] = "<div class='pretty' style='text-align:center; width:400px'>Nemozete si prezerat tuto stranku!<button onclick=\"agreementRedirect('changeValue')\">Zmenil som názor</button></div>";
        }

    }

    if(isset($_SESSION['agreement']) && ($_SESSION['agreement'] != "")) {

        if ($_SESSION['agreement'] == 'accepted') {
            header("Location:index.php");
        }

    }


?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <title>Zadanie 7</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery.redirect.js"></script>
</head>
<body>

<?php
    if(isset($_SESSION['message'])) {
        echo $_SESSION['message'];
    }

    if($_SESSION['agreement'] != 'declined') {
        echo "
            <div id=\"boxes\" style='font-weight: bold;'>
                <div id=\"dialog\" class=\"window\">
                    Súhlasíte, že bude spracovávaná Vaša IP adresa a GPS súradnice?
                    <div id=\"popupfoot\">
                        <a href=\"#\" class=\"close agree\" onclick=\"agreementRedirect('accepted')\">Súhlasím</a> | 
                        <a class=\"agree\" style=\"color:red;\" href=\"#\" onclick=\"agreementRedirect('declined')\">Nesúhlasím</a> 
                    </div>
                </div>
                <div id=\"mask\"></div>
            </div>
        ";
    }
?>

  <script>
      $(document).ready(function() {
          var id = '#dialog';

            //Get the screen height and width
          var maskHeight = $(document).height();
          var maskWidth = $(window).width();

            //Set heigth and width to mask to fill up the whole screen
          $('#mask').css({'width':maskWidth,'height':maskHeight});

            //transition effect
          $('#mask').fadeIn(500);
          $('#mask').fadeTo("slow",0.9);

            //Get the window height and width
          var winH = $(window).height();
          var winW = $(window).width();

            //Set the popup window to center
          $(id).css('top',  winH/2-$(id).height()/2);
          $(id).css('left', winW/2-$(id).width()/2);

            //transition effect
          $(id).fadeIn(2000);

            //if close button is clicked
          $('.window .close').click(function (e) {
                //Cancel the link behavior
              e.preventDefault();

              $('#mask').hide();
              $('.window').hide();
          });
      });

      function agreementRedirect(response) {
          $.redirect('privacy_confirm.php', {'agreement': response});
      }

    </script>
</body>
</html>

