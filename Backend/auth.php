<?php

# Backend - Release Candidate

// Getting credentials from middle
$page = $_POST["page"];
$jsonData = $_POST["info"];
//echo "BACKEND";
//echo $jsonData;
$data = json_decode($jsonData,true);

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors' , 1);

// incude information to login to MySQL
include ("account.php");
include ("backendFunctions.php");


$db = mysqli_connect($hostname,$username,$password,$project);

// Failed to connect to MySQL
if(mysqli_connect_errno())
 {
	echo "Failed to connect to MySQL:". mysqli_connect_error();
	exit();
 }

// Successfully connected to MySQL

mysqli_select_db($db,$project);				


if($page == 'login') {
	$user = $data['user'];
	$pass = $data['pass'];
	
	$user = safe($user, $db);
	$pass = safe($pass, $db);
	
	$delay = 3;
	if(!authenticate($user, $pass, $db)){
		// Invalid login
		$jsonData = array("auth" => "false");

	}
	else {
		// Successful login
		if (strcmp(getTSType($user, $pass, $db), "teacher") == 0) {
			// user is teacher
			$jsonData = array("auth" => "true", "teacher" => "true");
		} else {
			// user is student
			$jsonData = array("auth" => "true", "teacher" => "false");
		}
	}

	// Sending json with information to middle end
	$jsonOutput = json_encode($jsonData);
	echo $jsonOutput;
}

if ($page == 'makeQuestion') {
	$delay = 3;
	//get info from middle
	//make question id
	// insert info into table
	
	$questionLevel = $data['questionlevel'];
	$question = safe($data['question'], $db);	
	$qType = $data['questiontype'];
	$constraint = $data['const'];
	
	// Test case & output 1
	if (strcmp($data['testc1'], "") == 0) {
		$testCase1 = "null";
	} else {
		$testCase1 = safe($data['testc1'], $db);
	}
	if (strcmp($data['otestc1'], "") == 0) {
		$output1 = "null";
	} else {
		$output1 = safe($data['otestc1'], $db);
	}
	
	// Test case & output 2
	if (strcmp($data['testc2'], "") == 0) {
		$testCase2 = "null";
	} else {
		$testCase2 = safe($data['testc2'], $db);
	}
	if (strcmp($data['otestc2'], "") == 0) {
		$output2 = "null";
	} else {
		$output2 = safe($data['otestc2'], $db);
	}
	
	// Test case & output 3
	if (strcmp($data['testc3'], "") == 0) {
		$testCase3 = "null";
	} else {
		$testCase3 = safe($data['testc3'], $db);
	}
	if (strcmp($data['otestc3'], "") == 0) {
		$output3 = "null";
	} else {
		$output3 = safe($data['otestc3'], $db);
	}
	
	// Test case & output 4
	if (strcmp($data['testc4'], "") == 0) {
		$testCase4 = "null";
	} else {
		$testCase4 = safe($data['testc4'], $db);
	}
	if (strcmp($data['otestc4'], "") == 0) {
		$output4 = "null";
	} else {
		$output4 = safe($data['otestc4'], $db);
	}
	
	// Test case & output 5
	if (strcmp($data['testc5'], "") == 0) {
		$testCase5 = "null";
	} else {
		$testCase5 = safe($data['testc5'], $db);
	}
	if (strcmp($data['otestc5'], "") == 0) {
		$output5 = "null";
	} else {
		$output5 = safe($data['otestc5'], $db);
	}	
	
	makeQuestion($db, $questionLevel, $question, $qType, $constraint, $testCase1, $output1,
		$testCase2, $output2, $testCase3, $output3, $testCase4, $output4, $testCase5, $output5);
}

if ($page == 'displayQuestions') {
	// get questions from bank
	// put in json
	// send back
	displayQuestions($db);
	
}

if ($page == 'filterQuestions') {
	$qLevel = $data['level'];
	$qType = $data['type'];
	$keyWord = $data['keyWord'];
	
	filterQuestions($db, $qLevel, $qType, $keyWord);
}

if ($page == 'selectQuestions') {
	//select questions from Q_BANK, and put in EXAM table
	// send questions from question bank
	// get selected question IDs
	// put in exam table
	
	$examID = $data['examName'];
	$qExamIDs = explode(",",$data['questionIds']);
	$qPointValues = array_map('intval', explode(',', $data['points']));

	makeExam($db, $examID, $qExamIDs, $qPointValues);
}

if ($page == 'displayExamOptions') {
	//display exams available for students to take

	displayExamOptions($db);
}

if ($page == 'displayExam') {
	//display actual exam & questions
	$examID = $data['examName'];
	
	displayExam($db, $examID);
}

if ($page == 'submitExamQuestion') {
	
	$studentResUN = $data['userId'];
	$examResID = $data['examId'];
	$qResID = $data['qid'];
	$studentAnswer = $data['qans'];
	
	submitExamQuestion($db, $studentResUN, $examResID, $qResID, $studentAnswer);
}

if ($page == 'submitExam') {
	//display actual exam & questions
	
	$studentResUN = $data['userId'];
	$examResID = $data['examId'];
	$qResIDs = json_decode($data['qids'],true); //array
	$studentAnswers = json_decode($data['qans'],true); //array
	
	submitExam($db, $studentResUN, $examResID, $qResIDs, $studentAnswers);
}

if ($page == 'autogradeExam') {
	// store students' responses in STUDENT_EXAM_RESULTS
	// send info to middle to grade - sen	d test cases & outputs for each question in exam
	// after autograded store points
	$examID = $data['examname'];
	
	autogradeExam($db, $examID);
}

