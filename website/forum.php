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
        $User = $_SESSION["Username"];
        $CorsoStudio = $_SESSION["CorsoStudio"];
        $Categoria = $_SESSION["CategoriaScelta"];

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
                    <li><a href="registrazioneES.php">Eventi</a></li>
                    <li><a href="impianti.php">Impianti</a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown" style="background-color: #e7e7e7">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <form action ="forum.php" method = "post">
                                <li><input type = "submit" class="btn btn-link" name="Calcio" value="Calcio"></li>
                                <li><input type = "submit" class="btn btn-link" name="Basket" value="Basket"></li>
                                <li><input type = "submit" class="btn btn-link" name="Tennis" value="Tennis"></li> 
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
                <!--STAMPA IL CORSO DI STUDIO E LA CATEGORIA CHE HA SCELTO DI VEDERE-->
                <?php    
                    echo "<center><h1>".$CorsoStudio." - ".$Categoria." </h1></center>";
                ?>

                <!--CODICE PER STAMPARE I DIVERSI COMMENTI-->
                <?php
                    try {
                        $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                    } catch (PDOException $e){
                        echo "Connessione al database non riuscita";
                        exit();
                    } 

                    try {
                        $sqlpost = 'SELECT Data, Testo, Foto, UserUtente FROM POST WHERE NomeCds = "'.$CorsoStudio.'" AND CategoriaSport = "'.$Categoria.'"'; 
                        $post = $connection -> prepare($sqlpost);
                        $post -> execute();
                    } catch (Exception $e){
                        echo "Errore: ".$e -> getMessage();
                        exit(); 
                    }

                    $row_count = $post -> rowCount();

                    if ($row_count <= 0) {                               
                        echo  "<center><h3>Nessun Commento</h3><center><br>";
                    } else {
                        $i = 0;
                        while ($row = $post -> fetch()){
                            $UtenteCommento = $row["UserUtente"];  
                            $Data = $row["Data"];
                            $Commento = $row["Testo"];
                            $CaricamentoFoto = $row["Foto"];

                            try {
		                        $sqlfoto = 'SELECT Foto FROM UTENTE WHERE Username = "'.$UtenteCommento.'"';
		                        $foto = $connection -> query($sqlfoto);
		                    } catch (Exception $e){
		                        echo "Errore: ".$e -> getMessage();
		                        exit(); 
		                    }
                          
                            while ($row = $foto -> fetch()){
                                $FotoUtente = $row["Foto"];
                            }             
                                   
                            $FormatoStampa = $i % 2;
                            if ($FormatoStampa == 0){
                                //se i%2 = 0 è pari stampa il primo formato 
                                echo '<br><div class="container">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <section class="comment-list">                                        
                                                        <article class="row">
                                                            <div class="col-lg-1 col-lg-1 hidden-xs">
                                                                <figure class="thumbnail">
                                                                    <img class="img-responsive" src="images/'.$FotoUtente.'">
                                                                    <figcaption class="text-center">'.$UtenteCommento.'</figcaption>
                                                                </figure>
                                                            </div>                                                        
                                                            <div class="col-lg-11 col-lg-11">
                                                                <div class="panel panel-default arrow left">
                                                                    <div class="panel-body" style="background: none">                
                                                                        <header class="text-left">
                                                                            <time class="comment-date" datetime="16-12-2014 01:05"><i class="fa fa-clock-o"></i>'.$Data.'</time>
                                                                        </header>';
                                FormatoStampa($CaricamentoFoto, $Commento, $i);                           
                                echo '                              </div>
                                                                </div>
                                                            </div>
                                                        </article>  
                                                    </section>
                                                </div>
                                            </div>
                                        </div><br>';
                            } else {
                                //se i % 2 = 1 è dispari stampa il secondo formato     
                                echo '<div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <section class="comment-list">                                        
                                                    <article class="row">
                                                        <div class="col-lg-11 col-lg-11">
                                                            <div class="panel panel-default arrow right">
                                                                <div class="panel-body">
                                                                    <header class="text-right">                                
                                                                        <time class="comment-date" datetime="16-12-2014 01:05"><i class="fa fa-clock-o"></i>'.$Data.'</time>
                                                                    </header>';
                                FormatoStampa($CaricamentoFoto, $Commento, $i);
                                echo                           '</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1 col-lg-1 hidden-xs">
                                                            <figure class="thumbnail">
                                                                <img class="img-responsive" src="images/'.$FotoUtente.'">
                                                                <figcaption class="text-center">'.$UtenteCommento.'</figcaption>
                                                            </figure>
                                                        </div>
                                                    </article>
                                                </section>
                                            </div>
                                        </div>
                                    </div>';
                            }
                            $i++;
                        }
                    }

                    $connection = NULL;
                ?>

                <!--Funzioni per il formato di stampa-->
                <?php
                    function FormatoStampa($FotoCaricare, $Commento, $indice){
                        if ($FotoCaricare != "NULL"){
                        echo '<div class="comment-post" style="height:100px">
                                <div id="Postato'.$indice.'" class="carousel slide text-center" data-ride="carousel" data-interval="false" style="height:100px">     
                                        <ol class="carousel-indicators">
                                            <li data-target="#Postato'.$indice.'" data-slide-to="0" class="active"></li>
                                            <li data-target="#Postato'.$indice.'" data-slide-to="1"></li>                                   
                                        </ol>

                                        <div class="carousel-inner">
                                            <div class="item active">
                                                <div text-center">
                                                    <p>'.$Commento.'</p>            
                                                </div>
                                            </div>                                     
                                            <div class="item">
                                                <div text-center"> 
                                                    <img class="d-block img-fluid" src="images/'.$FotoCaricare.'" style="height:100px">
                                                </div>                            
                                            </div>
                                        </div>
                                 
                                        <a class="left carousel-control" href="#Postato'.$indice.'" data-slide="prev" style="background:none">
                                            <span class="glyphicon glyphicon-chevron-left" style="color:#C0C0C0"></span>
                                        </a>
                                        <a class="right carousel-control" href="#Postato'.$indice.'" data-slide="next" style="background:none">
                                            <span class="glyphicon glyphicon-chevron-right" style="color:#C0C0C0"></span>
                                        </a>                                        
                                    </div>         
                                </div>';
                        } else {
                        echo '<div class="comment-post">
                                    <p>'.$Commento.'</p>
                                </div>';
                        }           
                    }
                ?>

                <!--FRAME-->
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-lg-12">
                            <!--Codice per il model del caricamento immagine -->
                            <div class="container"> 
                                <!-- Modal -->
                                <div class="modal fade" id="inserimentoImmagine" role="dialog">
                                    <div class="modal-dialog modal-sm">                          
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">INSERISCI IMMAGINE</h4>
                                            </div>
                                        <div class="modal-body">  
                                            <form action="#inserimentoImmagine"" method="post" enctype="multipart/form-data">
                                                <input type="file" name="upload">      
                                                <input type="submit" name="up" value="Carica">
                                            </form>  
                                        </div>                                                     
                                    
                                        <!--CODICE PER CARICARE UN'IMMAGINE-->
                                        <?php
                                            //Cartella in cui verrà inserita l'immagine
                                            $cartella_upload = "images/"; 
                                            //Tipi di immagini consentite
                                            $tipi_consentiti = array("gif","png","jpeg","jpg");  
                                            if (isset($_FILES["upload"]) && isset($_POST["up"])){  
                                                $separaNomeEstenzione = explode('.', $_FILES["upload"]["name"]);
                                                $estenzioneImmagine = end($separaNomeEstenzione);
                                                // verifichiamo che l'utente abbia selezionato un file  
                                                if (trim($_FILES["upload"]["name"]) == ''){  
                                                    echo 'Non hai selezionato alcun file!';  
                                                } // verifichiamo che il file è stato caricato  
                                                else if (!is_uploaded_file($_FILES["upload"]["tmp_name"]) || $_FILES["upload"]["error"] > 0){  
                                                    echo 'Si sono verificati problemi nella procedura di upload!';  
                                                } // verifichiamo che il tipo è fra quelli consentiti  
                                                else if (!in_array($estenzioneImmagine,$tipi_consentiti)){  
                                                    echo 'Il file che si desidera uplodare non è fra i tipi consentiti!';  
                                                } // verifichiamo che la cartella di destinazione settata esista  
                                                else if (!is_dir($cartella_upload)){  
                                                    echo 'La cartella in cui si desidera salvare il file non esiste!';  
                                                } // verifichiamo il successo della procedura di upload nella cartella settata  
                                                else if (!move_uploaded_file($_FILES["upload"]["tmp_name"], $cartella_upload.$_FILES["upload"]["name"])){  
                                                    echo 'Ops qualcosa è andato storto nella procedura di upload!';  
                                                } // altrimenti significa che è andato tutto ok  
                                                else {
                                                    $_SESSION["FileDaCaricare"] = $_FILES["upload"]["name"];
                                                } 
                                            } 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-body">  
                                <form accept-charset="UTF-8" action="forum.php" method="POST">
                                    <h4>COMMENTA</h4>
                                    <textarea class="form-control counted" name="message" placeholder="Scrivi un commento" id="comment"></textarea>
                                        <br>
                                        <!--CODICE PER AGGIUNGERE UN NUOVO POST-->
                                        <?php
                                            try {
                                                $connection = new mysqli("localhost", "root", "", "UNIBOSSS");      
                                            } catch (Exception $e){
                                                echo "Connessione al database non riuscita";
                                                exit();
                                            }

                                            if (isset($_POST['InviaCommento'])) {
                                                $dataInserire = date("Y-m-d");
                                                $commentoInserire = $_POST["message"];

                                                $FileDaCaricare = $_SESSION["FileDaCaricare"];                                                
                                                if ($FileDaCaricare == ""){
                                                    $FileDaCaricare = "NULL";
                                                }                              
                                          
                                                try {
                                                    $sqlnewpost = 'CALL NuovoPost("'.$dataInserire.'", "'.$commentoInserire.'", "'.$FileDaCaricare.'", "'.$User.'", "'.$CorsoStudio.'", "'.$Categoria.'")';
                                                    $newpost = $connection -> prepare($sqlnewpost);
                                                    $newpost -> execute(); 
                                                } catch (Exception $e){
                                                    echo "Errore: ".$e -> getMessage();
                                                    exit(); 
                                                }
                                                //CANCELLA LA SESSIONE DOPO CHE E' STATO CARICATO
                                                unset($_SESSION['FileDaCaricare']);
                                                header('Location:forum.php');
                                            }

                                            $connection -> close();
                                        ?>

                                        <input type="submit" class ="btn pull-right" value="INVIA" name="InviaCommento" style="margin-left:5px">
                                        <button type="button" class="btn pull-right" data-toggle="modal" data-target="#inserimentoImmagine" title="INSERISCI IMMAGINE">
                                            <span class="glyphicon glyphicon-picture" style="margin-right: 5px; font-size: 16px"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
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