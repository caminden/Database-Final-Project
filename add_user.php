<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

echo("<form method=\"post\" action =\"add_user.php?sessionid=$sessionid\">
	Student?  <input type = \"checkbox\" name = \"sFlag\" value = \"$sFlag\"> </br>
        Admin?  <input type = \"checkbox\" name = \"aFlag\" value = \"$aFlag\">  </br>
	<input type = \"submit\" value=\"Move to info\">
        </form>
	");

echo("
        <form method = \"post\" action = \"welcomepage.php?sessionid=$sessionid\">
        <input type = \"submit\" value = \"Go Back\">
        </form>"
        );

$sFlag = (isset($_POST["sFlag"])) ? 1: 0;
$aFlag = (isset($_POST["aFlag"])) ? 1: 0;

if($aFlag == 1 && $sFlag == 0){
echo("
	<h3> Admin Add </h3>
	<form method=\"post\" action =\"add_user_action.php?sessionid=$sessionid\">
	User#(mandatory, up to 10#s): <input type =\"number\" value = \"$userid\" size = \"10\" name = \"userid\">
	</br>
	Login username (mandatory, 8 char max)): <input type =\"text\" value = \"$clientid\" size = \"8\" 
	name = \"clientid\"> 
	</br>
	Password (12 character max): <input type = \"text\" value = \"$password\" size = \"12\" name = \"password\">
	</br>
	<input type = \"submit\" value=\"Add\">
	</form>
	");	
}
if($sFlag == 1){
	echo("
	<h3> Student Add </h3>
 	<form method=\"post\" action =\"add_student_user_action.php?sessionid=$sessionid&sFlag=$sFlag&aFlag=$aFlag\">
	User#(mandatory, up to 10#s): <input type =\"number\" value = \"$userid\" size = \"10\" 
	name = \"userid\"> </br>
	Login username (mandatory, 8 char max)): <input type =\"text\" value = \"$clientid\" size = \"8\" 
	name = \"clientid\"> </br>
        Password (12 character max): <input type = \"text\" value = \"$password\" size = \"12\" name = \"password\"> </br></br>
	Name(first, last): <input type =\"text\" name = \"fname\"><input type =\"text\" name = \"lname\">
	Graduate?  <input type = \"checkbox\" name = \"typeFlag\" value = \"$typeFlag\"></br>
	Age: <input type = \"number\" name = \"age\"> </br>
	Address: </br>
<input type=\"text\" name=\"city\"><input type=\"text\" name=\"state\"><input type=\"number\" name=\"zip\">
<pre>(City)			(State - 2 letter)	(Zip)</pre>
	<input type = \"submit\" value=\"Add\">
	</form>
	");
}
?>

