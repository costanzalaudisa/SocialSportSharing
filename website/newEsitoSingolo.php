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
					<li><a href="impianti.php">Impianti</a></li>
					<li class="active"><a href="esiti.php">Esiti</a></li>
					<li><a href="statistiche.php">Statistiche</a></li>
					<li><a href="cercaUtente.php">Utenti</a></li>
					<li class="dropdown">
						<a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <form action ="newEsito.php" method = "post">
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
					<li><a href="profiloUP.php">Profilo</a></li>
					<li><a href="login.php">Logout</a></li>
				</ul>
			</div> <!-- /.navbar-collapse -->
		</div> <!-- /.container-fluid -->
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<h1>INSERISCI NUOVO ESITO</h1>
				<?php 
					try {
						$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
					} catch (PDOException $e){
						echo "Connessione al database non riuscita";
						exit();
					}

					try {
						$sqlevento = 'SELECT * FROM ES WHERE Id = "'.$Id.'"';
						$evento = $connection -> query($sqlevento);
					} catch (PDOException $e){
                       	echo "Errore: ".$e -> getMessage();
                       	exit(); 
                   	}

					while ($row = $evento -> fetch()){
						$Categoria = $row['CategoriaSport'];
						$data = $row['Data'];
						$impianto = $row['NomeImpianto'];
						echo "<h2>Categoria: ", $Categoria, " |  Data:   ", $data, " |    Impianto:   ", $impianto,"</h2>";
					} 

					$dataCorrente = date('20y-m-d');
					$dataES = strtotime($data);
					$dataCorrentestr = strtotime($dataCorrente);
					$checkData = true;

					if ($dataCorrentestr < $dataES){
						$checkData = false;
						echo '<p id="errore">L\'evento non si è ancora tenuto. Non puoi ancora inserire questo esito.</p>';
						exit();
					} 

					$connection = null;
				?>
				<form action="newEsitoSingolo.php" method="post">
					<table id="esito">
						<tr>
							<td>
								<h3>Inserisci il nome del primo giocatore</h3>
								<select name="Giocatore1">
									<?php
										try {
											$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
										} catch (PDOException $e){
											echo "Connessione al database non riuscita";
											exit();
										}

										try {
											$sqlgiocatore1 = 'SELECT UserUtente FROM GIOCATORE WHERE IdEvento = "'.$Id.'" ';
											$giocatore1 = $connection -> query($sqlgiocatore1);
										} catch (PDOException $e){
				                        	echo "Errore: ".$e -> getMessage();
				                        	exit(); 
				                    	}

										while ($row = $giocatore1 -> fetch()){
											echo '<option value="';
											foreach($row as $value){
												echo $value,'">',$value,'</option>';
											}
										}

										$connection = null;
									?>
								</select>
								<h3>Inserisci i set vinti dal primo giocatore</h3>
								<input type="number" name="Punti1" placeholder="00" min="0" max="7" style="width: 100px">
							</td>
							<td>
								<h3>Inserisci il nome del secondo giocatore</h3>
								<select name="Giocatore2">
									<?php
										try {
											$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
										} catch (PDOException $e){
											echo "Connessione al database non riuscita";
											exit();
										}

										try {
											$sqlgiocatore2 = 'SELECT UserUtente FROM GIOCATORE WHERE IdEvento = "'.$Id.'" ';
											$giocatore2 = $connection -> query($sqlgiocatore2);
										} catch (PDOException $e){
				                        	echo "Errore: ".$e -> getMessage();
				                        	exit(); 
				                    	}

										while ($row = $giocatore2 -> fetch()){
											echo '<option value="';
											foreach($row as $key => $value){
												echo $value,'">',$value,'</option>';
											}
										}

										$connection = null;
									?>
								</select>
								<h3>Inserisci i set vinti dal secondo giocatore</h3>
								<input type="number" name="Punti2" placeholder="00" min="0" max="7" style="width: 100px">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<h3>Arbitro</h3>
								<select name="Arbitro">
									<?php
										try {
											$connection = mysqli_connect('localhost','root','','UNIBOSSS');
										} catch (Exception $e){
											echo "Connessione al database non riuscita";
											exit();
										}

										try {
											$sqlarbitro = 'SELECT UserUtente FROM ARBITRO WHERE IdEvento = "'.$Id.'" ';
											$arbitri = mysqli_query($connection,$sqlarbitro); 
											$numArbitri = mysqli_num_rows($arbitri);
										} catch (PDOException $e){
					                        echo "Errore: ".$e -> getMessage();
					                       	exit(); 
					                    }

										if ($numArbitri == 0){
											echo '<option value="NULL">Nessun Arbitro</option>';
										} else { 
											for ($j = 0; $j < $numArbitri; $j++) { 
												$rowArbitro = mysqli_fetch_row($arbitri);
												$arbitro = $rowArbitro[0];
												echo $arbitro;
												echo '<option value="', $arbitro,'">',$arbitro,'</option>';
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<h3>Inserisci la durata dell'evento:</h3>
								<!-- ALERT! Qui ho inserito del CSS, ma è da mettere nel file comune modificato -->
								<input id="durata" type="number" name="hh" min="0" max="24" placeholder="00"> HH
								<input id="durata" type="number" name="mm" min="0" max="60" placeholder="00"> MM
								<input id="durata" type="number" name="ss" min="0" max="60" placeholder="00"> SS
							</td>
						</tr>
					</table>
					<br>
					<input type="Submit" name="Conferma">
					<br><br>
				</form>
				<?php 
					try {
						$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");              
					} catch (PDOException $e){
						echo "Connessione al database non riuscita";
						exit();
					} 

					if (isset($_POST['Conferma'])) {
						if (empty($_POST['Giocatore1']) || empty($_POST['Punti1']) || empty($_POST['Giocatore2']) || empty($_POST['Punti2']) ||	empty($_POST['Arbitro']) || empty($_POST['hh']) || empty($_POST['mm']) || empty($_POST['ss']) || !$checkData){
							echo '<p id="errore">Alcuni campi sono vuoti.</p>';
							exit();
						} else {
							$giocatore1 = $_POST['Giocatore1'];
							$punti1 = $_POST['Punti1'];
							$giocatore2 = $_POST['Giocatore2'];
							$punti2 = $_POST['Punti2'];
							$arbitro = $_POST['Arbitro'];
							$hh = $_POST['hh'];
							$mm = $_POST['mm'];
							$ss = $_POST['ss'];
							$durata =''.$hh.':'.$mm.':'.$ss.'';
							$checkGiocatore = true;
							$sumPunti = $punti1 + $punti2;

							#controllo se è stato inserito due volte lo stesso giocatore
							if ($giocatore1 == $giocatore2) {
								$checkGiocatore = false;
								echo '<p id="errore">Hai inserito due volte lo stesso giocatore.</p>';
								exit();
							}

							#controllo se la somma dei punti è maggiore di 7, numero massimo di set possibili
							if ($sumPunti > 7){
								echo '<p id="errore"> Hai inserito una somma di punti maggiore di 7.</p>';
								exit();
							}

							if ($checkGiocatore){
								try {
									$sqlesito = 'CALL NuovoEsitoSingolo("'.$Id.'","'.$dataCorrente.'","'.$User.'","'.$punti1.'","'.$giocatore1.'","'.$punti2.'","'.$giocatore2.'","'.$durata.'","'.$arbitro.'")';
									$nuovoEsitoSingolo = $connection -> prepare($sqlesito);
									$nuovoEsitoSingolo -> execute();
								} catch (Exception $e) {
									echo "Errore: ", $e.getMessage();
									exit();
								}
								
								header('Location:esiti.php');
							} else {
								echo '<p id="errore"> Impossibile inserire l\' esito, i dati immessi non sono corretti </p>';
								exit();
							}
						}
					}
					ob_end_flush();        
				?>
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