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
							<form action ="newEsitoGruppo.php" method = "post">
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
					} catch (Exception $e){
               	   	    echo "Errore: ".$e -> getMessage();
                   	   	exit(); 
               		}

					while ($row = $evento -> fetch()){
						$Categoria = $row['CategoriaSport'];
						$data = $row['Data'];
						$impianto = $row['NomeImpianto'];
						echo "<h2>Categoria: ", $Categoria, " | Data: ", $data, " | Impianto: ", $impianto,"</h2>";
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
				<form action="newEsitoGruppo.php" method="post">
					<table id="esito">
						<tr>
							<td>
								<h3> Inserisci il nome della prima squadra</h3>
								<input type="text" name="squadra1" placeholder="Nome Squadra">
								<h3>Inserisci i componenti della squadra</h3>
								<?php 
									try {
										$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
									} catch (PDOException $e){
										echo "Connessione al database non riuscita";
										exit();
									}

									$sqlsq1 = 'SELECT UserUtente FROM GIOCATORE WHERE IdEvento = "'.$Id.'" ';
									for ($i = 0; $i < 5 ; $i++) { 
										try {
											$squadra1 = $connection -> query($sqlsq1);  
										} catch (Exception $e){
            				          		echo "Errore: ".$e -> getMessage();
				                        	exit(); 
                				    	}

										echo '<select  name= "giocatore'.$i.'" style="width: 200 px">';
										while ($row = $squadra1 -> fetch()){
											echo '<option value="';
											foreach ($row as $key => $value){
												echo $value,'" >',$value,'</option>';
											}
										}
										echo "</select>";
										echo '<input type="number" min="0" name="punti'.$i.'"  placeholder="00" style="width: 100px">';
									}

									$connection = null;
								?>
							</td>

							<td>
								<h3>Inserisci il nome della seconda squadra</h3>
								<input type="text" name="squadra2" placeholder="Nome Squadra">
								<h3>Inserisci i componenti della squadra</h3>
								<?php 
									try {
										$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
									} catch (PDOException $e){
										echo "Connessione al database non riuscita";
										exit();
									}

									$sqlsq2 = 'SELECT UserUtente FROM GIOCATORE WHERE IdEvento = "'.$Id.'" ';
									for ($i = 5; $i < 10 ; $i++) { 
										try {
											$squadra2 = $connection -> query($sqlsq2);   
										} catch (PDOException $e){
        				            	    echo "Errore: ".$e -> getMessage();
        				                	exit(); 
        				            	}

										echo '<select  name= "giocatore'.$i.'" style="width: 200 px">';
										while ($row = $squadra2 -> fetch()){
											echo '<option value="';
											foreach ($row as $key => $value){
												echo $value,'" >',$value,'</option>';
											}
										}
										echo "</select>";
										echo '<input type="number" min="0" name="punti'.$i.'"  placeholder="00" style="width: 100px">';
									}

									$connection = null;
								?>
							</td>
						</tr>
		
						<tr>
							<td colspan="2">
								<h3>Inserisci l'arbitro:</h3>
								<select name="arbitro">
									<?php
										try {
											$connection = mysqli_connect('localhost','root','','UNIBOSSS');	
										} catch (Exception $e){
											echo "Connessione al database non riuscita";
											exit();
										}

										try {
											$sqlarbitri = 'SELECT UserUtente FROM ARBITRO WHERE IdEvento = "'.$Id.'" ';
											$arbitri = mysqli_query($connection,$sqlarbitri); 
											$numArbitri = mysqli_num_rows($arbitri);
										} catch (Exception $e){
        					                echo "Errore: ", mysqli_error($connection);
        					                exit(); 
        				    	        }

										if ($numArbitri == 0){
											echo '<option value="NULL">Nessun Arbitro</option>';
										} else { 
											for ($j = 0; $j < $numArbitri; $j++) { 
												$rowArbitro = mysqli_fetch_row($arbitri);
												$arbitro = $rowArbitro[0];
												echo '<option value="', $arbitro,'">',$arbitro,'</option>';
											}
										}
										mysqli_close($connection);
									?>
								</select>
							</td>
						</tr>
					</table>
					<br>
					<input type="submit" name="conferma">
					<br><br>
					<?php 
						try {
							$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");              
						} catch (PDOException $e){
							echo "Connessione al database non riuscita";
							exit();
						}

						if (isset($_POST['conferma'])){
							$giocatore = array();
							$puntiGiocatori = array();
							$checkDati = true;

							//controllo che tutti i giocatori e i punti siano inseriti e li inserisco in un array
							for ($i = 0; $i < 10; $i++){
								$giocatore[$i] = $_POST['giocatore'.$i];
								$puntiGiocatori[$i] = $_POST['punti'.$i];

								if (empty($puntiGiocatori[$i]) || empty($giocatore[$i])){
									$checkDati = false;
									break;
								}
							}

							if (empty(trim($_POST['squadra1'])) || empty(trim($_POST['squadra2'])) || empty($_POST['arbitro']) || !$checkDati){
								echo '<p id="errore">Non hai inserito tutti i dati.</p><br><br>';
								exit();
							} else {     
								$squadra1 = trim($_POST['squadra1']);
								$squadra2 = trim($_POST['squadra2']);
								$arbitro = $_POST['arbitro'];
								$checkGiocatore = true;
								$checkSquadra = true;

								//controllo che i giocatori siano inseriti una sola volta
								for ($i = 0; $i < 10; $i++){
									for ($j = 0; $j < 10; $j++){
										if ($giocatore[$i] == $giocatore[$j] && $i != $j) {
											$checkGiocatore = false;
											echo $giocatore[$i], " = ", $giocatore[$j];
											break;
										}
									}
								}

								//controllo che le squadre abbiano nomi differenti
								if ($squadra1 == $squadra2) {
									$checkSquadra = false;
								}

								//calcolo i punti totali delle due squadre.
								$sumPunti1 = 0;
								for ($i = 0; $i < 5  ; $i++) { 
									$sumPunti1 = $sumPunti1 + $puntiGiocatori[$i];
								}

								$sumPunti2 = 0;
								for ($i = 5; $i < 10 ; $i++) { 
									$sumPunti2 = $sumPunti2 + $puntiGiocatori[$i];
								}

								if (!$checkGiocatore || !$checkSquadra || !$checkData){
									echo '<p id="errore">Hai inserito più volte lo stesso giocatore o le nome delle squadre sono uguali.</p><br><br>';
									exit();
								} else {
									//controllo se la squadra esiste già, se non è presente l'aggiungo.
									try {
										$sqlsquadra = 'SELECT * FROM SQUADRA';
										$squadra = $connection -> query($sqlsquadra);
									} catch (PDOException $e){
										echo "Errore: ".$e -> getMessage();
										exit(); 
									}

									$squadra1Assente = true;
									$squadra2Assente = true;

									while ($row = $squadra -> fetch()) {
										if ($squadra1 == $row['NomeSquadra']){
											$squadra1Assente = false;
											break;
										}
									}	

									if ($squadra1Assente) {
										try {
											$sqlsquadra1 = 'CALL NuovaSquadra("'.$squadra1.'")';
											$nuovaSquadra1 = $connection -> prepare($sqlsquadra1);
											$nuovaSquadra1 -> execute();
										} catch (PDOException $e){
											echo "Errore: ".$e -> getMessage();
											exit(); 
										}	
									}

									while ($row = $squadra -> fetch()) {
										if ($squadra2 == $row['NomeSquadra']){
											$squadra2Assente = false;
											break;
										}
									}	

									if ($squadra2Assente) {
										try {
											$sqlsquadra2 = 'CALL NuovaSquadra("'.$squadra2.'")';
											$nuovaSquadra2 = $connection -> prepare($sqlsquadra2);
											$nuovaSquadra2 -> execute();
										} catch (PDOException $e){
											echo "Errore: ".$e -> getMessage();
											exit(); 
										}
									}	

									//inserisco l'esito dell'evento.
									try {
										$sqlesito = 'CALL NuovoEsitoGruppo("'.$Id.'","'.$dataCorrente.'","'.$User.'", "'.$sumPunti1.'","'.$squadra1.'","'.$sumPunti2.'","'.$squadra2.'","'.$arbitro.'")';
										$nuovoEsitoGruppo = $connection -> prepare($sqlesito);
										$nuovoEsitoGruppo -> execute();
									} catch (PDOException $e){
										echo "Errore: ".$e -> getMessage();
										exit();	
									}

									//compongo le squadre
									for ($i = 0; $i < 10; $i++){
										if ($i < 5){
											$squadra = $squadra1;
										} else {
											$squadra = $squadra2;
										}

										try {
											$sqlcomposizione = 'CALL ComposizioneSquadra("'.$squadra.'","'.$giocatore[$i].'","'.$Id.'","'.$puntiGiocatori[$i].'")';
											$composizione = $connection -> prepare($sqlcomposizione);
											$composizione -> execute();
										} catch (PDOException $e){
											echo "Errore: ".$e -> getMessage();
											exit();	
										}
									}

									header('Location:esiti.php');
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