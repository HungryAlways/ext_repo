<?php
require("send_mail.php");
function copy_patch_file($tmp_name, $name, $fr_id){
	$save_file_name = "patchs/" . $fr_id . "_" . $name;
	move_uploaded_file($tmp_name, $save_file_name);
	return $save_file_name;
}

function update_column($fr_id, $field, $value){
  $con = mysql_connect("localhost","root","123456");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }
  mysql_select_db("repo_db", $con);

  mysql_query("UPDATE external_repo_manage_table SET $field = '$value' WHERE fr_id = '$fr_id'", $con);

  $result = mysql_query("SELECT * FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);

  mysql_close($con);

  if($field != "state")
	  return;

  $mail_content = "";
  $mail_to = "";
  
  if($value == "Delivered"){
	  $mail_content .= "Hi,<br>";
	  $mail_content .= "Your request has been dropped,please modify the configure file in your fdt with the latest changeset and do verification and drop in fdt repo.<br>";
  }
  while($row = mysql_fetch_array($result))
  {
      $mail_content .= format_one_line($row);
      $mail_to .= get_mail_to($row['extra_repo']);
  }
  send_mail("ExtRepo <ext_repo@localdomain.com>" ,$mail_to,"Ext repo request has been updated!",$mail_content);
}

function delete_entry($fr_id){
  $con = mysql_connect("localhost","root","123456");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }
  mysql_select_db("repo_db", $con);
  $result = mysql_query("SELECT * FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);
  mysql_query("DELETE FROM external_repo_manage_table WHERE fr_id = '$fr_id'", $con);

  mysql_close($con);

  $mail_content = "";
  $mail_to = "";
  while($row = mysql_fetch_array($result))
  {
      $mail_content .= format_one_line($row);
      $mail_to .= get_mail_to($row['extra_repo']);
  }
  send_mail("ExtRepo <ext_repo@localdomain.com>" ,$mail_to,"Ext repo request has been deleted!",$mail_content);
}

//Main Entry
if(isset($_GET["fr_id"]) && isset($_GET["state"])){
  if($_GET["state"] == "Deleted")
	  delete_entry($_GET["fr_id"]);
  else
      update_column($_GET["fr_id"], "state", $_GET["state"]);
  echo $_GET["fr_id"] . " is updated successfully!";
}
elseif(isset($_GET["fr_id"]) && isset($_GET["changeset"])){
  update_column($_GET["fr_id"], "changeset", $_GET["changeset"]);
  echo $_GET["fr_id"] . " is updated successfully!";
}
elseif(isset($_GET["fr_id"]) && isset($_FILES["patch_file"]["name"])){
  if ($_FILES["patch_file"]["error"] > 0)
  {
    echo "Patch file Error: " . $_FILES["patch_file"]["error"] . "<br />";
	exit("ERR: patch file is not set!");
  }
  else
  {
	$patch_file_name = $_FILES["patch_file"]["name"];
    $patch_file_tmp_name = $_FILES["patch_file"]["tmp_name"];
    $save_file_name = copy_patch_file($patch_file_tmp_name, $patch_file_name, $_GET["fr_id"]);
    update_column($_GET["fr_id"], "patch_file_name", $patch_file_name);
	echo "Patch file is updated successfully!";
  }
}
else
{
  echo $_GET["fr_id"] . " is updated failed!";
}

?>
