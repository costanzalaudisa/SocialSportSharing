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
        $UserCercato = $_SESSION["UserCercato"];

        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
            $sqldata = "CALL ControlloData();";
            $data = $connection -> query($sqldata);                                           
        } catch(PDOException $e){
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
                    <li><a href="registrazioneES.php">Eventi</a></li>
                    <li><a href="impianti.php">Impianti</span></a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li class="active"><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <form action ="profiloGenerico.php" method="post">
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
                        while (($row = $up ->fetch()) && ($presente == 0)){
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

    <div class="container" id="signup">
        <div class="row">
            <div class="col-lg-12">	
                <div class="container">
                    <div class="row">
                        <div class="col-lg-2">
                            <!--CODICE PER STAMPARE I DATI DELL'UTENTE CERCATO-->
                            <?php
                                try {
                                    $connection = new mysqli("localhost", "root", "", "UNIBOSSS");      
                                } catch (Exception $e){
                                    echo "Connessione al database non riuscita";
                                    exit();
                                }

                                try {
                                    $sqlutente = "SELECT * FROM UTENTE";
                                    $utente = $connection -> query($sqlutente);
                                } catch (Exception $e){
                                    echo "Errore: ".$e -> getMessage();
                                    exit(); 
                                }
                               
                                while ($row = $utente -> fetch_assoc()){
                                    if ($UserCercato == $row["Username"]){
                                        $NomeCercato = $row["Nome"];
                                        $CognomeCercato = $row["Cognome"];
                                        $CdS = $row["NomeCdS"];
                                        break;
                                    }               
                                }
                            ?>         

                            <!--CODICE PER INSERIRE L'IMMAGINE NEL PROFILO-->                            
                            <?php
                                try {
                                    $con = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                                } catch (PDOException $e){
                                    echo "Connessione al database non riuscita";
                                    exit();
                                }

                                try {
                                    $sqlfoto = 'SELECT Foto FROM UTENTE WHERE Username = "'.$UserCercato.'"';
                                    $foto = $con -> query($sqlfoto);
                                } catch (Exception $e){
                                    echo "Errore: ".$e -> getMessage();
                                    exit(); 
                                }
                               
                                while ($row = $foto -> fetch()) {
                                    $FotoDaCaricare = $row["Foto"];
                                }   

                                $percorsoImmagine = 'images/'.$FotoDaCaricare;
                                $con = NULL;
                            ?>

                            <div class="thumbnail">
                                <img src=<?php echo $percorsoImmagine; ?> alt="Image">  
                            </div>    

                            <div class="panel panel-info">
                                <div class="panel-heading">DATI PERSONALI</div>
                                    <div class="panel-body">
                                        <dl>
                                            <dt>USER</dt><?php echo $UserCercato ?>                                        
                                            <dt>CDS</dt><?php echo $CdS ?>                                       
                                        </dl>     
                                    </div>
                                </div> 
                            </div><!--CHIUSURA DI LG 2-->						

                            <div class="col-lg-10">                                                                          
                                <h1><?php echo $NomeCercato."  ".$CognomeCercato ?></h1>
                                <br>
                                <h2>EVENTI CHE HA ORGANIZZATO</h2>  
                                <table class="table" id="tab">
                                    <?php
                                        #EVENTI ORGANIZZATI
                                        try {
                                            $sqlorganizzati = 'SELECT Data, Stato, CategoriaSport, NomeImpianto FROM ES WHERE (UserUP = "'.$UserCercato.'")';
                                            $organizzati = $connection -> query($sqlorganizzati);
                                        } catch (Exception $e){
                                            echo "Errore: ".$e -> getMessage();
                                            exit(); 
                                        }                                   

                                        if ($organizzati -> num_rows <= 0) {
                                            echo"<h3>Non ha organizzato alcun evento<h3>";                                      
                                        } else {
                                            while ($row = $organizzati -> fetch_assoc()) {                                      
                                                $DataOrganizzati = $row["Data"]; 
                                                $Stato = $row["Stato"];
                                                $CategoriaSport = $row["CategoriaSport"];  
                                                $ImpiantoOrganizzati = $row["NomeImpianto"];
                                                echo '<tr>';
                                                echo '<td>'.$DataOrganizzati.'</td>';
                                                echo '<td>'.$Stato.'</td>';
                                                echo '<td>'.$CategoriaSport.'</td>';
                                                echo '<td>'.$ImpiantoOrganizzati.'</td>';
                                                echo '</tr>';
                                            }
                                        }                                    
                                    ?>	
                                </table>
                                <br>
                                <h2>EVENTI A CUI HA PARTECIPATO</h2>  
                                <table class="table" id="tab">
                                    <?php 
                                        #EVENTI PARTECIPATI
                                        try {
                                            $sqlpartecip = 'SELECT Data, Categoria,Impianto  FROM ListaESUtente WHERE ((Utente = "'.$UserCercato.'") AND (Data < CURDATE()))';
                                            $partecip = $connection -> query($sqlpartecip);
                                        } catch (Exception $e){
                                            echo "Errore: ".$e -> getMessage();
                                            exit(); 
                                        }                                    

                                        if ($partecip -> num_rows <= 0) {
                                            echo"<h3>Non ha partecipato ad alcun evento<h3>";                                      
                                        } else {
                                            echo '<tr style="background-color: #DDDDDD">';                              
                                            echo '<th>DATA</th>';
                                            echo '<th>IMPIANTO</th>';
                                            echo '<th>CATEGORIA</th>';                                 
                                            echo '</tr>';

                                            while ($row = $partecip -> fetch_assoc()) {                                      
                                                $ES = $row["Data"]; 
                                                $Impianto = $row["Impianto"];
                                                $Categoria = $row["Categoria"];                                        
                                                echo '<tr>';
                                                echo '<td>'.$ES.'</td>';
                                                echo '<td>'.$Impianto.'</td>';
                                                echo '<td>'.$Categoria.'</td>';
                                                echo '</tr>';
                                            }
                                        }
                                    ?>
                                </table>
                                <br>
                                <h2>VALUTAZIONI</h2>    
                                <table class="table" id="tab">
                                    <?php 
                                        #Valutazioni
                                        try {
                                            $sqlvalut = 'SELECT Data, Voto, Commento, UserValutante  FROM Valutazione WHERE (UserGiocatore = "'.$UserCercato.'")';
                                            $valut = $connection -> query($sqlvalut);
                                        } catch (Exception $e){
                                            echo "Errore: ".$e -> getMessage();
                                            exit(); 
                                        }   
                                    
                                        if ($valut -> num_rows <= 0) {
                                            echo"<h3>Non ha valutazioni<h3>";                                     
                                        } else {
                                            echo '<tr style="background-color: #DDDDDD">';                              
                                            echo '<th>DATA VALUTAZIONE</th>';
                                            echo '<th>VALUTANTE</th>';
                                            echo '<th>VOTO</th>';
                                            echo '<th>COMMENTO</th>';                               
                                            echo '</tr>';

                                            while ($row = $valut -> fetch_assoc()) {  
                                                $DataValutazione = $row["Data"];                                   
                                                $Valutante = $row["UserValutante"]; 
                                                $Voto = $row["Voto"];
                                                $Commento = $row["Commento"];                                      
                                                echo '<tr>';
                                                echo '<td>'.$DataValutazione.'</td>';
                                                echo '<td>'.$Valutante.'</td>';
                                                echo '<td>'.$Voto.'</td>';
                                                echo '<td>'.$Commento.'</td>';
                                                echo '</tr>';
                                            }
                                        }
                                        $connection -> close();
                                    ?>
                                </table>
                                <br>
                                <br>
                            </div><!--CHIUSURA DI LG 10-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php  ob_end_flush(); ?>
    
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