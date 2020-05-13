<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);



//search clientid to ssee whose online
$sql = "select clientid " .
	"from myclientsession " .
	"where sessionid = '$sessionid'";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
	die("Failed to find client");
}
$values = oci_fetch_array($cursor);
$activeClient = $values[0];

//starts search for adminFlag
$sql = "select userID, aFlag, sFlag " .
	"from users " .
	"where clientid = '$activeClient'";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
	die("Failed to find user Flags");
}
$values = oci_fetch_array($cursor);
	$activeUser = $values[0];
	$aFlag = $values[1];
	$sFlag = $values[2];
oci_free_statement($cursor);


//search password for current user
$sql = "select password " .
        "from myclient " .
        "where clientid = '$activeClient'";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if($result == false){
        die("Failed to find user password");
}
$values = oci_fetch_array($cursor);
        $userpass = $values[0];
oci_free_statement($cursor);


if($sFlag == 1){
//get info from student (if they are a student) and print it
$sql = "select Sid, fname, lname, age, studentTypeFlag, statusFlag, addressCode " .
        "from students " .
        "where userID = $activeUser";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no student fould");
}
$values = oci_fetch_array($cursor);
	$studentID = $values[0];
	$firstName = $values[1];
	$lastName = $values[2];
	$age = $values[3];
	$studentType = $values[4];
	$status = $values[5];
	$userAddressCode = $values[6];
oci_free_statement($cursor);


//get address info
$sql = "select city, state, zip from address where addressCode = $userAddressCode";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no address given");
}
$values = oci_fetch_array($cursor);
	$city = $values[0];
	$state = $values[1];
	$zipcode = $values[2];
oci_free_statement($cursor);
}

// Here we can generate the content of the welcome page
echo("<center><h1> Homepage </h1></center>");
echo "<P align = right>";
echo("$activeClient is online </br>");
if($aFlag == 1) {echo(" *Admin* ");}
if($sFlag == 1) {echo(" *Student* ");}
echo "</br>";
echo("Click <A HREF = \"logout_action.php?sessionid=$sessionid\">here</A> to exit to login.");
echo("</P><hr></br>");

if($sFlag == 1){
	if($studentType == 1){
                $gradType = "Graduate";
        }
        if($studentType == 0){
                $gradType = "Undergraduate";
        }
        if($status == 1){
                $prob = "Not On Probation";
        }
        if($status == 0){
                $prob = "On Probation";
	}
	
//echo ("<h2>Student Info</h2>");
echo "<center><table border=0.5>";
	echo "<tr><th>Student ID</th> <th>Name</th> <th>Age</th> <th>Student Type</th> <th>Probation?</th>
		<th>Address</th> </tr>";
	echo "<tr> <td>$studentID</td> <td>$firstName $lastName</td> <td>$age</td> <td>$gradType</td>
		<td>$prob</td> <td>$city, $state, $zipcode</td> </tr>";
	echo "</table></center>";
	echo "</br>";
}


//admin only user search
if($aFlag == 1){
echo("
	<h3>User Search</h3>
	<form method=\"post\" action=\"serach_users.php?sessionid=$sessionid\">
	StudentID: <input type =\"text\" name = \"stuID\" size = \"8\">
	Name: <input type = \"text\" name = \"name\" size = \"8\">
	CRN:<input type = \"text\" name = \"crn\" size = \"8\"></br>
	Grad? <input type=\"checkbox\" name=\"graduate\">
	Undergrad? <input type=\"checkbox\" name=\"undergrad\">|
	On Probation? <input type=\"checkbox\" name=\"onprob\">
	Not On Probation? <input type=\"checkbox\" name=\"notprob\"></br> 
	(Show admins? <input type=\"checkbox\" name=\"admin\">)</br>
	<input type=\"submit\" value=\"Search\"> (Empty search to display all)
	</form>
	");
}


//displayed for all
echo("<h3>Password Change</h3>");
echo("<FORM method='POST' action=\"update_password.php?sessionid=$sessionid\">
	Old Password: <INPUT type='text' name = \"oldpass\" size = '12'> ($userpass) <br />
	New Password: <INPUT type='text' name = \"newpass\" size = '12' >
	<INPUT type='submit' name='submit' value='Change'>
	</FORM>");


if($sFlag == 1){
echo "<h3> Classes and Enrollment</h3>";	
echo("<A HREF = \"view_sections.php?sessionid=$sessionid&stuID=$studentID\">View Sections Taking/Taken</A>");
echo("</br></br>");
echo("<A HREF = \"enrollment.php?sessionid=$sessionid&stuID=$studentID\">Enroll</A>");
echo("</br></br>");
}

if($aFlag == 1){
	echo "<h3> Admin only <hr> </h3>";
	echo "<h4>Add Users </h4>";
	echo("<A HREF = \"add_user.php?sessionid=$sessionid\">Add User</A>");
	echo("</br>");
	echo "<h4> Enter grades </h4>";
	echo ("<A HREF = \"enter_grades.php?sessionid=$sessionid\">Enter Grade</A>");
}//end of admin flag rule


?>


