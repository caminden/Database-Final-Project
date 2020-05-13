<?
include "utility_functions.php";

$sessionid = $_GET["sessionid"];
verify_session($sessionid);
$stuID = $_GET["stuID"];
$deadline = $_GET["dl"];

$crn1 = $_POST["enroll_crn1"];
$crn2 = $_POST["enroll_crn2"];	
$crn3 = $_POST["enroll_crn3"];
$crn4 = $_POST["enroll_crn4"];
$crn5 = $_POST["enroll_crn5"];

$i = 0;
$enroll_crn = array(5);

if (isset($crn1) and trim($crn1) !=""){
	$enroll_crn[$i] = $crn1;
	$i++;
};
if (isset($crn2) and trim($crn2) !=""){
        $enroll_crn[$i] = $crn2;
	$i++;
};
if (isset($crn3) and trim($crn3) !=""){
        $enroll_crn[$i] = $crn3;
        $i++;
};
if (isset($crn4) and trim($crn4) !=""){
        $enroll_crn[$i] = $crn4;
        $i++;
};
if (isset($crn5) and trim($crn5) !=""){
        $enroll_crn[$i] = $crn5;
        $i++;
};

$returnMessage = array();
for($x = 0; $x < $i; $x++) {
	$ReqMet = false;
	$notTaken = true;
	//start search for prereqs and already passed classes
	//this sql finds the info attached to this cnr
	$currentcrn = $enroll_crn[$x];
	echo("$currentcrn");
	$sql = "select sectionId, prereq from section where crn=$currentcrn";
	$result_array = execute_sql_in_oracle ($sql);
	$result = $result_array["flag"];
	$cursor = $result_array["cursor"];
	if ($result == false){
	  display_oracle_error_message($cursor);
	 die("Client Query Failed.");
	}
	$values = oci_fetch_array($cursor);
	$sectionID = $values[0];		//currentcrn section id (ie CMS####)
	$prereq_number = $values[1];		//currrentcrn prereq crn
	oci_free_statement($cursor);

	echo "PrereqID: $prereq_number </br>";

	//find prereq section if has one
	if($prereq_number != null){
		$sql = "select sectionId from section where crn=$prereq_number";
		$result_array = execute_sql_in_oracle ($sql);
	        $result = $result_array["flag"];
	        $cursor = $result_array["cursor"];
        	if ($result == false){
        	  display_oracle_error_message($cursor);
        	  die("Client Query Failed.");
		}
	        $values = oci_fetch_array($cursor);
		$prereqSectionID = $values[0];
	oci_free_statement($cursor);
	}
	else{
		$ReqMet = true;
	}
	
	//find sections this student has taken
	$sql = "select sectionId from enrolledIn e1 join section s1 on e1.crn=s1.crn where Sid = '$stuID'";
	$result_array = execute_sql_in_oracle ($sql);
        $result = $result_array["flag"];
        $cursor = $result_array["cursor"];
        if ($result == false){
          display_oracle_error_message($cursor);
         die("Client Query Failed.");
	}
        while($values = oci_fetch_array($cursor)){
		$stuSectionTaken = $values[0];
		if($prereqSectionID == $stuSectionTaken){
			$ReqMet = true;
		}
		if($stuSectionTaken == $sectionID){
			$notTaken = false;
		}
		//echo("SECTIONS: $stuSectionTaken </br>");
	}	
	echo("REQ: $ReqMet, TAKEN: $notTaken </br>");
	//find seat count left
	//if prereq matches one of the taken, enroll = true
	//if one of the sections matches what is being enrolled into, enroll = false
	if($ReqMet == true && $notTaken == true){		
		$connection = oci_connect("gq050", "iuenwh", "gqiannew2:1521/pdborcl");
		$sql = 'BEGIN ' . 
			'Enroll(:Sid, :deadline, :crn, :output); ' .
			'END; ';
		$stmt = oci_parse($connection, $sql);
		oci_bind_by_name($stmt, ':Sid', $stuID, 32);
		oci_bind_by_name($stmt, ':deadline', $deadline, 12);
		oci_bind_by_name($stmt, ':crn', $currentcrn);
		oci_bind_by_name($stmt, ':output', $message, 32);
		$input = $currentcrn;
		oci_execute($stmt);
		array_push($returnMessage, $message . " ");
	}
	else{
		if($ReqMet == false) $error = "Reqirement not met";
		if($notTaken == false) $error = "Already taken/enrolled";
		array_push($returnMessage, $error . " ");
	}
	
}//end of for 
//echo $returnMessage[0], $returnMessage[1];
Header("Location:enrollment.php?sessionid=$sessionid&stuID=$stuID&ret1=$returnMessage[0]&ret2=$returnMessage[1]&ret3=$returnMessage[2]&ret4=$returnMessage[3]&ret5=$returnMessage[4]&i=$i");
?>
