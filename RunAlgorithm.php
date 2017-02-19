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


$query = "SELECT * from PatientAttributeNames";
$result = $conn->query($query);
if(!result) die ($conn->error);
$rows = $result->num_rows;

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
            <br><br>
            <div id="contentBox" style="margin:0px auto; width:100%" align="center">

                <h1>Run Algorithm</h1>
                <form action="runAlg.php" method="post">
                    <table>
                        <tr>
                            <td>Algorithm Name: </td>
                            <td>
                                <select name="algName">
                                    <option selected disabled>Select Algorithm</option>
_END;

$algQuery = "SELECT AlgorithmName From ProLungdx.AlgorithmList";
$algResults = $conn->query($algQuery);
if (!$algResults) die ($conn->error);
$algRows = $algResults->num_rows;
for ($j=0; $j<$algRows; $j++){
    $algResults->data_seek($j);
    $algRow = $algResults->fetch_array(MYSQLI_NUM);
    echo<<<_END
                                    <option>$algRow[0]</option>
_END;
}
                                

echo<<<_END
                                </select>
                            </td>
                        </tr>
                    </table>
                        <br><input type="submit" value="Run Algorithm">
                    
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