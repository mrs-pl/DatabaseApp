<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}

if(isset($_POST['algName'])){
    $algName = $_POST['algName'];
	if(isset($_POST['allQuery'])) {
		$allQuery = $_POST['allQuery'];	
	}
	if(isset($_POST['numAttributes'])){
		$numAttributes = $_POST['numAttributes'];	
	}
    
    
    if($algName == 'All Algorithms'){
        //User has indicated that they want to view all of the Algorithm Results
        $algIDQuery = "SELECT AlgorithmID, AlgorithmName From ProLungdx.AlgorithmList";

        $algIDResults = $conn->query($algIDQuery);
        if(!$algIDResults) die ($conn->error);
        $algIDRows = $algIDResults->num_rows;
        $algs = array(array(array()));


        for($j=0;$j<$algIDRows;$j++){
            $algIDResults->data_seek($j);
            $algIDRow = $algIDResults->fetch_array(MYSQLI_NUM);
            $algID = $algIDRow[0];
            $algNames[$j] = $algIDRow[1];
            $query = "SELECT Patient.SubjectID, Patient.Sex, Patient.MalignantBenign, Calculations.AlgScore, Calculations.Prediction, Calculations.Performance from ProLungdx.Patient Join ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis = '1'";
            $queryResult = $conn->query($query);
            if(!$queryResult) die ($conn->error);
            $queryRows = $queryResult->num_rows;
            for($i=0;$i<$queryRows;$i++){
                $queryResult->data_seek($i);
                $queryArray = $queryResult->fetch_array(MYSQLI_NUM);
                $algs[$j][$i] = $queryArray;

            }
        }
        
    } else{
        //User has indicated they want to view a speficic set of results and performance
        $algIDQuery = "SELECT AlgorithmID From ProLungdx.AlgorithmList WHERE AlgorithmList.AlgorithmName = '$algName'";
        $algIDResults = $conn->query($algIDQuery);
        if(!$algIDResults) die ($conn->error);
        $algIDResults->data_seek(0);
        $algIDRow = $algIDResults->fetch_array(MYSQLI_NUM);
        $algID = $algIDRow[0];
        
        $resultsQuery = "SELECT Patient.SubjectID, Patient.Sex, Patient.MalignantBenign, Calculations.AlgScore, Calculations.Prediction, Calculations.Performance from ProLungdx.Patient Join ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis = '1'";
        $resultsResult = $conn->query($resultsQuery);
        if(!$resultsResult) die ($conn->error);
        $resultsRows = $resultsResult->num_rows;


        $detailedExportQuery = "SELECT * FROM ProLungdx.Patient JOIN ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID JOIN ProLungdx.ScanData on Calculations.SubjectID = ScanData.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis ='1'";
		$patientExportQuery = "SELECT * FROM ProLungdx.Patient JOIN ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis = '1'";
	}
    
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

_END;


if($algName == "All Algorithms"){
    //User has indicated they want to view all Algorithm Resutls
    
    echo<<<_END
        <body>
            <br><br>
            <div id="contentBox" style="margin:0px auto; width:100%" align="center">
                <h1 align="center">All Algorithm Predictions</h1>
                <table border=".5">
                <tr>
                    <td>SubjectID</td>
                    <td>Sex</td>
                    <td>Malignant/Benign</td>
_END;
   for($x=0;$x<$algIDRows;$x++){
        echo<<<_END
                    <td>$algNames[$x] Comp Score</td>
                    <td>$algNames[$x] Prediction</td>
                    <td>$algNames[$x] Performance</td>
_END;
    }
    
echo<<<_END
                </tr>
                
_END;
    
for($x=0;$x<$queryRows;$x++){
    $id = $algs[0][$x][0];
    $sex = $algs[0][$x][1];
    $maligBenign = $algs[0][$x][2];
    
    echo<<<_END
                <tr>
                    <td>$id</td>
                    <td>$sex</td>
                    <td>$maligBenign</td>
                    
_END;
    
    for($y=0;$y<$algIDRows;$y++){
        $cScore = $algs[$y][$x][3];
        $aPredict = $algs[$y][$x][4];
        $aPerf = $algs[$y][$x][5];
        echo<<<_END
                    <td>$cScore</td>
                    <td>$aPredict</td>
                    <td>$aPerf</td>
        
_END;
    }
    
    echo<<<_END
                    
                </tr>
_END;
}
    
echo<<<_END
                
                </table>
            
            <div>
        
        </body>
    
_END;
    
    
} else{
    
    //User has indicated that they want to view a specific set of algorithm results and performance
    echo<<<_END
        <body>
        <br><br>
        <div id="contentBox" style="margin:0px auto; width:90%">
                <div id="column1" style="float:left; margin:0;width:60%;">
                    <h1 align="center">$algName Results</h1>
                    
                    <div align="center">
                        <form action="export.php" method = "post">
                            <input type="hidden" value="$detailedExportQuery" name="resultsQuery">
                            <input type="hidden" value="ResultsSearch.php" name="url">
                            <input type="submit" value="Export Detailed Results">
                        </form>
						<form action="export.php" method="post">
							<input type="hidden" value = "$patientExportQuery" name="patientResultsQuery">
							<input type="hidden" value = "ResultsSearch.php" name="url">
							<input type="submit" value = "Export Results">
						</form>
                        <table border=".5">
                            <tr>
                                <td>SubjectID</td>
                                <td>Sex</td>
                                <td>MalignantBenign</td>
                                <td>Composite Score</td>
                                <td>Algorithm Prediction</td>
                                <td>Result</td>
                            </tr>
_END;


$truePosCount = 0;
$trueNegCount = 0;
$falsePosCount = 0;
$falseNegCount = 0;
$patientCount = 0;
for ($j=0; $j<$resultsRows; $j++) {
	$patientCount = $patientCount + 1;
    $resultsResult->data_seek($j);
    $resultsRow=$resultsResult->fetch_array(MYSQLI_NUM);
    if ($resultsRow[5] == "True Positive"){
        $truePosCount = $truePosCount + 1;
    } elseif ($resultsRow[5] == "True Negative") {
        $trueNegCount = $trueNegCount + 1;
    } elseif ($resultsRow[5] == "False Positive") {
        $falsePosCount = $falsePosCount + 1;
    } elseif ($resultsRow[5] == "False Negative") {
        $falseNegCount = $falseNegCount + 1;
    }
    echo <<<_END
        <form action="PatientDetails.php" method="post">
        <tr align="center">
            <td>$resultsRow[0]<input type="hidden" value="$resultsRow[0]" name="SubjectId"></td>
            <td>$resultsRow[1]</td>
            <td>$resultsRow[2]</td>
            <td>$resultsRow[3]</td>
            <td>$resultsRow[4]</td>
            <td>$resultsRow[5]</td>
            <td><input type="submit" value="View Patient Details">
        </tr>
        </form>
_END;
}

$sensitivity = ($truePosCount/($truePosCount + $falseNegCount));
$specificity = ($trueNegCount/($trueNegCount + $falsePosCount));
$ppv = ($truePosCount / ($truePosCount + $falsePosCount));
$npv = ($trueNegCount / ($trueNegCount + $falseNegCount));
$acc = (($truePosCount + $trueNegCount)/ $patientCount);
echo <<<_END
            
                        </table>
                    </div>
                </div>
            </div>
        <div style="float:right; width:40%">
        <h1 align="center">Performance</h1>
        <table border =".5" width="100%">
            <tr>
                <td></td>
                <td>Malignant</td>
                <td>Benign</td>
            </tr>
            <tr>
                <td>Test Score Positive</td>
                <td>$truePosCount</td>
                <td>$falsePosCount</td>
            </tr>
            <tr>
                <td>Test Score Negative</td>
                <td>$falseNegCount</td>
                <td>$trueNegCount</td>
            </tr>
        </table>
        <br>
		Patient Count = $patientCount <br>
        Sensitivity = $sensitivity <br>
        Specificity = $specificity <br>
		Positive Predictive Value = $ppv <br>
		Negative Predictive Value = $npv <br>
		Accuracy = $acc <br>
        </div>
        
        </body>
    
    </html>

_END;
    
}
   
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