<?php
$valid_extensions = ['xlsx'];

$path_save_file = '../import/';

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

            //TODO: Verificar funcioamiento en plataforma windows
            $output = shell_exec('cd .. && php sph.php');

            $response = [
                'status' => 1,
                'msg' => $output
            ];

        } else {

            $response = [
                'status' => 2,
                'msg' => 'Error: ocurrio un error al mover el archivo (move_uploaded_file...)'
            ];
        }

    }else{
        
        $response = [
            'status' => 3,
            'msg' => 'Extencion de archivo invalida (solo se permiten archivos .xlsx)'
        ];

    }

}

echo json_encode($response);
exit();
