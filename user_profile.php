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

//create basic html structure for page, and table and forms for user preferences
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
        <div id="contentBox" style="margin:0px auto; width:100%" align="center">
        <h1>$username</h1>    
        </div>
        <div style="float:left; width:50%" align="center">
            <h2 align="center">Attribute Preferences</h2>
            <table>
                <tr>
                    <td>
                        <form action="PrefUpdate.php" method="post">
                            <input type="hidden" name="url" value="user_profile.php">
                            <input type="hidden" name="allVisible" value="1">
                            <input type="submit" value="Set All Visible">
                        </form>
                    </td>
                    <td>
                        <form action="PrefUpdate.php" method="post">
                            <input type="hidden" name="url" value="user_profile.php">
                            <input type="hidden" name="allNotVisible" value="1">
                            <input type="submit" value="Set All Not Visible">
                        </form>
                    </td>
                    <td>
                        <form action="PrefUpdate.php" method="post">
                            <input type="hidden" name="url" value="user_profile.php">
                            <input type="hidden" name="allExport" value="1">
                            <input type="submit" value="Set All Exported">
                        </form>
                    </td>
                    <td>
                        <form action="PrefUpdate.php" method="post">
                            <input type="hidden" name="url" value="user_profile.php">
                            <input type="hidden" name="allNotExport" value="1">
                            <input type="submit" value="Set All Not Exported">
                        </form>
                    </td>
                </tr>
            </table>
            <form action="PrefUpdate.php" method="post">
            <table border=".5">
                <tr>
                    <td>Attribute</td>
                    <td>Is Visible</td>
                    <td>Is Exported</td>
                </tr>
_END;

//create and execute query to select all of the patient attributes
$attributeQuery = "SELECT * from PatientAttributeNames";
$attributeResult = $conn->query($attributeQuery);
$attributeRows = $attributeResult->num_rows;

//loop through query results and execute a sql query to find the user preferences for each patient attribute
for ($j=0; $j<$attributeRows; $j++) {
    $attributeResult->data_seek($j);
    $attributeRow = $attributeResult->fetch_array(MYSQLI_NUM);
    $prefQuery = "SELECT * from UserPreferences where UserPreferences.UserName = '$username' and UserPreferences.AttributeNumber = '$attributeRow[0]'";
    $prefResult = $conn->query($prefQuery);
    if(!$prefResult) die ($conn->error);
    $prefRow = $prefResult->fetch_array(MYSQLI_NUM);
    $isVisible = $prefRow[3];
    $isExported = $prefRow[4];
    
    //display user preferences for each attribute
    echo <<<_END
        <tr>
            <td>$attributeRow[1]</td>
            <td>
                <select name="isVisible$j">
_END;
    
    if ($isVisible == "Yes") {
        if ($attributeRow[0] == 1){
            //this only happens for SubjectID, which has to always be both visible and exported
            echo <<<_END
                    <option selected>Yes</option>
_END;
        }
        else {
            echo <<<_END
                    <option selected>Yes</option>
                    <option>No</option>
                    
_END;
        }
    } else {
        echo <<<_END
                    <option>Yes</option>
                    <option selected>No</option>
_END;
    }

    echo <<<_END
                </select>
            </td>
            <td>
                <select name="isExported$j">
                
_END;
    
    if ($isExported == "Yes") {
        if ($attributeRow[0] == 1){
            echo <<<_END
                    <option selected>Yes</option>
_END;
        }
        else {
        echo <<<_END
                    <option selected>Yes</option>
                    <option>No</option>
_END;
        }
    } else {
        echo <<<_END
                    <option>Yes</option>
                    <option selected>No</option>
_END;
    }
    
echo <<<_END
                </select>
            </td>
        </tr>
        
_END;
}


//finish html structure for the user preferences table and form. create the table for saved queries
echo<<<_END
            
            </table>
            <br>
            <input type="hidden" name="attributeNum" value="$attributeRows">
            <input type="hidden" name="url" value="user_profile.php">
            <input type="submit" value="Save Changes">
            </form>
       
            
            
        </div>
        
        <div style="float:left; width:50%">
            <h2 align="center">Saved Queries</h2>
            <table border=".5">
                <tr>
                    <td>Query Name</td>
                    <td>Query Notes</td>
                </tr>
        
_END;

//create and execute sql query to select all of the user's saved queries
$savedQueriesQuery = "SELECT * From SavedQueries where SavedQueries.UserName = '$username'";
$savedQueryResult = $conn->query($savedQueriesQuery);
if(!$savedQueryResult) die ($conn->error);
$savedQueryRows = $savedQueryResult->num_rows;

//loop through the query results and assign all of the saved values to variables
for ($j=0; $j<$savedQueryRows; $j++) {
    $savedQueryResult->data_seek($j);
    $savedQueryRow=$savedQueryResult->fetch_array(MYSQLI_NUM);
    $attribute1 = $savedQueryRow[4];
    $attribute2 = $savedQueryRow[5];
    $attribute3 = $savedQueryRow[6];
    $attribute4 = $savedQueryRow[7];
    $attribute5 = $savedQueryRow[8];
    $attribute6 = $savedQueryRow[9];
    $attribute7 = $savedQueryRow[10];
    $attribute8 = $savedQueryRow[11];
    $attribute9 = $savedQueryRow[12];
    $attribute10 = $savedQueryRow[13];
    $operator1 = $savedQueryRow[14];
    $operator2 = $savedQueryRow[15];
    $operator3 = $savedQueryRow[16];
    $operator4 = $savedQueryRow[17];
    $operator5 = $savedQueryRow[18];
    $operator6 = $savedQueryRow[19];
    $operator7 = $savedQueryRow[20];
    $operator8 = $savedQueryRow[21];
    $operator9 = $savedQueryRow[22];
    $operator10 = $savedQueryRow[23];
    $input1 = $savedQueryRow[24];
    $input2 = $savedQueryRow[25];
    $input3 = $savedQueryRow[26];
    $input4 = $savedQueryRow[27];
    $input5 = $savedQueryRow[28];
    $input6 = $savedQueryRow[29];
    $input7 = $savedQueryRow[30];
    $input8 = $savedQueryRow[31];
    $input9 = $savedQueryRow[32];
    $input10 = $savedQueryRow[33];
    
    //reconstruct saved query using the variables
    $queryCriteria = $attribute1.$operator1.$input1;
    if($operator1 == 'like') {
        $query1 = "Select * from Patient where Patient.$attribute1 $operator1 '%$input1%'";
    } else{
        $query1 = "Select * from Patient where Patient.$attribute1 $operator1'$input1'";
    }
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
    
    
    //finish creating the html table and form for saved queries
    echo <<<_END
                <tr>
                    <td>$savedQueryRow[2]</td>
                    <td>$savedQueryRow[3]</td>
                    <td align="center" valign="center">
                        <br>
                        <form method="post" action="RunSavedQuery.php">
                            <input type="hidden" value="$queryCriteria" name="queryCriteria">
                            <input type="hidden" value="$query1" name="query">
                            <input type="submit" value="Run Query">
                        </form>
                        <form method="post" action="DeleteSavedQuery.php">
                            <input type="hidden" name="queryId" value="$savedQueryRow[0]">
                            <input type="submit" name="delete" value="Delete Query">
                        </form>
                    </td>
                </tr>
_END;
}

echo <<<_END
            </table>
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