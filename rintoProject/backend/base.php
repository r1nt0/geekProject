<?php
require './connection.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Retrieve Authorization header
$token = "";
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $token = $headers['Authorization'];
}elseif(isset($headers['authorization'])){
    $token = $headers['authorization'];
}

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with 200 OK
    header("HTTP/1.1 200 OK");
    exit();
}

// Parse request URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (substr($uri, -1) == "/") {
    $uri = substr($uri, 0, -1);
}
$uri = explode('/', $uri);
$requestMethod = $_SERVER["REQUEST_METHOD"];
if (count($uri) == 3) {
    $id = $uri[2];
    $data = $uri[1];
} elseif (count($uri) == 2) {
    $id = '';
    $data = $uri[1];
} else {
    $id = '';
    $data = '';
}

// Function to check if request method matches the expected method
function checkMethod($requestMethod, $required)
{
    if ($requestMethod == $required) {
        return true;
    } else {
        // Handle error properly
        error(405);
    }
}


function error($val)
{
    switch ($val) {
        case 200:
            header("HTTP/1.1 200 OK");
            $msg['response'] = 'Success';
            echo json_encode($msg);
            break;
        case 401:
            header("HTTP/1.1 401 Wrong Credentials");
            $msg['response'] = 'Wrong Credentials';
            echo json_encode($msg);
            break;
        case 403:
            header("HTTP/1.1 403 Forbidden");
            $msg['response'] = 'Forbidden';
            echo json_encode($msg);
            break;
        case 404:
            header("HTTP/1.1 404 Not Found");
            $msg['response'] = 'No Such URL Found';
            echo json_encode($msg);
            break;
        case 405:
            header("HTTP/1.1 405 METHOD NOT ALLOWED");
            $msg['response'] = 'Method not allowed';
            echo json_encode($msg);
            break;
        case 451:
            header("HTTP/1.1 401 Unavailable For Legal Reasons");
            $msg['response'] = 'Blocked! Please contact Admin';
            echo json_encode($msg);
            break;
        default:
            header("HTTP/1.1 406 Undefined Error");
            $msg['response'] = 'unidentified error';
            echo json_encode($msg);
            break;
    }
    exit();
}
function jwtSecret()
{
    $secret = bin2hex(random_bytes(32));
    return $secret;
}
function jwtMaker($data, $secret)
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($data);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    return $jwt;
}
function jwtVerify($jwt, $secret)
{
    $json = base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $jwt)[1])));
    $tokenParts = explode('.', $jwt);
    $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $secret, true);
    $base64_url_signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $expiration = json_decode(base64_decode($tokenParts[1]))->exp;
    $is_signature_valid = ($base64_url_signature === $tokenParts[2]);
    $is_token_expired = ($expiration - time()) < 0;
    if ($is_signature_valid && !$is_token_expired) {
        return True;
    } else {
        return False;
    }
}
function jwtDecode($jwt)
{
    $json = base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $jwt)[1])));
    return $json;
}
?>
