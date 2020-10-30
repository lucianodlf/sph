<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

// TODO: setear por configuracion
//StringHelper::setDecimalSeparator('.');
//StringHelper::setThousandsSeparator(',');

function export($hours_data, $export_file = NULL, $absences_summary = NULL)
{

	$export_file = validateFileExport($export_file);

	if (!is_array($hours_data)) {
		return FALSE;
	}

	exportExcel($hours_data, $export_file, $absences_summary);

	return $export_file;
}


function validateFileExport($export_file = NULL)
{

	if ($export_file === NULL) {

		if (strrpos($GLOBALS['CONFIG']['OUTPUT_DIR_PATH'], '/') !== strlen($GLOBALS['CONFIG']['OUTPUT_DIR_PATH']) - 1) {
			$GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] = $GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] . '/';
		}


		createDirectory($GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] . DATE_EXECUTION);

		$name_file_export = $GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] . DATE_EXECUTION . '/' . date('Ymdhis') . '_' . 'export.xlsx';

		$GLOBALS['CONFIG']['OUTPUT_FILE'] = $name_file_export;

		return $name_file_export;
	} else {

		return $export_file;
	}
}


function exportExcel($hours_data, $export_file, $absences_summary = NULL)
{

	/** Create a new Spreadsheet Object **/
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Premoldeados Bertone')
		->setLastModifiedBy('Premoldeados Bertone')
		->setTitle('Office 2007 XLSX Test Document')
		->setSubject('Office 2007 XLSX Test Document')
		->setDescription('Detalle de horas trabajadas')
		->setKeywords('horas')
		->setCategory('Administration');

	// Set global style font
	$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
	$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('DETALLE DE HORAS');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);


	/**
	 *
	 * ========================== Prepare value title for export ================================
	 * 
	 */

	$titles = [
		'A' => 'CU',
		'B' => 'NOMBRE',
		'C' => 'INTERVALO',
		'D' => 'INGRESO',
		'E' => 'EGRESO',
		'F' => 'DIURNAS',
		'G' => 'DIURNAS-E',
		'H' => 'NOCTURNAS',
		'I' => 'NOCTURNAS-E',
		'J' => 'H100',
		'K' => 'FI',
		'L' => 'FE',
		'M' => 'ALERTA',
		'N' => 'TOTAL',
		'O' => 'OBSERVACIÓN',
		'P' => 'DFIXED'
	];

	$row_title = 1;

	$style_title_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => true,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'startColor' => [
				'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK,
			],
		],
	];


	foreach ($titles as $column => $value) {

		// Set columns autozise for title
		$spreadsheet->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(TRUE);

		if ($column === 'K') {
			$spreadsheet->getActiveSheet()->getComment("{$column}{$row_title}")->getText()->createTextRun('Indica si es FERIADO el dia de INGRESO');
		}

		if ($column === 'L') {
			$spreadsheet->getActiveSheet()->getComment("{$column}{$row_title}")->getText()->createTextRun('Indica si es FERIADO el dia de EGRESO');
		}

		if ($column === 'P') {
			$spreadsheet->getActiveSheet()->getComment("{$column}{$row_title}")->getText()->createTextRun('Indica si fue necesario corregir la fecha de ingreso');
		}

		// // Set bold
		// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->getFont()->setBold(TRUE);

		// // Set font color
		// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

		// // Set background color
		// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
		// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);

		// // Set align
		// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		// Set style from array
		$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->applyFromArray($style_title_array);

		// Set value
		$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_title}", $value);
	}


	/**
	 *
	 * ========================== Prepare value data for export ================================
	 * 
	 */

	// Set style cells of data
	$style_data_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => false,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],
		// 'fill' => [
		// 	'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		// 	'startColor' => [
		// 		'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK,
		// 	],
		// ],
	];

	// Set array data for write excel
	$arr_data_users = [];

	// Set array data for summary hours by each user
	$arr_summary_hours = [];

	foreach ($hours_data as $user_id => $user) {

		foreach ($user['INTERVALS'] as $interval_id => $interval) {

			// var_dump($interval); die();

			$arr_data_users[] = [
				'A' => (string) $user_id,
				'B' => (string) $interval['input']['name'],
				'C' => (string) $interval_id,
				'D' => (string) $interval['input']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']),
				'E' => (string) $interval['output']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']),
				'F' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['diurnas_n']),
				'G' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['diurnas_e']),
				'H' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['nocturnas_n']),
				'I' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['nocturnas_e']),
				'J' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['h100']),
				'K' => (string) $interval['input']['is_feriado'],
				'L' => (string) $interval['output']['is_feriado'],
				'M' => (returnStatusAlert($interval)) ? "SI" : "",
				'N' => (string) convertMinutosToHoursAndMinutes($interval['RESULT']['total']),
				'O' => (string) returnObservationAlert($interval),
				'P' => ($interval['input']['is_date_fixed']) ? "SI" : "",
			];

			$arr_summary_hours[$user_id] = [
				'A' => (string) $user_id,
				'B' => (string) $interval['input']['name'],
				'C' => $arr_summary_hours[$user_id]['C'] + $interval['RESULT']['diurnas_n'],
				'D' => $arr_summary_hours[$user_id]['D'] + $interval['RESULT']['diurnas_e'],
				'E' => $arr_summary_hours[$user_id]['E'] + $interval['RESULT']['nocturnas_n'],
				'F' => $arr_summary_hours[$user_id]['F'] + $interval['RESULT']['nocturnas_e'],
				'G' => $arr_summary_hours[$user_id]['G'] + $interval['RESULT']['h100'],
				'H' => $arr_summary_hours[$user_id]['H'] + ($interval['input']['is_feriado'] || $interval['output']['is_feriado']) ? 1 : 0,
				'I' => $arr_summary_hours[$user_id]['I'] + $interval['RESULT']['total'],
			];
		}
	}

	$row_data = 2;

	foreach ($arr_data_users as $row) {

		foreach ($row as $column => $value) {

			// var_dump($hours_data[$row['A']]['INTERVALS'][$row['C']]['is_odd_records']); die();

			$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_data}", $value);

			$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->applyFromArray($style_data_array);

			// Set style if ALERT
			if ($column === 'M' && $value === 'SI' || $column === 'P' && $value === 'SI') {

				// Set font color
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

				// Set background color
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

				// Set bold
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->setBold(TRUE);
			}

			if ($hours_data[$row['A']]['INTERVALS'][$row['C']]['is_odd_records']) {
				$spreadsheet->getActiveSheet()->getStyle("A$row_data:E$row_data")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle("A$row_data:E$row_data")->getFill()->getStartColor()->setARGB("FF5722");
			}
		}

		$row_data++;
	}


	if ($absences_summary !== NULL) {
		exportSummaryAbsencesExcel($spreadsheet, $absences_summary, $hours_data);
	}

	exportSummaryHours($spreadsheet, $arr_summary_hours);


	$write = IOFactory::createWriter($spreadsheet, 'Xlsx');

	$write->save($export_file);
}






