<?php

//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESION['username'];
} else {
    $username = '';
}

//find the current highest alg id in database to assign alg id for new algorithm
$algIDQuery = "SELECT DISTINCT AlgorithmID from ProLungdx.AlgorithmList Order by AlgorithmID desc";
$algIDResult = $conn->query($algIDQuery);
if (!$algIDResult) die ($conn->error);
$algIDResult->data_seek(0);
$algIDRow = $algIDResult->fetch_array(MYSQLI_NUM);
$highestID = $algIDRow[0];
$newID = $highestID + 1;

//get variables for algorithm construction
if(isset($_POST['algName'])) {
    $algName = $_POST['algName'];
    $algNotes = $_POST['algNotes'];
    $numAttributes = $_POST['numAttributes'];
    $algCutPoint = $_POST['algCutPoint'];
    
}

//create arrays to store names of points, attributes, rocs, and cut points
$pointValueArray = array();
$attributeValueArray = array();
$rocArray = array();
$cutPointArray = array();
for($j=1;$j<$numAttributes;$j++){
    $pointValueArray[$j] = $_POST["point$j"];
    $attributeValueArray[$j] = $_POST["attribute$j"];
    $rocArray[$j] = $_POST["maxROC$j"];
    $cutPointArray[$j] = $_POST["cutPoint$j"];
}

//create loop to store algorithm values
for($j=1; $j<$numAttributes; $j++){
    $insertValuesQuery = "INSERT INTO ProLungdx.AlgorithmValues (AlgorithmID, PointID, Attribute, MaxROC, CutPoint) VALUES ('$newID', '$pointValueArray[$j]', '$attributeValueArray[$j]', '$rocArray[$j]', '$cutPointArray[$j]')";
    $insertValuesResult = $conn->query($insertValuesQuery);
    if(!$insertValuesResult) die ($conn->error);
    
}

//create record in algorithm list table for the new algorithm
$insertAlgQuery = "INSERT INTO ProLungdx.AlgorithmList (AlgorithmID, AlgorithmName, AlgorithmNotes, CutPoint) Values ('$newID', '$algName', '$algNotes', '$algCutPoint')";
$insertAlgResult = $conn->query($insertAlgQuery);
if(!$insertAlgResult) die ($conn->error);

 echo "<script> window.location.assign('AlgorithmList.php');</script>";


$conn->close();
?>