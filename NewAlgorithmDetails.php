<?php

//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}


//output main structure, heading, etc of html page
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
                    <a href="login.php">Login</a>
                </div>
                <div id="loggedIn" align="right" width="50%">
                    <a id="admin" href="Admin.php">Admin</a>
                    <a href="user_profile.php">$username</a>
                    <a href="logout.php">Log out</a>
                </div>
            </div>
            
        </head>
        <body> 
            <div id="body" align="center" style="float:left; width:100%">
                <h2 align="center">
                    New Algorithm
                </h2>
                <form action="NewAlgorithm.php" method="post">
                    <table>
                        <tr>
                            <td>Algorithm Name</td>
                            <td><input type="text" name="algName"></td>
                        </tr>
                        <tr>
                            <td>Alrogithm Notes/Description</td>
                            <td><input type="text" name="algNotes"></td>
                        </tr>
                        <tr>
                            <td>Number of Attributes</td>
                            <td><input type="number" name="numAttributes"></td>
                        </tr>
                        <tr>
                            <td>Algorithm Cut Point</td>
                            <td><input type="number" step=".00001" name="algCutPoint"></td>
                        </tr>
 
                    </table><br>
                    <input type="submit" value="Proceed to Attribute Selection">
                </form>
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

//close database connection
$conn->close();
?>