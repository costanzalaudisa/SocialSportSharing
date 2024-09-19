<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Unibo Social Sport Sharing</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/scrolling-nav.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- Custon Icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="icon/favicon-16x16.png">
    <link rel="manifest" href="icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <?php
        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
            $sqldata = "CALL ControlloData();";
            $data = $connection -> query($sqldata);                                           
        } catch (PDOException $e){
            echo "Connessione al database non riuscita";
            exit();
        }
    ?>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="Home.php"><img id="logo" src="images/logo.png" alt="Image" width="200px" height="50px"></a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Signup</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <div class="container" id="home">
        <div class="row">
            <div class="col-lg-12">
                <h1>Cos'è Unibo Social Sport Sharing?</h1>
                <br>
                <p>Unibo Social Sport Sharing è una piattaforma social dedicata agli studenti dell'Università di Bologna che permette di organizzare eventi sportivi presso le strutture del CUSB, di partecipare ad eventi sportivi e di interagire con altri utenti attraverso news e meccanismi di rating.</p>
                <br>
                <a class="btn btn-default page-scroll" href="login.php">Accedi!</a>
                <br>
                <a id="signuplink" href="signup.php">Non sei registrato?</a>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Scrolling Nav JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/scrolling-nav.js"></script>
    <script src="js/hover-dropdown.js"></script>
</body>
</html>