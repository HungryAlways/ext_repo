<html>
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
<script src="js/jquery-1.11.1.min.js" ></script>

<script type="text/javascript">
function showAddForm() {
  if ( $("#addform").is(':visible') ) 
      $("#addform").hide();
  else
	  //alert("HDR5401 is taking MS, the request is locked!");
      $("#addform").show();
}

function showInfo(result) {
  if ( $("#info").is(':visible') ) 
      $("#info").hide();
  else{
	  $("#info span:first").html(result);
      $("#info").show();
	  }
}

function copy_flow(dst, src){
	var i;
	dst.className = src.className;
	for (i = 0; i < dst.cells.length;i++){
    	dst.cells[i].innerHTML = src.cells[i].innerHTML;
	}
}

function sort_table(lnk,clid) {
  var head = lnk.parentNode;
  var table = head.parentNode;
  var new_row;
  var j;
  var k;
  var cur_sort_key;
  var pre_sort_key;
  var table_length = table.rows.length;

  if(table_length <= 1){
	  alert("No items for column:" + lnk.cellIndex);
	  return;
  }

  new_row = table.insertRow( table_length );
  for ( var i=0; i<head.cells.length; i++ ){
	  var objNewCell = new_row.insertCell(i);
  }

  pre_sort_key = table.rows[1].cells[lnk.cellIndex].innerHTML;
  for (var j = 2; j < table_length;j++) { 
	  cur_sort_key = table.rows[j].cells[lnk.cellIndex].innerHTML;
	  if(pre_sort_key == cur_sort_key){
		continue;
	  }

	  copy_flow(new_row, table.rows[j]);

      for (k = j + 1; k < table_length;k++) {
          if(table.rows[k].cells[lnk.cellIndex].innerHTML == pre_sort_key){
              copy_flow(table.rows[j], table.rows[k]);
              copy_flow(table.rows[k], new_row);
			  break;
		  }

	  }
      pre_sort_key = table.rows[j].cells[lnk.cellIndex].innerHTML;
  }
	
  table.deleteRow( table_length )

}

function replaceAll(strOrg,strFind,strReplace){
 var index = 0;
 while(strOrg.indexOf(strFind,index) != -1){
  strOrg = strOrg.replace(strFind,strReplace);
  index = strOrg.indexOf(strFind,index);
 }
 return strOrg
} 

