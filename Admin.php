<?php

//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
	$isAdmin = $_SESSION['isAdmin'];
} else {
    $username = '';
}

if($isAdmin == "No") {
	header("Location: home.php");
}
//create basic html page structure, header, etc.
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
        
        
        <br><br>
        <div id="body" style="margin:0px auto; width:100%" align="center">
            <h1>Administration</h1>
            <div style="width:50%; float:left" align="center">
                <h2>Users</h2>
                <table border = ".5">
                    <tr align="center">
                        <td>Username</td>
                        <td>Admin</td>
                        <td>Can Edit</td>
                        <td>Can Add Attributes</td>
                        
                    </tr>
_END;

//create and execute query to select all users in the users table
$userQuery = "SELECT * FROM ProLungdx.Users";
$userResult = $conn->query($userQuery);
if(!$userResult) die ($conn->error);
$userRows = $userResult->num_rows;
for($j=0;$j<$userRows;$j++){
    $userResult->data_seek($j);
    $userRow = $userResult->fetch_array(MYSQLI_NUM);
    echo <<<_END
        <tr align="center">
        <form method="post" action="Admin.php">
            <td>
				$userRow[1]
				<input type="hidden" name="userChangeName" value = "$userRow[1]">
			</td>
_END;
    
    //check to see if the User is Admin and display check boxes accordingly
    if($userRow[3] == "Yes"){
        echo<<<_END
            <td><input type="checkbox" name="IsAdmin" value="Yes" checked>Yes<br><input type="checkbox" name="IsAdmin" value="No"> No
            </td>
_END;
    } else {
        echo<<<_END
            <td><input type="checkbox" name="IsAdmin" value="Yes">Yes<br><input type="checkbox" name="IsAdmin" value="No" checked> No
            </td>
_END;
    }
    
    //check to see if the User can edit and display check boxes accordingly
    if($userRow[4] == "Yes"){
        echo<<<_END
            <td><input type="checkbox" name="CanEdit" value="Yes" checked>Yes<br><input type="checkbox" name="CanEdit" value="No"> No
            </td>
_END;
    } else {
        echo<<<_END
            <td><input type="checkbox" name="CanEdit" value="Yes">Yes<br><input type="checkbox" name="CanEdit" value="No" checked> No
            </td>
_END;
    }
    if($userRow[5] == "Yes"){
        echo<<<_END
            <td><input type="checkbox" name="CanAddAttributes" value="Yes" checked>Yes<br><input type="checkbox" name="CanAddAttributes" value="No"> No
            </td>
_END;
    } else {
        echo<<<_END
            <td><input type="checkbox" name="CanAddAttributes" value="Yes">Yes<br><input type="checkbox" name="CanAddAttributes" value="No" checked> No
            </td>
_END;
    }
    
    
    
echo<<<_END
            <td>
				<input type="submit" value="Save Changes" name="userChange">
			</td>
            <td>
                <form action="Admin.php" method="post">
                    <input type="hidden" name="user" value="$username">
                    <input type="submit" value="Reset Password">
                </form>
            </td>
        </form>
        </tr>
_END;
}

echo<<<_END
                </table>
            </div>
            <div style="width:50%; float:left" align="center">
                <h2>Administrative Actions</h2>
                <a href="NewUser.php">New User</a>
            </div>
        </div>
        </body>
    

    </html>
_END;

//jquery script to change the header depending on whether or not a user is logged in
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

if(isset($_POST['userChange'])){
	$userChangeName = $_POST['userChangeName'];
	$isAdminChange = $_POST['IsAdmin'];
	$canEditChange = $_POST['CanEdit'];
	$canAddChange = $_POST['CanAddAttributes'];
	$changeQuery = "UPDATE prolungdx.users SET IsAdmin = '$isAdminChange', CanEdit = '$canEditChange', CanAddAttribute ='$canAddChange' WHERE UserName = '$userChangeName'";
	$changeResult = $conn->query($changeQuery);
	if(!$changeResult) die ($conn->error);
}

?>