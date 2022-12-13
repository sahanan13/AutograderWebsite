<?php

// Authenticate if username & password is correct
function authenticate ($user, $pass, $db) {
	global $t;
	$s = "select * from accounts where user='$user' and pass='$pass'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	$num = mysqli_num_rows($t);

	if($num == 0){ return false;}  else { return true; }
}

// Get type (student or teacher) of user
function getTSType ($user, $pass, $db) {
	$s = "select type from accounts where user='$user' and pass='$pass'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	$row = mysqli_fetch_row($t);
	$Type = $row[0];
	
	return $Type;
}

// Checking if username & password are safe
function safe ($fieldname, $db) {
	//$temp = trim($fieldname);
	$temp = mysqli_real_escape_string($db,$fieldname);
	return $temp;
}

function makeQuestion($db, $questionLevel, $question, $qType, $constraint, $testCase1, $output1, $testCase2, $output2, $testCase3, $output3, $testCase4, $output4, $testCase5, $output5) {
	$questionID = (new DateTime())->format('YmdHis');
		
	$s = "INSERT INTO QUESTION_BANK VALUES ('$questionID', '$questionLevel', '$question', '$qType', '$constraint', '$testCase1', '$output1', '$testCase2', '$output2', '$testCase3', '$output3', '$testCase4', '$output4', '$testCase5', '$output5')";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	echo "Question Created<br>";
}

function displayQuestions($db) {
	$s = "select * from QUESTION_BANK";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array();
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			if ($field == "questionID" or $field == "questionLevel" or $field == "question" or $field == "qType" or $field == "constraint") {
				$thisQuestion[] = $value;
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}

	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
}

function filterQuestions($db, $qLevel, $qType, $keyWord) {
	
	$qLevelExist = ($qLevel != "");
	$qTypeExist = ($qType != "");
	$keyWordExist = ($keyWord != "");
	
	if ($qLevelExist and !$qTypeExist and !$keyWordExist) {			//qLevel only
		$s = "select * from QUESTION_BANK where questionLevel = '$qLevel'";
	} else if (!$qLevelExist and $qTypeExist and !$keyWordExist) {	//qType only
		$s = "select * from QUESTION_BANK where qType = '$qType'";
	} else if (!$qLevelExist and !$qTypeExist and $keyWordExist) {	//keyWord only
		$s = "select * from QUESTION_BANK where question LIKE '%$keyWord%'";
	} else if ($qLevelExist and $qTypeExist and !$keyWordExist) {	//qLevel and qType
		$s = "select * from QUESTION_BANK where questionLevel = '$qLevel' and qType = '$qType'";
	} else if ($qLevelExist and !$qTypeExist and $keyWordExist) {	//qLevel and keyWord
		$s = "select * from QUESTION_BANK where questionLevel = '$qLevel' and question LIKE '%$keyWord%'";
	} else if (!$qLevelExist and $qTypeExist and $keyWordExist) {	//qType and keyWord
		$s = "select * from QUESTION_BANK where qType = '$qType' and question LIKE '%$keyWord%'";
	} else if ($qLevelExist and $qTypeExist and $keyWordExist) {	//qLevel and qType and keyWord
		$s = "select * from QUESTION_BANK where questionLevel = '$qLevel' and qType = '$qType' and question LIKE '%$keyWord%'";
	} else {
		$s = "select * from QUESTION_BANK";
	}
	
	
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array();
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			if ($field == "questionID" or $field == "questionLevel" or $field == "question" or $field == "qType") {
				$thisQuestion[] = $value;
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}

	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
}

//get array of exam IDs and array of points
function makeExam($db, $examID, $qExamIDs, $qPointValues) {
	for($i = 0;$i < count($qExamIDs);$i++) {
		$s = "INSERT INTO EXAM VALUES ('$examID', '$qExamIDs[$i]', $qPointValues[$i])";
		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}
		echo "Question Inserted<br>";
	}
	echo "Exam Created<br>";
}

function displayExamOptions($db) {
	$s = "SELECT DISTINCT examID FROM EXAM";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$availExams = array();
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			$availExams[] = $value;
		}
	}
	$jsonAvailExams = json_encode($availExams);
	echo $jsonAvailExams;
	
}

