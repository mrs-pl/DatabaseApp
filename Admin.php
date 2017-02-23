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
            <td>$userRow[1]</td>
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
            <td><input type="submit" value="Save Changes"></td>
            <td>
                <form action="Admin.php" method="post">
                    <input type="hidden" name="user" value="$username">
                    <input type="submit" value="Reset Password">
                </form>
            </td>
            <td>
                <form action="Admin.php" method="post">
                    <input type="hidden" name="user" value="$username">
                    <input type
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


?>