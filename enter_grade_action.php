<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);
$crn = $_GET["crn"];
$stuID = $_GET["stuID"];
$grade = $_POST["grade"];

//echo "$crn, $stuID, $grade";
$sql = "begin
	lock table enrolledIn in exclusive mode;
	update enrolledIn set grade = $grade
	where Sid = '$stuID' and crn = $crn;
	commit;
	GPA;
end;
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}

Header("Location:enter_grades.php?sessionid=$sessionid");

?>