function displayExam($db, $examID) {
	$s = "select * from EXAM where examID = '$examID'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array(); 
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			$thisQuestion[] = $value;
			if ($field == "qExamID") {
				//Getting info from question bank
				$s2 = "select * from QUESTION_BANK where questionID = '$value'";
				( $t2 = mysqli_query($db, $s2)) or die ( mysqli_error($db) ) ;
				
				if (!$t2) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				
				while ($row1 = mysqli_fetch_assoc($t2)) {
					foreach ($row1 as $field1 => $value1) {
						if ($field1 == "questionLevel" or $field1 == "question" or $field1 == "qType" or $field1 == "constraint") {
							$thisQuestion[] = $value1;
						}
					}
				}
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}
	
	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
}

function submitExamQuestion($db, $studentResUN, $examResId, $qResID, $studentAnswer) {
	$studentAns = safe($studentAnswer, $db);
	$s = "INSERT INTO STUDENT_EXAM_RESULTS (studentResUN, examResID, qResID, studentAns) VALUES ('$studentResUN', '$examResId', '$qResID', '$studentAns')";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	echo "Student Question Answer Saved<br>";
}

function submitExam($db, $studentResUN, $examResID, $qResIDs, $studentAnswers) {
	// store students' responses in STUDENT_EXAM_RESULTS
	
	for($i = 0;$i < count($qResIDs);$i++) {
		$studentAns = safe($studentAnswers[$i], $db);
		$s = "INSERT INTO STUDENT_EXAM_RESULTS (studentResUN, examResID, qResID, studentAns) VALUES ('$studentResUN', '$examResID', '$qResIDs[$i]', '$studentAns')";
		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}
		echo "Student Question Answer Saved<br>";
	}
	echo "Student Exam Saved<br>";
}

function autogradeExam($db, $examID) {
	// send info to middle to grade - send test cases & outputs for each question in exam	
	//sending like: ((studentResUN, examResID, qResID, qPointValue, question, tc1, o1, tc2, o2, studentAns), ...

	$s = "select studentResUN, examResID, qResID, studentAns from STUDENT_EXAM_RESULTS where examResID = '$examID'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array(); 
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			$thisQuestion[] = $value;
			
			if ($field == "qResID") {
				$s2 = "select qPointValue from EXAM where examID = '$examID' and qExamID = '$value'";
				( $t2 = mysqli_query($db, $s2)) or die ( mysqli_error($db) ) ;
				if (!$t2) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row1 = mysqli_fetch_assoc($t2)) {
					foreach ($row1 as $field1 => $value1) {
						$thisQuestion[] = $value1;
					}
				}
				
				//Getting info from question bank
				$s3 = "select * from QUESTION_BANK where questionID = '$value'";
				( $t3 = mysqli_query($db, $s3)) or die ( mysqli_error($db) ) ;
				if (!$t3) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row2 = mysqli_fetch_assoc($t3)) {
					foreach ($row2 as $field2 => $value2) {
						if (($field2 != "questionID") and ($field2 != "questionLevel") and ($field2 != "qType")) {
//						if ($field2 == "question" or $field2 == "testCase1" or $field2 == "output1" or $field2 == "testCase2" or $field2 == "output2") {
							$thisQuestion[] = $value2;
						}
					}
				}
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}
	
	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
	
}

function putInGrades($db, $studentResUN, $examResID, $qResIDs, $actualFunctionNames, $studentFunctionNames, $studentTC1outputs, 
	$studentTC2outputs, $studentTC3outputs, $studentTC4outputs, $studentTC5outputs, 
	$pointsWorthFN, $pointsWorthTC1, $pointsWorthTC2, $pointsWorthTC3, $pointsWorthTC4, $pointsWorthTC5, $pointsWorthConst,
	$pointsEarnedFN, $pointsEarnedTC1, $pointsEarnedTC2, $pointsEarnedTC3, $pointsEarnedTC4, $pointsEarnedTC5, $pointsEarnedConst,
	$pointsEarnedTotalArr) {

	//get grades for each question from middle and insert in table
	
	for($i = 0;$i < count($qResIDs);$i++) {
		$s = "UPDATE STUDENT_EXAM_RESULTS 	
				SET actualFunctionName = '$actualFunctionNames[$i]',
					studentFunctionName = '$studentFunctionNames[$i]',
					studentTC1output = '$studentTC1outputs[$i]',
					studentTC2output = '$studentTC2outputs[$i]',
					studentTC3output = '$studentTC3outputs[$i]',
					studentTC4output = '$studentTC4outputs[$i]',
					studentTC5output = '$studentTC5outputs[$i]',
					pointsWorthFN = $pointsWorthFN[$i],
					pointsWorthTC1 = $pointsWorthTC1[$i],
					pointsWorthTC2 = $pointsWorthTC2[$i],
					pointsWorthTC3 = $pointsWorthTC3[$i],
					pointsWorthTC4 = $pointsWorthTC4[$i],
					pointsWorthTC5 = $pointsWorthTC5[$i],
					pointsWorthConst = $pointsWorthConst[$i],
					pointsEarnedFN = $pointsEarnedFN[$i],
					pointsEarnedTC1 = $pointsEarnedTC1[$i],
					pointsEarnedTC2 = $pointsEarnedTC2[$i],
					pointsEarnedTC3 = $pointsEarnedTC3[$i],
					pointsEarnedTC4 = $pointsEarnedTC4[$i],
					pointsEarnedTC5 = $pointsEarnedTC5[$i],
					pointsEarnedConst = $pointsEarnedConst[$i],
					pointsEarnedTotal = $pointsEarnedTotalArr[$i] 
			WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}
		//echo "Student Autograde Point Value Saved<br>";
	}
	//echo "Student Exam Autograded<br>";
	
}

