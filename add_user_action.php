<?
include "utility_functions.php";
$sessionid =$_GET["sessionid"];
verify_session($sessionid);
$sFlag = $_GET["sFlag"];
$aFlag = $_GET["aFlag"];

$userid = $_POST["userid"];
$clientid = $_POST["clientid"];
$password = $_POST["password"];


$sql = "insert into myclient  " .
	"values ('$clientid', '$password')";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  // Error handling interface.
  echo "<B>Insert Failed.</B> <BR />";
  display_oracle_error_message($cursor);
  die("<i>
  <form method=\"post\" action=\"welcomepage?sessionid=$sessionid\">
  Read the error message, and then try again:
  </form>
  </i>
  ");
}
oci_free_statement($cursor);

$sql = "insert into users " .
	"values ('$userid', '$clientid', 1, 0)";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if ($result == false){
	echo "<B>Insertion Failed.</B> <BR />";
	display_oracle_error_message($cursor);
	die("Terminated");
}
oci_free_statement($cursor);

Header("Location:welcomepage.php?sessionid=$sessionid");

?>
