<?php
/** 
 *  cmd:    php update.php
 *  import: 
 *  input:  
 *  output: 
 *  description: 
 */

include("data/config/config.php");

$sql = "CREATE TABLE dtb_volunteer ("
	."volunteer_id int  NOT NULL,"
	."member_id int  NOT NULL,"
	."name text NOT NULL,"
	."kana text NOT NULL,"
	."zip01 text,"
	."zip02 text,"
	."pref smallint,"
	."addr01 text,"
	."addr02 text,"
	."email text NOT NULL,"
	."tel01 text,"
	."tel02 text,"
	."tel03 text,"
	."fax01 text,"
	."fax02 text,"
	."fax03 text,"
	."description text,"
	."latitude text,"
	."longitude text,"
	."PRIMARY KEY (volunteer_id)"
	.") ENGINE=MyISAM";

$link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
if(!$link)
	die("接続できませんでした：" . mysql_error());
echo "接続に成功しました。";
mysql_select_db(DB_NAME, $link);
mysql_query($sql, $link);
echo "dtb_volunteerテーブルの作成に成功しました。";
mysql_close($link);

// D.S.G.