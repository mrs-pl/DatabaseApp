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
        
        
        <br><br><br><br>
        <div id="contentBox" style="width:100%">
            <div id="column1" align="center" style="float:left; margin:0; width:25%;">
                <a href="PatientList.php"><h2 align="center">Patients</h2>
                <img src="Images/iconPatients.png" width="75%"></a>
            </div>

            <div id="column2" align="center" style="float:left; margin:0; width:25%;">
                    <a href="AttributeList.php"><h2 align="center">Attributes</h2>
                    <img src="Images/iconAttributes.png" width="75%"></a>
            
            </div>

            <div id="column3" align="center" style="float:left; margin:0; width:25%;">
                    <a href="AlgorithmList.php"><h2 align="center">Algorithms</h2>
                    <img src="Images/iconAlgorithms.png" width="75%"></a>
            
            </div>
            
            <div id="column4" align="center" style="float:left; margin:0; width:25%;">
                <a href="ResultsSearch.php"><h2 align="center">Results</h2>
                <img src="Images/iconResults.png" width="75%"></a>
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