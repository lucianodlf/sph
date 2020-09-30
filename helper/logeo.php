<?php

// Logueo
function logeo($text = "", $hidde = FALSE, $force = FALSE, $nodate = FALSE)
{

	if (!$GLOBALS['CONFIG']['QUIET']) {

		if (($GLOBALS['CONFIG']['VERBOSE'] && !$hidde) || $force) {
			if ($nodate) {

				echo  $text . PHP_EOL;
			} else {

				echo date('Y-m-d h:i:s') . ": " . $text . PHP_EOL;
			}
		}
	}

	if ($GLOBALS['CONFIG']['APIWEB']) {

		if (($GLOBALS['CONFIG']['VERBOSE'] && !$hidde) || $force) {
			if ($nodate) {

				file_put_contents(dirname(__FILE__, 2) . '/logs/sphl.log', $text . PHP_EOL, FILE_APPEND);
			} else {

				file_put_contents(dirname(__FILE__, 2) . '/logs/sphl.log', date('Y-m-d h:i:s') . ": " . $text . PHP_EOL, FILE_APPEND);
			}
		}
	}
}


function deleteOldLogFile(){
	if(file_exists(dirname(__FILE__,2) . '/logs/sphl.log')){
		unlink(dirname(__FILE__,2) . '/logs/sphl.log');
	}
}


// Show resumen calculte by interval
function debugShowRersumen($hours_interval_result)
{
	if ($GLOBALS['CONFIG']['VERBOSE']) {
		logeo();

		logeo('END ITERATOR BY HOURS OF DAY ---------------------------------');
		logeo('Status Array Data Result(hours_interval_result): ' . json_encode($hours_interval_result));

		logeo();

		logeo('=========================================================================================');
		logeo('================================== RESUMEN INTERVALO ====================================');
		logeo('Total: ' . ($hours_interval_result['total']));
		logeo('Diurnas Normales: ' . ($hours_interval_result['diurnas_n']));
		logeo('Diurnas Extras: ' . ($hours_interval_result['diurnas_e']));
		logeo('Nocturnas Normales: ' . ($hours_interval_result['nocturnas_n']));
		logeo('Nocturnas Extras: ' . ($hours_interval_result['nocturnas_e']));
		logeo('Horas al 100%: ' . ($hours_interval_result['h100']));
		logeo('Horas sabado NO 100%: ' . ($hours_interval_result['sabadonoth100']));
		logeo('ALERTA MAX HORAS: ' . ($hours_interval_result['alert_max_hours']));
		logeo('ALERTA SUMA HORAS: ' . ($hours_interval_result['alert_sum_hours']));
		logeo('=========================================================================================');

		logeo();
	}
}



// For show current params
function debugCurrentParams($current_params)
{
	if ($GLOBALS['CONFIG']['DEBUG_MODE']) {

		logeo('================================ CONTROL PARAMS OF INTERVAL =============================');
		foreach ($current_params as $key => $value) {
			if (array_key_exists('start', $value) && array_key_exists('end', $value)) {
				logeo("$key - START:{$value['start']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])} - END:{$value['end']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])}");
			} else {
				logeo("$key - VALUE: " . json_encode($value));
			}
		}

		logeo('========================================================================================');
	}
}



// For show data imported
function debugInitialDataPrepare($hours_data)
{
	if ($GLOBALS['CONFIG']['DEBUG_MODE']) {
		logeo("================= DATA IMPORTED FOR PROCESS ====================");

		foreach ($hours_data as $row) {
			logeo(json_encode($row));
		}

		logeo("=================================================================");
	}
}



// For show number and name of day of week
function debugShowNumberDayOfWeek()
{
	if ($GLOBALS['CONFIG']['DEBUG_MODE'] && $GLOBALS['CONFIG']['VERBOSE']) {

		logeo("============== Name and number of day of week ===================");

		var_dump((DateTime::createFromFormat('d/m/Y', '02/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '03/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '04/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '05/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '06/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '07/09/2019'))->format('w - l'));
		var_dump((DateTime::createFromFormat('d/m/Y', '08/09/2019'))->format('w - l'));

		logeo("=================================================================");
	}
}
