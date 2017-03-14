<?php
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}


$whereClause = '';
if(isset($_POST['algName'])){
    $algName = $_POST['algName'];
	if(isset($_POST['allQuery'])) {
		$allQuery = $_POST['allQuery'];	
	} else {
		$allQuery = '';
	}
	if(isset($_POST['numAttributes'])){
		$numAttributes = $_POST['numAttributes'];	
	}
	if(isset($_POST['attribute1']) && isset($_POST['operator1']) && isset($_POST['input1'])) {
		$attribute1 = $_POST['attribute1'];
		$operator1 = $_POST['operator1'];
		$input1 = $_POST['input1'];
		if($operator1 == "like"){
			$whereClause = $whereClause." AND Patient.$attribute1 LIKE '%$input1%'";
		} else {
			$whereClause = $whereClause." AND Patient.$attribute1 $operator1 '$input1'";
		}
	}
	if(isset($_POST['attribute2']) && isset($_POST['operator2']) && isset($_POST['input2'])) {
		$attribute2 = $_POST['attribute2'];
		$operator2 = $_POST['operator2'];
		$input2 = $_POST['input2'];
		if($operator2 == "like"){
			$whereClause = $whereClause." AND Patient.$attribute2 LIKE '%$input2%'";
		} else {
			$whereClause = $whereClause." AND Patient.$attribute2 $operator2 '$input2'";
		}
	}
	if(isset($_POST['attribute3']) && isset($_POST['operator3']) && isset($_POST['input3'])) {
		$attribute3 = $_POST['attribute3'];
		$operator3 = $_POST['operator3'];
		$input3 = $_POST['input3'];
		if($operator3 == "like"){
			$whereClause = $whereClause." AND Patient.$attribute3 LIKE '%$input3%'";
		} else {
			$whereClause = $whereClause." AND Patient.$attribute3 $operator3 '$input3'";
		}
	}
	if(isset($_POST['attribute4']) && isset($_POST['operator4']) && isset($_POST['input4'])) {
		$attribute4 = $_POST['attribute4'];
		$operator4 = $_POST['operator4'];
		$input4 = $_POST['input4'];
		if($operator4 == "like"){
			$whereClause = $whereClause." AND Patient.$attribute4 LIKE '%$input4%'";
		} else {
			$whereClause = $whereClause." AND Patient.$attribute4 $operator4 '$input4'";
		}
	}
	if(isset($_POST['attribute5']) && isset($_POST['operator5']) && isset($_POST['input5'])) {
		$attribute5 = $_POST['attribute5'];
		$operator5 = $_POST['operator5'];
		$input5 = $_POST['input5'];
		if($operator5 == "like"){
			$whereClause = $whereClause." AND Patient.$attribute5 LIKE '%$input5%'";
		} else {
			$whereClause = $whereClause." AND Patient.$attribute5 $operator5 '$input5'";
		}
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
			if($whereClause != '') {
				$query = $query.$whereClause;
			}
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
        $algIDQuery = "SELECT AlgorithmID, CutPoint From ProLungdx.AlgorithmList WHERE AlgorithmList.AlgorithmName = '$algName'";
        $algIDResults = $conn->query($algIDQuery);
        if(!$algIDResults) die ($conn->error);
        $algIDResults->data_seek(0);
        $algIDRow = $algIDResults->fetch_array(MYSQLI_NUM);
        $algID = $algIDRow[0];
		$cutPoint = $algIDRow[1];
        
        $resultsQuery = "SELECT Patient.SubjectID, Patient.Sex, Patient.MalignantBenign, Calculations.AlgScore, Calculations.Prediction, Calculations.Performance, Calculations.Cut1Perf, Calculations.Cut2Perf, Calculations.Cut3Perf, Calculations.Cut4Perf, Calculations.Cut5Perf, Calculations.Cut6Perf, Calculations.Cut7Perf from ProLungdx.Patient Join ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis = '1'";
		if($whereClause != '') {
				$resultsQuery = $resultsQuery.$whereClause;
		}
        $resultsResult = $conn->query($resultsQuery);
        if(!$resultsResult) die ($conn->error);
        $resultsRows = $resultsResult->num_rows;


        $detailedExportQuery = "SELECT * FROM ProLungdx.Patient JOIN ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID JOIN ProLungdx.ScanData on Calculations.SubjectID = ScanData.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis ='1'";
		$patientExportQuery = "SELECT * FROM ProLungdx.Patient JOIN ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID' AND Patient.SubmittedToAnalysis = '1'";
		if($allQuery != '' && $allQuery != "All Patients") {
			$whereClause = $whereClause." AND Patient.DataSet = '$allQuery'";
		}
		if($whereClause != '') {
				$detailedExportQuery = $detailedExportQuery.$whereClause;
				$patientExportQuery = $patientExportQuery.$whereClause;
		} 
	}
    
	
	
} else {
	 echo "<script> window.location.assign('ResultsSearch.php'); </script>";
	
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
$cut1TPCount = 0;
$cut1TNCount =  0;
$cut1FPCount = 0;
$cut1FNCount = 0;

$cut2TPCount = 0;
$cut2TNCount =  0;
$cut2FPCount = 0;
$cut2FNCount = 0;

$cut3TPCount = 0;
$cut3TNCount =  0;
$cut3FPCount = 0;
$cut3FNCount = 0;

$cut4TPCount = 0;
$cut4TNCount =  0;
$cut4FPCount = 0;
$cut4FNCount = 0;

$cut5TPCount = 0;
$cut5TNCount =  0;
$cut5FPCount = 0;
$cut5FNCount = 0;

$cut6TPCount = 0;
$cut6TNCount = 0;
$cut6FPCount = 0;
$cut6FNCount = 0;

$cut7TPCount = 0;
$cut7TNCount = 0;
$cut7FPCount = 0;
$cut7FNCount = 0;


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
	if ($resultsRow[6] == "True Positive"){
        $cut1TPCount = $cut1TPCount + 1;
    } elseif ($resultsRow[6] == "True Negative") {
        $cut1TNCount = $cut1TNCount + 1;
    } elseif ($resultsRow[6] == "False Positive") {
        $cut1FPCount = $cut1FPCount + 1;
    } elseif ($resultsRow[6] == "False Negative") {
        $cut1FNCount = $cut1FNCount + 1;
    }
	if ($resultsRow[7] == "True Positive"){
        $cut2TPCount = $cut2TPCount + 1;
    } elseif ($resultsRow[7] == "True Negative") {
        $cut2TNCount = $cut2TNCount + 1;
    } elseif ($resultsRow[7] == "False Positive") {
        $cut2FPCount = $cut2FPCount + 1;
    } elseif ($resultsRow[7] == "False Negative") {
        $cut2FNCount = $cut2FNCount + 1;
    }
	if ($resultsRow[8] == "True Positive"){
        $cut3TPCount = $cut3TPCount + 1;
    } elseif ($resultsRow[8] == "True Negative") {
        $cut3TNCount = $cut3TNCount + 1;
    } elseif ($resultsRow[8] == "False Positive") {
        $cut3FPCount = $cut3FPCount + 1;
    } elseif ($resultsRow[8] == "False Negative") {
        $cut3FNCount = $cut3FNCount + 1;
    }
	if ($resultsRow[9] == "True Positive"){
        $cut4TPCount = $cut4TPCount + 1;
    } elseif ($resultsRow[9] == "True Negative") {
        $cut4TNCount = $cut4TNCount + 1;
    } elseif ($resultsRow[9] == "False Positive") {
        $cut4FPCount = $cut4FPCount + 1;
    } elseif ($resultsRow[9] == "False Negative") {
        $cut4FNCount = $cut4FNCount + 1;
    }
	if ($resultsRow[10] == "True Positive"){
        $cut5TPCount = $cut5TPCount + 1;
    } elseif ($resultsRow[10] == "True Negative") {
        $cut5TNCount = $cut5TNCount + 1;
    } elseif ($resultsRow[10] == "False Positive") {
        $cut5FPCount = $cut5FPCount + 1;
    } elseif ($resultsRow[10] == "False Negative") {
        $cut5FNCount = $cut5FNCount + 1;
    }
	if ($resultsRow[11] == "True Positive"){
        $cut6TPCount = $cut6TPCount + 1;
    } elseif ($resultsRow[11] == "True Negative") {
        $cut6TNCount = $cut6TNCount + 1;
    } elseif ($resultsRow[11] == "False Positive") {
        $cut6FPCount = $cut6FPCount + 1;
    } elseif ($resultsRow[11] == "False Negative") {
        $cut6FNCount = $cut6FNCount + 1;
    }
	if ($resultsRow[12] == "True Positive"){
        $cut7TPCount = $cut7TPCount + 1;
    } elseif ($resultsRow[12] == "True Negative") {
        $cut7TNCount = $cut7TNCount + 1;
    } elseif ($resultsRow[12] == "False Positive") {
        $cut7FPCount = $cut7FPCount + 1;
    } elseif ($resultsRow[12] == "False Negative") {
        $cut7FNCount = $cut7FNCount + 1;
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

$cut1Sens = ($cut1TPCount / ($cut1TPCount + $cut1FNCount));
$cut1Spec = ($cut1TNCount / ($cut1TNCount + $cut1FPCount));
$cut2Sens = ($cut2TPCount / ($cut2TPCount + $cut2FNCount));
$cut2Spec = ($cut2TNCount / ($cut2TNCount + $cut2FPCount));
$cut3Sens = ($cut3TPCount / ($cut3TPCount + $cut3FNCount));
$cut3Spec = ($cut3TNCount / ($cut3TNCount + $cut3FPCount));
$cut4Sens = ($cut4TPCount / ($cut4TPCount + $cut4FNCount));
$cut4Spec = ($cut4TNCount / ($cut4TNCount + $cut4FPCount));
$cut5Sens = ($cut5TPCount / ($cut5TPCount + $cut5FNCount));
$cut5Spec = ($cut5TNCount / ($cut5TNCount + $cut5FPCount));
$cut6Sens = ($cut6TPCount / ($cut6TPCount + $cut6FNCount));
$cut6Spec = ($cut6TNCount / ($cut6TNCount + $cut6FPCount));
$cut7Sens = ($cut7TPCount / ($cut7TPCount + $cut7FNCount));
$cut7Spec = ($cut7TNCount / ($cut7TNCount + $cut7FPCount));


$sensitivity = ($truePosCount/($truePosCount + $falseNegCount));
$specificity = ($trueNegCount/($trueNegCount + $falsePosCount));
$ppv = ($truePosCount / ($truePosCount + $falsePosCount));
$npv = ($trueNegCount / ($trueNegCount + $falseNegCount));
$acc = (($truePosCount + $trueNegCount)/ $patientCount);

$minusSpec = 1-$specificity;
$minusSpec1 = 1-$cut1Spec;
$minusSpec2 = 1-$cut2Spec;
$minusSpec3 = 1-$cut3Spec;
$minusSpec4 = 1-$cut4Spec;
$minusSpec5 = 1-$cut5Spec;
$minusSpec6 = 1-$cut6Spec;
$minusSpec7 = 1-$cut7Spec;

$adjSens = $sensitivity * 300;
$adjSens1 = $cut1Sens * 300;
$adjSens2 = $cut2Sens * 300;
$adjSens3 = $cut3Sens * 300;
$adjSens4 = $cut4Sens * 300;
$adjSens5 = $cut5Sens * 300;
$adjSens6 = $cut6Sens * 300;
$adjSens7 = $cut7Sens * 300;


$specArray = array($specificity, $cut1Spec, $cut2Spec, $cut3Spec, $cut4Spec, $cut5Spec, $cut6Spec, $cut7Spec);
$xValArray = array();
$yValArray = array();
$cutPointArray = array();
rsort($specArray);

for ($j=0; $j<8; $j++) {
	$curSpec = $specArray[$j];
	if ($curSpec == $specificity) {
		$xValArray[$j] = $minusSpec * 300;
		$yValArray[$j] = 300 - $adjSens;
		$cutPointArray[$j] = $cutPoint;
	} elseif ($curSpec == $cut1Spec) {
		$xValArray[$j] = $minusSpec1 * 300;
		$yValArray[$j] = 300 - $adjSens1;
		$cutPointArray[$j] = .1;
	} elseif ($curSpec == $cut2Spec) {
		$xValArray[$j] = $minusSpec2 * 300;
		$yValArray[$j] = 300 - $adjSens2;
		$cutPointArray[$j] = .3;
	} elseif ($curSpec == $cut3Spec) {
		$xValArray[$j] = $minusSpec3 * 300;
		$yValArray[$j] = 300 - $adjSens3;
		$cutPointArray[$j] = .5;
	} elseif ($curSpec == $cut4Spec) {
		$xValArray[$j] = $minusSpec4 * 300;
		$yValArray[$j] = 300 - $adjSens4;
		$cutPointArray[$j] = .7;
	} elseif ($curSpec == $cut5Spec) {
		$xValArray[$j] = $minusSpec5 * 300;
		$yValArray[$j] = 300 - $adjSens5;
		$cutPointArray[$j] = .9;
	} elseif ($curSpec == $cut6Spec) {
		$xValArray[$j] = $minusSpec6 * 300;
		$yValArray[$j] = 300 - $adjSens6;
		$cutPointArray[$j] = .4;
	} elseif ($curSpec == $cut7Spec) {
		$xValArray[$j] = $minusSpec7 * 300;
		$yValArray[$j] = 300 - $adjSens7;
		$cutPointArray[$j] = .2;
	}
	
	
}

$xVal = $minusSpec * 300;
$yVal = 300 - $adjSens;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp = $areaTotal/90000;

$xVal = $minusSpec1 * 300;
$yVal = 300 - $adjSens1;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp1 = $areaTotal/90000;

$xVal = $minusSpec2 * 300;
$yVal = 300 - $adjSens2;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp2 = $areaTotal/90000;

$xVal = $minusSpec3 * 300;
$yVal = 300 - $adjSens3;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp3 = $areaTotal/90000;

$xVal = $minusSpec4 * 300;
$yVal = 300 - $adjSens4;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp4 = $areaTotal/90000;

$xVal = $minusSpec5 * 300;
$yVal = 300 - $adjSens5;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp5 = $areaTotal/90000;

$xVal = $minusSpec6 * 300;
$yVal = 300 - $adjSens6;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp6 = $areaTotal/90000;

$xVal = $minusSpec7 * 300;
$yVal = 300 - $adjSens7;
$area1 = ($xVal * (300-$yVal) * .5);
$area2 = ((300 - $xVal) * $yVal * 0.5);
$area3 = (300-$xVal)*(300-$yVal);
$areaTotal = $area1 + $area2 + $area3;
$rocEmp7 = $areaTotal/90000;

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
		<h4 align="center">ROC GRAPH</h4>
        <div align="center">
        <canvas id="roc" width="300" height="300" style="border:1px solid #000000;">
        </canvas>
        </div>
        <script>
            var canvas = document.getElementById("roc");
            var ctx = canvas.getContext("2d");
            ctx.moveTo(0,300);
            ctx.lineTo($xValArray[0],$yValArray[0]);
            ctx.stroke();
			ctx.lineTo($xValArray[1],$yValArray[1]);
			
            ctx.stroke();
			ctx.lineTo($xValArray[2],$yValArray[2]);
            ctx.stroke();
			ctx.lineTo($xValArray[3],$yValArray[3]);
            ctx.stroke();
			ctx.lineTo($xValArray[4],$yValArray[4]);
            ctx.stroke();
			ctx.lineTo($xValArray[5],$yValArray[5]);
            ctx.stroke();
			ctx.lineTo($xValArray[6],$yValArray[6]);
            ctx.stroke();
			ctx.lineTo($xValArray[7],$yValArray[7]);
            ctx.stroke();
            ctx.lineTo(300,0);
            ctx.stroke();
            ctx.setLineDash([5, 3]);
            ctx.moveTo(0,300);
            ctx.lineTo(300,0);
            ctx.stroke();
			ctx.fillText("x1 $cutPointArray[0]", $xValArray[0],$yValArray[0]);
			ctx.fillText("x2 $cutPointArray[1]", $xValArray[1],$yValArray[1]);
			ctx.fillText("x3 $cutPointArray[2]", $xValArray[2],$yValArray[2]);
			ctx.fillText("x4 $cutPointArray[3]", $xValArray[3],$yValArray[3]);
			ctx.fillText("x5 $cutPointArray[4]", $xValArray[4],$yValArray[4]);
			ctx.fillText("x6 $cutPointArray[5]", $xValArray[5],$yValArray[5]);
			ctx.fillText("x7 $cutPointArray[6]", $xValArray[6],$yValArray[6]);
			ctx.fillText("x8 $cutPointArray[7]", $xValArray[7],$yValArray[7]);
        </script>
        <div align="center">
        <br>ROC with Cutpoint: $cutPoint = $rocEmp<br><br>
        Excel Plot Point for Graph:<br>
        (0,0)<br>
        ($minusSpec, $sensitivity)<br>
        (1,1)<br>
        </div>
		<br><br>
		<div align="center">
		<strong align="center">Sensitivity and Specificity Table</strong>
		</div>
		<table align="center" border=".5">
			<tr>
				<td>Cut Point</td>
				<td>Sensitivity</td>
				<td>Specificity</td>
				<td>ROC</td>
			</tr>
			<tr>
				<td>0.1</td>
				<td>$cut1Sens</td>
				<td>$cut1Spec</td>
				<td>$rocEmp1</td>
			</tr>
			<tr>
				<td>0.2</td>
				<td>$cut7Sens</td>
				<td>$cut7Spec</td>
				<td>$rocEmp7</td>
			</tr>
			<tr>
				<td>0.3</td>
				<td>$cut2Sens</td>
				<td>$cut2Spec</td>
				<td>$rocEmp2</td>
			</tr>
			<tr>
				<td>0.4</td>
				<td>$cut6Sens</td>
				<td>$cut6Spec</td>
				<td>$rocEmp6</td>
			</tr>
			<tr>
				<td>0.5</td>
				<td>$cut3Sens</td>
				<td>$cut3Spec</td>
				<td>$rocEmp3</td>
			</tr>
			<tr>
				<td>0.7</td>
				<td>$cut4Sens</td>
				<td>$cut4Spec</td>
				<td>$rocEmp4</td>
			</tr>
			<tr>
				<td>0.9</td>
				<td>$cut5Sens</td>
				<td>$cut5Spec</td>
				<td>$rocEmp5</td>
			</tr>
		</table>
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