function exportSummaryAbsencesExcel($spreadsheet, $absences_summary, $hours_data)
{

	//Creamos nueva worksheet
	$worksheetSummaryAbsences = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'RESUMEN INASISTENCIAS');

	// Agrega la nueva worksheet con indice 1
	$spreadsheet->addSheet($worksheetSummaryAbsences, 2);

	// Activamos la nueva worksheet para trabajar sobre ella
	$spreadsheet->setActiveSheetIndex(2);

	/**
	 *
	 * ========================== Prepare value title for export ================================
	 * 
	 */

	$titles = [
		'A' => 'CU',
		'B' => 'NOMBRE',
		'C' => 'FECHA',
		'D' => 'TIPO',
		'E' => 'OBSERVACIÓN'
	];

	$row_title = 1;

	$style_title_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => true,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'startColor' => [
				'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK,
			],
		],
	];


	foreach ($titles as $column => $value) {

		// Set columns autozise for title
		$spreadsheet->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(TRUE);

		// Set style from array
		$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->applyFromArray($style_title_array);

		// Set value
		$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_title}", $value);
	}


	/**
	 *
	 * ========================== Prepare value data for export ================================
	 * 
	 */

	// Set style cells of data
	$style_data_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => false,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		]
	];

	// Set array data for write excel
	$arr_absences = [];

	foreach ($absences_summary as $user_id => $dates) {

		foreach ($dates as $date => $type_absence) {

			switch (DateTime::createFromFormat('d/m/Y', $date)->format('w')) {
				case "0":
					$obs = 'Domingo';
					break;
				case "5":
					$obs = 'Viernes';
					break;
				case "6":
					$obs = 'Sábado';
					break;

				default:
					$obs = '';
					break;
			}


			$arr_absences[] = [
				'A' => (string) $user_id,
				'B' => (string) $hours_data[$user_id][0]['name'],
				'C' => (string) $date,
				'D' => (string) $type_absence,
				'E' => (string) $obs,
			];
		}
	}



	$row_data = 2;

	foreach ($arr_absences as $row) {

		foreach ($row as $column => $value) {

			$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_data}", $value);

			$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->applyFromArray($style_data_array);

			if ($column === 'D' && $value === 'IT') {

				// Set font color
				// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

				// Set background color
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

				// Set bold
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->setBold(TRUE);
			} else if ($column === 'D' && validateTypeJournal(DateTime::createFromFormat('d/m/Y', $row['C'])->format('w'), TRUE) > $value) {

				// Set font color
				// $spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

				// Set background color
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);

				// Set bold
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->setBold(TRUE);
			}

			if ($column === 'D' && $value === 'FF') {
				// Set background color
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

				// Set bold
				$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->getFont()->setBold(TRUE);
			}
		}

		$row_data++;
	}

	$spreadsheet->getActiveSheet()->getStyle("G2:H5")->applyFromArray($style_data_array);
	$spreadsheet->getActiveSheet()->getColumnDimension("H")->setAutoSize(TRUE);

	// Set font color
	//$spreadsheet->getActiveSheet()->getStyle("G2:G4")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

	// Set background color
	$spreadsheet->getActiveSheet()->getStyle("G2:G4")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
	$spreadsheet->getActiveSheet()->getStyle("G2")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
	$spreadsheet->getActiveSheet()->getStyle("G3")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
	$spreadsheet->getActiveSheet()->getStyle("G4")->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

	// Set bold
	$spreadsheet->getActiveSheet()->getStyle("G2:G5")->getFont()->setBold(TRUE);
	$spreadsheet->getActiveSheet()->setCellValue("G2", "IT");
	$spreadsheet->getActiveSheet()->setCellValue("G3", "");
	$spreadsheet->getActiveSheet()->setCellValue("G4", "FF");
	$spreadsheet->getActiveSheet()->setCellValue("G5", "N");

	$spreadsheet->getActiveSheet()->setCellValue("H2", "Inasistencia Total");
	$spreadsheet->getActiveSheet()->setCellValue("H3", "Inasistencia Parcial");
	$spreadsheet->getActiveSheet()->setCellValue("H4", "Feriado");
	$spreadsheet->getActiveSheet()->setCellValue("H5", "Cantidad de horas registradas");

	// Volvemos a ctivar la worksheet principal
	$spreadsheet->setActiveSheetIndex(0);
}




