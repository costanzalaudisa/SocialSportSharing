<?php
    session_start();
?>

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

    <!-- Custom Icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="icon/favicon-16x16.png">
    <link rel="manifest" href="icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <?php
        $Nome = $_SESSION["Nome"];
        $Cognome = $_SESSION["Cognome"];
        $User = $_SESSION["Username"];
        $ymd = new DateTime();
        $oggi = $ymd -> format('Y-m-d');

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
                <a class="navbar-brand" href="HomeMembro.php"><img id="logo" src="images/logo.png" alt="Image" width="200px" height="50px"></a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="categorie.php">Categorie</span></a></li>
                    <li><a href="registrazioneES.php">Eventi</a></li>
                    <li><a href="impianti.php">Impianti</span></a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                             <form action ="HomeMembro.php" method = "post">
                                <li><input type = "submit" class="btn btn-link" name="Calcio" value="Calcio"></li>
                                <li><input type = "submit" class="btn btn-link" name="Basket" value="Basket"></li>
                                <li><input type = "submit" class="btn btn-link" name="Tennis" value="Tennis"></a></li> 
                            </form>  
                            <?php
                                if (isset($_POST['Calcio'])){
                                   $_SESSION["CategoriaScelta"] = "Calcio"; 
                                   header('Location:forum.php');
                                } else if (isset($_POST['Basket'])){
                                   $_SESSION["CategoriaScelta"] = "Basket";
                                   header('Location:forum.php');
                                } else if (isset($_POST['Tennis'])){
                                   $_SESSION["CategoriaScelta"] = "Tennis";
                                   header('Location:forum.php');
                                }
                            ?>            
                        </ul>
                    </li>    
                </ul>        
                
                <ul class="nav navbar-nav navbar-right">        
                    <?php 
                        try {
                            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
                            $sqlup = "SELECT * FROM UP";
                            $up = $connection -> query($sqlup);                                               
                        } catch (PDOException $e){
                            echo "Connessione al database non riuscita";
                            exit();
                        }
                        
                        $presente = 0;
                        $TipoUser = $_SESSION["Username"];
                        while (($row = $up -> fetch()) && ($presente == 0)){
                            if ($TipoUser == $row["UserUP"]){                                   
                                $presente = 1;
                                break;
                            }
                        }
                        
                        if ($presente == 1){
                            $profilo = "profiloUP.php";
                        } else {
                            $profilo = "profiloUS.php";
                        }               
                    ?>
                    
                    <li><a href="<?php echo $profilo ?>">Profilo</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <div class="container" id="home">
        <div class="row">
            <div class="col-lg-12">
                <div class="container-fluid bg-grey">
                    <div class="row">
                        <div class="col-lg-2">
                            <!--CODICE PER INSERIRE L'IMMAGINE NEL PROFILO-->
                            <?php
                                try {
                                    $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                                } catch (PDOException $e){
                                    echo "Connessione al database non riuscita";
                                    exit();
                                }

                                try {
                                    $sqlfoto = 'SELECT Foto FROM UTENTE WHERE Username = "'.$User.'"';
                                    $foto = $connection -> query($sqlfoto);
                                } catch (Exception $e){
                                    echo "Errore: ".$e -> getMessage();
                                    exit(); 
                                }
                                           
                                while ($row = $foto -> fetch()) {
                                    $FotoCaricare = $row["Foto"];
                                }

                                $percorsoImmagine = 'images/'.$FotoCaricare;
                            ?>
                            <div class="thumbnail">
                                <img src=<?php echo $percorsoImmagine; ?> alt="Image">  
                            </div> 
                        </div>
                        <div class="col-lg-10">
                            <h1><?php echo 'Benvenuto, ',$Nome,'  ',$Cognome; ?></h1>
                            <br>
                            <p>
                                Il tuo username è: <?php echo $User?><br>
                                <a href="<?php echo $profilo ?>">Vai al tuo profilo →</a>
                            </p>
                            <br>
                            <table class="table" id="eventi">
                                <tr>
                                    <th><center>Prossime partite:</center></th>
                                </tr>
                                <?php
                                    try {
                                        $sqles = "SELECT * FROM ES WHERE Data > CURDATE() AND Stato = 'APERTO' ORDER BY Data ASC LIMIT 10";
                                        $es = $connection -> query($sqles);
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    }

                                    while ($row = $es -> fetch()){
                                        echo '<tr>';
                                        echo '<td>Evento n°',$row["Id"],' | Sport: ',$row["CategoriaSport"],' | Data: ',$row["Data"],' | Impianto: ',$row["NomeImpianto"],'</td></tr>';
                                        echo '</tr>';
                                    }

                                    $connection = NULL;
                                ?>
                            </table>
                            <a href="registrazioneES.php">Registrati ad un evento →</a>
                            <br><br>
                        </div>
                    </div>
                </div>
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