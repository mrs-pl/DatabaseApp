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
     <html class="gr__10_1_10_85">
     <link rel="stylesheet" type="text/css" href="ProLungDatabaseStyles.css">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" type="text/css" href="ProLungDatabaseStyles.css">
            <title>
                ProLungdx Clinical Database
            </title>
            <!-- JavaScript to load jquery -->
            <script src="./Home_files/jquery.min.js.download"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
            <!-- JavaScript to load fonts from Typekit -->
            <script src="https://use.typekit.net/gyp1njd.js"></script>
            <script>try{Typekit.load({ async: true });}catch(e){}</script>
        </head>
		<body data-gr-c-s-loaded="true">
			
            <div id="header" width="100%">
                <div id="nav" style="float:left; margin: 25px;" width="50%">
                    <a class="button" href="PatientList.php">Patients</a>
					<a class="button" href="AttributeList.php">Attributes</a>  
					<a class="button" href="AlgorithmList.php">Algorithms</a>  
					<a class="button" href="ResultsSearch.php"> Results </a>   
                </div>
				
				<div id="logo" align="center" style="display: inline-block; width: 460px; height: 70px; margin-top: 10px; margin-left: 12%;">
                <a href="home.php"><img src="Images/ProLungDatabaseLogo.png" height="70" width="460"></a>
				</div>
			
                <div id="loginNeeded" align="right" style="float:right; margin: 25px;" width="50%">
                    <a class="button" href="login.php">Login</a>
                </div>
                <div id="loggedIn" align="right" width="50%" style="display: none; float: right; margin: 25px;">
                    <a class="button" id="admin" href="Admin.php">Admin</a>
                    <a class="button" href="user_profile.php">$username</a>
                    <a class="button" href="logout.php">Log out</a>
                </div>
            </div>
            </div>
        
        
        <br><br><br><br><br>
        <div id="contentBox" style="margin:0px auto; width:100%" align="center">
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
    
