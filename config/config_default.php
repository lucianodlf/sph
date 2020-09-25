<?php

// Base path aplication
define('APP_BASE_PATH', dirname(__DIR__ . '../') . '/');

// Headers path
define('HEADER_PATH', APP_BASE_PATH . 'header/');

// Helpers path
define('HELPER_PATH', APP_BASE_PATH . 'helper/');

// Config path
define('CONFIG_PATH', APP_BASE_PATH . 'config/');

// Date refers execution
define('DATE_EXECUTION', date('Ymd'));


/**
 * Sretea y retorna parametros
 * de configuracion global
 */
function getDefaultGlobalConfig($hconfig = NULL)
{

	$gconfig['APP_BASE_PATH'] = APP_BASE_PATH;
	$gconfig['INPUT_DIR_PATH'] = APP_BASE_PATH . 'import/';
	$gconfig['INPUT_FILE'] = NULL;
	$gconfig['OUTPUT_DIR_PATH'] = APP_BASE_PATH . 'export/';
	$gconfig['OUTPUT_FILE'] = NULL;
	$gconfig['DATE_FORMAT_READ'] = 'm/d/Y H:i';
	$gconfig['DATE_FORMAT_SHOW'] = 'd/m/Y H:i';
	$gconfig['RANGE_READ_INPUT_FILE'] = 'A1:D10000';
	$gconfig['SORT_ORDER_INPUT_FILE'] = 0;
	$gconfig['DEFAULT_TIMEZONE'] = 'America/Argentina/Buenos_Aires';
	$gconfig['DEBUG_MODE'] = FALSE;
	$gconfig['DEBUG_ONLY_CODE_USER'] = 0;
	$gconfig['DEBUG_COUNT_MAX_USER'] = 0;
	$gconfig['DEBUG_COUNT_MAX_INTERVAL_USER'] = 0;
	$gconfig['DEBUG_HIDDE_LOG_BY_MINUTE'] = FALSE;
	$gconfig['DEBUG_RANGE_DATETIME'] = NULL;
	$gconfig['DEBUG_ONLY_INTERVALS'] = NULL;
	$gconfig['VERBOSE'] = FALSE;
	$gconfig['QUIET'] = FALSE;
	$gconfig['EXPORT_EXCEL'] = TRUE;
	$gconfig['APIWEB'] = FALSE;

	$gconfig['TEXT_ALERT_SUM_HOURS_OBS'] = 'La suma del total de horas desglosadas es DIFERENTE al total de horas';

	if ($hconfig && key_exists('MAX_HOURS_BY_INTERVAL_ALERT', $hconfig)) {

		$gconfig['TEXT_ALERT_MAX_HOURS_OBS'] = "El total de horas exede las " . $hconfig['MAX_HOURS_BY_INTERVAL_ALERT'][0] . " Hs";
	}

	$gconfig['TEXT_ALERT_CHANGE_JOURNAL_OBS'] = "El intervalo tiene un cambio de jornada entre ingreso y egreso";

	return $gconfig;
}



/**
 * Array config parameters defautl
 * HDIA => [start daty (HH:MM), hours add to end day (HH)]
 * HDIURNAS => [start hours (HH:MM), hours add to end (HH)]
 * HDIURNAS_EXT => [start hours (HH:MM), hours add to end (HH)]
 * HNOCTURNAS => [start hours (HH:MM), hours add to end (HH)]
 * HNOCTURNAS_EXT => [start hours (HH:MM), hours add to end (HH)]
 * HS100 => [start hours (HH:MM), hours add to end (HH)] 
 * JLNORMAL_D => [days of week] 
 * JLNORMAL_HS => [hours duration (HH)] 
 * JLDIF_D => [days of week] 
 * JLDIF_HS => [hours duration (HH)]
 * NSABADO => [number day]
 * FERIADOS => [date of days feriados (dd/mm/yyyy)]
 */

function getDefaultConfigParams()
{

	$hconfig['HDIA'] = ['00:00', 24];
	$hconfig['HDIURNAS'] = ['06:00', 15];
	$hconfig['HDIURNAS_EXT'] = ['06:00', 15];
	$hconfig['HNOCTURNAS'] = ['21:00', 9];
	$hconfig['HNOCTURNAS_EXT'] = ['21:00', 9];
	$hconfig['HS100'] = ['13:00', 11];
	$hconfig['JLNORMAL_D'] = [1, 2, 3, 4]; // Jornada laboral normal: numero de dias de la semana (1=Lunes, 2=Martes, 3=Miercoles, 4=Jueves, 5=Viernes)
	$hconfig['JLNORMAL_HS'] = [9]; // Numero de hs que dura la jornada
	$hconfig['JLDIF_D'] = [5]; // Jornada laboral diferencial: numero de dia de la semana
	$hconfig['JLDIF_HS'] = [8];
	$hconfig['NSABADO'] = [6]; // Numero de dia de la semana para sabado
	$hconfig['FERIADOS'] = ['15/01/2020'];
	$hconfig['MAX_HOURS_BY_INTERVAL_ALERT'] = [12]; // Numero maximo de horas en un intervalo para alertar

	return $hconfig;
}
