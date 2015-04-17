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
        <link rel="stylesheet" media="screen" href="../css/sequencejs-theme.modern-slide-in.css"/>
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Ruluko|Sirin+Stencil">

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

        <script src="../js/responsiveslides.min.js"></script>

        <script>
            if (typeof jQuery == 'undefined') {
                document.write(unescape('%3Cscript src="scripts/jquery-min.js" %3E%3C/script%3E'));
            }
        </script>

        <script src="../js/jquery.sequence-min.js"></script>
        <script src="../js/sequencejs-options.modern-slide-in.js"></script>

    </head>
    <body>
    <div class="sequence-theme">
        <div id="sequence">

            <img class="sequence-prev" src="../assets/images/ppp1/bt-prev.png" alt="Previous Frame"/>
            <img class="sequence-next" src="../assets/images/ppp1/bt-next.png" alt="Next Frame"/>

            <ul class="sequence-canvas">
                <li class="animate-in">
                    <h2 class="title">Built using Sequence.js</h2>

                    <h3 class="subtitle">The Responsive Slider with Advanced CSS3 Transitions</h3>
                    <img class="model" src="../assets/images/ppp1/Folie1.PNG" alt="Model 1"/>
                </li>
                <li>
                    <h2 class="title">Creative Control</h2>

                    <h3 class="subtitle">Create unique sliders using CSS3 transitions</h3>
                    <img class="model" src="../assets/images/ppp1/Folie2.PNG" alt="Model 2"/>
                </li>
                <li>
                    <h2 class="title">Cutting Edge</h2>

                    <h3 class="subtitle">Supports modern browsers, old browsers (IE7+), touch devices and responsive
                        designs</h3>
                    <img class="model" src="../assets/images/ppp1/Folie3.PNG" alt="Model 3"/>
                </li>
            </ul>

        </div>
    </div>
    </body>
    </html>
<?php
include '../config/footer.php';
?>