<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$stuID = $_POST["stuID"];
$name = $_POST["name"];
$crn = $_POST["crn"];
$gradFlag = (isset($_POST["graduate"])) ? 1: 0;
$ungradFlag = (isset($_POST["undergrad"])) ? 1: 0;
$onprob = (isset($_POST["onprob"])) ? 1: 0;
$notprob = (isset($_POST["notprob"]))? 1: 0;
$admin = (isset($_POST["admin"]))? 1: 0;

$where = "1=1 ";
$nameparts = explode(" ", $name);
$fname = $nameparts[0];
$lname = $nameparts[1];

if(isset($stuID) and trim($stuID) != ""){
	$where .= "and Sid like '%$stuID%' ";
}
if(isset($fname) and trim($fname) != ""){
	$where .= "and fname like '%$fname%' ";
}
if(isset($lname) and trim($lname) != ""){
	$where .= "and lname like '%$lname%' ";
}
//check all flag combos on graduate status
if($gradFlag == 1 && $ungradFlag ==1){
	die("they can only be grad or ungrad, not both");
}
else if($gradFlag == 1){
	$where .= "and studentTypeFlag = 1 ";
}
else if($ungradFlag == 1){
	$where .= "and studentTypeFlag = 0 ";
}
else{
	$where;
}
//check all flag combos on probation status
if($onprob == 1 && $notprob ==1){
        die("they can only be on probation or not, not both");
}
else if($onprob == 1){
        $where .= "and statusFlag = 0 ";
}
else if($notprob == 1){
        $where .= "and statusFlag = 1 ";
}
else{
        $where;
}

//query to find students matching criteria
$sql = "select Sid, fname, lname, age, studentTypeFlag, statusFlag, city, state, zip, userID " .
	"from students s1 join address a1 on s1.addressCode = a1.addressCode " .
	"where $where";	
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
echo "<h3>Students</h3>";
echo "<table border=1>";
echo "<tr> <th>StudentID</th> <th>Name</th> <th>Age</th> <th>Student Type</th> <th>Probation?</th> 
	<th>Address</th> <th>Classes</th>";
echo"</tr>";
//start loop for printing students
while($values = oci_fetch_array($cursor)){
	$stuIDprint = $values[0];
	$fnameprint = $values[1];
	$lnameprint = $values[2];
	$ageprint = $values[3];
	$STFprint = $values[4];
	$SFprint = $values[5];
	$cityprint = $values[6];
	$stateprint = $values[7];
	$zipprint = $values[8];
	$userid = $values[9];
        if($STFprint == 1){
                $gradType = "Graduate";
        }
        if($STFprint == 0){
                $gradType = "Undergraduate";
        }
        if($SFprint == 1){
                $prob = "Not On Probation";
        }
        if($SFprint == 0){
                $prob = "On Probation";
        }
	echo "<tr> " .
	"<td>$stuIDprint</td> <td>$fnameprint $lnameprint</td> <td>$ageprint</td> <td>$gradType</td> " .
	"<td>$prob</td> <td>$cityprint, $stateprint, $zipprint </td>";

	//start search for sections, either specified or all
	if(isset($crn) and trim($crn) != ""){
		$sql2 = "select crn " .
                	"from enrolledIn e1 join students s1 on e1.Sid = s1.Sid " .
			"where e1.Sid = '$stuIDprint' and e1.crn like '%$crn%'";
		$connection = oci_connect("gq050", "iuenwh", "gqiannew2:1521/pdborcl");
		$stmt = oci_parse($connection, $sql2);
		$output = oci_execute($stmt);
		if($output == false){oci_close($connection); die("error");}
		echo"<td>";
		//start loop for printing courses matching crn input
		while($value = oci_fetch_array($stmt)){
			$courses = $value[0];
			echo("|$courses|");
		}
		oci_free_statement($stmt);
		echo"</td>";
	}else{
		$sql2 = "select crn " .
			"from enrolledIn " .
			"where Sid = '$stuIDprint'";
		$connection = oci_connect("gq050", "iuenwh", "gqiannew2:1521/pdborcl");
                $stmt = oci_parse($connection, $sql2);
                $output = oci_execute($stmt);
                if($output == false){oci_close($connection); die("error");}
		echo"<td>";
		//start looop for printing all courses this student is taking
                while($value = oci_fetch_array($stmt)){
			$courses = $value[0];
                        echo("|$courses|");
                }
                oci_free_statement($stmt);
                echo"</td>";
	}

	echo "<td><A HREF=\"upd_user.php?sessionid=$sessionid&userid=$userid\">Update</A></td>";
	echo "<td><A HREF=\"del_user.php?sessionid=$sessionid&userid=$userid\">Delete</A></td>";
	echo"</tr>";

}	//end of student table
echo "</table> </br> </br>";
oci_free_statement($cursor);


//search admins to display if admin flag was raised
if($admin == 1){
echo "<h3> Admins </h3>";
echo "<table border=1>";
echo "<tr> <th>AdminID</th> <th>Username</th> <th>Password</th>";
echo"</tr>";
	$sql = "select userid, u1.clientid, password " .
		"from users u1 join myclient c1 on u1.clientid = c1.clientid " .
		"where aFlag = 1";
	$result_array = execute_sql_in_oracle ($sql);
	$result = $result_array["flag"];
	$cursor = $result_array["cursor"];
	if($result == false){
        die("error finding admin");
	}
	while($values = oci_fetch_array($cursor)){
		$userid = $values[0];
		$clientid = $values[1];
		$password = $values[2];
	echo "<tr>";
        echo"<td>$userid</td> <td>$clientid</td> <td>$password</td>";
	echo "</tr>";
	}
	echo"</table>";
}//end of admin table
oci_free_statement($cursor);

?>
