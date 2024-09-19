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
        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");
            $sqldata = "CALL ControlloData();";
            $data = $connection -> query($sqldata);                                           
        } catch (PDOException $e){
            echo "Connessione al database non riuscita";
            exit();
        }
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
                <a class="navbar-brand" href="Home.php"><img id="logo" src="images/logo.png" alt="Image" width="200px" height="50px"></a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">       
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Signup</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Login</h1>
				<br>
				<form action="login.php" method="post">
					<table id="formtab">
						<tr>
							<td>Username:</td>
							<td><input type="text" name="Username" placeholder="Username"><br></td>
						</tr>
						<tr>
							<td>Password:</td>  
							<td><input type="password" name="Password" placeholder="Password"></td>
						</tr>
						</table>			
					<br>
					<br>
					<input class="btn btn-default page-scroll" type="submit" value="Login" name ="submit">
                    <br><br>
                    <?php
                        try {
                            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", ""); 
                        } catch(PDOException $e){
                            echo "Connessione al database non riuscita";
                            exit();
                        }                   
                    
                        if (isset($_POST['submit'])) {                          
                            if ((empty(trim($_POST["Username"]))) || (empty(trim($_POST["Password"])))){                              
                                echo '<p id="errore">Non hai inserito Username o Password</p>';
                                exit();
                            } else {
                                $Username = trim($_POST["Username"]);
                                $Password = md5(trim($_POST["Password"]));
                                $esiste = 0;
                                $sqlutente = "SELECT * FROM UTENTE";
                                $utente = $connection -> query($sqlutente);
                                while ($row = $utente -> fetch()){
                                    if (($Username == $row["Username"]) && ($Password == $row["Password"])){                             
                                        $_SESSION["Username"]=$row["Username"]; #Memorizzo il username dentro una variabile sessione
                                        $_SESSION["Nome"]=$row["Nome"];         #Memorizzo il nome dentro una variabile sessione
                                        $_SESSION["Cognome"]=$row["Cognome"];   #Memorizzo il cognome dentro una variabile sessione
                                        $_SESSION["CorsoStudio"]=$row["NomeCdS"];   #Memorizzo il cognome dentro una variabile sessione
                                        $esiste = 1;
                                    }
                                }

                                if ($esiste == 1){
                                    header('Location:HomeMembro.php');
                                } else {
                                    echo '<p id="errore">L\'username o la password sono sbagliati. Riprova.</p>';
                                    exit();
                                }
                            }
                        }
                    ?>				
                </form>
            </div>
        </div>
    </div>

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