function exportSummaryHours($spreadsheet, $arr_summary_hours)
{

	//Creamos nueva worksheet
	$worksheetSummaryHours = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'RESUMEN HORAS');

	// Agrega la nueva worksheet con indice 1
	$spreadsheet->addSheet($worksheetSummaryHours, 1);

	// Activamos la nueva worksheet para trabajar sobre ella
	$spreadsheet->setActiveSheetIndex(1);

	/**
	 *
	 * ========================== Prepare value title for export ================================
	 * 
	 */

	$titles = [
		'A' => 'CU',
		'B' => 'NOMBRE',
		'C' => 'DIURNAS',
		'D' => 'DIURNAS E',
		'E' => 'NOCTURNAS',
		'F' => 'NOCTURNAS E',
		'G' => 'H100',
		'H' => 'FERIADOS',
		'I' => 'TOTAL',
	];

	$row_title = 1;

	$style_title_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => true,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		],
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'startColor' => [
				'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK,
			],
		],
	];


	foreach ($titles as $column => $value) {

		// Set columns autozise for title
		$spreadsheet->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(TRUE);

		// Set style from array
		$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_title}")->applyFromArray($style_title_array);

		// Set value
		$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_title}", $value);
	}

	/**
	 *
	 * ========================== Prepare value data for export ================================
	 * 
	 */

	// Set style cells of data
	$style_data_array = [
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
			],
		],
		'font' => [
			'bold' => false,
			'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK]
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		]
	];


	foreach ($arr_summary_hours as $user_id => $data) {
		$data['C'] = ($data['C'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['C']) : 0;
		$data['D'] = ($data['D'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['D']) : 0;
		$data['E'] = ($data['E'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['E']) : 0;
		$data['F'] = ($data['F'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['F']) : 0;
		$data['G'] = ($data['G'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['G']) : 0;
		$data['I'] = ($data['I'] !== 0) ? (string) convertMinutosToHoursAndMinutes($data['I']) : 0;

		$arr_summary_hours[$user_id] = $data;
	}


	$row_data = 2;

	foreach ($arr_summary_hours as $row) {
		foreach ($row as $column => $value) {

			$spreadsheet->getActiveSheet()->setCellValue("{$column}{$row_data}", $value);

			$spreadsheet->getActiveSheet()->getStyle("{$column}{$row_data}")->applyFromArray($style_data_array);
		}
		$row_data++;
	} 

	$spreadsheet->setActiveSheetIndex(0);
}
