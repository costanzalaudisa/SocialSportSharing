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
                    <li class="active"><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
							  <form action ="statistiche.php" method = "post">
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
	
	<?php
		#funzione per stampare la media voti ricevuta dal giocatore migliore
      	function stampaMediaTop($Categoria){
       		try{
				$connection = mysqli_connect('localhost','root','','UNIBOSSS');                        			 
			}catch(Exception $e){
				echo "Connessione al database non riuscita";
				exit();
			}

			try {
				$sqltopgioc = mysqli_query($connection, 'CALL TopGiocatore("'.$Categoria.'")');
				$topgioc = mysqli_num_rows($sqltopgioc);
			} catch (Exception $e){
              	echo "Errore: ", mysqli_error($connection);
               	exit(); 
			}

			if ($topgioc == 0) {
			 	echo "Giocatori non valutati.";
			} else {
				for ($i = 0; $i < $topgioc; $i++){
					$row = mysqli_fetch_row($sqltopgioc);
					$username = $row[0];
					$media = $row[2];
					echo '<tr>', '<td>', $username, '</td>';
					echo '<td>', $media, '</td>','</tr>';
				}
			} 

			mysqli_close($connection);
		}

		#funzione per stampare il giocatore che ha partecipato al maggior numero di partite
		function stampaNumeroPartite($Categoria){
			try {
				$connection = mysqli_connect('localhost','root','','UNIBOSSS');                        			 
			} catch (Exception $e){
				echo "Connessione al database non riuscita";
				exit();
			}

			try {
				$sqltoppart = 'SELECT * FROM TopPartite'.$Categoria.'';
				$toppart = mysqli_query($connection, $sqltoppart);
			} catch (Exception $e){
               	echo "Errore: ", mysqli_error($connection);
               	exit(); 
			}

			$totale = mysqli_num_rows($toppart);
			if ($totale == 0) {
				echo "Non ci sono giocatori.";
			} else {
				for ($i = 0; $i < $totale; $i++) { 
					$row = mysqli_fetch_row($toppart);
					$username = $row[0];
					$partite = $row[1];
					echo '<tr>', '<td>', $username, '</td>';
					echo  '<td>', $partite, '</td>','</tr>';
				}
			}

			mysqli_close($connection);
		}

		#funzione per stampare la squadra che ha vinto il maggior numero di partite
		function vittorieSquadra($Categoria){
			try {
				$connection = mysqli_connect('localhost','root','','UNIBOSSS');                       			 
			} catch (Exception $e){
				echo "Connessione al database non riuscita";
				exit();
			}
			
			try {
				$sqltopsquadr = 'CALL topNumeriPartite("'.$Categoria.'")';
				$topsquadr = mysqli_query($connection, $sqltopsquadr);
			} catch (Exception $e){
               	echo "Errore: ", mysqli_error($connection);
               	exit(); 
			}

			$totale = mysqli_num_rows($topsquadr);
			if ($totale == 0) {
				echo "Nessuna squadra.";
			} else {
				for ($i = 0; $i < $totale; $i++) { 
					$row = mysqli_fetch_row($topsquadr);
					$squadra = $row[0];
					$vittorie = $row[1];
					echo '<tr>';
					echo '<td>', $squadra, '</td>';
					echo  '<td>', $vittorie, '</td>';
					echo '</tr>';
				}
			}

			mysqli_close($connection);
		}

		#funzione per stampare la squadra che ha partecipato al maggior numero di partite
		function partecipazioneES($Categoria){
			try {
				$connection = mysqli_connect('localhost','root','','UNIBOSSS');                        			 
			} catch (Exception $e){
				echo "Connessione al database non riuscita";
				exit();
			}

			try {
				$sqltoppartec = 'CALL TopPartecipazioniES("'.$Categoria.'")';
				$toppartec = mysqli_query($connection, $sqltoppartec);
			} catch (Exception $e){
               	echo "Errore: ", mysqli_error($connection);
               	exit(); 
			}

			$totale = mysqli_num_rows($toppartec);
			if ($totale == 0) {
				echo "Nessuna squadra.";
			} else {
				for ($i = 0; $i < $totale; $i++) { 
					$row = mysqli_fetch_row($toppartec);
					$squadra = $row[0];
					$partite = $row[1];
					echo '<tr>', '<td>', $squadra, '</td>';
					echo  '<td>', $partite, '</td>','</tr>';
				}
			}

			mysqli_close($connection);
		}
	?>
	
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
				<h1>Giocatore con la media migliore</h1>					
				<div class="container-fluid bg-3 text-center"> 
					<div class="row">
						<div class="col-sm-4">
							<h3>CALCIO</h3>						  
							<table class="table">
						  		<tbody> 
							  		<?php 
										$Categoria = "Calcio";
										stampaMediaTop($Categoria);
						        	?>
								</tbody> 
							</table>
						</div>

						<div class="col-sm-4">
							<h3>BASKET</h3>						  
							<table class="table">
								<tbody> 
									<?php 
										$Categoria = "Basket";
										stampaMediaTop($Categoria);	
									?>								
								</tbody> 
							</table>
						</div>
						
						<div class="col-sm-4"> 
							<h3>TENNIS</h3>						  
							  <table class="table">
								<tbody> 
									<?php 
										$Categoria = "Tennis";
										stampaMediaTop($Categoria);	
									?>
								</tbody>
							</table>
						</div>
						
					</div>
				</div>
            </div>
        </div>
    </div>

	<div class="container">
        <div class="row">
            <div class="col-lg-12">
				<h1>Utente che ha partecipato al maggior numero di partite</h1>
                <div class="container-fluid bg-3 text-center"> 
					<div class="row">
						<div class="col-sm-4">
							<h3>CALCIO</h3>						  
						    <table class="table">
							  	<tbody> 
									<?php 
										$Categoria = "Calcio";
										stampaNumeroPartite($Categoria);
									?>
								</tbody> 
							</table>
						</div>
						
						<div class="col-sm-4">
							<h3>BASKET</h3>						  
							<table class="table">
								<tbody> 
									<?php 
										$Categoria = "Basket";
										stampaNumeroPartite($Categoria);
									?>								
								</tbody> 
							</table>
						</div>
						
						<div class="col-sm-4"> 
							<h3>TENNIS</h3>						  
							<table class="table">
								<tbody> 
									<?php 
										$Categoria = "Tennis";
										stampaNumeroPartite($Categoria);	
									?>
								</tbody>
							</table>
						</div>	
					</div>
				</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
				<h1>Squadra che ha partecipato al maggior numero di partite</h1>
				<div class="container-fluid bg-3 text-center"> 
					<div class="row">
						<div class="col-lg-6">
							<h3>CALCIO</h3>						  
							<table class="table">
								<tbody>
									<?php 
										$Categoria = "Calcio";
										PartecipazioneES($Categoria);
								  	?>
							  	</tbody>
							</table>
						</div>
						
						<div class="col-lg-6">
							<h3>BASKET</h3>						  
							<table class="table">
								<tbody>
									<?php 
										$Categoria = "Basket";
										PartecipazioneES($Categoria);
								  	?>
							  	</tbody>
							</table>
						</div>			
					</div>
				</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
				<h1>Squadra che ha vinto più partite </h1>
				<div class="container-fluid bg-3 text-center"> 
					<div class="row">
						<div class="col-lg-6">
							<h3>CALCIO</h3>						  
							<table class="table">
								<tbody>
									<?php 
										$Categoria = "Calcio";
										vittorieSquadra($Categoria);
								  	?>
							  	</tbody>
							</table>
						</div>
						
						<div class="col-lg-6">
							<h3>BASKET</h3>						  
							<table class="table">
								<tbody>
									<?php 
										$Categoria = "Basket";
										vittorieSquadra($Categoria);
								  	?>
							  	</tbody>
							</table>
						</div>			
					</div>
				</div>
				<br><br>
            </div>
        </div>
    </div>

    <?php ob_end_flush(); ?>

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