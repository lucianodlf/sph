<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

// TODO: setear por configuracion
//StringHelper::setDecimalSeparator('.');
//StringHelper::setThousandsSeparator(',');

function export($hours_data, $export_file = NULL)
{
	$export_file = validateFileExport($export_file);

	if (!is_array($hours_data)) {
		return FALSE;
	}

	exportExcel($hours_data, $export_file);
}


function validateFileExport($export_file = NULL)
{

	if ($export_file === NULL) {

		createDirectory($GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] . DATE_EXECUTION);

		$name_file_export = $GLOBALS['CONFIG']['OUTPUT_DIR_PATH'] . DATE_EXECUTION . '/' . date('Ymdhis') . '_' . 'export.xlsx';

		$GLOBALS['CONFIG']['OUTPUT_FILE'] = $name_file_export;

		return $name_file_export;
	} else {

		return $export_file;
	}
}


function exportExcel($hours_data, $export_file)
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
		'O' => 'OBSERVACION',
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

		if($column === 'K'){
			$spreadsheet->getActiveSheet()->getComment("{$column}{$row_title}")->getText()->createTextRun('Indica si es FERIADO el dia de INGRESO');
		}

		if($column === 'L'){
			$spreadsheet->getActiveSheet()->getComment("{$column}{$row_title}")->getText()->createTextRun('Indica si es FERIADO el dia de EGRESO');
		}

		if($column === 'P'){
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

	foreach ($hours_data as $user_id => $user) {

		foreach ($user['INTERVALS'] as $interval_id => $interval) {

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
		}
	}

	$row_data = 2;

	foreach ($arr_data_users as $row) {

		foreach ($row as $column => $value) {

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
		}

		$row_data++;
	}

	$write = IOFactory::createWriter($spreadsheet, 'Xlsx');

	$write->save($export_file);
}

