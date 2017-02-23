<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}
if (isset($_POST['AlgorithmId'])) {
    $algorithmId = $_POST['AlgorithmId'];
} else {
    $algorithmId = '';
}

$algDetailsQuery = "SELECT * From AlgorithmList where AlgorithmList.AlgorithmID = '$algorithmId'";
$algDetailsResult = $conn->query($algDetailsQuery);
if(!$algDetailsResult) die ($conn->error);



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
        <div id="contentBox" style="margin:0px auto; width:90%">
            <div id="column1" style="float:left; margin:0; width:0%;">
             
            </div>

            <div id="column2" style="float:left; margin:0;width:100%;">
            <h1 align="center">Algorithm Details</h1>
            <div align="center">
            <form action="PatientList.php" method="post">
            <table border=".5">
                <tr align="center">
                    <td><strong><u>Point ID</u></strong></td>
                    <td><strong><u>Attribute</u></strong></td>
                    <td><strong><u>Max ROC</u></strong></td>
                    <td><strong><u>Cut Point</u></strong></td>
                </tr>
_END;

$query = "SELECT * from AlgorithmValues where AlgorithmValues.AlgorithmId = '$algorithmId'";
$result = $conn->query($query);
if(!$result) die ($conn->error);
$rows = $result->num_rows;
$row = $result->fetch_array(MYSQLI_NUM);
for ($j=0; $j<$rows; $j++) {
    $result->data_seek($j);
    $row=$result->fetch_array(MYSQLI_NUM);
    echo <<<_END
        
        <tr align="center">
            <td>$row[2]<input type="hidden" value="$row[1]" name="$row[2]"></td>
            <td>$row[3]</td>
            <td>$row[4]</td>
            <td><input type="text" value="$row[5]" name="$row[5]"</td>
        </tr>
        
_END;
}

echo <<<_END
            
            </table>
            <br>
            <input type="submit" value="Save Changes">
            </form>
            </div>
            </div>

            
        </div>          
        
        
        </body>
    
    </html>

_END;
   
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