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

//initialize variable to store the number of patients returned by query
$patientCount = 0;

//create basic html structure, header, etc
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
        <div id="contentBox" style="margin:0px auto; width:100%">
            <div id="column1" style="float:left; margin:0; width:100%;">
                <form action="PatientQuery.php" method="post">
                    Query patients with: <br>
					
_END;

//create the attribute form 10 times
for($x=1; $x<11; $x++){
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
                    <select name="andOr$x">
                        <option selected>And</option>
                        <option>Or</option>
                    </select>
                    <br>
_END;
}

//create remainder of html page and submit button
echo<<<_END
                    
                    <input type="submit" name="querySubmit">
                    <br>
            </div>
            
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

//check if submit query has been clicked then build sql query using the form input
if(isset($_POST['querySubmit'])){
    if(isset($_POST['attribute1'])){
		$attribute1 = $_POST['attribute1'];
		$operator1 = $_POST['operator1'];
		$input1 = $_POST['input1']; 
		if($operator1 == 'like') {
			$query1 = "Select * from Patient where Patient.$attribute1 $operator1 '%$input1%'";
		} else{
			$query1 = "Select * from Patient where Patient.$attribute1 $operator1'$input1'";
		}
	} else {
		$attribute1 = '';
		$operator1 = '';
		$input1 = '';
	}
	if(isset($_POST['attribute2'])){
		$attribute2 = $_POST['attribute2'];
		$operator2 = $_POST['operator2'];
		$input2 = $_POST['input2'];
	} else {
		$attribute2 = '';
		$operator2 = '';
		$input2 = '';
	}
	if(isset($_POST['attribute3'])) {
		$attribute3 = $_POST['attribute3'];
		$operator3 = $_POST['operator3'];
		$input3 = $_POST['input3'];
	} else {
		$attribute3 = '';
		$operator3 = '';
		$input3 = '';
	}
	if(isset($_POST['attribute4'])) {
		$attribute4 = $_POST['attribute4'];
		$operator4 = $_POST['operator4'];
		$input4 = $_POST['input4'];
	} else {
		$attribute4 = '';
		$operator4 = '';
		$input4 = '';
	}
	if(isset($_POST['attribute5'])) {
		$attribute5 = $_POST['attribute5'];
		$operator5 = $_POST['operator5'];
		$input5 = $_POST['input5'];
	} else {
		$attribute5 = '';
		$operator5 = '';
		$input5 = '';
	}
	if(isset($_POST['attribute6'])) {
		$attribute6 = $_POST['attribute6'];
		$operator6 = $_POST['operator6'];
		$input6 = $_POST['input6'];
	} else {
		$attribute6 = '';
		$operator6 = '';
		$input6 = '';
	}
	if(isset($_POST['attribute7'])) {
		$attribute7 = $_POST['attribute7'];
		$operator7 = $_POST['operator7'];
		$input7 = $_POST['input7'];
	} else {
		$attribute7 = '';
		$operator7 = '';
		$input7 = '';
	}
	if(isset($_POST['attribute8'])) {
		$attribute8 = $_POST['attribute8'];
		$operator8 = $_POST['operator8'];
		$input8 = $_POST['input8'];
	} else {
		$attribute8 = '';
		$operator8 = '';
		$input8 = '';
	}
	if(isset($_POST['attribute9'])) {
		$attribute9 = $_POST['attribute9'];
		$operator9 = $_POST['operator9'];
		$input9 = $_POST['input9'];
	} else {
		$attribute9 = '';
		$operator9 = '';
		$input9 = '';
	}
	if(isset($_POST['attribute10'])) {
		$attribute10 = $_POST['attribute10'];
		$operator10 = $_POST['operator10'];
		$input10 = $_POST['input10'];
	} else {
		$attribute10 = '';
		$operator10 = '';
		$input10 = '';
	}
    $queryCriteria = $attribute1.$operator1.$input1;
    if($attribute2 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute2.$operator2.$input2;  
        if($operator2 == 'like'){
            $query1 = $query1." and Patient.$attribute2 $operator2'%$input2%'";
        } else{
            $query1 =$query1." and Patient.$attribute2 $operator2'$input2'";
        }    
    }
    if($attribute3 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ".$attribute3.$operator3.$input3;
        if($operator3 == 'like'){
            $query1 = $query1." and Patient.$attribute3 $operator3'%$input3%'";
        } else{
            $query1 =$query1." and Patient.$attribute3 $operator3'$input3'";
        }
    }
    if($attribute4 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ".$attribute4.$operator4. $input4;
        if($operator4 == 'like'){
            $query1 = $query1." and Patient.$attribute4 $operator4'%$input4%'";
        } else{
            $query1 =$query1." and Patient.$attribute4 $operator4'$input4'";
        }
    }
    if($attribute5 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute5. $operator5. $input5; 
        if($operator5 == 'like'){
            $query1 = $query1." and Patient.$attribute5 $operator5'%$input5%'";
        } else{
            $query1 =$query1." and Patient.$attribute5 $operator5'$input5'";
        }
    }
    if($attribute6 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute6. $operator6. $input6; 
        if($operator6 == 'like'){
            $query1 = $query1." and Patient.$attribute6 $operator6'%$input6%'";
        } else{
            $query1 =$query1." and Patient.$attribute6 $operator6'$input6'";
        }
    }
    if($attribute7 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute7. $operator7. $input7; 
        if($operator7 == 'like'){
            $query1 = $query1." and Patient.$attribute7 $operator7'%$input7%'";
        } else{
            $query1 =$query1." and Patient.$attribute7 $operator7'$input7'";
        }
    }
    if($attribute8 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute8. $operator8. $input8; 
        if($operator8 == 'like'){
            $query1 = $query1." and Patient.$attribute8 $operator8'%$input8%'";
        } else{
            $query1 =$query1." and Patient.$attribute8 $operator8'$input8'";
        }
    }
    if($attribute9 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute9. $operator9. $input9; 
        if($operator9 == 'like'){
            $query1 = $query1." and Patient.$attribute9 $operator9'%$input9%'";
        } else{
            $query1 =$query1." and Patient.$attribute9 $operator9'$input9'";
        }
    }
    if($attribute10 == "") {
        
    } else {
        $queryCriteria = $queryCriteria.", ". $attribute10. $operator10. $input10; 
        if($operator10 == 'like'){
            $query1 = $query1." and Patient.$attribute10 $operator10'%$input10%'";
        } else{
            $query1 =$query1." and Patient.$attribute10 $operator10'$input10'";
        }
    }
	$query1 = $query1." AND Patient.SubmittedToAnalysis = '1'";
    $result1 = $conn->query($query1);
    if(!$result) die ($conn->error);
    $rows1 = $result1->num_rows;
    
    
    echo<<<_END
    
    <table border=".5">
                <tr align="center">
_END;

    //create and execute query to select only attributes that should be visible
    $visibleAttributes = "SELECT * from PatientAttributeNames Join UserPreferences on PatientAttributeNames.AttributeNumber = UserPreferences.AttributeNumber Where UserPreferences.Username ='$username' and UserPreferences.IsVisible = 'Yes'";
    $visibleResult = $conn->query($visibleAttributes);
    if(!$visibleResult) die ($conn->error);
    $visibleCount = $visibleResult->num_rows;
    $visibleAttributes = array();
    for ($j=0; $j<$visibleCount; $j++) {
        $visibleResult->data_seek($j);
        $visibleRow = $visibleResult->fetch_array(MYSQLI_NUM);
        $visibleAttributes[$j] = $visibleRow[0];
        echo <<<_END
                        <td>$visibleRow[1]</td>
_END;
}

echo <<<_END
                </tr>
    
_END;
    
    
for ($j=0; $j<$rows1; $j++) {
        $patientCount = $patientCount+1;
        $result1->data_seek($j);
        $row = $result1->fetch_array(MYSQLI_NUM);
        echo<<<_END
        
        <form action="PatientDetails.php" method="post">
        <tr align="center">
_END;

        for ($x=0; $x<$visibleCount; $x++) {
            
            $arrayNum = $visibleAttributes[$x] - 1;
            if($x==0){
                echo<<<_END
                <td>$row[0]<input type="hidden" value="$row[0]" name="SubjectId"></td>
_END;
            } else{
                echo <<<_END
                    <td>$row[$arrayNum]</td>

_END;
            }   
    
        }
            echo <<<_END
            <td><input type="submit" value="View Patient Details">
        </tr>
        </form>
_END;
    }
 
    
//create query for exporting patient data combined with scan data
//html to finish displaying page, create forms for exporting query results, exporting query results with scan data, and saving query
//store all query information to POST variables
$query3 = substr($query1, 21);
$query3 = "SELECT * FROM Patient Join ScanData on Patient.SubjectID = ScanData.SubjectID ".$query3;
echo<<<_END
    
    <div align="center">
    <br><br>
    Query Criteria: $queryCriteria<br>
    Patient Count: $patientCount<br>
    </div>
    <div style="width:33%; float:left" align="center">
        <form action="export.php" method = "post">
                    <input type="hidden" value="$query1" name="query">
                    <input type="hidden" value="PatientQuery.php" name="url">
                    <input type="submit" value="Export Query Results">
        </form>
        <br>
    </div>
    
    <div align="center" style="width:33%; float:left">
        <form action="export.php" method = "post">
                    <input type="hidden" value="$query3" name="query2">
                    <input type="hidden" value="PatientQuery.php" name="url">
                    <input type="submit" value="Export Results With Scan Data">
        </form>
        <br>
    </div>
    
    <div style="width:33%; float:left" align="center">
        <form action="SaveQuery.php" method="post" align="center">
            <input type="hidden" name="query" value="$query1">
            <input type="hidden" name="queryCriteria" value="$queryCriteria">
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
            <input type="hidden" value="$operator10" name="operator10">
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
            <input type="submit" value="Save Query"><br>
        </form>
        <br>
    </div>
    
    
    </div><br>
    
_END;
}

$conn->close();
?>