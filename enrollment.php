<?
include "utility_functions.php";

$sessionid = $_GET["sessionid"];
verify_session($sessionid);
$stuID = $_GET["stuID"];
$returnMessage = array();
$r1 = $_GET["ret1"];
$r2 = $_GET["ret2"];
$r3 = $_GET["ret3"];
$r4 = $_GET["ret4"];
$r5 = $_GET["ret5"];
$i = $_GET["i"];
//set deadline for enrollment
//dd-mm-yyyy
$deadline = '15-05-2020';
array_push($returnMessage, $r1, $r2, $r3, $r4, $r5);
$j = 1;
for($x = 0; $x < $i; $x++){
	echo "#$j ";
	echo $returnMessage[$x];
	echo " ";
	$j++;
}

//get info for search
echo("
  <form method=\"post\" action=\"enrollment.php?sessionid=$sessionid&stuID=$stuID\">
  Semester: <select name=\"q_semester\">
	<option value=\"Sp\">Spring</option>
	<option value=\"Su\">Summer</option>
	<option value=\"Fa\">Fall</option>
	<option value=\"empty\" selected>  </option>
	</select>
  Course#(CMS####): <input type=\"text\" size=\"20\" maxlength=\"50\" name=\"q_course\">
  <input type=\"submit\" value=\"Search\"> (search empty to display all)
  </form>

  <form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>

  ");

$q_semester=$_POST["q_semester"];
$q_course=$_POST["q_course"];
$where = " 1=1 ";

if (isset($q_course) and trim($q_course)!=""){
	$where  .= "and sectionId like '%$q_course%'";
}
if($q_semester!='empty'){
	$where .= " and semester = '$q_semester'";
}

//query for finding future classes to enroll in with or without search input
$sql = " select s1.crn, sectionId, title, credits, semester, year, time, seats, enrolledCount  " .
	"from section s1 left join enrolled e1 on s1.crn=e1.crn " .
	"where $where and s1.crn NOT IN " .
	"(select crn from section where year < 2020 or (semester = 'Sp' and year = 2020))";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

//display section for entering crns
echo("<form method=\"post\" action=\"enroll_action.php?sessionid=$sessionid&stuID=$stuID&dl=$deadline\">
	Enter CRN to enroll: <input type=\"text\" size=\"5\" name=\"enroll_crn1\">
	<input type=\"text\" size=\"5\" name=\"enroll_crn2\">
	<input type=\"text\" size=\"5\" name=\"enroll_crn3\">
	<input type=\"text\" size=\"5\" name=\"enroll_crn4\">
	<input type=\"text\" size=\"5\" name=\"enroll_crn5\">

	<input type=\"submit\" value=\"Enroll\">
	</form> Deadline: $deadline
	");

echo "<table border=1>";
echo "<tr> <th>CRN</th> <th>SectionID</th> <th>Title</th> <th>Credits</th> <th>Semester</th> <th>Year</th>
       	<th>Time</th> <th>Max Seats</th> <th>Currently Enrolled</th> <th>Seats Left</th> </tr>";

while($values = oci_fetch_array($cursor)){
	$crn = $values[0];
	$sectionId = $values[1];
	$title = $values[2];
	$credits = $values[3];
	$semester = $values[4];
	$year = $values[5];
	$time = $values[6];
	$seats = $values[7];
	$enrolled = $values[8];
	
	if($enrolled == null){
		$enrolled = 0;
	}	
	$seats_left = $seats - $enrolled;
	echo("<tr>" .
	"<td>$crn</td> <td>$sectionId</td> <td>$title</td> <td>$credits</td> ".
	"<td>$semester</td> <td>$year</td> <td>$time</td> <td>$seats</td> <td>$enrolled</td> " .
	"<td>$seats_left</td>".
	"</tr>");

}//end while loop

echo "</table>";
oci_free_statement($cursor);
?>
