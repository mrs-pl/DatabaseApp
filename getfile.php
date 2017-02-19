<html>
<head>
<title>Process Uploaded File</title>
</head>
<body>
<?php
    
require_once 'dbInfo.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    //$userId = $_SESSION['userId'];
} else {
    $username = '';
}

    echo $username;
    echo "TEST";
    if(isset($_FILES['upload'])){
        echo "IS SET";
    } else {
        echo "Notset";
    }
    echo $_FILES["upload"]["name"];
    $documentName = $_FILES['upload']['name'];
    $filename = time() . $_FILES['upload']['name'];
move_uploaded_file ($_FILES['upload'] ['tmp_name'], 
       "uploads/$filename");
    
    /*$query = "INSERT into Upload (UploadID, User, Section, FileName, Document) VALUES (NULL, '$userId', '$sectionId', '$documentName', '$filename')";
    $result = $conn->query($query);
    if(!$result) die ($conn->error);
    
    echo "<script> window.location.assign('uploadSuccess.php'); </script>";
        */
        
        
function get_post($conn, $var) {
	return $conn->real_escape_string($_POST[$var]);
}
?>
</body>
</html>