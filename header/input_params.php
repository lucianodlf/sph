<?php

// Define doc options
$doc = <<<DOC
Usage: 
	sph.php [-h | --help]
	sph.php [--quiet | --verbose] [-x] [--o-file=FILE] [INPUT]
	sph.php [-f FILE | --cfg=FILE]
	sph.php [--verbose] [--not-export-excel] [--i-file=FILE | --i-dir-path=DIR] [--o-file=FILE | --o-dir-path=DIR] 
		[--date-format-read=FORMAT] [--date-format-show=FORMAT]
		[--range-read=RANGE] [--sort-order=ORDER]
	sph.php --debug-mode [--d-user-only=VALUE | --d-max-users=VALUE] [--d-interval-only=VALUE | --d-max-intervals=VALUE]
	[--d-hidde-log-by-minute] [--d-range-datetime=VALUE] 
	[--not-export-excel] [--i-file=FILE | --i-dir-path=DIR] [--o-file=FILE | --o-dir-path=DIR] 
	[--date-format-read=FORMAT] [--date-format-show=FORMAT]
	[--range-read=RANGE] [--sort-order=ORDER] [--verbose]
	sph.php --version
	sph.php --apiweb [--i-file=FILE | --i-dir-path=DIR] [--o-file=FILE | --o-dir-path=DIR] 

Example: 
	php sph.php --i-file=./import/planilla_horas_base_2.xlsx
	php sph.php --i-file=./import/planilla_input_test_2q_septimebre_2019.xlsx --o-file=./testeo.xlsx


Options:
-h --help			show this
-q --quiet			print less text
-v --verbose			print more text
-d --debug-mode			Enable mode debug
-x --not-export-excel		Disable export result to excel file (xlsx) [default: TRUE]
-f FILE --cfg=FILE		specify read configu file (DISABLE)
--apiweb	Enable mode web
--version


[File]
--i-file=FILE			specify output file export
--o-file=FILE			specify input file import
--i-dir-path=DIR		specify input dir path [default: ./import]
--o-dir-path=DIR		specify input dir path [default: ./export]


[Format date]
--date-format-read=FORMAT	specify format date for read [default: 'm/d/Y H:i']
--date-format-show=FORMAT	specify format date for show [default: 'd/m/Y H:i']
--range-read=VALUE		specify range read of input file [default: 'A1:D10000']
--sort-order=VALUE		specify order sorter of input file (0 = deshabilitado, 1 = ascendente, 2 = descendente) [default: 0]


[Debug]	Reuire --debug-mode = TURE
--d-user-only=VALUE		process only user code [default: 0]	
--d-max-users=VALUE		process a max number of users [default: 0]
--d-interval-only=VALUE		process only one interval. EXAMPLE: "INTERVAL_0"
--d-max-intervals=VALUE		process max num intervals by user [default: 0]. Example: "INTERVAL_1,INTERVAL_2"
--d-hidde-log-by-minute		hidde log by minute [default: FALSE]
--d-range-datetime=VALUE		proces only range date/time. FORMAT EXAMPLE: "06/09/2019 00:00,06/09/2019 09:15"

DOC;

// Define aditional params
$aditional_params = [
	'version' => '1.0.0',
	'--export-excel' => TRUE, //TODO: sirve de algo??
];

// Initialice args
$input_params = Docopt::handle($doc, $aditional_params);


$args = $input_params->args;

//var_dump($args); die();

// Get Config Params
$hconfig = getDefaultConfigParams();

// Get Config Default
$gconfig = getDefaultGlobalConfig($hconfig);

$cfg_file = CONFIG_PATH . 'sph.ini';

// For specify INPUT FILE
if ($args['-f'] && !is_null($args['FILE'])) {
	$cfg_file = trim($args['FILE']);
} elseif (!is_null($args['--cfg'])) {
	$cfg_file = trim($args['--cfg']);
}

// Get ini config
$parse_config = getConfigFile($cfg_file);

//TODO: revisar configuracion con importacion de archivo

//var_dump($parse_config);

/**
 * ==============================================================================
 * Validate and load params in config file ini
 */
