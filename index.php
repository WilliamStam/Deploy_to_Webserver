<?php
/*
 * Date: 2012/10/25
 * Time: 2:10 PM
 */

include_once("functions.php");


$cfg=array();
$cfg['base'] = dirname(dirname(__FILE__));
$cfg['db'] = array(
	"host"=>"localhost",
	"database"=>"deploy",
	"username"=>"",
	"password"=>""
);
//if (isset)
if (file_exists("config.inc.php")){
	include_once("config.inc.php");
}


$return = array();
$return['errors'] = array();


$link = mysql_connect($cfg['db']['host'], $cfg['db']['username'], $cfg['db']['password']);
mysql_select_db($cfg['db']['database'], $link);

$key = (isset($_GET['key'])) ? $_GET['key'] : "";
$repo = (isset($_GET['repo'])) ? $_GET['repo'] : "";


$sql = "SELECT * FROM sites WHERE auth = '$key'";

$result = mysql_query($sql, $link) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$siteID = "";
$payload = json_decode($_REQUEST['payload']);



if (!count($row) || (!isset($row['ID']))){
	if ($key){
		$return['errors'][] = "Key not found";
	} else {
		$return['errors'][] = "No key defined";
	};

} else {

	$siteID = $row['ID'];
	if (!$row['folder']){
		$return['errors'][] = "Folder not defined for this key";
	} else {
		if (!$payload && $repo == '') {
			$return['errors'][] = "Not a github push";
		} else {

			$values = array(
				"folder" => $row['folder'],
				"branch" => (isset($_GET['branch'])) ? $_GET['branch'] : "master",
				"auth"   => array(
					"username" => (isset($_GET['username'])) ? $_GET['username'] : "",
					"password" => (isset($_GET['password'])) ? $_GET['password'] : ""
				)
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
			chdir($folder);


			if (!$return['git']){
				shell_exec('git init');
			} else {
				shell_exec('git reset --hard HEAD');
			}

			if (file_exists($folder)) {
				$return['folder']['exists'] = true;
			}
			//@mkdir($folder, 0777, true);
			if (file_exists($folder . DIRECTORY_SEPARATOR . ".git")) {
				$return['git'] = true;
			}


			if ($return['git']){
				if ($repo){
					$url = $repo;
				} else {
					$url = ($payload->{'repository'}->{'url'});
				}

				$url .= ".git";


				$auth = "";
				if ($values['auth']['username']){
					$auth .= $values['auth']['username'].":";
				}
				if ($values['auth']['password']){
					$auth .= $values['auth']['password'];
				}

				if ($auth) {
					$auth = $auth."@";
					$url = str_replace("https://","https://$auth",$url);
				}

				$return['pull'] =  shell_exec("git pull $url ".$values['branch']."  2>&1");
			} else {
				$return['errors'][] = "Git not setup in the folder";
			}
		}
	}
}
//test_array($return);
$errors = $return['errors'];
unset($return['errors']);
$return = serialize($return);
$errors = serialize($errors);
$sql = "INSERT INTO logs (payload, errors, site) VALUES ('$return','$errors','$siteID')";
mysql_query($sql, $link) or die(mysql_error());
test_array(array("return"=> $return,"errors"=> $errors ));

?>
