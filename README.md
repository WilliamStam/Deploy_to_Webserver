add a github webhook to

<domain>/deploy?folder=<folder from root>&branch=<git branch>&key=<key>&username=<github username>&password=<github password>

this will create the folder on the web server based on the folder. initiate a git repo and do a pull

if the repo / folder exists it will just do the pull.

the username / password in the url is only if you pulling from a private repo.. leave it out if its public

---------------------------------------------

reason for this... i greatly detest FTP

---------------------------------------------

The Database
____________



CREATE TABLE IF NOT EXISTS `logs` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `datein` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `site` int(6) DEFAULT NULL,
  `payload` text,
  `errors` text,
  PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `sites` (
  `ID` int(6) NOT NULL AUTO_INCREMENT,
  `auth` varchar(100) DEFAULT NULL,
  `folder` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);


---------------------------------------------

The Setup
_________

add a key and folder to the "sites" table. for instance

key									folder
098f6bcd4621d373cagty4e832627b4f6	test

in the cfg set the base folder (by default it will use the parent folder of the script)

it will then use the path <base>/test

create a file called "config.inc.php" in the root of this script.. include your values

$cfg['base'] = dirname(dirname(__FILE__));
$cfg['db'] = array(
	"host"=>"localhost",
	"database"=>"deploy",
	"username"=>"",
	"password"=>""
);