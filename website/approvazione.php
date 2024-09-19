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
		$Categoria = "Calcio";

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
		
				<ul class="nav navbar-nav navbar-right">
					<li class="active"><a href="<?php echo $profilo ?>">Profilo</a></li>
					<li><a href="login.php">Logout</a></li>
				</ul>
			</div> <!-- /.navbar-collapse -->
		</div> <!-- /.container-fluid -->
	</nav>

	<div class="container"> 
		<center><H1>APPROVA O RIFIUTA UN'ISCRIZIONE<H1></center>
		<div class="table-responsive">  
			<form action ="approvazione.php" method = "post">                  
				<table class="table">
					<?php  
						$FormatoStampa = 1; #serve a non stampare i bottoni se non c'è nessuna scelta da CONFERMARE O RIFIUTARE

						#MOSTRARE LE ISCRIZIONI IN ATTESA
						#UP può approvare solo l'evento che ha creato
						try {
							$connection = new mysqli("localhost", "root", "", "UNIBOSSS");      
						} catch (Exception $e){
							echo "Connessione al database non riuscita";
							exit();
						}

						try {
							$sqliscrizioni = 'SELECT * FROM ISCRIZIONE WHERE (UPApprovazione = "'.$User.'") AND (UserUtente != "Gestore") AND (Stato = "IN ATTESA")';
							$iscrizioni = $connection -> query($sqliscrizioni);
						} catch (Exception $e){
							echo "Errore: ".$e -> getMessage();
							exit(); 
						}
				   
						$rowcount = mysqli_num_rows($iscrizioni);
						if ($iscrizioni -> num_rows <= 0) {
							echo"<center><h3>Non hai alcun'iscrizione da approvare o rifiutare<h3></center>"; 
							$FormatoStampa = 0;                                     
						} else {
							$i = 0; #indice che serve a differenziare i gruppi di radio
							
							while ($row = $iscrizioni -> fetch_assoc()) {
								$UtenteIscizione[$i] = $row["UserUtente"];
								$DataIscizione = $row["Data"];
								$StatoIscizione = $row["Stato"];
								$IdEventoIscrizione[$i] = $row["IdEvento"];
						
								try {
									$sqlcategoria = 'SELECT CategoriaSport FROM ES WHERE Id = "'.$IdEventoIscrizione[$i].'"';
									$risultatoCategoria = $connection -> query($sqlcategoria);
								} catch (Exception $e){
									echo "Errore: ".$e->getMessage();
									exit(); 
								}
								
								while ($rowCategoria = $risultatoCategoria -> fetch_assoc()) {
									$CategoriaSport = $rowCategoria["CategoriaSport"];
								}

								try {
									$sqlarbitro = 'SELECT * FROM ARBITRO WHERE ( UserUtente = "'.$UtenteIscizione[$i].'") AND (IdEvento = "'. $IdEventoIscrizione[$i].'")';
									$risultato = $connection -> query($sqlarbitro);
								} catch (Exception $e){
									echo "Errore: ".$e -> getMessage();
									exit(); 
								}

								if ($risultato -> num_rows <= 0) {
								   $Ruolo[$i] = "GIOCATORE";	

								} else {
								   $Ruolo[$i] = "ARBITRO";
								}

								echo '<tr>
									<td><label class="radio-inline"><input type = radio name = "Approvare'.$i.'" value = "APPROVA">APPROVA<label></td>
									<td><label class="radio-inline"><input type = radio name = "Approvare'.$i.'" value = "RIFIUTA">RIFIUTA<label></td>
									<td>'.$UtenteIscizione[$i].'</td>
									<td>'.$DataIscizione.'</td>
									<td>'.$StatoIscizione.'</td>
									<td>'.$IdEventoIscrizione[$i].'</td>
									<td>'.$CategoriaSport.'</td>
									<td>'.$Ruolo[$i].'</td>
								</tr>';
								$i++;
							}
						} 

						$connection -> close();                
					?>
				</table>
			</div>  

			<div class="btn-group btn-group-justified">
				<?php
					if ($FormatoStampa != 0){
					echo '<table> 
							<tr>
								<td>
								<input type="submit" class="btn btn-default" size=30 value="CONFERMA" name="SceltaApprovazione" >
								<input type="reset" class="btn btn-default" value="ANNULLA">
								</td>
							</tr> 
						</table>';
					}
				?>    
			</div>
		</form>
	</div>

	<?php 
		#MANDARE I RISULTATI DELL'APPROVAZIONE
		#manda 1 per APPROVARE e 0 per RIFIUTARE
		#Procedura Approvazione(IN UP VARCHAR(50), User VARCHAR(50), IN Evento INT, IN Approvato BOOLEAN)
		$indice = 0; #indice leggere tutte le possibilità dell'APPROVARE
		if (isset($_POST['SceltaApprovazione'])){

			while ($indice < $rowcount){  
				if (empty($_POST['Approvare'.$indice.''])) {
					echo "NESSUNA SCELTA FATTA<br>";
				} else {
					$Scelta = $_POST['Approvare'.$indice.''];
					if ($Scelta == "APPROVA"){ 
						$Decisione = 1;
					} else if ($Scelta == "RIFIUTA"){
						$Decisione = 0;
					}

					try {
						$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
					} catch (PDOException $e){
						echo "Connessione al database non riuscita";
						exit();
					} 
					
					if ($Ruolo[$indice] == "ARBITRO"){ 
						try {
							$sqlcheckarb = 'SELECT * FROM EventiConArbitro';
							$checkarb = $connection -> query($sqlcheckarb);
						} catch (Exception $e){
							echo "Errore: ".$e -> getMessage();
							exit(); 
						}

						while ($rowarb = $checkarb -> fetch()) {
							$Id = $rowarb["Id"];
							if($Id == $IdEventoIscrizione[$indice] ) {
								$Decisione = 0;
								break;
							}
						}

					} else if ($Ruolo[$indice] == "GIOCATORE"){
						try {
							$sqlcheckgioc = 'SELECT Id, Stato FROM ES';
							$checkgioc = $connection -> query($sqlcheckgioc);
						} catch (Exception $e){
							echo "Errore: ".$e -> getMessage();
							exit(); 
						}

						while ($rowgioc = $checkgioc -> fetch()) {
							$Id = $rowgioc["Id"];
							$Stato = $rowgioc["Stato"];
							if($Id == $IdEventoIscrizione[$indice] AND $Stato == "CHIUSO") {
								$Decisione = 0;
								break;
							}
						}
					}

					try {
						$sqlappr = 'CALL Approvazione("'.$User.'", "'.$UtenteIscizione[$indice].'", "'.$IdEventoIscrizione[$indice].'", "'.$Decisione.'")'; 
						$appr = $connection -> prepare($sqlappr);
						$appr -> execute();
					} catch (Exception $e){
						echo "Errore: ".$e -> getMessage();
						exit(); 
					}
					
					$connection = NULL;
				}

				$indice++;
			}  

			header('Location:approvazione.php');          
		}

		ob_end_flush(); 
	?>

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