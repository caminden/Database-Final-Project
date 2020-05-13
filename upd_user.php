<?
include "utility_functions.php";
$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$userid = $_GET["userid"];

//quere for student info
$sql = "select Sid, fname, lname, age, studentTypeFlag, statusFlag, city, state, zip, s1.addressCode " .
	"from students s1 join address a1 on s1.addressCode=a1.addressCode " .
	"where userID = $userid ";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
$values = oci_fetch_array($cursor);
	$stuID = $values[0];
        $fname = $values[1];
        $lname = $values[2];
        $age = $values[3];
        $sTypeFlag = $values[4];
        $statusFlag = $values[5];
        $city = $values[6];
        $state = $values[7];
	$zip = $values[8];
	$addrCode = $values[9];
oci_free_statement($cursor);

//start query for myclient and user info
$sql = "select password, u1.clientid, aFlag " .
	"from users u1 join myclient c1 on u1.clientid=c1.clientid " .
	"where userID = '$userid'";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
$values = oci_fetch_array($cursor);
	$password = $values[0];
	$username = $values[1];
	$aFlag = $values[2];
oci_free_statement($cursor);

if($sTypeFlag == null){
	$sTypeFlag = 0;
}
if($statusFlag == null){
	$statusFlag = 0;
}

//echo "$stuID, $fname, $lname, $age, $sTypeFlag, $statusFlag, $city, $state, $zip, $password, $username";


echo("<h3>Update info</h3> ");
echo "<h5>Immutable Data</h5>";
echo "<form method=\"post\" action=\"upd_user_action.php?sessionid=$sessionid
	&aFlag=$aFlag&stFlag=$statusFlag&grFlag=$sTypeFlag&userID=$userid&addrCode=$addrCode&client=$username\">";
	echo "StudentID: <input type=\"text\" readonly value = \"$stuID\"></br>";
	echo "UserID: <input type='text' readonly value = \"$userid\"></br>";
	echo "Username: <input type=\"text\" readonly value =\"$username\"></br></br>";

echo"<h5>Mutable Data</h5>";
	echo "Name: <input type=\"text\" value=\"$fname\" name=\"fname\">
	<input type=\"text\" value=\"$lname\" name=\"lname\"></br>";
	echo "Age: <input type=\"number\" value=\"$age\" name=\"age\"></br>";
	echo "Address: <input type=\"text\" value=\"$city\" name=\"city\">
		<input type=\"text\" value=\"$state\" name=\"state\">
		<input type=\"text\" value=\"$zip\" name=\"zip\"> </br>";
	echo "Password: <input type=\"text\" value=\"$password\", name=\"password\"></br>";
	echo "Graduate Status: <input type=\"checkbox\" name=\"gradType\">$sTypeFlag</br>";
	echo "Probation Status: <input type=\"checkbox\" name=\"statusType\">$statusFlag</br>";
	echo "Admin?: <input type=\"checkbox\" name=\"aType\">$aFlag</br>";
	
	echo "<input type=\"submit\" value=\"Update\"> </form>";

echo("</br>
	<form method=\"post\" action=\"update_password.php?sessionid=$sessionid\">
       	Old Password: <INPUT type='text' name = \"oldpass\" readonly value = \"$password\" size = '12'> </br>
        New Password: <INPUT type='text' name = \"newpass\" readonly value = \"password\" size = '12'>
        <INPUT type='submit' name='submit' value='Change'>
       </FORM>");

echo("	</br>
        <form method = \"post\" action = \"welcomepage.php?sessionid=$sessionid\">
        <input type = \"submit\" value = \"Go Back\">
        </form>"
       );

?>

