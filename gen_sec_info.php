<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$crn =$_GET["crn"];

$sql = "select sectionId, title, description, credits, semester, year, time " .
	"from section " .
	"where crn = $crn";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("Failed to find more info");
}
$values = oci_fetch_array($cursor);
	$section = $values[0];
	$title = $values[1];
	$descrip = $values[2];
	$credits = $values[3];
	$semester = $values[4];
	$year = $values[5];
	$time = $values[6];


echo("Section: $section </br>
	CRN: $crn </br>
	Title: $title </br>
	Credits: $credits </br>
	$semester Semester of year $year </br>
	Class Time $time </br>
	General Descripton: </br> $descrip </br>
");
?>
