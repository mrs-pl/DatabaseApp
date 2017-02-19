<?php

require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
/*$algs = array (
    array(
        array("SubjID", "Malignant"),
        array("1", "2")
    ),
    array(
        array("SubjID2", "Malignant2"),
        array("11", "22")
    )
);*/
/*$algs = array (array(array()));
$algs[0][0][0] = "TEST";
echo $algs[0][0][0]."<br>";
$testArray = array(1,2,3);
$algs[0][1] = $testArray;
echo $algs[0][1][2];
*/

$algIDQuery = "SELECT AlgorithmID From ProLungdx.AlgorithmList";

$algIDResults = $conn->query($algIDQuery);
if(!$algIDResults) die ($conn->error);
$algIDRows = $algIDResults->num_rows;
$algs = array(array(array()));


for($j=0;$j<$algIDRows;$j++){
    $algIDResults->data_seek($j);
    $algIDRow = $algIDResults->fetch_array(MYSQLI_NUM);
    $algID = $algIDRow[0];
    $query = "SELECT Patient.SubjectID, Patient.Sex, Patient.MalignantBenign, Calculations.AlgScore, Calculations.Prediction, Calculations.Performance from ProLungdx.Patient Join ProLungdx.Calculations on Patient.SubjectID = Calculations.SubjectID WHERE Calculations.AlgID = '$algID'";
    $queryResult = $conn->query($query);
    if(!$queryResult) die ($conn->error);
    $queryRows = $queryResult->num_rows;
    for($i=0;$i<$queryRows;$i++){
        $queryResult->data_seek($i);
        $queryArray = $queryResult->fetch_array(MYSQLI_NUM);
        $algs[$j][$i] = $queryArray;
    
    }
}
echo $algs[0][0][3];
?>
