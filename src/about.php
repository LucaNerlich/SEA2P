<?php
include '../config/config.php';
include '../config/header.php';
?>

    <div class="panel panel-default" align="center">
        <div class="panel-heading">
            <h2 class="panel-title">Erfahre mehr über unser Konzept</h2>
        </div>

        <div>
            <br>
            <a class="btn btn-primary btn-lg" href="../assets/files/Lastenheft.pdf" role="button"
               download="Download Lastenheft.pdf">Download Lastenheft</a>
            <a class="btn btn-primary btn-lg" href="../assets/files/User%20Stories.pdf" role="button"
               download="Download User_Stories.pdf">Download User Stories</a>
            <br><br>
            <a class="btn btn-primary btn-lg" href="/src/presentation2.php" role="button">PowerPoint 1</a>
            <a class="btn btn-primary btn-lg" href="../404.php" role="button">PowerPoint 2</a>
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
                            <td>Daniel</td>
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

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Technische Bausteine</h4>
                </div>
                <div id="main" class="panel-body">
                    <img src="../assets/images/Architektur-Technische%20Bausteine.png" style="width:90%"/>
                </div>
            </div>
        </div>


    </div>
<?php
include '../config/footer.php';
?>