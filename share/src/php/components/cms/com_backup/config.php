<?php Hw2\S_Core::checkAccess();
// Settings
$query="index.php?".Hw2\S_CoreDef::hw2ext."=backup";
$curDir=dirname(__FILE__);
$bakDir=HW2PATH_LOCAL.DS."files".DS."backups";
$table = '*';
$conf=new \JConfig;
$DBhost = $conf->host;
$DBuser = $conf->user;
$DBpass = $conf->password;
$DBName = $conf->db;

define( 'PCLZIP_TEMPORARY_DIR', $bakDir.DS );
?>
