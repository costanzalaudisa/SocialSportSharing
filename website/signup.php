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
                    <li><a href="login.php">Login</a></li>
                    <li class="active"><a href="signup.php">Signup</a></li>
                </ul>
            </div> <!-- /.navbar-collapse -->
        </div> <!-- /.container-fluid -->
    </nav>

    <?php
        try {
            $connection = new PDO("mysql:host=localhost;dbname=UNIBOSSS", "root", "");                    
        } catch (PDOException $e){
            echo "Connessione al database non riuscita";
            exit();
        }
    ?>

    <div class="container" id="signup">
        <div class="row">
            <div class="col-lg-12">
                <h1>Signup</h1>
                <br>
                <form action="signup.php" method="post">
                    <table id="formtab">
                        <tr>
                            <td>Username:</td>
                            <td><input type="text" name="Username" placeholder="Username"><br></td">
                        </tr>
                        <tr>
                            <td>Password:</td>  
                            <td><input type="password" name="Password" placeholder="Password"></td>
                        </tr>
                        <tr>
                            <td>Ripeti password:</td>
                            <td><input type="password" name="RepeatPass" placeholder="Ripeti password"></td>
                        </tr>
                        <tr>
                            <td>Nome:</td>
                            <td><input type="text" name="Nome" placeholder="Nome"></td>
                        </tr>
                        <tr>
                            <td>Cognome:</td>
                            <td><input type="text" name="Cognome" placeholder="Cognome"></td>
                        </tr>
                        <tr>
                            <td>Corso di studi:</td>
                            <td>
                                <select name="NomeCdS">
                                    <?php
                                        $sqlcds = "SELECT * FROM CDS";
                                        $cds = $connection -> query($sqlcds);

                                        while ($row = $cds -> fetch()){
                                            echo '<option value="';
                                            foreach ($row as $key => $value){
                                                echo $value,'">',$value,'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </td> 
                        </tr>
                        <tr>
                            <td>Matricola:</td>
                            <td><input type="text" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" name="Matricola" placeholder="xxxxxxxxxx"></td>
                        </tr>
                        <tr>
                            <td>Anno di nascita:</td>
                            <td><input type="number" min="1900" max="1999" name="AnnoNascita" placeholder="19xx"></td>
                        </tr>
                        <tr>
                            <td>Luogo di nascita:</td>
                            <td><input type="text" name="LuogoNascita" placeholder="Luogo di nascita"></td>
                        </tr>
                        <tr>
                            <td>Telefono:</td>
                            <td><input type="text" maxlength="14" oninput="this.value=this.value.replace(/[^0-9]/g,'');" name="Telefono" placeholder="0039 xxx xxxxxx"></td>   
                        </tr>
                    </table>
                    <br>
                    <br>
                    <input type="submit" name="Submit">
                </form>
                <br>
                <?php
                    if (isset($_POST['Submit'])) {
                        if (empty(trim($_POST['Username'])) || empty(trim($_POST['Password'])) || empty(trim($_POST['RepeatPass'])) || empty(trim($_POST['Nome'])) || empty(trim($_POST['Cognome'])) || empty($_POST['Matricola']) || empty($_POST['AnnoNascita']) || empty(trim($_POST['LuogoNascita'])) || empty($_POST['Telefono']) || empty($_POST['NomeCdS'])){
                            echo '<p id="errore">Alcuni campi sono vuoti.</p>';
                            exit();
                        } else {
                            $username = trim($_POST['Username']);
                            $password = md5(trim($_POST['Password']));
                            $repeatpass = md5(trim($_POST['RepeatPass']));
                            $nome = trim($_POST['Nome']);
                            $cognome = trim($_POST['Cognome']);
                            $matricola = $_POST['Matricola'];
                            $annonascita = $_POST['AnnoNascita'];
                            $luogonascita = trim($_POST['LuogoNascita']);
                            $telefono = $_POST['Telefono'];
                            $nomecds = $_POST['NomeCdS'];
                            $sqlcheck = "SELECT Username, Matricola FROM UTENTE";
                            $check = $connection -> query($sqlcheck);

                            while ($row = $check -> fetch()){
                                if ($username == $row["Username"]){
                                    echo '<p id="errore">L\'username che hai scelto esiste già.</p>';
                                    exit();
                                } else if ($matricola == $row["Matricola"]) {
                                    echo '<p id="errore">La tua matricola è già presente nel sistema.</p>';
                                    exit();
                                }
                            }
                            
                            if (!($password == $repeatpass)){
                                echo '<p id="errore">La password non coincide.</p>';
                                exit();
                            } else {
                                $sqlregister = "CALL NuovoUtente('".$username."','". $password."','". $nome."','". $cognome."','". $matricola."','". $annonascita."','". $luogonascita."','". $telefono."','utentegenerico.png','". $nomecds."')";
                                $register = $connection -> query($sqlregister);
                                echo '<p id="successo">Ti sei registrato con successo! Ora puoi accedere.</p>';
                            }
                        }
                    }
                ?>
                <br>
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