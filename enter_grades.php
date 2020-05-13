<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

echo " <style>
	table, th, td {
	border: 1px solid black;
	border-collapse: collapse;
	}
	th, td {
	text-align: left;
	}
	</style>";

echo "<table>";
echo "<tr> <th>StudentID</th><th>Courses</th><th>Grade</th><th>Update Grade</th></tr>";
$sql = "select c1.Sid, crncount " .
	"from students s1 join crnCount c1 on s1.Sid=c1.Sid " .
	"where c1.Sid in (select Sid from enrolledIn) ";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}
while($values = oci_fetch_array($cursor)){
	$stuID = $values[0];
	$crnCount = $values[1];
	$crnCount = $crnCount+1;	
	echo "<tr> <td rowspan=\"$crnCount\">$stuID</td> ";
	
	$sql2 = "select e1.crn, grade " .
                "from enrolledIn e1 join section s1 on e1.crn=s1.crn " .
		"where Sid = '$stuID' and year > 2019";
                $connection = oci_connect("gq050", "iuenwh", "gqiannew2:1521/pdborcl");
                $stmt = oci_parse($connection, $sql2);
                $output = oci_execute($stmt);
                if($output == false){oci_close($connection); die("error");}
                //start loop for printing courses matching crn input
		while($value = oci_fetch_array($stmt)){
			$courses = $value[0];
			$grade = $value[1];
			echo "<tr>";
			echo("<td> $courses</td>
			<td>
	<form method=\"post\" action=\"enter_grade_action.php?sessionid=$sessionid&crn=$courses&stuID=$stuID\">
	<input type=\"number\" min=0 max=4 step=\"0.1\" value=\"$grade\" name=\"grade\">
			</td>");
			echo "<td> <input type=\"submit\" value=\"Enter Grade\"> </td>";
			echo "</form></tr>";
		}
                oci_free_statement($stmt);
	echo "</tr>";
}//end of while

  echo"<form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\"></form>";

?>
