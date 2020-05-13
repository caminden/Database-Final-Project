<?
include "utility_functions.php";
$sessionid =$_GET["sessionid"];
verify_session($sessionid);
$sFlag = $_GET["sFlag"];
$aFlag = $_GET["aFlag"];

//get info from add_user
$userid = $_POST["userid"];
$clientid = $_POST["clientid"];
$password = $_POST["password"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$age = $_POST["age"];
$typeFlag = (isset($_POST["typeFlag"])) ? 1: 0;
$city = $_POST["city"];
$state = $_POST["state"];
$zip = $_POST["zip"];

$addressCode = mt_rand();

//lock table before next queries of reading and inserting
$sql = "lock table students in exclusive mode";

 $result_array = execute_sql_in_oracle ($sql);
 $result = $result_array["flag"];
 $cursor = $result_array["cursor"];
if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}


//query for getting the new number for studentID
//places lock prior to reading to ensure the value is right
$sql = "select Sid from students " .
        "order by Sid desc " .
        "fetch first 1 row with ties";
 $result_array = execute_sql_in_oracle ($sql);
 $result = $result_array["flag"];
 $cursor = $result_array["cursor"];
if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);
$string = $values[0];   //fetch Sid from the highest number
oci_free_statement($cursor);
//get the 6 numbers that follow the first letters
$substring = $string[2] . $string[3] . $string[4] . $string[5] . $string[6] . $string[7];
//change that to an int value and add 1 to get the new number
$newNum = intval($substring) + 1;

//generating new Sid
$Sid = strtolower($fname[0]) . strtolower($lname[0]) . $newNum;

//echo("$userid, $clientid, $password, $fname, $lname, $age, $typeFlag, $city, $state, $zip, $addressCode, $Sid");

//student add query
$sql = "begin " .
	"insert into myclient values('$clientid', '$password'); " .
	"insert into users values($userid, '$clientid', $aFlag, $sFlag); " .
	"insert into address values($addressCode, '$city', '$state', $zip); " .
	"insert into students values('$Sid', $userid, '$fname', '$lname', $age, $typeFlag, 1, $addressCode, 0.0); " .
	"commit; " .
	"end; ";
 $result_array = execute_sql_in_oracle ($sql);
 $result = $result_array["flag"];
 $cursor = $result_array["cursor"];
if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

Header("Location:welcomepage.php?sessionid=$sessionid&Sid=$Sid");

?>
