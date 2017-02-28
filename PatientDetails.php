<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}
if (isset($_POST['SubjectId'])) {
    $subjectId = $_POST['SubjectId'];
} else {
    $subjectId = '';
}

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
            <h1 align="center">Patient $subjectId</h1>
            <div align="center">
            <form action="PatientList.php" method="post">
            <table border=".5">
                <tr align="center">
                    <td><strong><u>Attribute</u></strong></td>
                    <td><strong><u>Value</u></strong></td>
                </tr>
_END;

$query = "SELECT * from Patient where Patient.SubjectId = '$subjectId'";
$result = $conn->query($query);
if(!$result) die ($conn->error);
$query2 = "SELECT * from PatientAttributeNames";
$result2 = $conn->query($query2);
$rows = $result->num_rows;
$rows2 = $result2->num_rows;
$row = $result->fetch_array(MYSQLI_NUM);
for ($j=0; $j<$rows2; $j++) {
    $result2->data_seek($j);
    $row2=$result2->fetch_array(MYSQLI_NUM);
    echo <<<_END
        
        <tr align="center">
            <td>$row2[1]<input type="hidden" value="$row2[1]" name="$row2[1]"></td>
            <td><input type="text" value="$row[$j]" name="$row[$j]"</td>
        </tr>
        
_END;
}

$scanAttributeQuery = "SELECT * FROM ScanDataAttributeNames order by AttributeNumber asc";
$scanAttributeResults = $conn->query($scanAttributeQuery);
if(!$scanAttributeResults) die ($conn->error);
$scanAttributeRows = $scanAttributeResults->num_rows;
echo <<<_END
            
            </table>
            <br>
            <input type="submit" value="Save Changes">
            </form>
            </div>
            </div>

            
        </div>  
        
        <h2 align="center">Scan Data</h2>
        
        <form align="center" action="export.php" method="post">
            <input type="hidden" value="SELECT * from Patient JOIN ScanData on Patient.SubjectID = ScanData.SubjectID where Patient.SubjectID = '$subjectId'" name="query">
            <input type="hidden" value="PatientList.php" name="url">
            <input type="submit" value="Export Scan Data">
        </form>
        
        <br>
        <table border=".5" align="center">
            <tr>
_END;
    for ($j=0; $j<$scanAttributeRows; $j++){
        $scanAttributeResults->data_seek($j);
        $scanAttributeRow = $scanAttributeResults->fetch_array(MYSQLI_NUM);
        echo<<<_END
            <td>$scanAttributeRow[1]</td>
_END;
    }
                
echo<<<_END
            </tr>
_END;

$dataQuery = "SELECT * from ScanData where ScanData.SubjectID ='$subjectId'";
$dataResult = $conn->query($dataQuery);
if(!$dataResult) die ($conn->error);
$dataRows = $dataResult->num_rows;

for ($x=0; $x<$dataRows; $x++){
    $dataResult->data_seek($x);
    $dataRow=$dataResult->fetch_array(MYSQLI_NUM);
    echo <<<_END
    <tr align="center">
_END;
    
    for($y=0; $y<$scanAttributeRows; $y++){
        echo<<<_END
            <td>$dataRow[$y]</td>
_END;
    }
        
    echo<<<_END
    </tr>
_END;
}

echo<<<_END
        
        
        </table>
        
        
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