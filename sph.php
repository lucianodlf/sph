<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(!E_NOTICE);

require __DIR__ . '/config/config_default.php';
require HEADER_PATH . 'header_default.php';

// Default timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

deleteOldLogFile();

logeo("Start script", FALSE, TRUE);

// Import and prepare data to process
$hours_data = import($GLOBALS['CONFIG']['INPUT_FILE']);


logeo("Process ...", FALSE, TRUE);

logeo(PHP_EOL, FALSE, FALSE, TRUE);

// Initial log
logeo('=========================================================================================', FALSE, TRUE);
logeo('Default timezone: ' . date_default_timezone_get(), FALSE, TRUE);
logeo('Fomat datetime read: ' . $GLOBALS['CONFIG']['DATE_FORMAT_READ'], FALSE, TRUE);
logeo('Format datetime show: ' . $GLOBALS['CONFIG']['DATE_FORMAT_SHOW'], FALSE, TRUE);
logeo('Range read in excel: ' . $GLOBALS['CONFIG']['RANGE_READ_INPUT_FILE'], FALSE, TRUE);
logeo('Sort default: ' . $GLOBALS['CONFIG']['SORT_ORDER_INPUT_FILE'], FALSE, TRUE);
//logeo('Confir params: ' . json_encode($config_params));
logeo('File read: ' . $GLOBALS['CONFIG']['INPUT_FILE'], FALSE, TRUE);
logeo('=========================================================================================', FALSE, TRUE);

logeo(PHP_EOL, FALSE, FALSE, TRUE);

logeo('=========================================================================================');

// Show data prepare for process - DEBUG
debugInitialDataPrepare($hours_data);

logeo(PHP_EOL, FALSE, FALSE, TRUE);

logeo('=========================================================================================');
logeo('============================ START CALCULATE MINUTES PER USER ===========================');
logeo('=========================================================================================');


// Set iterator count users
$count_iterator = 0;

/**
 * 
 * Recorremos todos los usuarios
 * 
 */
