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
    <html class="gr__10_1_10_85">
     <link rel="stylesheet" type="text/css" href="ProLungDatabaseStyles.css">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" type="text/css" href="ProLungDatabaseStyles.css">
            <title>
                ProLungdx Clinical Database
            </title>
            <!-- JavaScript to load jquery -->
            <script src="./Home_files/jquery.min.js.download"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
            <!-- JavaScript to load fonts from Typekit -->
            <script src="https://use.typekit.net/gyp1njd.js"></script>
            <script>try{Typekit.load({ async: true });}catch(e){}</script>
        </head>
		<body data-gr-c-s-loaded="true">
			
            <div id="header" width="100%">
                <div id="nav" style="float:left; margin: 25px;" width="50%">
                    <a class="button" href="PatientList.php">Patients</a>
					<a class="button" href="AttributeList.php">Attributes</a>  
					<a class="button" href="AlgorithmList.php">Algorithms</a>  
					<a class="button" href="ResultsSearch.php"> Results </a>   
                </div>
				
				<div id="logo" align="center" style="display: inline-block; width: 460px; height: 70px; margin-top: 10px; margin-left: 12%;">
                <a href="home.php"><img src="Images/ProLungDatabaseLogo.png" height="70" width="460"></a>
				</div>
			
                <div id="loginNeeded" align="right" style="float:right; margin: 25px;" width="50%">
                    <a class="button" href="login.php">Login</a>
                </div>
                <div id="loggedIn" align="right" width="50%" style="display: none; float: right; margin: 25px;">
                    <a class="button" id="admin" href="Admin.php">Admin</a>
                    <a class="button" href="user_profile.php">$username</a>
                    <a class="button" href="logout.php">Log out</a>
                </div>
            </div>
            </div>
        
        
        <br><br><br><br><br>
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

$dataSetQuery = "SELECT DISTINCT DataSet from ProLungdx.Patient";
$dataSetResult = $conn->query($dataSetQuery);
if(!$dataSetResult) die ($conn->error);
$dataSetRows = $dataSetResult->num_rows;

echo<<<_END
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Patient Population</td>
                            <td>
                                <select name="allQuery">
                                    <option selected disabled>Select Patient Population</option>
                                    <option>All Patients</option>
_END;

for($j=0; $j<$dataSetRows; $j++) {
	$dataSetResult->data_seek($j);
	$dataSetRow = $dataSetResult->fetch_array(MYSQLI_NUM);
	
	echo<<<_END
									<option value="$dataSetRow[0]">$dataSetRow[0]</option>
_END;
	
}


echo<<<_END
                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Attributes to Query</td>
                            <td>
                                
	
	


                               
								<br>
_END;

for($x=1; $x<6; $x++){
    echo<<<_END
                    <select name="attribute$x">
                        <option selected disabled>Choose Attribute</option>
_END;


    //create and execute sql query to return all attribute names
    $query = "SELECT * from PatientAttributeNames order by AttributeName ASC";
    $result = $conn->query($query);
    if(!$result) die ($conn->error);
    $rows = $result->num_rows;

    //loop through query results and create an option in the drop down menu for each attribute
    for($j=0; $j<$rows; $j++){
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);
    
        echo<<<_END
        <option value ="$row[1]">$row[1]</option>
_END;
	}

    //create and display the rest of the query builder
echo<<<_END
                    </select>
                    that is 
                    <select name="operator$x">
                        <option selected disabled>Choose Operator</option>
                        <option value = "=">Equal to</option>
                        <option value = ">">Greather Than</option>
                        <option value = "<">Less than</option>
                        <option value = "like">Like</option>
                    </select>
                    <input type="text" name="input$x">
                    <br>
_END;
}

echo <<<_END
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