<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
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
        <br><br>
        <div id="contentBox" style="margin:0px auto; width:90%">
            <div id="column1" style="float:left; margin:0; width:0%;">
             
            </div>

            <div id="column2" style="float:left; margin:0;width:100%;">
            <h1 align="center">Algorithms</h1>
            <div align="center">
            <table align="center">
                <tr>
                    <td>
                        <a href="NewAlgorithmDetails.php">New Algorithm</a><br><br>
                    </td>
                    <td>
                        <a href="RunAlgorithm.php">Run Algorithm</a><br><br>
                    </td>
                </tr>
            </table>
            <table border=".5">
                <tr>
                    <td>Algorithm ID</td>
                    <td>Algorithm Name</td>
                    <td>Creation Date</td>
                </tr>
_END;

$query = "SELECT * from AlgorithmList ORDER BY AlgorithmList.AlgorithmID ASC";
$result = $conn->query($query);
if(!$result) die ($conn->error);
$rows = $result->num_rows;
for ($j=0; $j<$rows; $j++) {
    $result->data_seek($j);
    $row=$result->fetch_array(MYSQLI_NUM);
    echo <<<_END
        <form action="AlgorithmDetails.php" method="post">
        <tr align="center">
            <td>$row[0]<input type="hidden" value="$row[0]" name="AlgorithmId"></td>
            <td>$row[1]</td>
            <td>$row[3]</td>
            <td><input type="submit" value="View Algorithm Details">
        </tr>
        </form>
_END;
}

echo <<<_END
            
            </table>
            
           
            
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