foreach ($hours_data as $user_id => $user) {

	// For debug or test, only process max count users
	if ($GLOBALS['CONFIG']['DEBUG_COUNT_MAX_USER'] > 0 && $count_iterator >= $GLOBALS['CONFIG']['DEBUG_COUNT_MAX_USER']) {
		continue;
	}

	logeo(PHP_EOL, FALSE, FALSE, TRUE);
	logeo("--------------------------------- USER( {$user[0]['user_id']} - {$user[0]['name']} ) START ---------------------------------");
	logeo(PHP_EOL, FALSE, FALSE, TRUE);

	$count_iterator++;

	logeo("COUNT USER ITERATOR: " . $count_iterator);

	// Set cantidad de intervalos del usuario
	$count_interbal_by_user = 0;

	/**
	 * 
	 * Recorremos cada intervalo del usuario actual
	 * 
	 */
	foreach ($user['INTERVALS'] as $id_interval => $interval) {

		$count_interbal_by_user++;

		/**
		 * Inicializamos array para almacenar
		 * resultados del intervalo
		 * 
		 */
		$minutes_interval_result = [
			'total' => 0,						// Total de horas del dia
			'diurnas_n' => 0,					// Total de horas diurnas
			'diurnas_e' => 0,					// Total de horas extras diurnas
			'nocturnas_n' => 0,					// Total de horas nocturnas
			'nocturnas_e' => 0,					// Total de horas extras nocturnas
			'h100' => 0,						// Total de horas al 100%
			'sabadonoth100' => 0,				// Total de horas del sabado no 100% (NO USADO POR AHORA)
			'alert_max_hours' => FALSE, 		// Alerta para indicar superacion de un maximo de horas del intervalo
			'alert_max_hours_obs' => NULL,		// Observacion de la alerta 'alert_max_hours_obs'
			'alert_sum_hours' => FALSE, 		// Alerta para indicar que la suma de horas desglosadas no es igual al total
			'alert_sum_hours_obs' => NULL,		// Observacion de la alerta 'alert_sum_hours'
			'alert_change_journal' => FALSE,	// Alerta para indicar que el intervalo tiene un cambio de tipo de joranda
			'alert_change_journal_obs' => NULL,	// Observacion de la alerta 'alert_change_journal'
		];

		//logeo('Prepare Array Data Result(hours_interval_result): ' . json_encode($minutes_interval_result));

		logeo("---------- INTERVAL ($id_interval) - ({$interval['input']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])} - {$interval['output']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])}) START ----------");

		logeo(PHP_EOL, FALSE, FALSE, TRUE);

		// ================ Seteamos los parametros de horas en base del dia actual para comparaciones ================/

		$current_params = setCurrentParamsByHour($interval);

		// =============================================================================================================/

		logeo(PHP_EOL, FALSE, FALSE, TRUE);

		// ======================= Debug - imprimos parametros de control =============================================/

		debugCurrentParams($current_params);

		// =============================================================================================================/

		logeo(PHP_EOL, FALSE, FALSE, TRUE);

		//================================= Validamos si el dia es un FERIADO ==========================================/

		validateIsDayFeriado();

		// =============================================================================================================/

		//================================= Validamos si hay cambio de jornada =========================================/

		validateIntervalChangeJournal($interval['input']['dateTime'], $interval['output']['dateTime']);

		// =============================================================================================================/


		//=================================== Validamos el tipo de jornada =============================================/

		// TODO: De esta forma tenemos solo en cuenta el dia de ingreso.
		$hours_of_journal = validateTypeJournal($interval['input']['dateTime']->format('w'));

		// =============================================================================================================/


		$acum_minutes_datetime = clone $interval['input']['dateTime'];


		logeo('################################## START ITERATOR BY MINUTES OF DAY ################################', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);


		// Recorremos cada minuto hasta el limite del intervalo
		while ($acum_minutes_datetime < $interval['output']['dateTime']) {

			if ($GLOBALS['CONFIG']['DEBUG_MODE']) {
				if (is_array($GLOBALS['CONFIG']['DEBUG_RANGE_DATETIME']) && $GLOBALS['CONFIG']['DEBUG_RANGE_DATETIME'][0] != NULL && $GLOBALS['CONFIG']['DEBUG_RANGE_DATETIME'][1] != NULL) {

					if ($acum_minutes_datetime >= $GLOBALS['CONFIG']['DEBUG_RANGE_DATETIME'][0] && $acum_minutes_datetime <= $GLOBALS['CONFIG']['DEBUG_RANGE_DATETIME'][1]) {
						$GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE'] = FALSE;
					} else {
						$GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE'] = TRUE;
					}
				}
			}


			// Add 1 Minute
			$acum_minutes_datetime->add(new DateInterval('PT1M'));

			// Add total minutes of interval
			$minutes_interval_result['total']++;

			logeo('Total minutos of day: ' . $minutes_interval_result['total'], $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

			// Validate if exeed limit alert hours by interval (MAX_HOURS_BY_INTERVAL_ALERT)
			if ($minutes_interval_result['total'] > (CONFIG_PARAMS['MAX_HOURS_BY_INTERVAL_ALERT'][0] * 60)) {

				$minutes_interval_result['alert_max_hours'] = TRUE;
				$minutes_interval_result['alert_max_hours_obs'] = $GLOBALS['CONFIG']['TEXT_ALERT_MAX_HOURS_OBS'];
			}

			// Get num day of week
			$day_of_week = $acum_minutes_datetime->format('w');

			logeo('############################# MINUTE START ################################', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

			logeo("Number day of week: " . $day_of_week, $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

			logeo("Hour of day to proccess: " . json_encode($acum_minutes_datetime), $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

			//*************** SI ES SABADO ****************/
			if ($day_of_week == CONFIG_PARAMS['NSABADO']) {

				validateIsDaySabado();
			} else {

				//*************** NO ES SABADO ****************/

				// Validate journal and get hours by type journal
				$flg_hour_normal = validateExeedHoursOfJournal($minutes_interval_result['total'], $hours_of_journal);

				logeo("flg_hours_normal (" . (int) $flg_hour_normal . ")", $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

				validateDiurnas();

				validateNocturnas();

				logeo('############################# MINUTE END ################################', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);
				logeo("", $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);
			}

			logeo("", $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);
		} // END While by minutes


		logeo('################################## END ITERATOR BY MINUTES OF DAY ##################################', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);


		// Validamos para otra alerta. Si la suma de las horas desglosadas no es igual al total de horas.
		$sum_hours = 0;
		$sum_hours += $minutes_interval_result['diurnas_n'];
		$sum_hours += $minutes_interval_result['diurnas_e'];
		$sum_hours += $minutes_interval_result['nocturnas_n'];
		$sum_hours += $minutes_interval_result['nocturnas_e'];
		$sum_hours += $minutes_interval_result['h100'];
		$sum_hours += $minutes_interval_result['sabadonoth100'];

		if ($sum_hours != $minutes_interval_result['total']) {
			$minutes_interval_result['alert_sum_hours'] = TRUE;

			$minutes_interval_result['alert_sum_hours_obs'] = $GLOBALS['CONFIG']['TEXT_ALERT_SUM_HOURS_OBS'];
		}

		debugShowRersumen($minutes_interval_result);

		// Add result of interval to data array
		$hours_data[$user_id]['INTERVALS'][$id_interval]['RESULT'] = $minutes_interval_result;

		logeo("------------ INTERVAL ($id_interval) - ({$interval['input']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])} - {$interval['output']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW'])} END -----------");


		logeo(PHP_EOL, FALSE, FALSE, TRUE);


		// For debug or test, only process max max count intervals by user
		if ($GLOBALS['CONFIG']['DEBUG_COUNT_MAX_INTERVAL_USER'] > 0 && $count_interbal_by_user >= $GLOBALS['CONFIG']['DEBUG_COUNT_MAX_INTERVAL_USER']) {
			break;
		}

		$sum_hours = 0;
	} // END Foreach by intervals user

	logeo("--------------------------- USER( {$user[0]['user_id']} - {$user[0]['name']} ) END ------------------------------");
} // END Foreach by users

logeo();

logeo('=========================================================================================');
logeo('============================ END CALCULATE MINUTES PER USER =============================');
logeo('=========================================================================================');

logeo();

//var_dump($hours_data);

if (!$GLOBALS['CONFIG']['QUIET']) {

	logeo(PHP_EOL, FALSE, TRUE, TRUE);

	// Export default in screen
	showResult($hours_data);
}

if ($GLOBALS['CONFIG']['EXPORT_EXCEL']) {

	logeo(PHP_EOL, FALSE, TRUE, TRUE);

	if ($GLOBALS['CONFIG']['APIWEB']) {

		logeo('Export mode (APPIWEB)', FALSE, TRUE);

		// Export data to file
		$output = export($hours_data, $GLOBALS['CONFIG']['OUTPUT_FILE']);

		$data_return = [
			'status' => 0,
			'path' => ''
		];

		if (file_exists($output)) {
			$data_return['path'] = realpath($output);
			$data_return['status'] = 1;
		}

		$json_data = json_encode($data_return);
		logeo("Export data: " . $json_data);
		echo $json_data;
	} else {

		// Export data to file
		export($hours_data, $GLOBALS['CONFIG']['OUTPUT_FILE']);

		logeo("Export result file: {$GLOBALS['CONFIG']['OUTPUT_FILE']}", FALSE, TRUE);
	}
}


logeo("End script ;)", FALSE, TRUE);

/************************************************ FUNCTIONS ***********************************************/




// For validate hours is NOCTURNAS
function validateNocturnas()
{

	global $current_params, $acum_minutes_datetime, $flg_hour_normal, $minutes_interval_result;

	// Verificamos si estamos dentro de  horas nocturnas
	logeo('Verify range hours NOCTURNA (' . $current_params['HNOCTURNAS']['start']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - (' . $current_params['HNOCTURNAS']['end']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - [' . $acum_minutes_datetime->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ']', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

	if ($acum_minutes_datetime > $current_params['HNOCTURNAS']['start'] && $acum_minutes_datetime <= $current_params['HNOCTURNAS']['end'] || $acum_minutes_datetime <= $current_params['HDIURNAS']['start']) {

		logeo('Hour NOCTURNA - OK', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

		if ($flg_hour_normal) {
			// Aumentamos 1 hora nocturna normal
			$minutes_interval_result['nocturnas_n']++;
		} else {
			//TODO: tendria que preguntar si esta dentro del rango de horas extra??
			// Aumentamos 1 hora nocturna extra
			$minutes_interval_result['nocturnas_e']++;
		}
	}
}




// For validate hours is DIURNAS
function validateDiurnas()
{
	global $current_params, $acum_minutes_datetime, $flg_hour_normal, $minutes_interval_result;

	// Verificamos si estamos dentro de horas diurnas
	logeo('Verify range hours DIURNA (' . $current_params['HDIURNAS']['start']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - (' . $current_params['HDIURNAS']['end']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - [' . $acum_minutes_datetime->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ']', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

	if ($acum_minutes_datetime > $current_params['HDIURNAS']['start'] && $acum_minutes_datetime <= $current_params['HDIURNAS']['end'] || $acum_minutes_datetime > $current_params['HNOCTURNAS']['end']) {

		logeo('Hour DIURNA - OK', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

		if ($flg_hour_normal) {
			// Aumentamos 1 hora diurna normal
			$minutes_interval_result['diurnas_n']++;
		} else {
			//TODO: tendria que preguntar si esta dentro del rango de horas extra??
			//TODO: en caso de que sea diferente el rango.
			// Aumentamos 1 hora diurna extra
			$minutes_interval_result['diurnas_e']++;
		}
	}
}


/**
 * Valida si hay cambio de tipo de jornada entre
 * fecha de ingreso y egreso para setear una alerta
 */
function validateIntervalChangeJournal($input_date = NULL, $output_date = NULL)
{
	global $minutes_interval_result;

	if (!$input_date || !$output_date) {
		return FALSE;
	}

	if (in_array($input_date->format('w'), CONFIG_PARAMS['JLNORMAL_D']) && in_array($output_date->format('w'), CONFIG_PARAMS['JLDIF_D'])) {

		$minutes_interval_result['alert_change_journal'] = TRUE;

		$minutes_interval_result['alert_change_journal_obs'] = $GLOBALS['CONFIG']['TEXT_ALERT_CHANGE_JOURNAL_OBS'];
	}

	if (in_array($output_date->format('w'), CONFIG_PARAMS['JLNORMAL_D']) && in_array($input_date->format('w'), CONFIG_PARAMS['JLDIF_D'])) {

		$minutes_interval_result['alert_change_journal'] = TRUE;

		$minutes_interval_result['alert_change_journal_obs'] = $GLOBALS['CONFIG']['TEXT_ALERT_CHANGE_JOURNAL_OBS'];
	}
}



/**
 * Valida el tipo de jornada y setea el maximo de horas
 * normales (no extras).
 * 
 */
function validateTypeJournal($day_of_week = NULL)
{

	$hours_of_journal = 0;

	if (in_array($day_of_week, CONFIG_PARAMS['JLNORMAL_D'])) {

		//***************** Jornada NORMAL
		$hours_of_journal = CONFIG_PARAMS['JLNORMAL_HS'][0];

		logeo("Normal Journal - OK: Hours of journal ($hours_of_journal)");
	} elseif ($day_of_week == CONFIG_PARAMS['JLDIF_D'][0]) {

		//***************** Jornada DIFERENCIAL
		$hours_of_journal = CONFIG_PARAMS['JLDIF_HS'][0];

		logeo("Diferencial Journal - OK: Hours of journal ($hours_of_journal)");
	}

	return $hours_of_journal;
}



/**
 * Valida si se excede el maximo de horas de la jornada
 * para considerar si son horas normales o extras.
 * 
 * Retorna TRUE (horas normales) o FLASE (horas extras)
 */
function validateExeedHoursOfJournal($total_minutes = 0, $hours_of_journal = 0)
{

	// Verificamos si cubrimos las horas de la jornada
	if ($total_minutes > ($hours_of_journal * 60)) {

		return FALSE;
	}

	return TRUE;
}



function validateIsDayFeriado()
{
	global $interval, $user_id, $id_interval, $hours_data;

	if (in_array($interval['input']['dateTime']->format('d/m/Y'), CONFIG_PARAMS['FERIADOS'])) {

		$hours_data[$user_id]['INTERVALS'][$id_interval]['input']['is_feriado'] = TRUE;
	} else {

		$hours_data[$user_id]['INTERVALS'][$id_interval]['input']['is_feriado'] = FALSE;
	}

	if (in_array($interval['output']['dateTime']->format('d/m/Y'), CONFIG_PARAMS['FERIADOS'])) {

		$hours_data[$user_id]['INTERVALS'][$id_interval]['output']['is_feriado'] = TRUE;
	} else {

		$hours_data[$user_id]['INTERVALS'][$id_interval]['output']['is_feriado'] = FALSE;
	}
}



function validateIsDaySabado()
{
	global $current_params, $acum_minutes_datetime, $minutes_interval_result;

	logeo('Day is SABADO', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

	// Verificamos si estamos dentro de horas al 100 %
	logeo('Verify range hours HS100 (' . $current_params['HS100']['start']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - (' . $current_params['HS100']['end']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ') - [' . $acum_minutes_datetime->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']) . ']', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

	if ($acum_minutes_datetime > $current_params['HS100']['start'] && $acum_minutes_datetime <= $current_params['HS100']['end']) {

		logeo('Hour H100 - OK', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);

		$minutes_interval_result['h100']++;
	} else {

		logeo('Hora es diurna (SABADO no al 100%', $GLOBALS['CONFIG']['DEBUG_HIDDE_LOG_BY_MINUTE']);
		$minutes_interval_result['sabadonoth100']++;
		$minutes_interval_result['diurna_e']++;
	}
}


function setCurrentParamsByHour()
{
	global $interval;

	$current_params = [
		'HDIURNAS' => [
			'start' => new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HDIURNAS'][0]),
			'end' => (new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HDIURNAS'][0]))->add(new DateInterval('PT' . CONFIG_PARAMS['HDIURNAS'][1] . 'H'))
		],
		'HDIURNAS_EXT' => [
			'start' => new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HDIURNAS_EXT'][0]),
			'end' => (new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HDIURNAS_EXT'][0]))->add(new DateInterval('PT' . CONFIG_PARAMS['HDIURNAS_EXT'][1] . 'H'))
		],
		'HNOCTURNAS' => [
			'start' => new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HNOCTURNAS'][0]),
			'end' => (new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HNOCTURNAS'][0]))->add(new DateInterval('PT' . CONFIG_PARAMS['HNOCTURNAS'][1] . 'H'))
		],
		'HNOCTURNAS_EXT' => [
			'start' => new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HNOCTURNAS_EXT'][0]),
			'end' => (new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HNOCTURNAS_EXT'][0]))->add(new DateInterval('PT' . CONFIG_PARAMS['HNOCTURNAS_EXT'][1] . 'H'))
		],
		'HS100' => [
			'start' => new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HS100'][0]),
			'end' => (new DateTime($interval['input']['dateTime']->format('Y-m-d') . ' ' . CONFIG_PARAMS['HS100'][0]))->add(new DateInterval('PT' . CONFIG_PARAMS['HS100'][1] . 'H'))
		],
		//'JLNORMAL_D' => [1,2,3,4], // Jornada laboral normal: numero de dias de la semana (1=Lunes, 2=Martes, 3=Miercoles, 4=Jueves, 5=Viernes)
		//'JLNORMAL_HS' => [9], // Numero de hs que dura la jornada
		//'JLDIF_D' => [5], // Jornada laboral diferencial
		//'JLDIF_HS' => [8],
		//'FERIADOS' => []
	];

	return $current_params;
}
