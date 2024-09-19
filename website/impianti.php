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
                    <li class="active"><a href="impianti.php">Impianti</span></a></li>
                    <li><a href="esiti.php">Esiti</a></li>
                    <li><a href="statistiche.php">Statistiche</a></li>
                    <li><a href="cercaUtente.php">Utenti</a></li>
                    <li class="dropdown">
                        <a href="#">Forum <span class="caret"></span></a>
                        <ul class="dropdown-menu">
							<form action ="impianti.php" method = "post">
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

    <div class="container" id="impianti">
        <div class="row">
            <div class="col-lg-12">
            	<h1>IMPIANTI</h1>
                <br>
                <table class="table" id="tab">
                    <tr style="background-color: #DDDDDD">
                        <th>Impianto</th>
                        <th>Luogo</th>
                        <th>Telefono</th>
                        <th>Email</th>
                    </tr>
                    <?php
                        /* connect to the db */
                        try {
                            $connection = mysqli_connect('localhost','root','','UNIBOSSS');
                        } catch (Exception $e){
                            echo "Connessione al database non riuscita";
                            exit();
                        }

                		try {
                            $sqlimpianto = 'SELECT NomeImpianto, Via, Telefono, Email FROM IMPIANTO';
                        	$impianto = mysqli_query($connection, $sqlimpianto);       
                        } catch (Exception $e){
                            echo "Errore: ".$e -> getMessage();
                            exit(); 
                        }                                             
                      
                        if (mysqli_num_rows($impianto)){
                            while ($row = mysqli_fetch_row($impianto)){
                                echo '<tr>';
                                foreach ($row as $key => $value){
                                    echo '<td>',$value,'</td>';
                                }
                                echo '</tr>';
                            }
                        }
                        
                        $connection -> close();
                    ?>
                </table>
                <br>
                <!-- Add Google Maps -->
                <div id="map">
                    <iframe src="https://www.google.com/maps/d/embed?mid=1ZRFxvlFsTJyVXUrj0UzPiwN-ph4" width="700" height="450"></iframe>
                </div>
                <br><br><br><br>
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