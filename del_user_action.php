<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$addrCode = $_GET["addrCode"];
$userid = $_POST["userid"];
$client = $_POST["clientid"];
$stuID = $_POST["stuID"];

//delete from enrolledIn
$sql = "begin
	lock table enrolledIn in exclusive mode;
	delete from enrolledIn where Sid = '$stuID';
	commit;
end;
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("delete failed");
}
oci_free_statement($cursor);

//delete from student table
$sql = "begin
	lock table student in exclusive mode;
	delete from students where Sid = '$stuID';
	commit;
end; 
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("delete failed");
}
oci_free_statement($cursor);

//delete from address, next in line
$sql = "begin
        lock table address in exclusive mode;
        delete from address where addressCode = $addrCode;
	commit;
end;
";

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("delete failed");
}
oci_free_statement($cursor);


$sql = "begin
	lock table users in exclusive mode;
	delete from users where userID = $userid;
	commit;
end;
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("delete failed");
}
oci_free_statement($cursor);


$sql = "begin
        lock table myclient in exclusive mode;
        delete from myclient where clientid = '$client';
        commit;
end;
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("delete failed");
}
oci_free_statement($cursor);


//head back home
Header("Location:welcomepage.php?sessionid=$sessionid");

?>
