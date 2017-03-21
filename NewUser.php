<?php
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
	$isAdmin = $_SESSION['isAdmin'];
} else {
    $username = '';
}

//if somebody who is not an admin tries to access this page they are redirected to the home page
if($isAdmin == "No") {
	header("Location: home.php");
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
			<h2 align="center">Create New User</h2>
            <div align="center">
			<form method='post' action="NewUser.php">
				Username <input type='text' name='newUsername'><br><br>
				Password <input type='password' name='newPassword'><br><br>
				Confirm Password <input type='password' name='confirmPassword'><br><br>
				Is Admin <select name='isAdmin'>
							<option>No</option>
							<option>Yes</option>
						</select><br><br>
				Can Edit <select name='canEdit'>
							<option>No</option>
							<option>Yes</option>
						</select><br><br>
				Can Add Attribute <select name='canAddAttribute'>
							<option>No</option>
							<option>Yes</option>
						</select><br><br>
				<input type="submit">
			</form>
            </div>
		</body>	
	</html>

_END;

if(isset($_POST['newUsername'])){
	//set variable values to the passed in values
	$newUsername = $_POST['newUsername'];
	$newPassword = $_POST['newPassword'];
	$confirmPassword = $_POST['confirmPassword'];
	$isAdmin = $_POST['isAdmin'];
	$canEdit = $_POST['canEdit'];
	$canAddAttribute = $_POST['canAddAttribute'];
	$checkUsernameQuery = "SELECT * FROM ProLungdx.Users where Users.UserName = '$newUsername'";
	$checkUsernameResults = $conn->query($checkUsernameQuery);
	if(!$checkUsernameResults) die ($conn->error);
	$checkUserRows = $checkUsernameResults->num_rows;
	
	//check the database for the requested username
	if($checkUserRows != 0) {
		echo "This Username has already been taken";
		
	} else{
		//check that the password and the password confirmation match
		if ($newPassword == $confirmPassword) {
			//sql query to insert user into the database table users
			$newUserQuery = "INSERT INTO ProLungdx.Users (UserName, Password, IsAdmin, CanEdit, CanAddAttribute) Values ('$newUsername', '$newPassword', '$isAdmin', '$canEdit', '$canAddAttribute')";
			$newUserResults = $conn->query($newUserQuery);
			if(!$newUserResults) die ($conn->error);
			
			//create loop to set user preferences for all attributes
			$numAttributeQuery = "SELECT * from prolungdx.patientattributenames";
			$numAttributeResult = $conn->query($numAttributeQuery);
			if(!$numAttributeResult) die ($conn->error);
			$numAttributes = $numAttributeResult->num_rows;
			for($x=1;$x<=$numAttributes;$x++){
				$prefQuery="INSERT INTO ProLungdx.userpreferences (UserName, AttributeNumber, IsVisible, IsExported) Values ('$newUsername', '$x', 'Yes', 'Yes')";
				$prefResults = $conn->query($prefQuery);
				if(!$prefResults) die ($conn->error);
				
			}
			echo <<<_END
			<div align="center">
			The User: $newUsername has been successfully created with permissions: <br>
			Is Admin = $isAdmin<br>
			Can Edit = $canEdit<br>
			Can Add Attribute = $canAddAttribute<br>
			
			</div>
			
_END;

		} else {
			//the passwords did not match. do not proceed
			echo "The passwords do not match";
		}
	}
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

?>