<?php
include '../config/config.php';
include '../config/header.php';
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="/favicon.ico">

        <title>Learn More</title>

        <link href="/css/bootstrap.css" rel="stylesheet">
        <link href="/css/bootstrap-custom.css" rel="stylesheet">
        <link href="/css/slider.css" rel="stylesheet">


        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>


    <br>

    <div class="panel panel-default" align="center">
        <div class="panel-heading">
            <h2 class="panel-title">Erfahre mehr über unser Konzept</h2>
        </div>

        <div>
            <a class="btn btn-primary btn-lg" href="/src/luca/ppp1.html" role="button">PPP1</a>
            <a class="btn btn-primary btn-lg" href="/src/submitData.html" role="button">PPP2</a>

        </div>

        <div class="panel-body">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Team</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Fabian</td>
                            <td>Simroth</td>
                            <td>Tester, Programmierer, Webcontent</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Stefan</td>
                            <td>Zinke</td>
                            <td>Tester, Datenbank</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Luca</td>
                            <td>Nerlich</td>
                            <td>Tester, Programmierer</td>
                        </tr>
                        <tr>
                            <th scope="row">4</th>
                            <td>Clemens</td>
                            <td>Rassbach</td>
                            <td>Analyse, Architekt, Programmierer</td>
                        </tr>
                        <tr>
                            <th scope="row">5</th>
                            <td>daniel</td>
                            <td>Sommerlig</td>
                            <td>Projektleiter, Analyse</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Hawai-After-Sales-Tracker</h4>
                </div>
                <div id="main" class="panel-body">
                    <article>
                        <p>Die HAWAICycle AG möchte ihren Kunden, durch kontinuierlichen Service nach dem Kauf, länger
                            binden.
                            Dem Kunden sollen Zubehör, Verschleißteile und eventuelle Wartungstermine möglichst
                            automatisiert
                            angeboten werden.
                            Hierzu wird dem Kunden eine Weboberfläche zur Erfassung seiner persönlichen Daten angeboten.
                            Zum
                            Beispiel die gefahrenen Kilometer und eventuelle Schäden.
                            Diese getrackten Daten werden dem Kunden dann aufbereitet dargestellt. Außerdem werden die
                            Daten
                            mit
                            dem HAWAICycle CRM synchronisiert.
                            Die HAWAICycle AG kann auf Basis dieser Daten dem Kunden Mitteilungen und Serviceangebote
                            schicken.
                        </p>
                    </article>
                    <br>

                    <div id="secondary" align="left">
                        <section>
                            <span class="label label-info">Kernprozesse:</span>
                            <ol>
                                <li>Benutzerlogin</li>
                                <li>Kunde übermittelt Daten</li>
                                <ul>
                                    <li>Gefahrene Kilometer</li>
                                    <li>Eventuelle Schäden</li>
                                </ul>
                                <li>Aufbereitung der empfangenen Daten</li>
                                <li>Eventuelle Rückmeldung</li>
                            </ol>

                            <br>
                            <span class="label label-info">Coding:</span>
                            <ul>
                                <li>HTML5</li>
                                <li>CSS3</li>
                                <li>php</li>
                                <li>Microsoft Azure</li>
                                <li>Codeverwaltung über Github</li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>
    </body>
    </html>

<?php
include '../config/footer.php';
?>