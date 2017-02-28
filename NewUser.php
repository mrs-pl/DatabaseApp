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