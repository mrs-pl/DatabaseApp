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

//create and execute query to select all patient attribute names
$query = "SELECT * from PatientAttributeNames";
$result = $conn->query($query);
if(!result) die ($conn->error);
$rows = $result->num_rows;


//create basic html page structure, header, etc
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
            <h1>Attributes</h1>
            <div style="width:50%; float:left" align="center">
                <h2>Patient Attributes</h2>
                <a href="NewAttribute.php">Add New Attribute</a><br><br>
                <table border=".5">
                    <tr align="center">
                        <td>Attribute Number</td>
                        <td>Attribute Name</td>
                        <td>Attribute Type</td>
                        <td>Attribute Description</td>
                    </tr>
_END;


//loop through query results and display results in an html table
for ($j=0; $j<$rows; $j++) {
    $result->data_seek($j);
    $row=$result->fetch_array(MYSQLI_NUM);
    echo <<<_END
        <form action="PatientDetails.php" method="post">
        <tr align="center">
            <td>$row[0]<input type="hidden" value="$row[0]" name="AttributeId"></td>
            <td>$row[1]</td>
            <td>$row[2]</td>
            <td>$row[3]</td>
        </tr>
        </form>
_END;
}

//create table structure for scan data attributes
echo <<<_END
            
                </table>
            </div>
            
            <div style="width:50%; float:left" align="center">
                <h2>Scan Data Attributes</h2>
                <table border=".5">
                <a href="NewAttribute.php">Add New Attribute</a><br><br>
                    <tr align="center">
                        <td>Attribute Number</td>
                        <td>Attribute Name</td>
                        <td>Attribute Type</td>
                        <td>Attribute Description</td>
                    </tr>
_END;

//create and execute query to select all scan data attributes
$scanQuery = "SELECT * FROM ScanDataAttributeNames";
$scanResult = $conn->query($scanQuery);
if(!$scanResult) die ($conn->error);
$scanRows = $scanResult->num_rows;


//loop through query results and display each in an html table
for ($j=0; $j<$scanRows; $j++){
    $scanResult->data_seek($j);
    $scanRow = $scanResult->fetch_array(MYSQLI_NUM);
    echo <<<_END
        <tr align="center">
            <td>$scanRow[0]</td>
            <td>$scanRow[1]</td>
            <td>$scanRow[2]</td>
            <td>$scanRow[3]</td>
        </tr>
_END;
}

echo <<<_END
                </table>
                
                
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


//close database connection
$conn->close();
?>