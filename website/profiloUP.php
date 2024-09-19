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
    
     <!-- PER TABELLA -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
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
        $CorsoStudio = $_SESSION["CorsoStudio"];

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

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="categorie.php">Categorie</a></li>
                    <li><a href="registrazioneES.php">Eventi</a></li>
                    <li><a href="impianti.php">Impianti</span></a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <form action ="profiloUP.php" method="post">
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
                    <li class="active"><a href="profiloUP.php">Profilo</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <div class="container" id="signup">
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
                                    $FotoDaCaricare = $row["Foto"];
                                }   

                                $percorsoImmagine = 'images/'.$FotoDaCaricare;
                            ?>                            

                            <div class="thumbnail">
                                <img src=<?php echo $percorsoImmagine; ?> alt="Image">
                                <br>
                                <button type="button" data-toggle="modal" data-target="#inserimentoImmagine">CAMBIA IMMAGINE</button>    
                            </div>  

                            <!--CODICE PER IL MODEL/FINESTRA DEL CARICAMENTO DELLA PAGINA-->
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
                                                <form action="#inserimentoImmagine" method="post" enctype="multipart/form-data">                
                                                    <input type="file" name="upload"> 
                                                    <input type="submit" name="up" value="Carica">
                                                </form> 
                                            </div>

                                            <!--CODICE PER IL CARICAMENTO DELL'IMMAGINE-->
                                            <?php
                                                //Cartella in cui verrà inserita l'immagine
                                                $cartella_upload ="images/"; 
                                                
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
                                                        echo 'Si sono verificati dei problemi nella procedura di upload!';  
                                                    } // verifichiamo che il tipo è fra quelli consentiti  
                                                    else if (!in_array($estenzioneImmagine,$tipi_consentiti)){  
                                                        echo 'Il file che si desidera caricare non è di un tipo consentito!';  
                                                    } // verifichiamo che la cartella di destinazione settata esista  
                                                    else if (!is_dir($cartella_upload)){  
                                                        echo 'La cartella in cui si desidera caricare il file non esiste!';  
                                                    } // verifichiamo il successo della procedura di upload nella cartella settata  
                                                   else if (!move_uploaded_file($_FILES["upload"]["tmp_name"], $cartella_upload.$_FILES["upload"]["name"])){  
                                                        echo 'Qualcosa è andato storto nella procedura di upload!';  
                                                    } // altrimenti è andato tutto ok  
                                                    else {            
                                                        try {
                                                            $sqlModificaImmagine = 'UPDATE UTENTE SET Foto = "'.$_FILES["upload"]["name"].'"    WHERE Username ="'.$User.'"';
                                                            $ModificaImmagine = $connection -> prepare($sqlModificaImmagine);
                                                            $ModificaImmagine -> execute();                                
                                                        } catch (Exception $e){
                                                            echo "Errore: ".$e -> getMessage();
                                                            exit(); 
                                                        }
                                                        $connection = NULL;
                                                        header("Location:profiloUP.php");                                                       
                                                    }   
                                                } 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--CODICE PER STAMPARE I DATI PERSONALI DELL'UTENTE NEL PROFILO-->
                            <?php
                                try {
                                    $connection = new mysqli("localhost", "root", "", "UNIBOSSS");      
                                } catch (Exception $e){
                                    echo "Connessione al database non riuscita";
                                    exit();
                                }

                                try { 
                                    $sqldati = "SELECT * FROM UTENTE";
                                    $dati = $connection -> query($sqldati);
                                } catch (Exception $e){
                                    echo "Errore: ".$e -> getMessage();
                                    exit(); 
                                }

                                while ($row = $dati -> fetch_assoc()){
                                    if ($User == $row["Username"]){
                                        $Matricola = $row["Matricola"];
                                        $Data = $row["AnnoNascita"];
                                        $Luogo = $row["LuogoNascita"];
                                        $Telefono = $row["Telefono"];

                                        $calcio = $row["PartiteCalcio"];
                                        $basket = $row["PartiteBasket"];
                                        $tennis = $row["PartiteTennis"];
                                        break;
                                    }               
                                }
                            ?>

                            <div class="panel panel-info">
                                <div class="panel-heading">DATI PERSONALI</div>
                                    <div class="panel-body">
                                        <dl>
                                            <dt>USERNAME</dt>
                                            <?php echo $User ?>
                                            <dt>CORSO</dt>
                                            <?php echo $CorsoStudio ?>
                                            <dt>MATRICOLA</dt>
                                            <?php echo  $Matricola ?>
                                            <dt>NASCITA</dt>
                                            <?php echo  $Data." a ".$Luogo ?>
                                            <dt>TELEFONO</dt>
                                            <?php echo  $Telefono ?>
                                        </dl>     
                                    </div>
                                </div> 

                                <!--ALTRE ATTIVITA' CHE PUO' FARE UN UTENTE UP-->
                                <div class="btn-group-vertical">                                
                                    <a href="newEvento.php" class="btn btn-default page-scroll">NUOVO EVENTO</a>
                                    <a href="approvazione.php" class="btn btn-default page-scroll">APPROVA</a>                                  
                                    <a href="newEsito.php" class="btn btn-default page-scroll">INSERISCI ESITO</a>
                                    <a href="scegliValutazione.php" class="btn btn-default page-scroll"> VALUTAZIONI</a>                                     
                                    <input type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#CancellazioneUtente" value="CANCELLA PROFILO">      
                                </div>  

                                <br><br>
                       
                                <!--CODICE CANCELLAZIONE UTENTE CON LA CREAZIONE DI UNA FINESTRA DI CONFERMA-->
                                <div class="container"> 
                                    <!-- Modal -->
                                    <div class="modal fade" id="CancellazioneUtente">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                    
                                                <div class="modal-body">  
                                                    <h4 class="modal-title">Sei sicuro di voler cancellare il tuo account?</h4>
                                                    <br>
                                                    <form action="#CancellazioneUtente" method="post" enctype="multipart/form-data">
                                                        <input type="submit" class="btn btn-default" value="SI" name="CancellaProfilo">
                                                        <input type="submit" class="btn btn-default" value="NO" name="Profilo"><!--Ricarica-->
                                                    </form>
                                                </div>

                                                <?php
                                                    if (isset($_POST["Profilo"])){                                               
                                                        header('Location:profiloUP.php');
                                                    }

                                                    if (isset($_POST["CancellaProfilo"])){
                                                        try {
                                                            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
                                                        } catch (PDOException $e){
                                                            echo "Connessione al database non riuscita";
                                                            exit();
                                                        }

                                                        try {
                                                            $sqlCancellazione = 'CALL RimozioneUtente("'.$User.'")';
                                                            $Cancellazione = $connection -> prepare($sqlCancellazione);
                                                            $Cancellazione -> execute();                                                    
                                                        } catch (Exception $e){
                                                            echo "Errore: ".$e -> getMessage();
                                                            exit(); 
                                                        }

                                                        $connection = NULL;
                                                        header('Location:login.php');
                                                    }
                                                ?>                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            </div><!--CHIUSURA DEL LG 2-->
    						
                            <div class="col-lg-10">
                                <h1><?php echo $Nome."  ".$Cognome." - UP" ?></h1>

                                <?php
                                    #Voto medio come calciatore
                                    try {
                                        $sqlvcalcio = 'SELECT Voto FROM ValutazioniMediaCalcio WHERE Username = "'.$User.'"';
                                        $vcalcio = $connection -> query($sqlvcalcio);       
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    }                                

                                    if ($vcalcio -> num_rows <= 0) {
                                        $VotoCalcio = "Nessun voto";
                                    } else {
                                        while ($row = $vcalcio -> fetch_assoc()) {
                                            $VotoCalcio = $row["Voto"];
                                        }
                                    }

                                    #Media goal/partita
                                    try {
                                        $sqlpcalcio = 'SELECT Punti FROM MediaPuntiCalcio WHERE Username = "'.$User.'"';
                                        $pcalcio = $connection -> query($sqlpcalcio);                                        
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    } 

                                    if ($pcalcio -> num_rows <= 0) {
                                        $MediaCalcio = "Nessun goal";
                                    } else {
                                        while ($row = $pcalcio -> fetch_assoc()) {
                                            $MediaCalcio = $row["Punti"];
                                        }
                                    }

                                    #Voto medio come cestista
                                    try {
                                        $sqlvbasket = 'SELECT Voto FROM ValutazioniMediaBasket WHERE Username = "'.$User.'"';
                                        $vbasket = $connection -> query($sqlvbasket);
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    } 
                           
                                    if ($vbasket -> num_rows <= 0) {
                                        $VotoBasket = "Nessun voto";
                                    } else {
                                        while($row = $vbasket -> fetch_assoc()) {
                                            $VotoBasket = $row["Voto"];
                                        }
                                    }

                                    #Media punti/partita
                                    try {
                                        $sqlpbasket = 'SELECT Punti FROM MediaPuntiBasket WHERE Username = "'.$User.'"';
                                        $pbasket = $connection->query($sqlpbasket);      
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    } 

                                    if ($pbasket -> num_rows <= 0) {
                                        $MediaBasket = "Nessun punto";
                                    } else {
                                        while($row = $pbasket -> fetch_assoc()) {
                                            $MediaBasket = $row["Punti"];
                                        }
                                    }

                                    #Voto medio come tennista
                                    try {
                                        $sqlvtennis = 'SELECT Voto FROM ValutazioniMediaTennis WHERE Username = "'.$User.'"';
                                        $vtennis = $connection -> query($sqlvtennis);  
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    } 

                                    if ($vtennis -> num_rows <= 0) {
                                        $VotoTennis = "Nessun voto";
                                    } else {
                                        while($row = $vtennis -> fetch_assoc()) {
                                            $VotoTennis = $row["Voto"];
                                        }
                                    }

                                    #numero partite tennis vinte:
                                    try {                                    
                                        $sqlptennis = 'SELECT Vittorie FROM VittorieTennis WHERE UserGiocatore = "'.$User.'"';
                                        $ptennis = $connection -> query($sqlptennis);     
                                    } catch (Exception $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    } 

                                    if ($ptennis -> num_rows <= 0) {
                                        $VinteTennis = "Nessuna vittoria";
                                    } else {
                                        while($row = $ptennis -> fetch_assoc()) {
                                            $VinteTennis = $row["Vittorie"];
                                        }
                                    }                           

                                    $connection -> close();
                                ?>  
                            <br>
                            <div class="container-fluid bg-3 text-center"> 
                                <div class="row">
                                    <!--VALORI DEL CALCIO-->
                                    <div class="col-sm-4">
                                        <h2>CALCIO</h2>                       
                                        <p><?php echo $calcio ?></p>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>VOTO MEDIO</td>
                                                    <td><?php echo $VotoCalcio ?></td>
                                                </tr>
                                                <tr>
                                                    <td>MEDIA GOAL</td>
                                                    <td><?php echo $MediaCalcio ?></td>                                 
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!--VALORI DEL BASKET-->
                                    <div class="col-sm-4">
                                        <h2>BASKET</h2>                       
                                        <p><?php echo $basket ?></p>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>VOTO MEDIO</td>
                                                    <td><?php echo $VotoBasket ?></td>
                                                </tr>
                                                <tr>
                                                    <td>MEDIA PUNTI</td>
                                                    <td><?php echo $MediaBasket ?></td>                                 
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!--VALORI DEL TENNIS-->
                                    <div class="col-sm-4"> 
                                        <h2>TENNIS</h2>                       
                                        <p><?php echo $tennis ?></p>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>VOTO MEDIO</td>
                                                    <td><?php echo $VotoTennis ?></td>
                                                </tr>
                                                <tr>
                                                    <td>PARTITE VINTE</td>
                                                    <td><?php echo $VinteTennis ?></td>                                 
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid bg-3 text-center"> 
                                <div class="row">
                                    <h2>EVENTI A CUI SEI ISCRITTO</h2>  
                                    <table class="table" id="profilo">
                                        <?php 
                                            try {
                                                $connection = new mysqli("localhost", "root", "", "UNIBOSSS");      
                                            } catch (Exception $e){
                                                echo "Connessione al database non riuscita";
                                                exit();
                                            }

                                            #Eventi a cui è iscritto
                                            try {
                                                $sqliscrizioni = 'SELECT IdEvento, CategoriaSport, NomeImpianto, Data, Stato FROM IscrizioniUtente WHERE ((UserUtente = "'.$User.'") AND (Data > CURDATE()))';
                                                $iscrizioni = $connection -> query($sqliscrizioni);                              
                                            } catch (Exception $e){
                                                echo "Errore: ".$e -> getMessage();
                                                exit(); 
                                            } 

                                            if ($iscrizioni -> num_rows <= 0) {
                                                echo"<h3>Non sei iscritto ad alcun evento<h3>";                                      
                                            } else {
                                                echo '<tr style="background-color: #DDDDDD">';                              
                                                echo '<th>EVENTO N°</th>';
                                                echo '<th>SPORT</th>';
                                                echo '<th>IMPIANTO</th>';
                                                echo '<th>DATA</th>';
                                                echo '<th>RUOLO</th>';
                                                echo '<th>STATO</th>';                               
                                                echo '</tr>';

                                                while ($row = $iscrizioni -> fetch_assoc()) {                                      
                                                    $ES = $row["IdEvento"]; 
                                                    $Sport = $row["CategoriaSport"];
                                                    $Impianto = $row["NomeImpianto"];
                                                    $Data = $row["Data"];
                                                    $Stato = $row["Stato"];

                                                    try {
                                                        $sqlarbitro = 'SELECT * FROM ARBITRO WHERE ( UserUtente = "'.$User.'") AND (IdEvento = "'.$ES.'")';
                                                        $risultato = $connection -> query($sqlarbitro);
                                                    } catch (Exception $e){
                                                        echo "Errore: ".$e -> getMessage();
                                                        exit(); 
                                                    }

                                                    if ($risultato -> num_rows <= 0) {
                                                       $Ruolo = "GIOCATORE";    
                                                    } else {
                                                       $Ruolo = "ARBITRO";
                                                    }
                                        
                                                    echo '<tr>';
                                                    echo '<td>'.$ES.'</td>';
                                                    echo '<td>'.$Sport.'</td>';
                                                    echo '<td>'.$Impianto.'</td>';
                                                    echo '<td>'.$Data.'</td>';
                                                    echo '<td>'.$Ruolo.'</td>';
                                                    echo '<td>'.$Stato.'</td>';
                                                    echo '</tr>';
                                                }
                                            }                                        
                                        ?>
                                    </table>
                                </div>
                            </div>

                            <div class="container-fluid bg-3 text-center"> 
                                <div class="row">
                                    <h2>EVENTI CHE HAI ORGANIZZATO</h2>  
                                    <table class="table" id="profilo">
                                        <?php
                                            #EVENTI ORGANIZZATI
                                            try {
                                                $sqlorganizzati = 'SELECT Data, Stato, CategoriaSport, NomeImpianto FROM ES WHERE (UserUP = "'.$User.'")';
                                                $organizzati = $connection -> query($sqlorganizzati);
                                            } catch (Exception $e){
                                                echo "Errore: ".$e -> getMessage();
                                                exit(); 
                                            }                                   

                                            if ($organizzati -> num_rows <= 0) {
                                                echo"<h3>Non ha organizzato alcun evento<h3>";                                      
                                            } else {
                                                echo '<tr style="background-color: #DDDDDD">';                              
                                                echo '<th>DATA</th>';
                                                echo '<th>STATO</th>';
                                                echo '<th>SPORT</th>';
                                                echo '<th>IMPIANTO</th>';                               
                                                echo '</tr>';

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
                                </div>
                            </div>

                            <div class="container-fluid bg-3 text-center"> 
                                <div class="row">

                                    <h2>EVENTI A CUI HAI PARTECIPATO</h2>  
                                    <table class="table" id="profilo">
                                        <?php 
                                            #Eventi che ha partecipato
                                            try { 
                                                $sqlpartecipaz = 'SELECT Data, Categoria, Impianto FROM ListaESUtente WHERE ((Utente = "'.$User.'") AND (Data < CURDATE()))';
                                                $partecipaz = $connection -> query($sqlpartecipaz);                              
                                            } catch (Exception $e){
                                                echo "Errore: ".$e -> getMessage();
                                                exit(); 
                                            } 

                                            if ($partecipaz -> num_rows <= 0) {
                                                echo"<h3>Non hai partecipato ad alcun evento<h3>";                                      
                                            } else {
                                                echo '<tr style="background-color: #DDDDDD">';                              
                                                echo '<th>DATA</th>';
                                                echo '<th>IMPIANTO</th>';
                                                echo '<th>CATEGORIA</th>';                                 
                                                echo '</tr>';

                                                while ($row = $partecipaz -> fetch_assoc()) {                                      
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
                                </div>
                            </div>

                            <div class="container-fluid bg-3 text-center"> 
                                <div class="row">
                                    <h2>VALUTAZIONI</h2>    
                                    <table class="table" id="profilo">
                                        <?php 
                                            #Valutazioni
                                            try { 
                                                $sqlvalutaz = 'SELECT Data, Voto, Commento, UserValutante  FROM Valutazione WHERE (UserGiocatore = "'.$User.'")';
                                                $valutaz = $connection->query($sqlvalutaz);                             
                                            } catch (Exception $e){
                                                echo "Errore: ".$e -> getMessage();
                                                exit(); 
                                            }                                         

                                            if ($valutaz -> num_rows <= 0) {
                                                echo"<h3>Non hai valutazioni<h3>";                                     
                                            } else {
                                                echo '<tr style="background-color: #DDDDDD">';                              
                                                echo '<th>DATA VALUTAZIONE</th>';
                                                echo '<th>VALUTANTE</th>';
                                                echo '<th>VOTO</th>';
                                                echo '<th>COMMENTO</th>';                               
                                                echo '</tr>';

                                                while ($row = $valutaz -> fetch_assoc()) {                                      
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
                                </div>
                            </div>
                        </div><!--CHIUSURA DEL LG 10-->
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