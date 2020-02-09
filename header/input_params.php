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


$hconfig = getDefaultConfigParams();

define('CONFIG_PARAMS', $hconfig);

/**
 * Validate input config params
 */
$gconfig = getDefaultGlobalConfig();

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
		
		foreach($intervals as $interval){

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



if ($args['--verbose'] || $args['-v']) {

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


$GLOBALS['CONFIG'] = $gconfig;