function updateColumn(x,fr)
{
  var xmlhttp;
  var value;

  if(fr.length == 0) 
  {
    return;
  }

  if(window.XMLHttpRequest)
  {
    xmlhttp=new XMLHttpRequest();
  }
  else
  {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange=function()
  {
    if(xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      //ok
      //alert(xmlhttp.responseText);
    }
  }
  value = x.value; 
  //comment.replace("\n","<br>");
  value = replaceAll(value, "\n", "<br>");
  xmlhttp.open("GET","update_column.php?fr_id="+fr+"&&"+x.parentElement.id+"="+value,true);
  xmlhttp.send();
}


function dbclkOnField(x,fr)
{
//	var comment = x.firstChild.innerHTML;
//	comment = replaceAll(comment, "<br>", "\n");
//	x.lastChild.value = comment;
    x.firstChild.style.display="none";
    x.lastChild.style.display="";
	x.lastChild.focus();
}

function onfocusTexa(x,fr)
{
    x.style.background="yellow";
}

function onblurColumn(x,fr)
{
	updateColumn(x, fr);
    x.style.background="white";
	x.style.display="none";
	x.parentElement.firstChild.innerHTML = replaceAll(x.value, "\n", "<br>");	
    x.parentElement.firstChild.style.display="";
	//location.reload(true);
}

function doUpload(){
    var oData = new FormData(document.forms.namedItem("request" ));  
    oData.append( "CustomField", "This is some extra data" );  
    var oReq = new XMLHttpRequest();  
    oReq.open( "POST", "add_request.php" , true );  
    oReq.onload = function(oEvent) {  
          if (oReq.status == 200) {  
			  document.getElementById('request').reset();
			  showAddForm();
			  showInfo(oReq.responseText);
         } else {  
              location.reload(true);
         }  
    };  
    oReq.send(oData);  	
}

function do_update_new_patch_file(x, fr){
    var oData = new FormData(document.forms.namedItem(fr));  
    //oData.append( "fr_id", fr );  
    var oReq = new XMLHttpRequest();  
    oReq.open( "POST", "update_column.php?fr_id=" + fr , true );  
    oReq.onload = function(oEvent) {  
          if (oReq.status == 200) {  
              alert(oReq.responseText);
			  location.reload(true);
         } else {  
              alert(oReq.responseText);
         }  
    };  
    oReq.send(oData);  	
}
</script>
</head>

<body>

<script type="text/javascript">
	window.onload = function() { 
    	$("#showaddformbutton").click( function()  {
        	showAddForm();
        });

        $("#cancelbutton").click( function() {
        	showAddForm();
        });

        $("#info_cancel_button").click( function() {
        	showInfo();
			location.reload(true);
        });
	};
</script>

<?php
$fr_link = "http://isam-cq.web.alcatel-lucent.com/cqweb/#/prod/ALU/RECORD/";
$review_board_link = "http://135.251.206.105/r/";

function list_table_head_col($id, $content, $is_sortable){
	$col_id = "+" . $id . "+";
	if($is_sortable)
		echo "<th id=$col_id onclick=\"sort_table(this, '$col_id');\" >" . $content . "</th>";
	else
	    echo "<th id=$col_id>" . $content . "</th>";
}

function list_table_head(){
  $col_id=0;

  echo "<tr>";
  list_table_head_col($col_id, "Repo", 0);
  $col_id++;
  list_table_head_col($col_id, "FR/IR", 0);
  $col_id++;
  list_table_head_col($col_id, "Target Release", 0);
  $col_id++;	
  list_table_head_col($col_id, "FDT", 0);
  $col_id++;		
  list_table_head_col($col_id, "Review Board ID", 0);
  $col_id++;	
  list_table_head_col($col_id, "Description", 0);
  $col_id++;
  list_table_head_col($col_id, "User Name", 0);
  $col_id++;	
  list_table_head_col($col_id, "Patch File", 0);
  $col_id++;		
  list_table_head_col($col_id, "Dependency On AONT repo", 0);
  $col_id++;			
  list_table_head_col($col_id, "State", 0);
  $col_id++;	
  list_table_head_col($col_id, "New Changeset", 0);
  $col_id++;
  list_table_head_col($col_id, "Owner", 0);
  $col_id++;	
  echo "</tr>";
}

function list_one_line($row, $row_alt){
  global $fr_link;
  global $review_board_link;
  $fr_id = $row['fr_id'];

  if($row_alt % 2 == 0)
    echo "<tr>";
  else
    echo "<tr class='alt'>";

  echo "<td>" . $row['extra_repo'] . "</td>";
  echo "<td><a href=\"" . $fr_link . $row['fr_id'] . "\">" . $row['fr_id'] . "</td>";
  echo "<td>" . $row['rel'] . "</td>";
  echo "<td>" . $row['fdt'] . "</td>";	
  echo "<td><a href=\"" . $review_board_link . $row['review_board_id'] . "\">" . $row['review_board_id'] . "</td>";
  echo "<td>" . $row['description'] . "</td>";
  echo "<td>" . $row['user_name'] . "</td>";
  echo "<td><a href=\"" . "patchs/" . $row['fr_id'] . "_" . $row['patch_file_name'] . "\">" . $row['patch_file_name'] . "</a>";
  echo "<form name='$fr_id' id='$fr_id' method='post' enctype='multipart/form-data'>";
  echo "<a href=\"javascript:;\" class=\"a-upload\">";
  echo "<input type='file' name='patch_file' id='patch_file' class='a-upload' onchange=\"do_update_new_patch_file(this,'$fr_id');\"/>Upload";
  echo "</a>";
  echo "</form>";
  echo "</td>";	

  echo "<td>" . $row['dependency'] . "</td>";	
  echo "<td name='state' id='state' onclick=\"dbclkOnField(this,'$fr_id');\"><span>" . $row['state'] . "</span>" . "<select name='state_sel' style='display:none;' onfocus=\"onfocusTexa(this,'$fr_id');\" onblur=\"onblurColumn(this,'$fr_id');\">" . "<option value='New'>New</option><option value='Delivered'>Delivered</option><option value='Rejected'>Rejected</option><option value='Deleted'>Deleted</option>" . "</select>" . "</td>";	
  echo "<td name='changeset' id='changeset' onclick=\"dbclkOnField(this,'$fr_id');\"><span>" . $row['changeset'] . "</span>" . "<textarea rows='1' cols='16' name='texta' style='display:none;' onfocus=\"onfocusTexa(this,'$fr_id');\" onblur=\"onblurColumn(this,'$fr_id');\">" . $row['changeset']  . "</textarea>" . "</td>";
  echo "<td>";

  if($row['extra_repo'] == "OPENSOURCE")
	  echo "He Dingjun/Xin Zhen";
  if($row['extra_repo'] == "BRDLT_SP4_SDK")
	  echo "Yuan Yali";	
  if($row['extra_repo'] == "BCMSDK_4.14L04")
	  echo "Yuan Yali/Zhao Biao";	
  if($row['extra_repo'] == "BCMSDK_4.16L03")
	  echo "Yuan Yali/Zhao Biao";
  if($row['extra_repo'] == "BCMSDK_4.16L04")
	  echo "Yuan Yali/Zhao Biao";
  echo "</td>";
  echo "</tr>";

}


function show_request($repo, $state){
  $con = mysql_connect("localhost","root","123456");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }
  mysql_select_db("repo_db", $con);

  $result = mysql_query("SELECT * FROM external_repo_manage_table", $con);

  echo "<table id='customers' border='1px solid #98bf21'>"; 

  list_table_head();

  $row_alt = 0;
  while($row = mysql_fetch_array($result))
  {
	  if(($repo != "all" ) && ($repo != $row['extra_repo']))
		  continue;
	  if(($state != "open" ) && ($state != "all" ) && ($state != $row['state']))
		  continue;
	  if(($state == "open" ) && ($row['state'] != "New") && ($row['state'] != "new"))
		  continue;	  
      $row_alt++;
      list_one_line($row, $row_alt);
  }
  echo "</table>";

  mysql_close($con);
}

