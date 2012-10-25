<?php
/*
 * Date: 2012/10/25
 * Time: 2:10 PM
 */
//if (isset)
include_once("functions.php");


$cfg=array();
$cfg['base'] = dirname(dirname(__FILE__));




$link = mysql_connect('localhost', '', '');
mysql_select_db('deploy', $link);

$key = (isset($_GET['key'])) ? $_GET['key'] : "";
$sql = "SELECT * FROM sites WHERE auth = '$key'";

$result = mysql_query($sql, $link) or die(mysql_error());
$row = mysql_fetch_assoc($result);

if (!count($row)){
	exit("not authorized to do that");
}
if (!$row['folder']){
	exit("no folder specified");
}
$values = array(
	"folder" => $row['folder'],
	"branch" => (isset($_GET['branch'])) ? $_GET['branch'] : "",
	"auth"   => array(
		"username" => (isset($_GET['username'])) ? $_GET['username'] : "",
		"password" => (isset($_GET['password'])) ? $_GET['password'] : ""
	)
);

$return = array(
	"cfg" => $cfg,
	"val" => $values,
);

$folder = $cfg['base'] . DIRECTORY_SEPARATOR . $values['folder'];
$return['folder'] = array(
	"path"   => $folder,
	"exists" => false
);
$return['git'] = false;


if (file_exists($folder)) {
	$return['folder']['exists'] = true;
}
//@mkdir($folder, 0777, true);
if (file_exists($folder . DIRECTORY_SEPARATOR . ".git")) {
	$return['git'] = true;
}

if (!$return['folder']['exists']){
	@mkdir($folder, 0777, true);
}
$root_folder = dirname(__FILE__);

if (!$return['git']){
	chdir($folder);
	shell_exec('git init');
}

if (file_exists($folder)) {
	$return['folder']['exists'] = true;
}
//@mkdir($folder, 0777, true);
if (file_exists($folder . DIRECTORY_SEPARATOR . ".git")) {
	$return['git'] = true;
}

if ($return['git']){
	shell_exec('git reset --hard HEAD');
}

test_array($return);

?>
