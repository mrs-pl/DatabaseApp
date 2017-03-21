<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}
if (isset($_POST['AlgorithmId'])) {
    $algorithmId = $_POST['AlgorithmId'];
} else {
    $algorithmId = '1';
}

$algDetailsQuery = "SELECT * From AlgorithmList where AlgorithmList.AlgorithmID = '$algorithmId'";
$algDetailsResult = $conn->query($algDetailsQuery);
if(!$algDetailsResult) die ($conn->error);



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
        <div id="contentBox" style="margin:0px auto; width:90%">
            <div id="column1" style="float:left; margin:0; width:0%;">
             
            </div>

            <div id="column2" style="float:left; margin:0;width:100%;">
            <h1 align="center">Modify Algorithm</h1>
            <div align="center">
            <form action="PatientList.php" method="post">
			<strong>New Algorithm Name:</strong> <input type="text" name="newName"><br><br>
			<strong>New Algorithm Cut Point: </strong> <input type="text" name="newCutPoint"><br><br>
            <table border=".5">
                <tr align="center">
                    <td><strong><u>Point ID</u></strong></td>
                    <td><strong><u>Attribute</u></strong></td>
                    <td><strong><u>Max ROC</u></strong></td>
                    <td><strong><u>Cut Point</u></strong></td>
					<td><strong><u>Delete Attribute</u></strong></td>
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
			<td><input type="checkbox" name="deleteAttribute" value="Yes"></td>
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