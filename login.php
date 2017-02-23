<?php
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}

echo <<<_END
    <html>
     <link rel="stylesheet" type="text/css" href="style.css">
        <head>
            <title>
                ProLungdx Clinical Database
            </title>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
            <h1 align="center">
                <a href="home.php"><img src="ProLungdx.png"></a>
            </h1><br>
            <div id="header">
                <div id="nav" align="left" width="50%">
                    <a href="PatientList.php">Patients</a> <a href="AttributeList.php">Attributes</a>  <a href="AlgorithmList.php">Algorithms</a>  <a href="ResultsSearch.php"> Results </a>   
                </div>
                <div id="loginNeeded" align="right" width="50%">
                    <a href="login.php">Login</a></pre>
                </div>
                <div id="loggedIn" align="right" width="50%">
                    <a href="user_profile.php">$username</a>
                    <a href="logout.php">Log out</a>
                </div>
            </div>
            
        </head>
		<body>
            <div align="center">
			<form method='post' action="login.php">
				Username <input type='text' name='username'><br><br>
				Password <input type='password' name='password'><br><br>
				<input type="submit">
			</form>
            </div>
		</body>	
	</html>

_END;

//echo $_POST['password'];

if (isset($_POST['username']) && isset($_POST['password'])) {
    $un_temp = mysql_entities_fix_string($conn, $_POST['username']);
    $pw_temp = mysql_entities_fix_string($conn, $_POST['password']);
    
    $query = "SELECT * from Users where UserName='$un_temp'";
    $result = $conn->query($query);
    if(!$result) die("incorrect username");
    if ($result->num_rows == 0){
        $message = "Incorrect Username/Password Combo";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
    elseif($result->num_rows){
        $row = $result->fetch_array(MYSQLI_NUM);
        $correct_pw = $row[2];
        $userId = $row[0];
        $userName = $row[1];
		$isAdmin = $row[3];
		$canEdit = $row[4];
		$canAddAttribute = $row[5];
        
        if($pw_temp == $correct_pw){
            //session_start();
            
            $_SESSION["username"]=$userName;
            $_SESSION["password"]=$pw_temp;
            $_SESSION['userId']=$userId;
			$_SESSION['isAdmin'] = $isAdmin;
			$_SESSION['canEdit'] = $canEdit;
			$_SESSION['canAddAttribute'] = $canAddAttribute;
            
            
            
            echo "<script> window.location.assign('home.php'); </script>";
            
        }else{
            
            $message = "Incorrect Username/Password Combo";
            echo "<script type='text/javascript'>alert('$message');</script>";
            
            exit();
        }
    } else{
        exit();
    }
}else{
    exit();
}

if(isset($_SESSION['username'])){
    echo "<script>
            $('#loginNeeded').hide()
            $('#loggedIn').show()
        </script>";
} else {
    echo "<script>
            $('#loggedIn').hide()
            $('#loginNeeded').show()
        </script>";
    
}
$conn->close();

function mysql_entities_fix_string($conn, $string){
	return htmlentities(mysql_fix_string($conn, $string));
}

function mysql_fix_string($conn, $string){
	if(get_magic_quotes_gpc()) $string = stripslashes($string);
	return $conn->real_escape_string($string);
}

?>
    
