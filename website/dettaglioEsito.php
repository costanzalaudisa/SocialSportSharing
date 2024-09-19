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
        $Id = $_SESSION["Esito"];

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
					<li class="active"><a href="esiti.php">Esiti</a></li>
					<li><a href="statistiche.php">Statistiche</a></li>
					 <li><a href="cercaUtente.php">Utenti</a></li>
					<li class="dropdown">
						<a href="#">Forum <span class="caret"></span></a>
						<ul class="dropdown-menu">
							 <form action ="esiti.php" method="post">
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
                                } else if(isset($_POST['Tennis'])){
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
				<form action="dettaglioEsito.php" method="post">
					<h1>DETTAGLI ESITO</h1>
					<table class="table" id="tab">
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
							echo '<tr>';
							echo '<td><h3>Categoria: ', $Categoria, '</h3></td>';
							echo '<td><h3>Data: ', $data, '</h3></td>';
							echo '<td><h3>Impianto: ', $impianto,'</h3></td>';
							echo '</tr>';
						} 
					?>
					<?php
						try {
							$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
						} catch (PDOException $e){
							echo "Connessione al database non riuscita";
							exit();
						}
					
						try {
							$sqlarbitro = 'SELECT * FROM ARBITRAGGIO';
							$arbitro = $connection -> query($sqlarbitro);
						} catch (PDOException $e){
   			                echo "Errore: ".$e -> getMessage();
               			    exit(); 
               			}

						while ($row = $arbitro -> fetch()) {
							$userArbitro = $row['UserArbitro'];
							$idArbitro = $row['IdEvento'];
							
							if ($idArbitro == $Id){
								$userArbitro = $userArbitro;
								break;
							} else {
								$userArbitro = "Nessuno";
							}
						}

						if ($Categoria == 'Basket' || $Categoria == 'Calcio'){ 
							try {
								$sqlgruppo = 'SELECT * FROM GRUPPO WHERE IdEventoGruppo = "'.$Id.'"';
								$gruppo = $connection -> query($sqlgruppo);
							} catch (PDOException $e){
           		                echo "Errore: ".$e -> getMessage();
                   		        exit(); 
                       		}

							while ($row = $gruppo -> fetch()) {
								$Squadra = $row['NomeSquadra'];
								$Punti = $row['NumeroPunti'];
						
								try {
									$sqlcomposizione = 'SELECT * FROM COMPOSIZIONE WHERE IdEvento ="'.$Id.'" AND NomeSquadra ="'.$Squadra.'"';
									$composizione = $connection -> query($sqlcomposizione);
								} catch (PDOException $e){
                               		echo "Errore: ".$e -> getMessage();
   		                            exit(); 
           		                }

   								echo '<tr><th><b>Squadra:</b><br>',$Squadra,'</th><th><b>Punti squadra:</b><br>',$Punti,'</th><th><b>Arbitro:</b><br>',$userArbitro,'</th></tr>';
								while ($row = $composizione -> fetch()){
									$Giocatore = $row['UserGiocatore'];
									$Punti = $row['Punti'];
									echo "<tr><td>",$Giocatore,"</td><td>",$Punti,"</td><td></td></tr>";
								}
							}
						} else { 
							try {
								$sqlsingolo = 'SELECT * FROM SINGOLO WHERE IdEventoSingolo = "'.$Id.'"';
								$singolo = $connection -> query($sqlsingolo);
							} catch (PDOException $e){
                   		        echo "Errore: ".$e -> getMessage();
                           		exit(); 
	                        }

	                        echo '<tr>
									<td><h3>Giocatori:</h3></td>
									<td><h3>Punti:</h3></td>
									<td><h3>Arbitro:</h3></td>
								</tr>';

							while ($row = $singolo -> fetch()) {
								$Giocatore = $row['UserGiocatore'];
								$Punti = $row['NumeroPunti'];
								$durata = $row['Durata'];
								echo '<tr><h3><td>',$Giocatore,'</td><td>',$Punti,'</td><td>',$userArbitro,'</td></h3>';
							}
							echo "<tr><td colspan='3'><h3>Durata partita: ",$durata,"</h3></td></tr>";
						}
					?>
					</table>
	  				<input type="submit" name="Indietro" value="Torna indietro">
	  				<br>
	  				<br>
	  			</form>
			</div>
		</div>
	</div>

	<?php
	 	try {
			$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
		} catch (PDOException $e){
			echo "Connessione al database non riuscita";
			exit();
		}

	 	if (isset($_POST['Indietro'])){
	  		header('Location:esiti.php');
	  	}
	  	
	  	ob_end_flush();
	?>

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