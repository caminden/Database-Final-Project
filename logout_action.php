<?
include "utility_functions.php";

$sessionid = $_GET["sessionid"];
verify_session($sessionid);

$sql = "delete from myclientsession where sessionid = '$sessionid'";

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result==false){
	display_oracle_error_message($cursor);
	die("Session removal failed");
}

header("Location:login.html");
?>
