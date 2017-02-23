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
$result = $conn->query($query);
if(!$result) die ($conn->error);
$rows = $result->num_rows;


//create html page structure, header, forms for exporting patient list and patient list with scan data
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
        <div id="contentBox" style="margin:0px auto; width:100%">
            <div id="column1" style="float:left; margin:0; width:0%;">
                
            </div>

            <div id="column2" style="float:left; margin:0;width:100%;">
            <h1 align="center">Patients</h1>
            <a href="PatientQuery.php"><h3 align="center">Query Patients</h3></a>
            <form align="center" action="export.php" method = "post">
                <input type="hidden" value="$query" name="query">
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