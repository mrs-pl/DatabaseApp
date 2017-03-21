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

//check that the query was added to the post stream, save info from past form into variables
if(isset($_POST['query'])){
    $savedQuery = $_POST['query'];
    $queryCriteria = $_POST['queryCriteria'];
}

//initiate variable to display the number of patients returned by query
$patientCount = 0;

//use the SQL query passed from the form to create the correct SQL query for combining patient and scan data
$savedQuery2 = substr($savedQuery, 21);
$savedQuery2 = "SELECT * From Patient Join ScanData on Patient.SubjectID = ScanData.SubjectID ".$savedQuery2;

//Execute saved query, die if no result
$savedQueryResult = $conn->query($savedQuery);
if(!$savedQueryResult) die ($conn->error);
$savedRows = $savedQueryResult->num_rows;

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
        
        
        <br><br><br><br><br><br>
        <div id="contentBox" style="margin:0px auto; width:100%">
            <div id="column1" style="float:left; margin:0; width:100%;" align="center">
                   
                <table border=".5">
                    <tr align="center">

_END;

//query the database to find which attributes should be visible
$visibleAttributes = "SELECT * from PatientAttributeNames Join UserPreferences on PatientAttributeNames.AttributeNumber = UserPreferences.AttributeNumber Where UserPreferences.Username ='$username' and UserPreferences.IsVisible = 'Yes'";
$visibleResult = $conn->query($visibleAttributes);
if(!$visibleResult) die ($conn->error);
$visibleCount = $visibleResult->num_rows;
$visibleAttributes = array();

//Create table header using the query for visible attributes
//Assign visible attributes to an array variable that will be used to display correct attributes
for ($j=0; $j<$visibleCount; $j++) {
    $visibleResult->data_seek($j);
    $visibleRow = $visibleResult->fetch_array(MYSQLI_NUM);
    $visibleAttributes[$j] = $visibleRow[0];
    echo <<<_END
                        <td>$visibleRow[1]</td>
                        
_END;
}

echo <<<_END
                 </tr>
                 
_END;


//Loop through query results for each patient
for ($j=0; $j<$savedRows; $j++) {
        $patientCount = $patientCount+1;
        $savedQueryResult->data_seek($j);
        $savedRow = $savedQueryResult->fetch_array(MYSQLI_NUM);
        echo<<<_END
        
        <form action="PatientDetails.php" method="post">
        <tr align="center">
_END;
        
    
        //Use the array from earlier to only output the attributes that should be visible
        for ($x=0; $x<$visibleCount; $x++) {
            
            $arrayNum = $visibleAttributes[$x] - 1;
            //If the attribute is SubjectID, display differently with hidden field for form
            if($x==0){
                echo<<<_END
                <td>$savedRow[0]<input type="hidden" value="$savedRow[0]" name="SubjectId"></td>
_END;
            } else{
                echo <<<_END
                    <td>$savedRow[$arrayNum]</td>

_END;
            }   
    
        }
            echo <<<_END
            <td><input type="submit" value="View Patient Details">
        </tr>
        </form>
_END;
    }
    

//Display query criteria, patient count, and buttons for exporting query results and query results combined with scan data
echo<<<_END
    
    <div align="center">
    
    Query Criteria: $queryCriteria<br>
    Patient Count: $patientCount<br>
    <div align="center" style="float:left; width:50%">
        <form action="export.php" method = "post">
                    <input type="hidden" value="$savedQuery" name="query">
                    <input type="hidden" value="PatientQuery.php" name="url">
                    <input type="submit" value="Export Query Results">
        </form>
        <br>
    </div>
    <div align="center" style="float:left; width:50%">
        <form action="export.php" method = "post">
                    <input type="hidden" value="$savedQuery2" name="query2">
                    <input type="hidden" value="PatientQuery.php" name="url">
                    <input type="submit" value="Export Results With Scan Data">
        </form>
        <br><br>
    </div>
    <br>
    </div><br>
    
_END;

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