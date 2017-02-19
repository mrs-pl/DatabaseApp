<?php
//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}

//create and execute sql query to delete saved query
if(isset($_POST['queryId'])){
    $queryID = $_POST['queryId'];
    $deleteQuery = "DELETE FROM ProLungdx.SavedQueries where SavedQueries.ID = '$queryID'";
    $deleteResult = $conn->query($deleteQuery);
    if(!deleteResult) die ($conn->error);
    echo "<script> window.location.assign('user_profile.php'); </script>";
}
?>