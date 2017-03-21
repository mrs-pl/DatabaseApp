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

if(isset($_POST['numAttributes'])){
    $numAttributes = $_POST['numAttributes'] + 1;
} else {
    $numAttributes = 2;
}

if(isset($_POST['algName'])){
    $algName = $_POST['algName'];
    $algNotes = $_POST['algNotes'];
    $algCutPoint = $_POST['algCutPoint'];
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
        
        
        <br><br><br><br><br>
            <div id="body" align="center" style="float:left; width:100%">
                <h2 align="center">
                    Algorithm Point-Attribute Values
                </h2>
                <form action="AlgorithmProcessing.php" method="post">
                    <input type="hidden" value="$algName" name="algName">
                    <input type="hidden" value="$algNotes" name="algNotes">
                    <input type="hidden" value="$algCutPoint" name="algCutPoint">
                    <input type="hidden" value="$numAttributes" name="numAttributes">
                    <table>
                        <tr align="center">
                            <td>Point</td>
                            <td>Attribute</td>
                            <td>Max ROC</td>
                            <td>Cut Point</td>
                        </tr>
_END;

for($x=1; $x<$numAttributes; $x++){
    


echo <<<_END
                        <tr>
                            <td>
                                <select name="point$x">
                                    <option selected disabled>Choose Point</option>
_END;

//create and execute sql query to return all point names
$pointQuery = "SELECT * from PointList";
$pointResult = $conn->query($pointQuery);
if(!$pointResult) die ($conn->error);
$pointRows = $pointResult ->num_rows;

//loop through results and add an option to the drop down for each point
for($j=0; $j<$pointRows; $j++) {
    $pointResult->data_seek($j);
    $pointRow = $pointResult->fetch_array(MYSQLI_NUM);
    
    echo <<<_END
    <option value="$pointRow[1]"> $pointRow[1] </option>
_END;
}


echo <<<_END
                                </select>
                            </td>
                            <td>
                                <select name="attribute$x">
                                    <option selected disabled>Choose Attribute</option>
_END;

//create and execute sql query to return all attribute names
$attributeQuery = "SELECT * from PointAttributeNames";
$attributeResult = $conn->query($attributeQuery);
if(!$attributeResult) die ($conn->error);
$attributeRows = $attributeResult->num_rows;

for($j=0;$j<$attributeRows;$j++){
    $attributeResult->data_seek($j);
    $attributeRow = $attributeResult->fetch_array(MYSQLI_NUM);
    
    echo <<<_END
    <option value="$attributeRow[1]"> $attributeRow[1] </option>
_END;
}

echo <<<_END
                                </select>
                            </td>
                            <td>
                                <input type="number" step=".01" name="maxROC$x">
                            </td>
                            <td>
                                <input type="number" step="any" name="cutPoint$x">
                            </td>
                        </tr>
_END;
                        
}

echo <<<_END
                    </table>
                    <br>
                    <input type="submit" value="Save Algorithm">
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