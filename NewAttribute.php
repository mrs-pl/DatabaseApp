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

                <h1>New Attribute</h1>
                <form action="NewAttribute.php" method="post">
                    <table>
                        <tr>
                            <td>Attribute of:</td>
                            <td><select name="attributeTable">
                                <option selected disabled>Select Table</option>
                                <option>Patient</option>
                                <option>Scan Data</option>
                            </select></td>
                        </tr>
                        <tr>
                            <td>Attribute Name: </td>
                            <td><input type="text" name="attributeName"></td>
                        </tr>
                        <tr>
                            <td>Attribute Type: </td>
                            <td><select name="attributeType">
                                <option selected disabled>Select Data Type</option>
                                <option>Integer</option>
                                <option>Decimal</option>
                                <option>Date</option>
                                <option>Text</option>
                            </select></td>
                        </tr>
                        <tr>
                            <td>Attribute Description:</td> <td><textarea name="attributeDescription" rows="3" cols="18"></textarea></td>
                        </tr>
                    </table>
                        <br><input type="submit" value="Save Attribute">
                    
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


//Activated after Save Attribute button pressed
if(isset($_POST['attributeName'])){
    
    //Assign info from Post Stream to variables
    $attributeName = $_POST['attributeName'];
    $attributeType = $_POST['attributeType'];
    $attributeDescription = $_POST['attributeDescription'];
    $attributeTable = $_POST['attributeTable'];
    
    //create and execute query to find last attribute name and number
    if($attributeTable == 'Patient'){
        $lastAttributeQuery = "SELECT * from PatientAttributeNames order by AttributeNumber ASC";
    } else {
        $lastAttributeQuery = "SELECT * from ScanDataAttributeNames order by AttributeNumber ASC";
    }
    $lastResult = $conn->query($lastAttributeQuery);
    if (!$lastResult) die ($conn->error);
    $lastRows = $lastResult->num_rows;
    if($attributeTable == 'Patient'){
        $lastResult->data_seek($lastRows-1);
    } else {
        $lastResult->data_seek($lastRows-2);
    }
    
    $lastRow = $lastResult->fetch_array(MYSQLI_NUM);
    $lastName = $lastRow[1];
    $lastNumber = $lastRow[0];
    $attributeNumber = $lastNumber + 1;
    
    
    
    //remove spaces, if any, in attribute name
    $attributeName = str_replace(' ', '', $attributeName);

    //Start alter query with correct Table to alter
    //Start attribute name query with correct table to insert into
    if ($attributeTable == 'Patient'){
        $alterQuery = "Alter Table ".$attributeTable." ADD ".$attributeName;
        $attributeNameQuery = "INSERT INTO ProLungdx.PatientAttributeNames (AttributeNumber, AttributeName, AttributeType, AttributeDescription) Values ('$attributeNumber', '$attributeName', '$attributeType', '$attributeDescription')";
    } elseif($attributeTable =='Scan Data'){
        $alterQuery = "Alter Table ScanData ADD ".$attributeName;
        $attributeNameQuery = "INSERT INTO ProLungdx.ScanDataAttributeNames (AttributeName, AttributeType, AttributeDescription) Values ('$attributeName', '$attributeType', '$attributeDescription')";
    }  
    
    //Construct alter query depending on type of Attribute
    if ($attributeType == "Text"){
        $alterQuery = $alterQuery. " VARCHAR (400) Not Null after ".$lastName;
    } elseif($attributeType == "Date") {
        $alterQuery = $alterQuery. " DATE Not NULL after ".$lastName;
    } elseif($attributeType == "Decimal") {
        $alterQuery = $alterQuery. " Decimal (20,10) Not NULL after ".$lastName;
    } elseif($attributeType == "Integer"){
        $alterQuery = $alterQuery. " Integer (15) Not NULL after ".$lastName;
    }
    
    //execute the finalized alter query to alter either the patient or scan data tables
    $alterResult = $conn->query($alterQuery);
    if(!$alterResult) die ($conn->error);
    
    //execute the query to add the new attribute to either the patient attribute names table or the scan data attribute names table
    $attributeNameResult = $conn->query($attributeNameQuery);
    if(!$attributeNameResult) die ($conn->error);
    
    //create and execute query to gather all usernames for user preferences
    $usersQuery = "SELECT UserName From Users";
    $usersResult = $conn->query($usersQuery);
    if(!$usersResult) die ($conn->error);
    $usersRows = $usersResult->num_rows;
    
    //loop through all users and add user preferences for new attribute
    if ($attributeTable == 'Patient'){
        for ($j=0; $j<$usersRows; $j++){
            $usersResult->data_seek($j);
            $usersRow = $usersResult->fetch_array(MYSQLI_NUM);
            $usernameInsert = $usersRow[0];
            $userPrefQuery = "INSERT INTO ProLungdx.UserPreferences (UserName, AttributeNumber, IsVisible, IsExported) Values ('$usernameInsert', '$attributeNumber', 'NO', 'NO')";
            $userPrefResult = $conn->query($userPrefQuery);
            if(!$userPrefResult) die ($conn->error);
        }
        
    }
    
    echo "<script> window.location.assign('user_profile.php'); </script>";
}  


//close database connection
$conn->close();
?>