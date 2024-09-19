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

        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
            $sqldata = "CALL ControlloData();";
            $data = $connection -> query($sqldata);                                           
        } catch (PDOException $e){
            echo "Connessione al database non riuscita";
            exit();
        }

        ob_start();
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
                    <li><a href="categorie.php">Categorie</a></li>
                    <li class="active"><a href="registrazioneES.php">Eventi</a></li>
                    <li><a href="impianti.php">Impianti</span></a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                             <form action ="registrazioneES.php" method = "post">
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

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>EVENTI DISPONIBILI</h1>
                <br>
                <form action="registrazioneES.php" method="post">                
                    <table class="table" id="regeventi">
                        <tr style="text-align:center">
                            <td colspan="3">
                                <b>Ruolo scelto:</b><br><input type="radio" name="Ruolo" value="Giocatore"> Giocatore&emsp;
                                <input type="radio" name="Ruolo" value="Arbitro"> Arbitro
                            </td>
                        </tr>

                        <?php
                            function stampaEventi($Categoria){
                                try {
                                    $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                                } catch (PDOException $e){
                                    echo "Connessione al database non riuscita";
                                    exit();
                                }

                                $sqlconarb = "SELECT * FROM EventiConArbitro WHERE CategoriaSport = '".$Categoria."' AND Data > CURDATE();";
                                $conarb = $connection -> query($sqlconarb);
                                while ($row = $conarb -> fetch()){
                                    if ($row["Stato"] == 'CHIUSO'){
                                        continue;
                                    } else {
                                        echo '<tr><td><input type="radio" name="ES" value="',$row["Id"],'"> Evento n°',$row["Id"],' | Data: ',$row["Data"],' | Impianto: ',$row["NomeImpianto"];
                                        echo '<br>&emsp;<font color="#ff4040">AMMISSIBILE SOLO GIOCATORE</font>';
                                        echo '</td></tr>';
                                    }
                                }
                                
                                $sqlsenzarb = "SELECT * FROM EventiSenzaArbitro WHERE CategoriaSport = '".$Categoria."' AND Data > CURDATE();";
                                $senzarb = $connection -> query($sqlsenzarb);
                                while ($row = $senzarb -> fetch()){
                                    if ($row["Stato"] == 'CHIUSO'){
                                        echo '<tr><td><input type="radio" name="ES" value="',$row["Id"],'"> Evento n°',$row["Id"],' | Data: ',$row["Data"],' | Impianto: ',$row["NomeImpianto"];
                                        echo '<br>&emsp;<font color="#ff4040">AMMISSIBILE SOLO ARBITRO</font>';
                                        echo '</td></tr>';
                                    } else {
                                        echo '<tr><td><input type="radio" name="ES" value="',$row["Id"],'"> Evento n°',$row["Id"],' | Data: ',$row["Data"],' | Impianto: ',$row["NomeImpianto"];
                                        echo '</td></tr>';
                                    }
                                }
                            }
                        ?>

                        <tr>
                            <td>
                                <table class="table" id="listaeventi">
                                    <tr style="background-color:#DDDDDD"><th>Calcio</th></tr>
                                    <?php
                                        $Categoria = "CALCIO";
                                        stampaEventi($Categoria); 
                                    ?>
                                </table>
                            </td>
                            <td>
                                <table class="table" id="listaeventi">
                                    <tr style="background-color:#DDDDDD"><th>Basket</th></tr>
                                    <?php
                                        $Categoria = "BASKET";
                                        stampaEventi($Categoria);
                                    ?>
                                </table>
                            </td>
                            <td>
                                <table class="table" id="listaeventi">
                                    <tr style="background-color:#DDDDDD"><th>Tennis</th></tr>
                                    <?php
                                        $Categoria = "TENNIS";
                                        stampaEventi($Categoria);
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="Submit">
                    <br>
                    <br>
                    <?php
                        try {
                            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                        } catch (PDOException $e){
                            echo "Connessione al database non riuscita";
                            exit();
                        } 
                        
                        if (isset($_POST['Submit'])) {
                            if (empty($_POST['ES']) || empty($_POST['Ruolo'])){
                                echo '<p id="errore">Devi scegliere un ruolo e un evento.</p><br>';
                                exit();
                            } else {
                                if ($User == 'Gestore'){
                                    echo '<p id="errore">In quanto Gestore, non puoi iscriverti agli eventi.</p>';
                                    exit();
                                }
                                
                                $scelta = $_POST['ES'];
                                $ruolo = $_POST['Ruolo'];
                                $sqlcheckisc = "SELECT * FROM ISCRIZIONE";
                                $checkisc = $connection -> query($sqlcheckisc);

                                while ($row = $checkisc -> fetch()){
                                    if (($User == $row["UserUtente"]) && ($scelta == $row["IdEvento"])){
                                        echo '<p id="errore">Sei già iscritto a questo evento.</p>';
                                        exit(); 
                                    }
                                }

                                if ($ruolo == 'Arbitro'){
                                    $sqlcheckarb = "SELECT * FROM EventiConArbitro";
                                    $checkarb = $connection -> query($sqlcheckarb);

                                    while ($row = $checkarb -> fetch()){
                                        if ($scelta == $row["Id"]){                                   
                                            echo '<p id="errore">C\'è già un arbitro per questo evento.</p>';
                                            exit();
                                        }
                                    }

                                    $sqles = "SELECT Id, UserUP FROM ES WHERE Id = '".$scelta."'";
                                    $es = $connection -> query($sqles);
                                    $row = $es -> fetch();
                                    $id = $row[0];
                                    $up = $row[1];
                                    $ymd = new DateTime();
                                    $data = $ymd -> format('Y-m-d');
                                    $sqliscrizione = "CALL IscriversiEvento('".$data."','".$User."','".$id."','".$up."','".$ruolo."')";
                                    $iscrizione = $connection -> query($sqliscrizione);
                                    echo '<p id="successo">Ti sei iscritto con successo all\'evento n°',$id,'!';
                                } else if ($ruolo == 'Giocatore') {
                                    $sqlcheckgio = "SELECT * FROM ES";
                                    $checkgio = $connection -> query($sqlcheckgio);

                                    while ($row = $checkgio -> fetch()){
                                        if (($scelta == $row["Id"]) && $row["Stato"] == 'CHIUSO'){
                                            echo '<p id="errore">Ci sono già abbastanza giocatori per questo evento.</p>';
                                            exit(); 
                                        }
                                    }
                                    
                                    $sqles = "SELECT Id, UserUP FROM ES WHERE Id = '".$scelta."'";
                                    $es = $connection -> query($sqles);
                                    $row = $es -> fetch();
                                    $id = $row[0];
                                    $up = $row[1];
                                    $ymd = new DateTime();
                                    $data = $ymd -> format('Y-m-d');
                                    $sqliscrizione = "CALL IscriversiEvento('".$data."','".$User."','".$id."','".$up."','".$ruolo."')";
                                    $iscrizione = $connection -> query($sqliscrizione);
                                    echo '<p id="successo">Ti sei iscritto con successo all\'evento n°',$id,'!';
                                }                            
                            }
                        }
                    ?>
                </form>
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