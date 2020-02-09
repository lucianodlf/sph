<?php
// Export result
define('SEPARATOR_TITLE', '|');
define('SEPARATOR_VALUE', '|');
define('STR_PAD_LENGHT', 20);
define('IS_PAD', TRUE);


function showResult($hours_data)
{

	$row_title = "|";
	$row_title .= write_output_row('CU', 4) . SEPARATOR_TITLE;
	$row_title .= write_output_row('NOMBRE', 15) . SEPARATOR_TITLE;
	$row_title .= write_output_row('INTERVALO', 11) . SEPARATOR_TITLE;
	$row_title .= write_output_row('INGRESO', 18) . SEPARATOR_TITLE;
	$row_title .= write_output_row('EGRESO', 18) . SEPARATOR_TITLE;
	$row_title .= write_output_row('DIURNAS', 10) . SEPARATOR_TITLE;
	$row_title .= write_output_row('DIURNAS (E)', 13) . SEPARATOR_TITLE;
	$row_title .= write_output_row('NOCTURNAS', 10) . SEPARATOR_TITLE;
	$row_title .= write_output_row('NOCTURNAS (E)', 13) . SEPARATOR_TITLE;
	$row_title .= write_output_row('H100', 5) . SEPARATOR_TITLE;
	$row_title .= write_output_row('FERIADO', 7) . SEPARATOR_TITLE;
	$row_title .= write_output_row('ALERTA', 6) . SEPARATOR_TITLE;
	$row_title .= write_output_row('TOTAL', 5) . SEPARATOR_TITLE;
	$row_title .= write_output_row('FIXED', 5) . SEPARATOR_TITLE . PHP_EOL;

	echo $row_title;
	echo str_pad('', strlen($row_title) - 1, "-") . PHP_EOL;

	$row_value = "";
	foreach ($hours_data as $user_id => $user) {

		if($GLOBALS['CONFIG']['DEBUG_ONLY_CODE_USER'] > 0 && $GLOBALS['CONFIG']['DEBUG_ONLY_CODE_USER'] != $user_id) continue;

		foreach ($user['INTERVALS'] as $interval_id => $interval) {

			$row_value = SEPARATOR_VALUE;

			$row_value .= write_output_row((string) $user_id, 4) . SEPARATOR_VALUE;
			$row_value .= write_output_row($interval['input']['name'], 15) . SEPARATOR_VALUE;
			$row_value .= write_output_row((string) $interval_id, 11) . SEPARATOR_VALUE;
			$row_value .= write_output_row($interval['input']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']), 18) . SEPARATOR_VALUE;
			$row_value .= write_output_row($interval['output']['dateTime']->format($GLOBALS['CONFIG']['DATE_FORMAT_SHOW']), 18) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['diurnas_n']), 10) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['diurnas_e']), 13) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['nocturnas_n']), 10) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['nocturnas_e']), 13) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['h100']), 5) . SEPARATOR_VALUE;
			$row_value .= write_output_row((int) $interval['input']['is_feriado'] . ";" . (int) $interval['output']['is_feriado'], 7) . SEPARATOR_VALUE;
			$row_value .= write_output_row((returnStatusAlert($interval)) ? "X" : "", 6) . SEPARATOR_VALUE;
			$row_value .= write_output_row(convertMinutosToHoursAndMinutes($interval['RESULT']['total']), 5) . SEPARATOR_VALUE;
			$row_value .= write_output_row(($interval['input']['is_date_fixed']) ? "X" : "" , 5) . SEPARATOR_VALUE . PHP_EOL;

			echo $row_value;
		}
	}
}


function write_output_row($text = "", $pad_lenght = STR_PAD_LENGHT)
{
	if (IS_PAD) {
		return str_pad(substr($text, 0, $pad_lenght), $pad_lenght, " ", STR_PAD_BOTH);
	} else {
		return $text;
	}
}
