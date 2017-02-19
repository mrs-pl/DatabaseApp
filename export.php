<?php
//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}


//check which type of query is being passed in, only patients or patients and scan data
if (isset($_POST['query'])) {
    //only patient data should be exported
    echo $_POST['query'];
    $query = $_POST['query'];
    $url = $_POST['url'];

    
    //initiate the query that will have its results exported
    $finalQuery = "SELECT ";
    
    //create the 'where' clause of the sql query
    $exportQuery = substr($query, 8);
    $exportPrefQuery = "SELECT PatientAttributeNames.AttributeName From UserPreferences Join PatientAttributeNames on UserPreferences.AttributeNumber = PatientAttributeNames.AttributeNumber where UserPreferences.UserName = '$username' and UserPreferences.IsExported = 'Yes'";
    
    //create and execute query to get attribute names of all attributes that should be exported based on user preferences
    $exportPrefResult = $conn->query($exportPrefQuery);
    if(!$exportPrefResult) die ($conn->error);
    $exportPrefRows = $exportPrefResult->num_rows;
    $exportAttributes = array();
    
    //loop through query results for exported attributes and construct the sql query that will only query those attributes
    for ($j=0; $j<$exportPrefRows; $j++){
        $exportPrefResult->data_seek($j);
        $exportPrefRow = $exportPrefResult->fetch_array(MYSQLI_NUM);
        $exportAttributes[$j]=$exportPrefRow[0];
        if ($j<$exportPrefRows-1){
            $finalQuery = $finalQuery.$exportPrefRow[0].", ";
        } else {
            $finalQuery = $finalQuery.$exportPrefRow[0];
        }
    }
    
    //combine selected attributes with where clause
    $finalQuery = $finalQuery.$exportQuery;


} elseif (isset($_POST['resultsQuery'])){
    $finalQuery = $_POST['resultsQuery'];
    $url = $_POST['url'];
    echo "TEST";
    
} elseif (isset($_POST['query2'])){
    //patient and scan data should be exported
    $url = $_POST['url'];
    
    //initialize variable for final query
    $finalQuery = "SELECT ";
    $query = $_POST['query2'];
    
    //create string with where clause
    $exportQuery = substr($query, 8);
    //create and execute query to select exportable patient attributes
    $exportPrefQuery = "SELECT PatientAttributeNames.AttributeName From UserPreferences Join PatientAttributeNames on UserPreferences.AttributeNumber = PatientAttributeNames.AttributeNumber where UserPreferences.UserName = '$username' and UserPreferences.IsExported = 'Yes'";
    $exportPrefResult = $conn->query($exportPrefQuery);
    if(!$exportPrefResult) die ($conn->error);
    $exportPrefRows = $exportPrefResult->num_rows;
    
    //loop through attribute query results and construct sql query to select only the correct patient attributes
    for ($j=0; $j<$exportPrefRows; $j++){
        $exportPrefResult->data_seek($j);
        $exportPrefRow = $exportPrefResult->fetch_array(MYSQLI_NUM);
        $finalQuery = $finalQuery."Patient.".$exportPrefRow[0].", ";
    
    }
    
    //create and execute query to select scan data attributes
    $scanAttributeNamesQuery = "SELECT * from ScanDataAttributeNames";
    $scanAttributeResult = $conn->query($scanAttributeNamesQuery);
    if(!$scanAttributeResult) die ($conn->error);
    $scanAttributeRows = $scanAttributeResult->num_rows;
    
    //loop through query results and add scan data attributes to final query
    for ($j=0; $j<$scanAttributeRows; $j++){
        $scanAttributeResult->data_seek($j);
        $scanAttributeRow = $scanAttributeResult->fetch_array(MYSQLI_NUM);
        if ($j<$scanAttributeRows-1){
            $finalQuery = $finalQuery."ScanData.".$scanAttributeRow[1].", ";
        } else {
            $finalQuery = $finalQuery."ScanData.".$scanAttributeRow[1];
        }
    }
    
    //combine attributes and where clause strings
    $finalQuery = $finalQuery.$exportQuery;
}

//execute final query and export results to export.csv
$result = $conn->query($finalQuery);
if(!result) die ($conn->error);
$rows = $result->num_rows;
$headers = $result->fetch_fields();
foreach($headers as $header) {
    $head[] = $header->name;
}
$fp = fopen('export.csv', 'w');

if ($fp && $result) {
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputcsv($fp, array_values($head)); 
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        fputcsv($fp, array_values($row));
    }
    
}

//open exported file in a new tab, causing it to be downloaded to computer
echo "<script> window.open('export.csv', '_blank'); win.focus();</script>";
//redirect after completing
echo "<script> window.location.assign('$url'); </script>";

?>