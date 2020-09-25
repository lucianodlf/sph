<?php
$valid_extensions = ['xlsx'];

$path_save_file = '../import/';

if ($_FILES['file']['error'] === 0) {
    // echo json_encode($_FILES['file']);

    $file_name = $_FILES['file']['name'];
    $tmp_path = $_FILES['file']['tmp_name'];

    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $new_path = strtolower($path_save_file . date('Ymdhis') . '_' . $file_name);

    if (in_array($extension, $valid_extensions)) {

        if (move_uploaded_file($tmp_path, $new_path)) {

            echo true;

        } else {
            echo false;
        }
    }

    echo $new_path;
    exit();
}

echo -1;
exit();
