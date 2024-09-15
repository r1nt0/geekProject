<?php
$data = "";
//require './videocontroller.php';
require './base.php';
require './controller.php';

$db_handle = new DBController();



switch ($data) {
    //default path url
    case '':
        if (checkMethod($requestMethod, "GET")) {
            echo json_encode('{}');
        }
        break;

    case 'signup':
        if (checkMethod($requestMethod, "POST")){
            $json = file_get_contents('php://input');
            $msg = signUpUser($json, $db_handle);
            echo json_encode($msg);
        }

    break;

    case 'login':
        if (checkMethod($requestMethod, "POST")){
            $json = file_get_contents('php://input');
            $msg = loginUser($json, $db_handle);
            echo json_encode($msg);
        }

    break;
        default:
        $msg = array(
            "status" => "error",
            "error" => true,
            "message" => "No Such Url Found"
        );
        echo json_encode($msg);
        break;
}
?>