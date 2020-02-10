<?php

getConfigFile();

function getConfigFile($cfg_file = NULL){
	
	$parse_conf = parse_ini_file(CONFIG_PATH . 'sph.ini', TRUE, INI_SCANNER_TYPED);

	var_dump($parse_conf);

	die();
}
