<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);

	require('../config.php');

}


global $db;

dol_include_once('/release/class/release.class.php');

$o=new Release($db);
$o->init_db_by_vars();

$o=new ReleaseLineLink($db);
$o->init_db_by_vars();

