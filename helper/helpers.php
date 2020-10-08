<?php


/**
 * Comparacion para ordenar array por fecha.
 * Compara $time1 con $time2
 */
function compareByTimestamp($time1, $time2)
{

	$time1 = $time1['dateTime']->getTimestamp();
	$time2 = $time2['dateTime']->getTimestamp();

	if ($time1 < $time2) {

		return ($GLOBALS['CONFIG']['SORT_ORDER_INPUT_FILE']) ? -1 : 1;
	} else if ($time1 > $time2) {

		return ($GLOBALS['CONFIG']['SORT_ORDER_INPUT_FILE']) ? 1 : -1;
	} else {

		return 0;
	}
}


/**
 * Compara dos fechas tomando el string de fechas
 * con formato dd/mm/yyyy
 * 
 * Utilizado para uksort 
 */
function compareByTimestampSummary($time1, $time2)
{
	// echo "(fechas) t1 = $time1 | t2 = $time2 \n";

	$time1 = DateTime::createFromFormat('d/m/Y', $time1)->getTimestamp();
	$time2 = DateTime::createFromFormat('d/m/Y', $time2)->getTimestamp();

	// echo "(unix) t1 = $time1 | t2 = $time2 \n";

	if ($time1 < $time2) {

		return -1;
	} else if ($time1 > $time2) {

		return 1;
	} else {

		return 0;
	}
}



/**
 * Convierte un valor entero de minutos a un string
 * con formato HH:II
 */
function convertMinutosToHoursAndMinutes($minutes = 0)
{
	if ($minutes < 1) {
		return NULL;
	}

	return sprintf("%02d:%02d", floor($minutes / 60), $minutes % 60);
}



/**
 * Escanea un directorio
 * 
 * files can be sorted on name and stat() attributes, ascending and descending:
 * name    file name
 * dev     device number
 * ino     inode number
 * mode    inode protection mode
 * nlink   number of links
 * uid     userid of owner
 * gid     groupid of owner
 * rdev    device type, if inode device *
 * size    size in bytes
 * atime   time of last access (Unix timestamp)
 * mtime   time of last modification (Unix timestamp)
 * ctime   time of last inode change (Unix timestamp)
 * blksize blocksize of filesystem IO *
 * blocks  number of blocks allocated
 */
function customScanDir($dir, $exp, $how = 'name', $desc = 0)
{
	$r = array();
	$dh = @opendir($dir);
	if ($dh) {
		while (($fname = readdir($dh)) !== false) {
			if (preg_match($exp, $fname)) {
				$stat = stat("$dir/$fname");
				$r[$fname] = ($how == 'name') ? $fname : $stat[$how];
			}
		}
		closedir($dh);
		if ($desc) {
			arsort($r);
		} else {
			asort($r);
		}
	}
	return (array_keys($r));
}



/**
 * Create a directory
 */
function createDirectory($path)
{

	logeo("Create directory: $path");

	if (!file_exists($path)) {

		mkdir($path, 0777, TRUE);
	}
}



/**
* Valida y retorna un mesaje de observacion sobre
* las alertas en caso de que existan 
*/
function returnObservationAlert($interval_data)
{
	$msg = NULL;

	if ($interval_data['RESULT']['alert_max_hours']) {

		$msg .=  str_replace('{%MAX_HOURS_BY_INTERVAL_ALERT}', CONFIG_PARAMS['MAX_HOURS_BY_INTERVAL_ALERT'][0], $interval_data['RESULT']['alert_max_hours_obs']) . " | ";
	}

	if ($interval_data['RESULT']['alert_sum_hours']) {

		$msg .= $interval_data['RESULT']['alert_sum_hours_obs'] . " | ";
	}

	if ($interval_data['RESULT']['alert_change_journal']) {

		$msg .= $interval_data['RESULT']['alert_change_journal_obs'];
	}

	return trim($msg);
}



/**
* Valida y retorna un estado de alerta
* si se presentan algunos de los casos
* validados
*/
function returnStatusAlert($interval_data)
{

	if ($interval_data['RESULT']['alert_max_hours']) {

		return TRUE;
	}

	if ($interval_data['RESULT']['alert_sum_hours']) {

		return TRUE;
	}

	if ($interval_data['RESULT']['alert_change_journal']) {

		return TRUE;
	}

	return FALSE;
}