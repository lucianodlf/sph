<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;


function import($input_file = NULL)
{

	$is_valid_file = validateFileImport($input_file);

	if (is_string($is_valid_file)) {

		$spread_sheet = importExcel($is_valid_file);

		return prepareData($spread_sheet);
	}

	logeo('ERROR: Not input file exist');
	exit(1);
}



function validateFileImport($input_file = NULL)
{

	logeo("File or dir input: $input_file");

	if ($input_file !== NULL) {

		if (file_exists($input_file)) {

			return $input_file;
		} else {

			return FALSE;
		}
	} else {

		logeo("File not especify");
		logeo("Search file in input dir: " . $GLOBALS['CONFIG']['INPUT_DIR_PATH']);

		if (file_exists($GLOBALS['CONFIG']['INPUT_DIR_PATH']) && is_dir($GLOBALS['CONFIG']['INPUT_DIR_PATH'])) {

			$files = customScanDir($GLOBALS['CONFIG']['INPUT_DIR_PATH'], '/^.*\.(xlsx)$/i', 'atime', 1);

			logeo("files in diretory: " . json_encode($files));


			if (strrpos($GLOBALS['CONFIG']['INPUT_DIR_PATH'], '/') !== strlen($GLOBALS['CONFIG']['INPUT_DIR_PATH']) - 1) {
				$GLOBALS['CONFIG']['INPUT_DIR_PATH'] = $GLOBALS['CONFIG']['INPUT_DIR_PATH'] . '/';
			}

			if ($files && file_exists($GLOBALS['CONFIG']['INPUT_DIR_PATH'] . "{$files[0]}")) {

				logeo("File more recent: " . $files[0]);
				$GLOBALS['CONFIG']['INPUT_FILE'] = $GLOBALS['CONFIG']['INPUT_DIR_PATH'] . "{$files[0]}";

				return $GLOBALS['CONFIG']['INPUT_DIR_PATH'] . "{$files[0]}";
			} else {

				return FALSE;
			}
		} else {

			return FALSE;
		}
	}
}


/**
 * For import data to excel
 */
function importExcel($input_file)
{

	$reader = new Xlsx();

	$spread_sheet = $reader->load($input_file);

	logeo("Import file: $input_file", FALSE, TRUE, FALSE);

	$sheet_data = $spread_sheet->getActiveSheet()->rangeToArray($GLOBALS['CONFIG']['RANGE_READ_INPUT_FILE'], NULL, TRUE, TRUE, TRUE);

	return ($sheet_data) ? $sheet_data : FALSE;
}


/**
 * For prepare data imported to process
 */
function prepareData($sheet_data)
{

	$count_days_in_interval = 0;
	$hours_data = [];
	$count_row = 0;

	foreach ($sheet_data as $idx => $row) {

		$str_row = implode("", $row);

		if (empty($str_row)) {
			continue;
		}

		// For debug or test, only prepare data of user_id = $GLOBALS['CONFIG']['DEBUG_ONLY_CODE_USER']
		if ($GLOBALS['CONFIG']['DEBUG_ONLY_CODE_USER'] > 0 && $GLOBALS['CONFIG']['DEBUG_ONLY_CODE_USER'] != trim($row['A'])) continue;

		$day_date_time = DateTime::createFromFormat($GLOBALS['CONFIG']['DATE_FORMAT_READ'], trim($row['C']) . " " . trim($row['D']));

		if (!$day_date_time) {
			logeo("ERROR: Existe algun error al procesar los datos del archivo importado. (index-row: $idx)", FALSE, TRUE, FALSE);
			logeo("Verificar si el excel tiene los titulos, si es asi, quitar esa fila ;)", FALSE, TRUE, FALSE);
			exit(1);
		}


		$hours_data[(int) $row['A']][] = [
			'user_id' => (int) $row['A'],
			'name' => (string) strtoupper(trim($row['B'])),
			'date' => (string) trim($row['C']),
			'time' => (string) trim($row['D']),
			'dateTime' => $day_date_time,
			'is_feriado' => FALSE,
			'is_date_fixed' => FALSE
		];

		$count_row++;
		//var_dump($hours_data);die();
	}

	//TODO: agregar un registro para mostrar en mensaje exportado o al final
	if ($count_row % 2 !== 0) {

		logeo("WARNING: El numero de registros procesados no es PAR", FALSE, TRUE, FALSE);
	}


	if ($GLOBALS['CONFIG']['SORT_ORDER_INPUT_FILE'] != 0) {
		// Order array by date
		foreach ($hours_data as $key => $user) {

			uasort($user, 'compareByTimestamp');

			$hours_data[$key] = $user;
		}
	}


	// For construct INTERVALS of time
	foreach ($hours_data as $key => $user) {

		$arr_interval = [];
		$count_interval_by_user = 0;

		foreach ($user as $idx => $interval) {

			if ($count_days_in_interval == 0) {

				$arr_interval['input'] = $interval;
				$count_days_in_interval++;
			} elseif ($count_days_in_interval == 1) {

				$arr_interval['output'] = $interval;

				$count_days_in_interval = 0;

				$name_interval = 'INTERVAL_' . $count_interval_by_user;

				// var_dump($name_interval);
				// var_dump($GLOBALS['CONFIG']['DEBUG_ONLY_INTERVALS']);

				if (is_array($GLOBALS['CONFIG']['DEBUG_ONLY_INTERVALS']) && !in_array($name_interval, $GLOBALS['CONFIG']['DEBUG_ONLY_INTERVALS'])) {
					$count_interval_by_user++;
					continue;
				}

				//TODO: aqui probamos correccion de fecha limite.
				//TODO: Agregar registro para identificar estos intervalos con una marca en el excel
				// Validamos si el DateTime INPUT es igual a un DateTime tomando la misma fecha con hora 00:00
				if ((new DateTime($arr_interval['input']['dateTime']->format('Y-m-d'))) == $arr_interval['input']['dateTime']) {

					// Validamos si la fehca de OUTPUT es mayor a la de INPUT
					if ((new DateTime($arr_interval['output']['dateTime']->format('Y-m-d'))) > (new DateTime($arr_interval['input']['dateTime']->format('Y-m-d')))) {

						// Sumamos 1 dia para compensar el error en los datos de entrada
						$arr_interval['input']['dateTime']->add(new DateInterval('P1D'));

						//TODO: test mark
						$arr_interval['input']['is_date_fixed'] = TRUE;
					}
				}
				//TODO: fin de prueba de fix error

				$hours_data[$key]['INTERVALS'][$name_interval] = $arr_interval;

				$count_interval_by_user++;
			}
		}

		$count_days_in_interval = 0;
	}
	return $hours_data;
}
