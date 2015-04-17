<?php
include '../config/config.php';
include '../config/header.php';
?>

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

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <link href="../css/responsiveslides.css" rel="stylesheet">
        <script src="../js/responsiveslides.min.js"></script>

        <script>
            $(function () {
                // Slideshow 2
                $("#slider1").responsiveSlides({
                    auto: false,
                    pager: true,
                    speed: 300,
                    maxwidth: 540
                });
            });
        </script>

    </head>
    <body>
    <div id="wrapper">
    <!-- Slideshow 2 -->
    <ul class="rslides" id="slider1">
        <li><img src="../assets/images/ppp1/Folie1.PNG" alt=""></li>
        <li><img src="../assets/images/ppp1/Folie2.PNG" alt=""></li>
        <li><img src="../assets/images/ppp1/Folie3.PNG" alt=""></li>
    </ul>
    </div>
    </body>
    </html>

<?php
include '../config/footer.php';
?>