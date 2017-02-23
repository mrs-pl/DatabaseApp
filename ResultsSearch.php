<?php

//include db file, start session, initiate connection, find username
require_once 'dbInfo.php';
require_once 'checkSession.php';
$conn = new mysqli($hn, $un, $pw, $db);
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
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
                    Query Results
                </h2>
                
                <form action="Results.php" method="post">
                    <table>
                        <tr>
                            <td>Algorithm Name</td>
                            <td>
                                <select name="algName">
                                    <option selected disabled>Choose Algorithm</option>
                                    <option>All Algorithms</option>
                                
_END;
    
$algListQuery = "SELECT AlgorithmName From ProLungdx.AlgorithmList";
$algListResult = $conn->query($algListQuery);
if(!$algListResult) die ($conn->error);
$algListRows = $algListResult->num_rows;

for($j=0; $j<$algListRows; $j++){
    
    $algListResult->data_seek($j);
    $algListRow = $algListResult->fetch_array(MYSQLI_NUM);
    echo<<<_END
                            
                                    <option>$algListRow[0]</option>
_END;
}


echo<<<_END
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>All Patients or Query Patients</td>
                            <td>
                                <select name="allQuery">
                                    <option selected disabled>Select Patient Population</option>
                                    <option>All Patients</option>
                                    <option>Query Patients</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Number of Attributes to Query</td>
                            <td>
                                <select name="numAttributes">
                                    <option selected disabled>Select # of Query Attributes</option>
                                
_END;

    for($j=1; $j<21; $j++){
        echo<<<_END
                                <option>$j</option>
        
        
_END;
    }

echo<<<_END
                                </select>
                            </td>
                        </tr>
 
                    </table><br>
                    <input type="submit" value="Submit">
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

//close database connection
$conn->close();
?>