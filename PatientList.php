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

//create and execute query for selecting all patients
$query = "SELECT * from Patient ORDER BY Patient.EnrollmentOrder ASC";
$exportQuery = "SELECT * from Patient WHERE Patient.SubmittedToAnalysis = '1'";
$result = $conn->query($query);
if(!$result) die ($conn->error);
$rows = $result->num_rows;


//create html page structure, header, forms for exporting patient list and patient list with scan data
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
        <div id="contentBox" style="margin:0px auto; width:100%">
            <div id="column1" style="float:left; margin:0; width:0%;">
                
            </div>

            <div id="column2" style="float:left; margin:0;width:100%;">
            <h1 align="center">Patients</h1>
            <a href="PatientQuery.php"><h3 align="center">Query Patients</h3></a>
            <form align="center" action="export.php" method = "post">
                <input type="hidden" value="$exportQuery" name="query">
                <input type="hidden" value="PatientList.php" name="url">
                <input type="submit" value="Export Patient List">
            </form>
            <form align="center" action="export.php" method="post">
                <input type="hidden" value="SELECT * from Patient Join ScanData on Patient.SubjectID = ScanData.SubjectID" name="query2">
                <input type="hidden" value="PatientList.php" name="url">
                <input type="submit" value="Export Patient List with Scan Data">
            </form>
            <div align="center">
            <table border=".5">
                <tr align="center">
_END;


//create and execute query to choose only the attributes that should be visible based on user preferences
$visibleAttributes = "SELECT * from PatientAttributeNames Join UserPreferences on PatientAttributeNames.AttributeNumber = UserPreferences.AttributeNumber Where UserPreferences.Username ='$username' and UserPreferences.IsVisible = 'Yes'";
$visibleResult = $conn->query($visibleAttributes);
if(!$visibleResult) die ($conn->error);
$visibleCount = $visibleResult->num_rows;
$visibleAttributes = array();

//loop through query results to make table header
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


//create table rows and form for each patient
for ($j=0; $j<$rows; $j++) {
    $result->data_seek($j);
    $row=$result->fetch_array(MYSQLI_NUM);
    echo <<<_END
        <form action="PatientDetails.php" method="post">
        <tr align="center">

_END;


    for ($x=0; $x<$visibleCount; $x++) {
        $arrayNum = $visibleAttributes[$x] - 1;
        if($x==0){
            echo<<<_END
            <td>$row[0]<input type="hidden" value="$row[0]" name="SubjectId"></td>
_END;
        } else{
            echo <<<_END
                <td>$row[$arrayNum]</td>
    
_END;
        }

    }
            
    //create submit button to view patient details        
    echo <<<_END
            <td><input type="submit" value="View Patient Details">
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