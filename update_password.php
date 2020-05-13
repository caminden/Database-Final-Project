<?
include "utility_functions.php";
$sessionid = $_GET["sessionid"];
verify_session($sessionid);

//ini_set("display_errors", 0):

$oldpass = $_POST["oldpass"];
$newpass = $_POST["newpass"];

//echo("$oldpass");
//echo("$newpass");

$sql = "update myclient set password = '$newpass' where password = '$oldpass' ";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  // Error handling interface.
  echo "<B>Update Failed.</B> <BR />";
  display_oracle_error_message($cursor);
  die("<i> 
  <form method=\"post\" action=\"welcomepage?sessionid=$sessionid\">
  <input type=\"hidden\" value = \"$oldpass\" name = \"oldpass\"> 
  Read the error message, and then try again:
  </form>
  </i>
  ");
}

$sql = "commit";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

Header("Location:welcomepage.php?sessionid=$sessionid");
 
?>
