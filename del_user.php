<?
include "utility_functions.php";
$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$userid = $_GET["userid"];

$sql = "select Sid, addressCode, fname, lname " .
	"from students " .
	"where userID = $userid ";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
$values = oci_fetch_array($cursor);
	$stuID = $values[0];
	$addrCode = $values[1];
	$fname = $values[2];
	$lname = $values[3];
oci_free_statement($cursor);

$sql = "select u1.clientid " .
	"from users u1 join myclient c1 on u1.clientid = c1.clientid " . 
	"where userID = $userid";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
$values = oci_fetch_array($cursor);
	$client = $values[0];

echo("
	<form method=\"post\" action =\"del_user_action.php?sessionid=$sessionid&addrCode=$addrCode\">
	Name:      <input type = \"text\" readonly value =\"$fname $lname\" size=\"20\"> </br>
	userID:    <input type =\"number\" readonly value = \"$userid\" size = \"10\" name = \"userid\"> </br>
	username:  <input type =\"text\" readonly value = \"$client\" size = \"20\" name = \"clientid\"> </br>
	StudentID: <input type =\"text\" readonly value=\"$stuID\" name=\"stuID\"></br>
	<input type =\"submit\" value=\"Delete\">
	</form>"	
);

echo("
        <form method = \"post\" action = \"welcomepage.php?sessionid=$sessionid\">
        <input type = \"submit\" value = \"Go Back\">
        </form>"
        );

?>