function displayScoresForTeacher($db, $studentUN, $examID) {
	//send info in form of:
	//((studentResUN, examResID, qResID, qPointValue, questionLevel, question, qType, testCase1, output1, testCase2, output2, studentAns, pointEarned)
	
	$s = "select * from STUDENT_EXAM_RESULTS where studentResUN = '$studentUN' and examResID = '$examID'";
	
	// don't put entries where testcase is null
	// check where studentTC1output is null, and don't include: *studentTC1output, *pointsEarnedTC1, it if it is
	// if constraint is None - Janice put 0 for pointsEarnedConst
	//go through each entry and select stuff from tables - if column is *studentTC1output, to 5 check if "null" if "null" dont add to arr
	//send actualFunctionName, studentFunctionName, 
	
	
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array();
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			if ($field != "comments" and $field != "visible") {
				/*//only add test cases that aren't null
				if ($field == "studentTC1output" or $field == "studentTC2output" or $field == "studentTC3output" or 
					$field == "studentTC4output" or $field == "studentTC5output") {
					if ($value != "null") {
						$thisQuestion[] = $value;
					}
				}
				else if ($field == "pointsWorthTC1" or $field == "pointsWorthTC2" or $field == "pointsWorthTC3" or 
					$field == "pointsWorthTC4" or $field == "pointsWorthTC5" or 
					$field == "pointsEarnedTC1" or $field == "pointsEarnedTC2" or $field == "pointsEarnedTC3" or
					$field == "pointsEarnedTC4" or $field == "pointsEarnedTC5") {
					if ($value != -1) {
						$thisQuestion[] = $value;
					}
				} else {
					$thisQuestion[] = $value;
				}*/
				$thisQuestion[] = $value;
			}
			
			if ($field == "qResID") {
				$s2 = "select qPointValue from EXAM where examID = '$examID' and qExamID = '$value'";
				( $t2 = mysqli_query($db, $s2)) or die ( mysqli_error($db) ) ;
				if (!$t2) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row1 = mysqli_fetch_assoc($t2)) {
					foreach ($row1 as $field1 => $value1) {
						$thisQuestion[] = $value1;
					}
				}
				
				//Getting info from question bank
				$s3 = "select * from QUESTION_BANK where questionID = '$value'";
				( $t3 = mysqli_query($db, $s3)) or die ( mysqli_error($db) ) ;
				if (!$t3) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row2 = mysqli_fetch_assoc($t3)) {
					foreach ($row2 as $field2 => $value2) {
						if ($field2 != "questionID") {						//with sending null values for test cases
							$thisQuestion[] = $value2;
						}
						
					}
				}
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}
	
	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
}

