<?php
include '../config/config.php';
include '../config/header.php';
?>

<!-- http://www.sequencejs.com/themes/sliding-horizontal-parallax/ -->

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="/favicon.ico">

        <title>Presentation 1</title>
        <link rel="stylesheet" type="text/css" media="screen" href="../css/sequencejs-theme.sliding-horizontal-parallax.css" />
        <link href="http://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet" type="text/css">

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

        <script>
            if (typeof jQuery == 'undefined'){
                document.write(unescape('%3Cscript src="scripts/jquery-min.js" %3E%3C/script%3E'));
            }
        </script>
        <script src="../js/jquery.sequence-min.js"></script>
        <script src="../js/sequencejs-options.sliding-horizontal-parallax.js"></script>

    </head>
    <body>

    <div id="sequence">
        <img class="sequence-prev" src="../assets/images/ppp1/bt-prev.png" alt="Previous" />
        <img class="sequence-next" src="../assets/images/ppp1/bt-next.png" alt="Next" />

        <ul class="sequence-canvas">
            <li class="animate-in">
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie1.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie2.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie3.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie4.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie5.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie6.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie7.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie8.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie9.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie10.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie11.PNG" alt="Blue Sky" />
            </li>
            <li>
                <div class="info">
                </div>
                <img class="sky" src="../assets/images/ppp1/Folie12.PNG" alt="Blue Sky" />
            </li>

        </ul>
    </div>

    </body>
    </html>
<?php
include '../config/footer.php';
?>