if ($page == 'putInGrades') {
	//put in grades into table
	$studentResUN = $data['studentResUN'];
	$examResID = $data['examResID'];
	$qResIDs = json_decode($data['qResIDs'], true); // array
	//$pointsEarnedArr = json_decode($data['pointsEarnedArr'], true); //array
	$actualFunctionNames = json_decode($data['actualFunctionNames'], true); //array;
	$studentFunctionNames = json_decode($data['studentFunctionNames'], true); //array;
	$studentTC1outputs = json_decode($data['studentTC1outputs'], true); //array
	$studentTC2outputs = json_decode($data['studentTC2outputs'], true); //array
	$studentTC3outputs = json_decode($data['studentTC3outputs'], true); //array				//put "null" if none
	$studentTC4outputs = json_decode($data['studentTC4outputs'], true); //array
	$studentTC5outputs = json_decode($data['studentTC5outputs'], true); //array

	$pointsWorthFN = json_decode($data['pointsWorthFN'], true); //array
	$pointsWorthTC1 = json_decode($data['pointsWorthTC1'], true); //array
	$pointsWorthTC2 = json_decode($data['pointsWorthTC2'], true); //array
	$pointsWorthTC3 = json_decode($data['pointsWorthTC3'], true); //array		// put -1 if none
	$pointsWorthTC4 = json_decode($data['pointsWorthTC4'], true); //array
	$pointsWorthTC5 = json_decode($data['pointsWorthTC5'], true); //array
	$pointsWorthConst = json_decode($data['pointsWorthConst'], true); //array	// put 0 if constraint is None
	
	$pointsEarnedFN = json_decode($data['pointsEarnedFN'], true); //array
	$pointsEarnedTC1 = json_decode($data['pointsEarnedTC1'], true); //array
	$pointsEarnedTC2 = json_decode($data['pointsEarnedTC2'], true); //array
	$pointsEarnedTC3 = json_decode($data['pointsEarnedTC3'], true); //array		// put -1 if none
	$pointsEarnedTC4 = json_decode($data['pointsEarnedTC4'], true); //array
	$pointsEarnedTC5 = json_decode($data['pointsEarnedTC5'], true); //array
	$pointsEarnedConst = json_decode($data['pointsEarnedConst'], true); //array	// put 0 if constraint is None
	$pointsEarnedTotalArr = json_decode($data['pointsEarnedTotalArr'], true); //array			//CHANGED
	
	putInGrades($db, $studentResUN, $examResID, $qResIDs, $actualFunctionNames, $studentFunctionNames, $studentTC1outputs, 
		$studentTC2outputs, $studentTC3outputs, $studentTC4outputs, $studentTC5outputs, 
		$pointsWorthFN, $pointsWorthTC1, $pointsWorthTC2, $pointsWorthTC3, $pointsWorthTC4, $pointsWorthTC5, $pointsWorthConst,
		$pointsEarnedFN, $pointsEarnedTC1, $pointsEarnedTC2, $pointsEarnedTC3, $pointsEarnedTC4, $pointsEarnedTC5, $pointsEarnedConst,
		$pointsEarnedTotalArr);
	
	$page = 'displayScoresForTeacher';
	$studentUN = $studentResUN;
	$examID = $examResID;
}

if ($page == 'displayScoresForTeacher') {
	//display scores for teacher from autograder
	
	//$studentUN = $data['studentResUN'];
	//$examID = $data['exam'];
	
	displayScoresForTeacher($db, $studentUN, $examID);
}

if ($page == 'modifyScores') {
	// send points and Qs to front end to display to teacher
	// when teachers change scores, change in student result table, and put comments in too
	
	$studentResUN = $data['userId'];
	$examResID = $data['examId'];
	
	//$qResIDs = explode(",",$data['qids']);
	//$newScores = array_map('intval', explode(',', $data['ptchanges']));
	
	$qResIDs = json_decode($data['qids'], true); //array
	
	$pointsEarnedFN = json_decode($data['pointsEarnedFN'], true); //array
	$pointsEarnedTC1 = json_decode($data['pointsEarnedTC1'], true); //array
	$pointsEarnedTC2 = json_decode($data['pointsEarnedTC2'], true); //array
	$pointsEarnedTC3 = json_decode($data['pointsEarnedTC3'], true); //array		// put -1 if none
	$pointsEarnedTC4 = json_decode($data['pointsEarnedTC4'], true); //array
	$pointsEarnedTC5 = json_decode($data['pointsEarnedTC5'], true); //array
	$pointsEarnedConst = json_decode($data['pointsEarnedConst'], true); //array	// put 0 if constraint is None
	//$pointsEarnedTotalArr = json_decode($data['pointsEarnedTotalArr'], true); //array			//CHANGED
	
	$comments = json_decode($data['comments'], true); //array
	
	//$comments = explode(",",$data['comments']);
		
	modifyScores($db, $studentResUN, $examResID, $qResIDs, $pointsEarnedFN, $pointsEarnedTC1, $pointsEarnedTC2, $pointsEarnedTC3, 
		$pointsEarnedTC4, $pointsEarnedTC5, $pointsEarnedConst, $comments);
	
}

if ($page == 'makeScoresVisible') {
	//make scores visible to students
	
	$examID = $data['examID'];
	
	makeScoresVisible($db, $examID);
}

if ($page == 'displayFinalScores') {
	// send student results tables to front end
	
	$studentUN = $data['studentResUN'];
	$examID = $data['examID'];
	
	displayFinalScores($db, $studentUN, $examID);
}

exit();

?>
