<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$studentID =$_GET["stuID"];

$sql = "select sectionId, sc.crn, title, semester, year, credits, grade " .
	"from enrolledIn e1 join section sc on e1.crn = sc.crn " .
	"where Sid = '$studentID' and year < 2020 or (Sid = '$studentID' and year = 2020 and semester='Sp') " .
	"order by year desc";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no address given");
}
while($values = oci_fetch_array($cursor)){
        $sectionID = $values[0];
	$crn = $values[1];
	$title = $values[2];
	$semester = $values[3];
	$year = $values[4];
	$credits = $values[5];
	$grade = $values[6];

	$courseCount = $courseCount+1;
	$adjustedgrade = $grade * $credits;
	$gradesum = $gradesum + $adjustedgrade;
	$creditsum = $creditsum + $credits;

if($year == 2020){
echo("Current sections: </br>");
echo("<pre>SectionID: $sectionID 	CRN: $crn
Class: $title 	Semester: $semester 
Credits: $credits 		Grade: $grade 
<A HREF = \"gen_sec_info.php?sessionid=$sessionid&crn=$crn\">More Info</A></pre>
</br>
");
}
if($year < 2020){
echo("Past sections: </br>");	
echo("<pre>SectionID: $sectionID	CRN: $crn
Class: $title	Semester: $semester, Year: $year
Credits: $credits		Grade: $grade 
<A HREF = \"gen_sec_info.php?sessionid=$sessionid&crn=$crn\">More Info</A> </pre> 
</br>
");
}

}//end of while loop


$finalGPA = $gradesum/$creditsum;
oci_free_statement($cursor);

echo("<pre>Courses Completed:		Credits Earned:			GPA so far: 	
$courseCount				$creditsum				$finalGPA
	</pre>
");


?>
