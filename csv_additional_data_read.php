<?php
	require_once 'dbInfo.php';
	set_time_limit(600);
	$conn = new mysqli($hn, $un, $pw, $db);
	$file_name = "scanUploadFile.csv"; // = this will be the file name as a String however it is entered
	$row = 1;
	if (($handle = fopen($file_name, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
			if($row == 1){
				echo "This is skipping the first line <br>";
				$row++;
			} else {
				// length of line
				$num = count($data);
				// last index to do regular adds to array
				$last_index = 26;
				// create array 
				$array_current_line = array();
				
				//echo "<p> $num fields in line $row: <br /></p>\n"; //debug
				$row++;
				
				// go from zero to last_index - 1
				for ($c=0; $c < $last_index; $c++) {
					//echo $data[$c] . "<br />\n"; //debug
					$array_current_line[$c] = $data[$c];
				}
				
				// create string to add to array_current_line
				$additional_data = "";
				
				// go from last_index to num of elements - 1 index
				for ($i=$last_index; $i < $num; $i++) {
					if($data[$i] != ''){
						$additional_data .= ($data[$i] . ", ");	
					}
				}
				
				// add last string of additional_data to our array
				$array_current_line[$last_index] = $additional_data;
				
				for($i=0; $i < 27; $i++) {
					echo " $i $array_current_line[$i] <br /> ";
				}
				$sqlString = "INSERT INTO ProLungdx.scandata (Outlier, Operator, EPNScanKey, SubjectID, FirstName, MiddleName, LastName, BirthDate, VisitID, DateTime, PointID, Max, Min, Rise, Fall, DropValue, TestTime, TotalArea, AUC1, AUC2, Seconds, 0Slope, 5SecBeginsM, TotalM, ValueAtBegin5, ValueAtLastM, Readings)
				VALUES ('$array_current_line[0]',  '$array_current_line[1]', '$array_current_line[2]', '$array_current_line[3]', '$array_current_line[4]', '$array_current_line[5]', '$array_current_line[6]', '$array_current_line[7]',  '$array_current_line[8]', '$array_current_line[9]', '$array_current_line[10]', '$array_current_line[11]', '$array_current_line[12]', '$array_current_line[13]', '$array_current_line[14]', '$array_current_line[15]', '$array_current_line[16]', '$array_current_line[17]', '$array_current_line[18]', '$array_current_line[19]', '$array_current_line[20]', '$array_current_line[21]', '$array_current_line[22]', '$array_current_line[23]', '$array_current_line[24]', '$array_current_line[25]', '$array_current_line[26]')";
				echo "$sqlString <br>";
				$sqlResults = $conn->query($sqlString);
				if(!$sqlResults) die ($conn->error);
				
			}
		}
		fclose($handle);
	}
?>
