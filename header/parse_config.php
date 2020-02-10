<?php

function getConfigFile($cfg_file = NULL)
{

	if ($cfg_file === NULL) {
		$cfg_file = CONFIG_PATH . 'sph.ini';
	}

	if (!file_exists($cfg_file)) {
		echo "WARNING: Confing file not exist" . PHP_EOL;
		return FALSE;
	}

	$parse_conf = parse_ini_file($cfg_file, TRUE, INI_SCANNER_TYPED);

	return $parse_conf;
}
