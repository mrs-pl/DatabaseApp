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
if(!$result) die ($conn->error);
$rows = $result->num_rows;

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