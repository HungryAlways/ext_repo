<?php
require("send_mail.php");
$allowed_release_list = array("HDR5401", "HDR55");
function is_allowed_release($rel){
	global $allowed_release_list;
	foreach($allowed_release_list as $val){
		if($rel == $val){
			return true;
		}
	}
    return false;
}
function is_request_exit($fr_id){
  $con = mysql_connect("localhost","root","123456");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }
  mysql_select_db("repo_db", $con);

  $result = mysql_query("SELECT fr_id FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);
  if(mysql_fetch_array($result)){ 
     mysql_close($con);
     return true;
  }

  mysql_close($con);
  return false;
}

function copy_patch_file($tmp_name, $name, $fr_id){
	$save_file_name = "patchs/" . $fr_id . "_" . $name;
	move_uploaded_file($tmp_name, $save_file_name);
	return $save_file_name;
}

function insert_request_to_db($repo_name, $fr_id, $release, $fdt, $review_board_id, $user_name, $patch_file_name, $description, $state, $dependency){
  $con = mysql_connect("localhost","root","123456");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db("repo_db", $con);

  $fr_description = htmlentities($fr_info[4], ENT_QUOTES);
  if(strlen($fr_info[12]) <= 1)
    $fr_engineer = "";
  else
    $fr_engineer = $fr_info[12];

  $result = mysql_query("SELECT fr_id FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);
  if(!mysql_fetch_array($result)){ 
     mysql_query("INSERT INTO external_repo_manage_table (extra_repo, fr_id, rel, fdt, review_board_id, user_name, patch_file_name, description, state, dependency) VALUES('$repo_name','$fr_id', '$release','$fdt','$review_board_id','$user_name','$patch_file_name','$description','$state', '$dependency')", $con); 

	 $mail_content = "";
     $mail_to = "";
	 $result = mysql_query("SELECT * FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);
     while($row = mysql_fetch_array($result))
     {
      $mail_content .= format_one_line($row);
      $mail_to .= get_mail_to($row['extra_repo']);
     }
     send_mail("ExtRepo <ext_repo@localdomain.com>" ,$mail_to,"Ext repo request has been submitted!",$mail_content);
  }

  mysql_close($con);
}

$repo_name = "";
$fr_id = "";
$release = "";
$fdt = "";
$review_board_id = "";
$user_name = "";
$patch_file_name = "";
$patch_file_tmp_name = "";
$save_file_name = "";
$description = "";
$dependency = "";

if(isset($_FILES["patch_file"]["name"])){
  if ($_FILES["patch_file"]["error"] > 0)
  {
    echo "Patch file Error: " . $_FILES["patch_file"]["error"] . "<br />";
	exit("ERR: patch file is not set!");
  }
  else
  {
	$patch_file_name = $_FILES["patch_file"]["name"];
    $patch_file_tmp_name = $_FILES["patch_file"]["tmp_name"];
	echo "Temp patch file: " . $patch_file_tmp_name . "<br>";
    echo "Patch file name: " . $patch_file_name . "<br>";	  
  }
}

if(isset($_POST["repo_name"])){
  $repo_name = $_POST["repo_name"];
  echo "repo_name:" . $repo_name . "<br>";
}
if(isset($_GET["repo_name"])){
  $repo_name = $_GET["repo_name"];
  echo "repo_name:" . $repo_name . "<br>";
}
if($repo_name == "")
  exit("ERR: repo name is not right!");

if(isset($_POST["fr_id"])){
  $fr_id = $_POST["fr_id"];
  echo "fr_id:" . $fr_id . "<br>";
}
if(isset($_GET["fr_id"])){
  $fr_id = $_GET["fr_id"];
  echo "fr_id:" . $fr_id . "<br>";
}
if($fr_id == "")
  exit("ERR: fr/ir id is not right!");

if(isset($_POST["release"])){
  $release = $_POST["release"];
  echo "release:" . $release . "<br>";
}
if(isset($_GET["release"])){
  $release = $_GET["release"];
  echo "release:" . $release . "<br>";
}

if(isset($_POST["fdt"])){
  $fdt = $_POST["fdt"];
  echo "fdt:" . $fdt . "<br>";
}
if(isset($_GET["fdt"])){
  $fdt = $_GET["fdt"];
  echo "fdt:" . $fdt . "<br>";
}

if(isset($_POST["dependency"])){
  $dependency = $_POST["dependency"];
  echo "fdt:" . $dependency . "<br>";
}
if(isset($_GET["dependency"])){
  $dependency = $_GET["dependency"];
  echo "dependency:" . $dependency . "<br>";
}

if(isset($_POST["review_board_id"])){
  $review_board_id = $_POST["review_board_id"];
  echo "review_board_id:" . $review_board_id . "<br>";
}
if(isset($_GET["review_board_id"])){
  $review_board_id = $_GET["review_board_id"];
  echo "review_board_id:" . $review_board_id . "<br>";
}

if(isset($_POST["user_name"])){
  $user_name = $_POST["user_name"];
  echo "user_name:" . $user_name . "<br>";
}
if(isset($_GET["user_name"])){
  $user_name = $_GET["user_name"];
  echo "user_name:" . $user_name . "<br>";
}

if(isset($_POST["description"])){
  $description = $_POST["description"];
  echo "description:" . $description . "<br>";
}
if(isset($_GET["description"])){
  $description = $_GET["description"];
  echo "description:" . $description . "<br>";
}

if(is_request_exit($fr_id)){
    exit("ERR: fr/ir id exist already, you can use ALU01234567_0,ALU01234567_1...!");
}

if(!is_allowed_release($release)){
	exit("ERR: $release is not allowed for dropping since it is maint or post-dr2 release! <br>You can refer to the policy link at bottom!");
}

$save_file_name = copy_patch_file($patch_file_tmp_name, $patch_file_name, $fr_id);
$state = "New";

insert_request_to_db($repo_name, $fr_id, $release, $fdt, $review_board_id, $user_name, $patch_file_name, $description, $state, $dependency);

echo "The request is submitted successfully!";
?>