if (is_array($parse_config)) {

	// ======================= Section [main] ==================================
	if (key_exists('main', $parse_config)) {

		if (key_exists('default_timezone', $parse_config['main'])) {
			$gconfig['DEFAULT_TIMEZONE'] = $parse_config['main']['default_timezone'];
		}

		//TODO: verificar si existe el directorio y si funciona el tipo de ruta
		if (key_exists('input_path', $parse_config['main'])) {
			$gconfig['INPUT_DIR_PATH'] = $parse_config['main']['input_path'];
		}

		if (key_exists('output_path', $parse_config['main'])) {
			$gconfig['OUTPUT_DIR_PATH'] = $parse_config['main']['output_path'];
		}

		if (key_exists('date_format_read', $parse_config['main'])) {
			$gconfig['DATE_FORMAT_READ'] = $parse_config['main']['date_format_read'];
		}

		if (key_exists('date_format_show', $parse_config['main'])) {
			$gconfig['DATE_FORMAT_SHOW'] = $parse_config['main']['date_format_show'];
		}

		if (key_exists('range_read_input_file', $parse_config['main'])) {
			$gconfig['RANGE_READ_INPUT_FILE'] = $parse_config['main']['range_read_input_file'];
		}

		if (key_exists('sort_order_input_file', $parse_config['main'])) {
			$gconfig['SORT_ORDER_INPUT_FILE'] = $parse_config['main']['sort_order_input_file'];
		}

		if (key_exists('apiweb', $parse_config['main'])) {
			$gconfig['APIWEB'] = $parse_config['main']['apiweb'];
		}
	}


	// ======================= Section [debug] =================================
	if (key_exists('debug', $parse_config)) {

		if (key_exists('debug_mode', $parse_config['debug'])) {
			$gconfig['DEBUG_MODE'] = $parse_config['debug']['debug_mode'];
		}

		if (key_exists('debug_only_code_user', $parse_config['debug'])) {
			$gconfig['DEBUG_ONLY_CODE_USER'] = $parse_config['debug']['debug_only_code_user'];
		}

		if (key_exists('debug_count_max_user', $parse_config['debug'])) {
			$gconfig['DEBUG_COUNT_MAX_USER'] = $parse_config['debug']['debug_count_max_user'];
		}

		if (key_exists('debug_only_intervals', $parse_config['debug'])) {
			$gconfig['DEBUG_ONLY_INTERVALS'] = $parse_config['debug']['debug_only_intervals'];
		}

		if (key_exists('debug_count_max_interval_user', $parse_config['debug'])) {
			$gconfig['DEBUG_COUNT_MAX_INTERVAL_USER'] = $parse_config['debug']['debug_count_max_interval_user'];
		}

		if (key_exists('debug_hidde_log_by_minute', $parse_config['debug'])) {
			$gconfig['DEBUG_HIDDE_LOG_BY_MINUTE'] = $parse_config['debug']['debug_hidde_log_by_minute'];
		}

		if (key_exists('debug_range_datetime', $parse_config['debug'])) {
			$gconfig['DEBUG_RANGE_DATETIME'] = $parse_config['debug']['debug_range_datetime'];
		}

		if (key_exists('verbose', $parse_config['debug'])) {
			$gconfig['VERBOSE'] = $parse_config['debug']['verbose'];
		}

		if (key_exists('quiet', $parse_config['debug'])) {
			$gconfig['QUIET'] = $parse_config['debug']['quiet'];
		}
	}



	// ======================= Section [export] ================================
	if (key_exists('export', $parse_config)) {

		if (key_exists('export_excel', $parse_config['export'])) {
			$gconfig['EXPORT_EXCEL'] = $parse_config['export']['export_excel'];
		}
	}



	// ======================= Section [hparams] ================================
	if (key_exists('hparams', $parse_config)) {

		if (key_exists('hdia', $parse_config['hparams'])) {
			$value = explode(',', $parse_config['hparams']['hdia']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HDIA'] = $value;
		}

		if (key_exists('hdiurnas', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['hdiurnas']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HDIURNAS'] = $value;
		}

		if (key_exists('hdiurnas_ext', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['hdiurnas_ext']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HDIURNAS_EXT'] = $value;
		}

		if (key_exists('hnocturnas', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['hnocturnas']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HNOCTURNAS'] = $value;
		}

		if (key_exists('hnocturnas_ext', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['hnocturnas_ext']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HNOCTURNAS_EXT'] = $value;
		}

		if (key_exists('h100', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['h100']);
			$value[0] = trim($value[0]);
			$value[1] = (int) trim($value[1]);
			$hconfig['HS100'] = $value;
		}

		if (key_exists('jlnormal_d', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['jlnormal_d']);

			foreach ($value as $i => $v) {
				$value[$i] = (int) trim($v);
			}

			$hconfig['JLNORMAL_D'] = $value;
		}

		if (key_exists('jlnormal_hs', $parse_config['hparams'])) {
			$hconfig['JLNORMAL_HS'] = [(int) $parse_config['hparams']['jlnormal_hs']];
		}

		if (key_exists('jldif_d', $parse_config['hparams'])) {

			$value = explode(',', $parse_config['hparams']['jldif_d']);

			foreach ($value as $i => $v) {
				$value[$i] = (int) trim($v);
			}

			$hconfig['JLDIF_D'] = $value;
		}

		if (key_exists('jldif_hs', $parse_config['hparams'])) {

			$hconfig['JLDIF_HS'] = [(int) $parse_config['hparams']['jldif_hs']];
		}

		if (key_exists('nsabado', $parse_config['hparams'])) {

			$hconfig['NSABADO'][(int) $parse_config['hparams']['nsabado']];
		}
	}



	// ======================= Section [alerts] ================================
	if (key_exists('alerts', $parse_config)) {

		if (key_exists('max_hours_by_interval', $parse_config['alerts'])) {
			$hconfig['MAX_HOURS_BY_INTERVAL_ALERT'] = [(int) $parse_config['alerts']['max_hours_by_interval']];
		}

		if (key_exists('text_alert_sum', $parse_config['alerts'])) {
			$gconfig['TEXT_ALERT_SUM_HOURS_OBS'] = (string) $parse_config['alerts']['text_alert_sum'];
		}

		if (key_exists('text_alert_max', $parse_config['alerts'])) {
			$gconfig['TEXT_ALERT_MAX_HOURS_OBS'] = (string) $parse_config['alerts']['text_alert_max'];
		}

		if (key_exists('text_alert_change_journal', $parse_config['alerts'])) {
			$gconfig['TEXT_ALERT_CHANGE_JOURNAL_OBS'] = (string) $parse_config['alerts']['text_alert_change_journal'];
		}
	}



	// ======================= Section [feriados] ==============================
	if (key_exists('feriados', $parse_config)) {

		if (is_array($parse_config['feriados']) && count($parse_config['feriados']) > 0) {
			$hconfig['FERIADOS'] = [];

			foreach ($parse_config['feriados'] as $year) {
				$feriados = explode(',', $year);

				if ($feriados && count($feriados) > 0) {

					foreach ($feriados as $i => $f) {

						$hconfig['FERIADOS'][] = trim($f);
					}
				}
			}
		}
	}
}

// var_dump($gconfig);
// var_dump($hconfig);

define('CONFIG_PARAMS', $hconfig);


/**
 * =============================================================================
 * Validate params in arguments 
 */

// For specify INPUT FILE
if (!is_null($args['INPUT'])) {

	$gconfig['INPUT_FILE'] = trim($args['INPUT']);
} elseif (!is_null($args['--i-file'])) {

	$gconfig['INPUT_FILE'] = trim($args['--i-file']);
}

// For specify OUTPUT FILE
if (!is_null($args['--o-file'])) {
	$gconfig['OUTPUT_FILE'] = trim($args['--o-file']);
}

// For specify INPUT DIR PATH
if (!is_null($args['--i-dir-path'])) {
	$gconfig['INPUT_DIR_PATH'] = trim($args['--i-dir-path']);
}

// For specify OUTPUT DIR PATH
if (!is_null($args['--o-dir-path'])) {
	$gconfig['OUTPUT_DIR_PATH'] = trim($args['--o-dir-path']);
}

// For specify DATE FORMAT READ
if (!is_null($args['--date-format-read'])) {

	//TODO: validar formato

	$gconfig['DATE_FORMAT_READ'] = trim($args['--date-format-read']);
}

// For specify DATE FORMAT SHOW
if (!is_null($args['--date-format-show'])) {

	//TODO: validar formato
	$gconfig['DATE_FORMAT_SHOW'] = trim($args['--date-format-show']);
}

// For specify RANGE READ
if (!is_null($args['--range-read'])) {

	//TODO: validar formato
	$gconfig['RANGE_READ_INPUT_FILE'] = trim($args['--range-read']);
}

// For specify SORT DATE ORDER
if (!is_null($args['--sort-order'])) {

	//TODO: validar valores
	$gconfig['SORT_ORDER_INPUT_FILE'] = trim($args['--sort-order']);
}


// For specify EXPORT EXCEL
if ($args['--not-export-excel'] || $args['-x']) {

	$gconfig['EXPORT_EXCEL'] = FALSE;
}

// For specify DEBUG MODE ENABLE
if ($args['-d'] || $args['--debug-mode']) {
	$gconfig['DEBUG_MODE'] = TRUE;

	// For specify ONLY USER CODE to process
	if (!is_null($args['--d-user-only'])) {

		$gconfig['DEBUG_ONLY_CODE_USER'] = trim($args['--d-user-only']);

		// For process MAX USER
	} elseif (!is_null($args['--d-max-users'])) {

		$gconfig['DEBUG_COUNT_MAX_USER'] = (int) trim($args['--d-max-users']);
	}

	// For specify ONLY INTERVAL
	if (!is_null($args['--d-interval-only'])) {

		$intervals = explode(",", $args['--d-interval-only']);

		foreach ($intervals as $interval) {

			$gconfig['DEBUG_ONLY_INTERVALS'][] = $interval;
		}


		// For process MAX INTERVALS BY USER
	} elseif (!is_null($args['--d-max-intervals'])) {

		$gconfig['DEBUG_COUNT_MAX_INTERVAL_USER'] = trim($args['--d-max-intervals']);
	}

	// For hidde log by minutes
	if ($args['--d-hidde-log-by-minute']) {

		$gconfig['DEBUG_HIDDE_LOG_BY_MINUTE'] = TRUE;
	}

	// For process only range datetime
	if (!is_null($args['--d-range-datetime'])) {

		$value = trim($args['--d-range-datetime']);
		$ex_value = explode(',', $value);

		// Validate format string value
		if (count($ex_value) != 2) {
			echo "Invalid value for --d-range-datetime" . PHP_EOL;
			exit(1);
		}

		// Validate format date start
		$dt = DateTime::createFromFormat("d/m/Y h:i", $ex_value[0]);
		if ($dt === FALSE || array_sum($dt::getLastErrors())) {
			echo "Invalid format dateTime for --d-range-datetime" . PHP_EOL;
			exit(1);
		}

		// Validate format dater end
		$dt = DateTime::createFromFormat("d/m/Y h:i", $ex_value[1]);
		if ($dt === FALSE || array_sum($dt::getLastErrors())) {
			echo "Invalid format dateTime for --d-range-datetime" . PHP_EOL;
			exit(1);
		}

		$gconfig['DEBUG_RANGE_DATETIME'][] = DateTime::createFromFormat('d/m/Y h:i', $ex_value[0]);
		$gconfig['DEBUG_RANGE_DATETIME'][] = DateTime::createFromFormat('d/m/Y h:i', $ex_value[1]);
	}
}



if ($args['--verbose'] || $args['-v'] || $gconfig['VERBOSE']) {

	$gconfig['VERBOSE'] = TRUE;

	echo "#################### INPUT PARAMS #######################" . PHP_EOL;
	var_dump($input_params);
	echo "#########################################################" . PHP_EOL;

	echo "#################### GLOBAL CONFIG ######################" . PHP_EOL;
	var_dump($gconfig);
	echo "#########################################################" . PHP_EOL;

	echo "#################### H PARAMS CONFIG ####################" . PHP_EOL;
	var_dump($hconfig);
	echo "#########################################################" . PHP_EOL;
}

if ($args['--quiet'] || $args['-q']) {

	$gconfig['QUIET'] = TRUE;
}

if ($args['--apiweb'] || $gconfig['APIWEB']) {
	$gconfig['APIWEB'] = TRUE;
	$gconfig['QUIET'] = TRUE;
	//$gconfig['VERBOSE'] = TRUE;
	$gconfig['EXPORT_EXCEL'] = TRUE;
}


$GLOBALS['CONFIG'] = $gconfig;
