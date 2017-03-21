<?php

//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}

//create and execute query to select all patient attribute names
$query = "SELECT * from PatientAttributeNames";
$result = $conn->query($query);
if(!$result) die ($conn->error);
$rows = $result->num_rows;


//create basic html page structure, header, etc
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