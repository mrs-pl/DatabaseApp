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

//initialize url variable from post information
$url = $_POST['url'];

//check to see which button the user pressed
if (isset($_POST['allVisible'])){
    //User wants to set all attributes to be visible. exequte sql query to set all attributes to visible for that user
    $setAllVisibleQuery = "UPDATE UserPreferences set IsVisible='Yes' where UserName = '$username'";
    $result = $conn->query($setAllVisibleQuery);
    if (!$result) die ($conn->error);
} elseif (isset($_POST['allNotVisible'])){
    //User wants to set all attributes to not be visible. execute sql query to set all attributes except for SubjectID to not be visible for that user
    $setAllNotVisibleQuery = "UPDATE UserPreferences set UserPreferences.IsVisible='No' where UserPreferences.UserName = '$username' AND UserPreferences.AttributeNumber <> 1";
    $result = $conn->query($setAllNotVisibleQuery);
    if (!$result) die ($conn->error);
} elseif (isset($_POST['allExport'])) {
    //User wants to set all attributes to be exported. execute sql query to set all attributes to be exported for that user
    $setAllExportedQuery = "UPDATE UserPreferences set IsExported='Yes' where UserName = '$username'";
    $result = $conn->query($setAllExportedQuery);
    if (!$result) die ($conn->error);
} elseif (isset($_POST['allNotExport'])){
    //User wants to set all attributes to be exported. Execute sql query to set all attributes except for SubjectID to not be exported for that user
    $setAllNotExportedQuery = "UPDATE UserPreferences set UserPreferences.IsExported='No' where UserPreferences.UserName = '$username' AND UserPreferences.AttributeNumber <> 1";
    $result = $conn->query($setAllNotExportedQuery);
    if (!$result) die ($conn->error);
} else {
    //User has made specific changes to their visible and export preferences. Loop through all attributes and execute sql query to update the user preference accordingly
    if (isset($_POST['attributeNum'])) {
        $attributes = $_POST['attributeNum'];
        
    }
    for ($j=0; $j<$attributes; $j++){
        $attributeNumber=$j+1;
        $name = "isExported".$j;
        $name2 = "isVisible".$j;
        $isVisible = $_POST["$name2"];
        $isExported = $_POST["$name"];
        $query = "UPDATE UserPreferences set IsVisible ='$isVisible' where UserName = '$username' AND AttributeNumber = '$attributeNumber'";
        $query2 = "UPDATE UserPreferences set IsExported ='$isExported' where UserName = '$username' AND AttributeNumber = '$attributeNumber'";
        $result = $conn->query($query);
        if(!$result) die ($conn->error);
        $result2 = $conn->query($query2);
        if(!$result2) die ($conn->error);

    }
    
}

//redirect to the user preferences page after finishing
echo "<script> window.location.assign('$url'); </script>";
?>