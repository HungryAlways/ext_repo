<?php
$fr_link = "http://isam-cq.web.alcatel-lucent.com/cqweb/#/prod/ALU/RECORD/";
$review_board_link = "http://135.251.206.105/r/";

function send_mail($from, $to, $subject, $message)  
{  
    if ($from == "")  
    {  
        $from = 'ExtRepo <ext_repo@localdomain.com>';
    }  
    $headers = 'MIME-Version: 1.0' . "\r\n";  
    $headers .= 'Content-type: text/html; charset=gb2312' . "\r\n";  
    $headers .= 'From: ' . $from . "\r\n";  
    mail($to, $subject, $message, $headers);  
}

function format_one_line($row){
  global $fr_link;
  global $review_board_link;

  $fr_id = $row['fr_id'];
  $ret = "<html><head></head><body><table class=MsoNormalTable border=1>";
  $ret .= "<tr>";
  $ret .=  "<td>" . $row['extra_repo'] . "</td>";
  $ret .= "<td><a href=\"" . $fr_link . $row['fr_id'] . "\">" . $row['fr_id'] . "</td>";
  $ret .= "<td>" . $row['rel'] . "</td>";
  $ret .= "<td>" . $row['fdt'] . "</td>";	
  $ret .= "<td><a href=\"" . $review_board_link . $row['review_board_id'] . "\">" . $row['review_board_id'] . "</td>";
  $ret .= "<td>" . $row['description'] . "</td>";
  $ret .= "<td>" . $row['user_name'] . "</td>";
  $ret .= "<td><a href=\"" . "http://135.251.25.50/ext_repo/patchs/" . $row['fr_id'] . "_" . $row['patch_file_name'] . "\">" . $row['patch_file_name'] . "</td>";	
  $ret .= "<td>" . $row['dependency'] . "</td>";	
  $ret .= "<td>" . $row['state'] . "</td>";	
  $ret .= "<td>" . $row['changeset'] . "</td>";
  $ret .= "<td>";
  if($row['extra_repo'] == "OPENSOURCE")
	  $ret .= "He Dingjun/Xin Zhen";
  if($row['extra_repo'] == "BRDLT_SP4_SDK")
	  $ret .= "Yuan Yali";	
  if($row['extra_repo'] == "BCMSDK_4.14L04")
	  $ret .= "Yuan Yali/Zhao Biao";		
  $ret .= "</td>";
  $ret .= "</tr>";
  $ret .= "</table></body></html>";
  return $ret;
}

function get_mail_to($repo){
	$mail_list = "dingjun.he@alcatel-sbell.com.cn;";
	if($repo == "OPENSOURCE")
        $mail_list = "dingjun.he@alcatel-sbell.com.cn;zhen.xin@alcatel-sbell.com.cn;";
	if($repo == "BCMSDK_4.14L04")
        $mail_list = "dingjun.he@alcatel-sbell.com.cn;yali.yuan@alcatel-sbell.com.cn;biao.c.zhao@alcatel-sbell.com.cn;";
	if($repo == "BRDLT_SP4_SDK")
        $mail_list = "dingjun.he@alcatel-sbell.com.cn;yali.yuan@alcatel-sbell.com.cn;";
	if($repo == "BCMSDK_4.16L03")
        $mail_list = "dingjun.he@alcatel-sbell.com.cn;yali.yuan@alcatel-sbell.com.cn;biao.c.zhao@alcatel-sbell.com.cn;";	
	if($repo == "BCMSDK_4.16L04")
        $mail_list = "dingjun.he@alcatel-sbell.com.cn;yali.yuan@alcatel-sbell.com.cn;biao.c.zhao@alcatel-sbell.com.cn;";		
	return $mail_list;
}
?>