function modifyScores($db, $studentResUN, $examResID, $qResIDs, $pointsEarnedFN, $pointsEarnedTC1, $pointsEarnedTC2, $pointsEarnedTC3, 
		$pointsEarnedTC4, $pointsEarnedTC5, $pointsEarnedConst, $comments) {
	
	// go through all new scores and turn them into ints
	// if empty string, keep original score the same
	// else change score
	// after changing score, loop through questionIDs and sum scores that aren't -1 and put in total
	
	//put in new scores and comments
	for($i = 0;$i < count($qResIDs);$i++) {
		$comment = safe($comments[$i], $db);
		//update comments
		$s = "UPDATE STUDENT_EXAM_RESULTS 
				SET comments = '$comment' 
				WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}
		
		//checking if individual point breakdowns should be changed
		if ($pointsEarnedFN[$i] != "") {
			$points = floatval($pointsEarnedFN[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedFN = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedTC1[$i] != "") {
			$points = floatval($pointsEarnedTC1[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedTC1 = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedTC2[$i] != "") {
			$points = floatval($pointsEarnedTC2[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedTC2 = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedTC3[$i] != "") {
			$points = floatval($pointsEarnedTC3[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedTC3 = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedTC4[$i] != "") {
			$points = floatval($pointsEarnedTC4[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedTC4 = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedTC5[$i] != "") {
			$points = floatval($pointsEarnedTC5[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedTC5 = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		if ($pointsEarnedConst[$i] != "") {
			$points = floatval($pointsEarnedConst[$i]);
			$s = "UPDATE STUDENT_EXAM_RESULTS 
					SET pointsEarnedConst = $points
					WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
			( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
			
			if (!$t) {
				echo 'Could not run query: ' . mysqli_error();
				exit;
			}
		}
		
		//change total
		//go through pointsEarnedFN, pointsEarnedTC1, pointsEarnedTC2, pointsEarnedTC3, pointsEarnedTC3, pointsEarnedTC4, pointsEarnedTC5,
		//	pointsEarnedConst
		//	pointsEarnedTotal
		
		$s = "SELECT pointsEarnedFN, pointsEarnedTC1, pointsEarnedTC2, pointsEarnedTC3, pointsEarnedTC4, pointsEarnedTC5,
					pointsEarnedConst 
				FROM STUDENT_EXAM_RESULTS
				WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";

		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}

		$pointTotal = 0;
		while ($row = mysqli_fetch_assoc($t)) {
			foreach ($row as $field => $value) {
				if ($value != -1) {
					$pointTotal += $value;
				}
			}
		}
		
		$s = "UPDATE STUDENT_EXAM_RESULTS 
				SET pointsEarnedTotal = $pointTotal
				WHERE studentResUN = '$studentResUN' AND examResID = '$examResID' AND qResID = '$qResIDs[$i]'";
		( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
		
		if (!$t) {
			echo 'Could not run query: ' . mysqli_error();
			exit;
		}
		echo "Student Modified Point Values Saved<br>";
	}
	echo "Student Exam Grades Modified<br>";
}

function makeScoresVisible($db, $examID) {
	$s = "UPDATE STUDENT_EXAM_RESULTS SET visible = 'True' WHERE examResID = '$examID'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	//echo $num . "<br>";
	echo "Student Exam Visible<br>";
}

function displayFinalScores($db, $studentUN, $examID) {
	//((studentResUN, examResID, qResID, -qPointValue, -questionLevel, question, qType, tc1, o1, tc2, o2,- studentAns, pointsEarned, comments) 
	
	$s = "select * from STUDENT_EXAM_RESULTS where studentResUN = '$studentUN' and examResID = '$examID' and visible = 'True'";
	( $t = mysqli_query($db, $s)) or die ( mysqli_error($db) ) ;
	
	if (!$t) {
		echo 'Could not run query: ' . mysqli_error();
		exit;
	}
	
	$allQuestions = array();
	
	while ($row = mysqli_fetch_assoc($t)) {
		$thisQuestion = array();
		foreach ($row as $field => $value) {
			if ($field != "visible") {
				$thisQuestion[] = $value;
			}
			
			if ($field == "qResID") {
				$s2 = "select qPointValue from EXAM where examID = '$examID' and qExamID = '$value'";
				( $t2 = mysqli_query($db, $s2)) or die ( mysqli_error($db) ) ;
				if (!$t2) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row1 = mysqli_fetch_assoc($t2)) {
					foreach ($row1 as $field1 => $value1) {
						$thisQuestion[] = $value1;
					}
				}
				
				//Getting info from question bank
				$s3 = "select * from QUESTION_BANK where questionID = '$value'";
				( $t3 = mysqli_query($db, $s3)) or die ( mysqli_error($db) ) ;
				if (!$t3) {
					echo 'Could not run query: ' . mysqli_error();
					exit;
				}
				while ($row2 = mysqli_fetch_assoc($t3)) {
					foreach ($row2 as $field2 => $value2) {
						if ($field2 != "questionID") {
							$thisQuestion[] = $value2;
						}
						
					}
				}
			}
		}
		$allQuestions[] = json_encode($thisQuestion);
	}
	
	$jsonQuestions = json_encode($allQuestions);
	echo $jsonQuestions;
}

/*
x_displayExamOptions
x_displayExam
x_submitExam
-?_autogradeExam
?_putInGrades
?_displayScoresForTeacher
x_modifyScores
x_makeScoresVisible
x_displayFinalScores
*/

?>