$repo = "all";
$state = "open";

if(isset($_GET["repo"])) {
  $repo = $_GET["repo"];
}
if(isset($_GET["state"])) {
  $state = $_GET["state"];
}

show_request($repo, $state);

?>

<div id='addform'>
	<form name='request' id='request' method='post' enctype='multipart/form-data'>

	<div id='left'>Repo Name :</div>
	<div id='right'>
		<select id='repo_name' name='repo_name'>
			<option value='BCMSDK_4.14L04'>BCMSDK_4.14L04</option>
			<option value='SWITCHSDK'>SWITCHSDK</option>
			<option value='OPENSOURCE'>OPENSOURCE</option>
			<option value='BRDLT_SP4_SDK'>BRDLT_SP4_SDK</option>
			<option value='BCMSDK_4.16L03'>BCMSDK_4.16L03</option>
			<option value='BCMSDK_4.16L04'>BCMSDK_4.16L04</option>
		</select>
	</div>
	<br>

	<div id='left'>FR/IR ID :</div>
	<div id='right'>
		<input id='fr_id' type='text' name='fr_id' placeholder='ex. ALUxxxxxxxx'/>
	</div>
	<br>

	<div id='left'>Release :</div>
	<div id='right'>
		<input type='text' name='release' id='release'  placeholder='ex. HDR53'/>
	</div>
	<br>

	<div id='left'>FDT :</div>
	<div id='right'>
		<input id='fdt_number' type='text' name='fdt' placeholder='ex. 1252'/>
	</div>
	<br>

	<div id='left'>Review Board ID :</div>
	<div id='right'>
		<input id='review_board_id' type='text' name='review_board_id' placeholder='ex. 1234'/>
	</div>
	<br>

	<div id='left'>Your name :</div>
	<div id='right'>
		<input id='user_name' type='text' name='user_name' placeholder='ex. dingjunh'/>
	</div>
	<br>

	<div id='left'>Dependent on AONT repo:</div>
	<div id='right'>
		<input id='dependency_yes' type='radio' name='dependency' value='yes' /> Yes<input id='dependency_yes' type='radio' name='dependency' value='no'  checked='checked' /> No<br>
	</div>
	<br>

	<div id='left'>Patch file :</div>
	<div id='right'>
		<input type='file' name='patch_file' id='patch_file' />
	</div>
	<br>
	<br>

	Description:
	<br>
	<textarea rows='4' cols='48' name='description'> </textarea>
	<br>
	<input type='button' class='button green' value='Submit' onclick='doUpload()'>
	<a id='cancelbutton' class='button delete'>Cancel</a>
	</form>
</div>

<div id="toolbar">
	<a id="showaddformbutton" class="button green"><i class="fa fa-plus"></i> Add new row</a>
</div>

<div id='info'>
	<span></span><br>
	<a id='info_cancel_button' class='button delete'>OK</a>
</div>
<a href="https://confluence.app.alcatel-lucent.com/pages/viewpage.action?title=External+Repo+Policy&spaceKey=FADOMAIN">External Repo Policy</a>
</body>
</html>

