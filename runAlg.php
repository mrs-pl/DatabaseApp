<?php

require 'dbInfo.php';
session_start();
$conn= new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}
if(isset($_POST['algName'])){
    //take the passed Algorithm Name from the form and use to query database and find appropriate Algorithm ID Number
    $algName = $_POST['algName'];
    $algNumQuery = "SELECT AlgorithmID From ProLungdx.AlgorithmList Where AlgorithmList.AlgorithmName = '$algName'";
    $algNumResult = $conn->query($algNumQuery);
    if(!$algNumResult) die ($conn->error);
    $algNumResult->data_seek(0);
    $algNumRow = $algNumResult->fetch_array(MYSQLI_NUM);
    $algId = $algNumRow[0];
}


$subjectsQuery = "SELECT DISTINCT SubjectID From ProLungdx.PointCalc";
$subjectsResult = $conn->query($subjectsQuery);
if(!$subjectsResult) die ($conn->error);
$subjectsRows = $subjectsResult->num_rows;
for($j=0; $j<$subjectsRows; $j++){
    
    $subjectsResult->data_seek($j);
    $subjectsRow = $subjectsResult->fetch_array(MYSQLI_NUM);
    $subjects[$j] = $subjectsRow[0];
}




function runAlgorithm($algId, $subjectID) {
    
    //echo "$subjectID <br>";
    require 'dbInfo.php';
    $conn= new mysqli($hn, $un, $pw, $db);
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        $username = '';
    }
    

    $algQuery = "SELECT PointID, Attribute, MaxROC, CutPoint From AlgorithmValues where AlgorithmValues.AlgorithmID = '$algId'";
    $algResult = $conn->query($algQuery);
    if(!$algResult) die ($conn->error);
    $algRows = $algResult->num_rows;



    //initialize arrays to store info about algorithm
    $pointArray = array();
    $attributeArray = array();
    $rocArray = array();
    $cutPointArray = array();
    $compSum = 0;
    $sumWeights = 0;


    //create a loop to populate algorithm arrays
    for($j=0; $j<$algRows; $j++) {
        $algResult->data_seek($j);
        $algRow = $algResult->fetch_array(MYSQLI_NUM);
        $pointArray[] = $algRow[0];
        $attributeArray[] = $algRow[1];
        $rocArray[] = $algRow[2];
        $cutPointArray[] = $algRow[3];
    }

    //loop through arrays to use alg information to query correct patient data
    for($j=0; $j<$algRows; $j++) {
        if($attributeArray[$j] == "Drop") {
            $patientQuery = "SELECT DropValue FROM ProLungdx.PointCalc WHERE SubjectID = '$subjectID' AND PointID = '$pointArray[$j]'";
        } else {
            $patientQuery = "SELECT $attributeArray[$j] FROM ProLungdx.PointCalc WHERE SubjectID = '$subjectID' AND PointID = '$pointArray[$j]'";
        }

        $patientResult = $conn->query($patientQuery);
        if(!$patientResult) die ($conn->error);
        $patientRow = $patientResult->fetch_array(MYSQLI_NUM);

        $patientValue = $patientRow[0];
        $cutPoint = $cutPointArray[$j];


        if($attributeArray[$j] == "Drop"){
            //If the attribute is drop then the ROC value is added if the Patient Value is greater than the cut point
            if($patientValue >= $cutPoint) {
                $compSum = $compSum + $rocArray[$j];
                $sumWeights = $sumWeights + $rocArray[$j];
            } else{
                $compSum = $compSum + 0;
                $sumWeights = $sumWeights + $rocArray[$j];
            }

        } elseif ($attributeArray[$j] == "Fall"){
            //If the attribute is Fall then the ROC value is added if the Patient Value is greater than the cut point
            if($patientValue >= $cutPoint) {
                $compSum = $compSum + $rocArray[$j];
                $sumWeights = $sumWeights + $rocArray[$j];
            } else{
                $compSum = $compSum + 0;
                $sumWeights = $sumWeights + $rocArray[$j];
            }
        }
        else {
            //For all other attributes the ROC value is added if the Patient Value is less than the cut pointd
            if($patientValue <= $cutPoint) {
                $compSum = $compSum + $rocArray[$j];
                $sumWeights = $sumWeights + $rocArray[$j];
            } else{
                $compSum = $compSum + 0;
                $sumWeights = $sumWeights + $rocArray[$j];
            }


        }


    }

    $compScore = $compSum / $sumWeights;
    //echo "EPN Comp Score: $compScore <br>";

    $diagnosisQuery = "SELECT MalignantBenign From ProLungdx.Patient where SubjectID = '$subjectID'";
    $diagnosisResult = $conn->query($diagnosisQuery);
    if(!$diagnosisResult) die ($conn->error);
    $diagnosisRow = $diagnosisResult->fetch_array(MYSQLI_NUM);
    $diagnosis = $diagnosisRow[0];
    //echo "Diagnosis: $diagnosis <br>";

    $cutPointQuery = "SELECT CutPoint From ProLungdx.AlgorithmList where AlgorithmID = '$algId'";
    $cutPointResult = $conn->query($cutPointQuery);
    if(!$cutPointResult) die ($conn->error);
    $cutPointRow = $cutPointResult->fetch_array(MYSQLI_NUM);
    $cutPoint = $cutPointRow[0];
    //echo "Cut Point: $cutPoint <br>";

    if($compScore >= $cutPoint) {
        $epnPrediction = "Malignant";
    } else {
        $epnPrediction = "Benign";
    }
	if($compScore > 0.1) {
		$cut1Pred = "Malignant";
	} else {
		$cut1Pred = "Benign";
	}
	if($compScore > 0.3) {
		$cut2Pred = "Malignant";
	} else {
		$cut2Pred = "Benign";
	} 
	if($compScore > 0.5) {
		$cut3Pred = "Malignant";
	} else {
		$cut3Pred = "Benign";
	}
	if($compScore > 0.7) {
		$cut4Pred = "Malignant";
	} else {
		$cut4Pred = "Benign";
	}
	if($compScore > 0.9) {
		$cut5Pred = "Malignant";
	} else {
		$cut5Pred = "Benign";
	}
	if($compScore > 0.4) {
		$cut6Pred = "Malignant";
	} else {
		$cut6Pred = "Benign";
	}
	if($compScore > 0.2) {
		$cut7Pred = "Malignant";
	} else {
		$cut7Pred = "Benign";
	}
    //echo "EPN Prediction: $epnPrediction <br>";

    //Logical test to compare the algorithm prediction with the actual diagnosis and determine the performance
    if($epnPrediction == "Malignant"){
        if($diagnosis == "Malignant"){
            $performance = "True Positive";
        } elseif($diagnosis == "Benign") {
            $performance = "False Positive";
        } else{
            $performance = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $performance = "False Negative";
        } elseif($diagnosis == "Benign") {
            $performance = "True Negative";
        } else {
            $performance = "No Diagnosis";
        }
    }
	if($cut2Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut1Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut1Perf = "False Positive";
        } else{
            $cut1Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut1Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut1Perf = "True Negative";
        } else {
            $cut1Perf = "No Diagnosis";
        }
    }
	if($cut2Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut2Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut2Perf = "False Positive";
        } else{
            $cut2Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut2Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut2Perf = "True Negative";
        } else {
            $cut2Perf = "No Diagnosis";
        }
    }
	if($cut3Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut3Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut3Perf = "False Positive";
        } else{
            $cut3Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut3Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut3Perf = "True Negative";
        } else {
            $cut3Perf = "No Diagnosis";
        }
    }
	if($cut4Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut4Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut4Perf = "False Positive";
        } else{
            $cut4Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut4Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut4Perf = "True Negative";
        } else {
            $cut4Perf = "No Diagnosis";
        }
    }
	if($cut5Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut5Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut5Perf = "False Positive";
        } else{
            $cut5Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut5Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut5Perf = "True Negative";
        } else {
            $cut5Perf = "No Diagnosis";
        }
    }
	if($cut6Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut6Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut6Perf = "False Positive";
        } else{
            $cut6Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut6Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut6Perf = "True Negative";
        } else {
            $cut6Perf = "No Diagnosis";
        }
    }
	if($cut7Pred == "Malignant"){
        if($diagnosis == "Malignant"){
            $cut7Perf = "True Positive";
        } elseif($diagnosis == "Benign") {
            $cut7Perf = "False Positive";
        } else{
            $cut7Perf = "No Diagnosis";
        }
    } else{
        if($diagnosis == "Malignant"){
            $cut7Perf = "False Negative";
        } elseif($diagnosis == "Benign") {
            $cut7Perf = "True Negative";
        } else {
            $cut7Perf = "No Diagnosis";
        }
    }
    //echo "Performance: $performance <br><br>";
    
    //search for database records of the stored prediction and performance for specific patient and specific algorithm
    $searchResultsQuery = "SELECT * From ProLungdx.Calculations Where Calculations.SubjectID = '$subjectID' AND Calculations.AlgID = '$algId'";
    $searchResults = $conn->query($searchResultsQuery);
    if(!$searchResults) die ($conn->error);
    $searchRows = $searchResults->num_rows;
    if($searchRows == 0){
        //no previous record exists for this patient and this algorithm. Create a new record
        $insertCalcQuery = "INSERT INTO ProLungdx.Calculations 
		(SubjectID, AlgID, AlgScore, Prediction, Performance, Cut1Pred, Cut1Perf, Cut2Pred, Cut2Perf, Cut3Pred, Cut3Perf, Cut4Pred, Cut4Perf, Cut5Pred, Cut5Perf, Cut6Perf, Cut7Perf) 
		Values ('$subjectID', '$algId', '$compScore', '$epnPrediction', '$performance', '$cut1Pred', '$cut1Perf', '$cut2Pred', '$cut2Perf', '$cut3Pred', '$cut3Perf', '$cut4Pred', '$cut4Perf', '$cut5Pred', '$cut5Perf', '$cut6Perf', '$cut7Perf')";
        $insertCalcResult = $conn->query($insertCalcQuery);
    } else{
        //a record for this patient has previously been created. Update the database to the current values in case any patient information has been changed since last calculation
        
        $updateCalcQuery = "UPDATE ProLungdx.Calculations SET AlgScore = '$compScore', Prediction='$epnPrediction', Performance = '$performance', Cut1Pred = '$cut1Pred', Cut1Perf = '$cut1Perf', Cut2Pred = '$cut2Pred', 
							Cut2Perf = '$cut2Perf', Cut3Pred = '$cut3Pred', Cut3Perf = '$cut3Perf', Cut4Pred = '$cut4Pred', Cut4Perf = '$cut4Perf', Cut5Pred = '$cut5Pred', Cut5Perf = '$cut5Perf', Cut6Perf = '$cut6Perf', Cut7Perf = '$cut7Perf' WHERE SubjectID = '$subjectID' AND AlgID = '$algId'";
        $updateCalcResults = $conn->query($updateCalcQuery);
        if(!$updateCalcResults) die ($conn->error);
    }
    
   
    
    $conn->close();
}


for($j=0; $j<$subjectsRows; $j++){
    runAlgorithm($algId, $subjects[$j]);
}
    

//output main structure, heading, etc of html page
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
                    <a href="login.php">Login</a>
                </div>
                <div id="loggedIn" align="right" width="50%">
                    <a id="admin" href="Admin.php">Admin</a>
                    <a href="user_profile.php">$username</a>
                    <a href="logout.php">Log out</a>
                </div>
            </div>
            
        </head>
        <body> 
            <div id="body" align="center" style="float:left; width:100%">
                <h2 align="center">
                    Algorithm Successfully Applied to $subjectsRows Patients
                </h2>
                
                <a href="ResultsSearch.php">View Results</a>
                
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


?>