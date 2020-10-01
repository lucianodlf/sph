<?php
$valid_extensions = ['xlsx'];

$path_save_file = 'import/';

$response = [
    'status' => 0,
    'msg' => 'No se cargo ningun archivo'
];

if ($_FILES['file']['error'] === 0) {
    // echo json_encode($_FILES['file']);

    $file_name = $_FILES['file']['name'];
    $tmp_path = $_FILES['file']['tmp_name'];

    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $new_path = strtolower($path_save_file . date('Ymdhis') . '_' . $file_name);

    if (in_array($extension, $valid_extensions)) {

        if (move_uploaded_file($tmp_path, $new_path)) {

            if (strtoupper(substr(PHP_OS, null, 3)) === 'WIN') {

                //	var_dump(dirname($_SERVER['DOCUMENT_ROOT']) . '\php\php.exe');	
                $output = shell_exec(dirname($_SERVER['DOCUMENT_ROOT']) . "\php\php.exe sph.php --apiweb");
            } else {

                // Asumimos que es linux
                // Por ahora realizado para poder desarrollar en entorno linux y desplegar en windows.
                $output = shell_exec("php sph.php --apiweb");
            }


            $decoded_output = (array) json_decode($output);

            if ($decoded_output['status'] == 1) {

                $response = [
                    'status' => 1,
                    'msg' => $decoded_output['path']
                ];
            } else {

                $response = [
                    'status' => 4,
                    'msg' => 'Error en ejecucion de sph.php. Revisar log'
                ];
            }
        } else {

            $response = [
                'status' => 2,
                'msg' => 'Error: ocurrio un error al mover el archivo (move_uploaded_file...)'
            ];
        }
    } else {

        $response = [
            'status' => 3,
            'msg' => 'Extencion de archivo invalida (solo se permiten archivos .xlsx)'
        ];
    }
}

echo json_encode($response);
exit();
