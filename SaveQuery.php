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

//assign POST data from form to variables
if(isset($_POST['query'])) {
    $queryToSave = $_POST['query'];
    $queryCriteria = $_POST['queryCriteria'];
    $attribute1 = $_POST['attribute1'];
    $attribute2 = $_POST['attribute2'];
    $attribute3 = $_POST['attribute3'];
    $attribute4 = $_POST['attribute4'];
    $attribute5 = $_POST['attribute5'];
    $attribute6 = $_POST['attribute6'];
    $attribute7 = $_POST['attribute7'];
    $attribute8 = $_POST['attribute8'];
    $attribute9 = $_POST['attribute9'];
    $attribute10 = $_POST['attribute10'];
    $operator1 = $_POST['operator1'];
    $operator2 = $_POST['operator2'];
    $operator3 = $_POST['operator3'];
    $operator4 = $_POST['operator4'];
    $operator5 = $_POST['operator5'];
    $operator6 = $_POST['operator6'];
    $operator7 = $_POST['operator7'];
    $operator8 = $_POST['operator8'];
    $operator9 = $_POST['operator9'];
    $operator10 = $_POST['operator10'];
    $input1 = $_POST['input1'];
    $input2 = $_POST['input2'];
    $input3 = $_POST['input3'];
    $input4 = $_POST['input4'];
    $input5 = $_POST['input5'];
    $input6 = $_POST['input6'];
    $input7 = $_POST['input7'];
    $input8 = $_POST['input8'];
    $input9 = $_POST['input9'];
    $input10 = $_POST['input10'];
}

//create html structure, header, etc
//create html forms to pass variable information into db
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
            <div id="contentBox" style="margin:0px auto; width:100%" align="center">

                <h1>New Saved Query</h1>
                <form action="SaveQuery.php" method="post">
                    <input type="hidden" name="savedQuery" value="$queryToSave">
                    Query Name: <input type="text" name="queryName"><br><br>
                    Notes About Query: <textarea name ="queryNotes" cols="25" rows="4">$queryCriteria</textarea><br><br>
                    <input type="hidden" value="$attribute1" name="attribute1">
                    <input type="hidden" value="$attribute2" name="attribute2">
                    <input type="hidden" value="$attribute3" name="attribute3">
                    <input type="hidden" value="$attribute4" name="attribute4">
                    <input type="hidden" value="$attribute5" name="attribute5">
                    <input type="hidden" value="$attribute6" name="attribute6">
                    <input type="hidden" value="$attribute7" name="attribute7">
                    <input type="hidden" value="$attribute8" name="attribute8">
                    <input type="hidden" value="$attribute9" name="attribute9">
                    <input type="hidden" value="$attribute10" name="attribute10">
                    <input type="hidden" value="$operator1" name="operator1">
                    <input type="hidden" value="$operator2" name="operator2">
                    <input type="hidden" value="$operator3" name="operator3">
                    <input type="hidden" value="$operator4" name="operator4">
                    <input type="hidden" value="$operator5" name="operator5">
                    <input type="hidden" value="$operator6" name="operator6">
                    <input type="hidden" value="$operator7" name="operator7">
                    <input type="hidden" value="$operator8" name="operator8">
                    <input type="hidden" value="$operator9" name="operator9">
                    <input type="hidden" value="$operator10" name="operato10">
                    <input type="hidden" value="$input1" name="input1">
                    <input type="hidden" value="$input2" name="input2">
                    <input type="hidden" value="$input3" name="input3">
                    <input type="hidden" value="$input4" name="input4">
                    <input type="hidden" value="$input5" name="input5">
                    <input type="hidden" value="$input6" name="input6">
                    <input type="hidden" value="$input7" name="input7">
                    <input type="hidden" value="$input8" name="input8">
                    <input type="hidden" value="$input9" name="input9">
                    <input type="hidden" value="$input10" name="input10">
                    <input type="submit" value="Save Query">
                </form>
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

//check if form has been submitted, then create and execute sql query to store query components into saved query db table
if(isset($_POST['queryName'])){
    $savedQuery = $_POST['savedQuery'];
    $queryName = $_POST['queryName'];
    $queryNotes = $_POST['queryNotes'];
    $attribute1 = $_POST['attribute1'];
    $attribute2 = $_POST['attribute2'];
    $attribute3 = $_POST['attribute3'];
    $attribute4 = $_POST['attribute4'];
    $attribute5 = $_POST['attribute5'];
    $attribute6 = $_POST['attribute6'];
    $attribute7 = $_POST['attribute7'];
    $attribute8 = $_POST['attribute8'];
    $attribute9 = $_POST['attribute9'];
    $attribute10 = $_POST['attribute10'];
    $operator1 = $_POST['operator1'];
    $operator2 = $_POST['operator2'];
    $operator3 = $_POST['operator3'];
    $operator4 = $_POST['operator4'];
    $operator5 = $_POST['operator5'];
    $operator6 = $_POST['operator6'];
    $operator7 = $_POST['operator7'];
    $operator8 = $_POST['operator8'];
    $operator9 = $_POST['operator9'];
    $operator10 = $_POST['operator10'];
    $input1 = $_POST['input1'];
    $input2 = $_POST['input2'];
    $input3 = $_POST['input3'];
    $input4 = $_POST['input4'];
    $input5 = $_POST['input5'];
    $input6 = $_POST['input6'];
    $input7 = $_POST['input7'];
    $input8 = $_POST['input8'];
    $input9 = $_POST['input9'];
    $input10 = $_POST['input10'];
    $saveQuery = "INSERT INTO ProLungdx.SavedQueries (UserName, QueryName, QueryNotes, Attribute1, Attribute2, Attribute3, Attribute4, Attribute5, Attribute6, Attribute7, Attribute8, Attribute9, Attribute10, Operator1, Operator2, Operator3, Operator4, Operator5, Operator6, Operator7, Operator8, Operator9, Operator10, Input1, Input2, Input3, Input4, Input5, Input6, Input7, Input8, Input9, Input10) values ('$username', '$queryName', '$queryNotes', '$attribute1', '$attribute2', '$attribute3', '$attribute4', '$attribute5', '$attribute6', '$attribute7', '$attribute8', '$attribute9', '$attribute10', '$operator1', '$operator2', '$operator3', '$operator4', '$operator5', '$operator6', '$operator7', '$operator8', '$operator9', '$operator10', '$input1',  '$input2',  '$input3',  '$input4',  '$input5',  '$input6',  '$input7',  '$input8',  '$input9',  '$input10')";
    $saveResult=$conn->query($saveQuery);
    if(!$saveResult) die ($conn->error);
    
    
    //redirect to user_profile page
    echo "<script> window.location.assign('user_profile.php');</script>";
}


//close database connection
$conn->close();
?>