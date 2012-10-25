<?php
/*
 * Date: 2012/10/25
 * Time: 2:10 PM
 */
//if (isset)
include_once("functions.php");


$cfg=array();
$cfg['base'] = dirname(dirname(__FILE__));

$return = array();
$return['errors'] = array();


$link = mysql_connect('localhost', 'deploy', 'deployit');
mysql_select_db('deploy', $link);

$key = (isset($_GET['key'])) ? $_GET['key'] : "";
$sql = "SELECT * FROM sites WHERE auth = '$key'";

$result = mysql_query($sql, $link) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$siteID = "";
$return = serialize($_POST);

$sql = "INSERT INTO logs (payload, errors, site) VALUES ('$return','','')";
mysql_query($sql, $link) or die(mysql_error());
exit();

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
		$push = $_POST;
		if (!isset($push['repository'])){
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
			}

			if (file_exists($folder)) {
				$return['folder']['exists'] = true;
			}
			//@mkdir($folder, 0777, true);
			if (file_exists($folder . DIRECTORY_SEPARATOR . ".git")) {
				$return['git'] = true;
			}


			if ($return['git']){
				$url = $push['repository']['url'].".git";



				// https://username:password@github.com/WilliamStam/DeployWebserver.git
				// https: //github.com/WilliamStam/DeployWebserver
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



				$return['action'] = array(
					"reset"=>shell_exec('git reset --hard HEAD'),
					"pull"=>shell_exec("git pull $url ".$values['branch']."  2>&1")
				);
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

?>
