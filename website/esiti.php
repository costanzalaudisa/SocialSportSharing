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
        $ymd = new DateTime();
        $oggi = $ymd -> format('Y-m-d');

        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
            $sqldata = "CALL ControlloData();";
            $data = $connection -> query($sqldata);                                           
        } catch (PDOException $e){
            echo "Connessione al database non riuscita";
            exit();
        }

        ob_start()
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
							 <form action ="esiti.php" method = "post">
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

	<?php	
		#funzione per stampare esito delle partite
		function stampa($Modalità,$Categoria) {
			try {
				$connection = mysqli_connect('localhost','root','','UNIBOSSS');		 
			} catch(Exception $e){
				echo "Connessione al database non riuscita";
				exit();
			}
			
			try {
				$sqlesito = 'SELECT * FROM VisualizzaEsito'.$Modalità.' WHERE Categoria = "'.$Categoria.'"';
				$resesito = mysqli_query($connection,$sqlesito);  
				$esito = mysqli_num_rows($resesito);
			} catch (Exception $e){
                echo "Errore: ", mysqli_error($connection);
                exit(); 
            }
	
			if ($esito == 0) {
				echo "Nessun esito inserito";
			} else {
				echo '<tr style="background-color: #DDDDDD">
						<th></th>
						<th>Data</th>
						<th>Squadra 1</th>
						<th>Punti Sq.1</th>
						<th>Squadra 2</th>
						<th>Punti Sq.2</th>
					</tr>';
				$prec = 0;
				for ($i = 0; $i < $esito; $i++){						
					$row = mysqli_fetch_row($resesito);
					$id = $row[0];
					$data = $row[1];
					$impiant = $row[2];
					$Punti = $row[3];
					$Squadra = $row[4];

					if ($id != $prec){
						echo "<tr>";
						echo '<td><input type="radio" name="Dettagli" value="'.$id.'"></td>';
						echo "<td>",$data, "</td>";
						echo "<td>", $Squadra, "</td>";
						echo "<td>", $Punti, "</td>";
						$prec = $id;
						$_SESSION["Esito"] = $id; 
					} else {
						echo "<td>", $Squadra, "</td>";
						echo "<td>", $Punti, "</td>";
						echo "</tr>";
					}
				}    
			}
			mysqli_close($connection);
		}
	?>

	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<form action="esiti.php" method="post">
					<h1>Esiti partite di calcio</h1>
					<table class="table" id="tab">
						<?php
						    $Categoria = "Calcio";
						    $Modalità = "Gruppo";
				   			stampa($Modalità, $Categoria);
						?>
					</table>

					<h1>Esiti partite di basket</h1>	
					<table class="table" id="tab">		
						<?php
						    $Categoria = "Basket";
				   			$Modalità = "Gruppo";
				   			stampa($Modalità, $Categoria);
						?>
					</table>

					<h1>Esiti partite di tennis</h1>
					<table class="table" id="tab">	
						<?php
						    $Categoria = "Tennis";
						    $Modalità = "Singolo";
				   			stampa($Modalità, $Categoria);
						?>
					</table>
					</br>
					<input id="Dettagli" type="submit" name="Conferma" value="Visualizza Dettagli">
				</form>
				<?php
					try {
						$connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
					} catch (PDOException $e){
						echo "Connessione al database non riuscita";
						exit();
					}

					if (isset($_POST['Conferma'])){
						if (empty($_POST["Dettagli"])){
							echo '<p id="errore">Devi selezionare un esito.</p>';
							exit();
						} else {
							$selezione = $_POST['Dettagli'];
							$_SESSION['Esito'] = $selezione;
							header('Location:dettaglioEsito.php');
						}
					}
					ob_end_flush();
					$connection = NULL;
				?>
				<br>
				<br>
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