<?
include "utility_functions.php";
$sessionid =$_GET["sessionid"];
verify_session($sessionid);

$aFlag = $_GET["aFlag"];
$stFlag = $_GET["stFlag"];
$grFlag = $_GET["grFlag"];
$userid = $_GET["userID"];
$addrCode = $_GET["addrCode"];
$client = $_GET["client"];
//get original values from browser to compare to later

$fname = $_POST["fname"];
$lname = $_POST["lname"];
$age = $_POST["age"];
$city = $_POST["city"];
$state = $_POST["state"];
$zip = $_POST["zip"];
$password = $_POST["password"];
$newStFlag = (isset($_POST["statusType"])) ? 1: 0;
$newAFlag = (isset($_POST["aType"])) ? 1: 0;
$newGrFlag = (isset($_POST["gradType"])) ? 1: 0;

//check flags and either reverse if clicked or set if clicked
if($newAFlag == 1){
	if($aFlag==1){$aFlag=0;}
	else{ $aFlag=1;}
}
if($newStFlag == 1){
        if($stFlag==1){$stFlag=0;}
        else{ $stFlag=1;}
}
if($newGrFlag == 1){
        if($grFlag==1){$grFlag=0;}
        else{ $grFlag=1;}
}

$sql = "begin
        lock table students in exclusive mode;
        update students set fname = '$fname', lname = '$lname', age = $age,
        studentTypeFlag = $grFlag, statusFlag = $stFlag
        where userID = $userid;
	lock table users in exclusive mode;
        update users set aFlag = $aFlag
        where userID = $userid;
        lock table address in exclusive mode;
        update address set city = '$city', state = '$state', zip = $zip
        where addressCode = $addrCode;
        lock table myclient in exclusive mode;
        update myclient set password = '$password' where clientid = '$client';
	commit;
end;
";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];
if($result == false){
        die("no contact found");
}

Header("Location:welcomepage.php?sessionid=$sessionid");

?>

