# Deploy to webserver with git and PHP

Using the github webhook, this will create the folder on the web server based on the folder. initiate a git repo and do a pull

If the repo / folder exists it will just do the pull.


## Usage
Add a github webhook to

`http://<domain>/deploy?key=<key>`

### Optional Options
<table>
  <tr>
    <th>Option</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>branch</td>
    <td>If you dont want it to pull the master branch, add the branch to this</td>
  </tr>
  <tr>
    <td>username</td>
    <td>If you are pulling a private repo you need to specify a username / password</td>
  </tr>
  <tr>
    <td>password</td>
    <td>If you are pulling a private repo you need to specify a username / password</td>
  </tr>

</table>



## Why do this?
Reason for this... i greatly detest FTP

## The Database

```
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
```

## The Setup

add a key and folder to the "sites" table. for instance

<table>
  <tr>
    <th>Key</th>
    <th>Folder</th>
  </tr>
  <tr>
    <td>098f6bcd4621d373cagty4e832627b4f6</td>
    <td>test</td>
  </tr>
</table>

In the cfg set the base folder (by default it will use the parent folder of the script)

It will then use the path `<base>/test`

create a file called "config.inc.php" in the root of this script.. include your values

```
  $cfg['base'] = dirname(dirname(__FILE__));
  $cfg['db'] = array(
  	"host"=>"localhost",
  	"database"=>"deploy",
  	"username"=>"",
  	"password"=>""
  );
```
