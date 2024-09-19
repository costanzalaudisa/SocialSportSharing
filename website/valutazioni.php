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
		$Id = $_SESSION["IdES"];

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
					<li><a href="impianti.php">Impianti</span></a></li>
					<li><a href="esiti.php">Esiti</a></li>
					<li><a href="statistiche.php">Statistiche</a></li>
					<li><a href="cercaUtente.php">Utenti</a></li>
					<li class="dropdown">
						<a href="#">Forum <span class="caret"></span></a>
						<ul class="dropdown-menu">
							 <form action ="cercaUtente.php" method = "post">
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
                    
                    <li class="active"><a href="<?php echo $profilo ?>">Profilo</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <div class="container" id="valutazioni">
    	<div class="row">
            <div class="col-lg-12">
    			<form action="valutazioni.php" method="post">
    				<h1>INSERISCI VALUTAZIONE</h1>
                    <h3>Quale giocatore desideri valutare?</h3>
    				<select name="giocatore">
    					<?php
        					try {
        						$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
        					} catch (PDOException $e){
        						echo "Connessione al database non riuscita";
        						exit();
        					}
                            try {
        					    $sqlgiocatori = 'SELECT UserUtente FROM GIOCATORE WHERE (IdEvento = "'.$Id.'") AND (UserUtente != "'.$User.'")';
            					$giocatori = $connection -> query($sqlgiocatori);
                            } catch (PDOException $e){
                                echo "Errore: ".$e -> getMessage();
                                exit(); 
                            }

        					while ($row = $giocatori -> fetch()){
    							echo '<option value="';
    							foreach ($row as $key => $value){
    								echo $value,'">',$value,'</option>';
    							}
    						}

        					$connection = null;
    					?>
    				</select>
                    <br><br>
    				<h3>Vota da 0 a 10 il giocatore: </h3>
                    <label class="radio-inline"><input type="radio" name="voto" value="00">0 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="1">1 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="2">2 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="3">3 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="4">4 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="5">5 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="6">6 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="7">7 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="8">8 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="9">9 </label>
    				<label class="radio-inline"><input type="radio" name="voto" value="10">10 </label>
                    <br><br>
    				<h3> Commenta la partita del giocatore: </h3>
    				<textarea name="commento" rows="7" cols="50" placeholder="Inserisci qui il tuo commento..."></textarea>
                    <br><br>
        			<input type="submit" name="conferma" value="Valuta">
                    <br><br>
                    <?php
                        try {
                            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
                        } catch (PDOException $e){
                            echo "Connessione al database non riuscita";
                            exit();
                        } 

                        if (isset($_POST['conferma'])){
                            if (empty($_POST['voto']) || empty($_POST['giocatore'])) {
                                echo '<p id="errore">Devi scegliere un giocatore e dargli un voto.</p>';
                                exit();
                            } else {
                                $voto = $_POST['voto'];
                                $giocatore = $_POST['giocatore'];
                                $commento = $_POST['commento'];
                                $data = date('20y-m-d');

                                try {
                                    $sqldavalutare = 'SELECT UserGiocatore,IdESGiocatore,UserValutante FROM VALUTAZIONE';
                                    $davalutare = $connection -> query($sqldavalutare);
                                    
                                    while ($row = $davalutare -> fetch()){ 
                                        if (($User == $row['UserValutante']) && ($Id == $row['IdESGiocatore']) && ($giocatore == $row['UserGiocatore'])){
                                            $checkGiocatore = false;
                                            break;
                                        } else {
                                            $checkGiocatore = true;
                                        }                                    
                                    }
                                } catch (PDOException $e){
                                    echo "Errore: ".$e -> getMessage();
                                    exit(); 
                                }

                                if ($checkGiocatore == false){
                                    echo "<p id=errore> Hai già valutato questo giocatore per questo evento.</p>";
                                    exit();
                                } else {
                                    try {
                                        $sqlvalutaz = 'CALL NuovaValutazione("'.$data.'", "'.$voto.'", "'.$commento.'","'.$giocatore.'", "'.$Id.'","'.$User.'","'.$Id.'")';
                                        $valutaz = $connection -> prepare($sqlvalutaz);
                                        $valutaz -> execute();
                                    } catch (PDOException $e){
                                        echo "Errore: ".$e -> getMessage();
                                        exit(); 
                                    }

                                    header('Location:'.$profilo.'');
                                }       
                            } 
                        }
                        
                        ob_end_flush();
                    ?>
                </form>
    		</div>
    	</div> 
    </div>
    	
	<!--  jQuery -->
	<script src="js/jquery.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>

	<!-- Scrolling Nav JavaScript -->
	<script src="js/jquery.easing.min.js"></script>
	<script src="js/scrolling-nav.js"></script>
	<script src="js/hover-dropdown.js"></script>
</body>
</html>