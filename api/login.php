<?php
// заголовки 
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include ('config/database.php');
include ('objects/user.php');
 
$database = new Database();
$db = $database->getConnection();
 
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$user_exist = $user->userExist();
 
include ('config/core.php');
include ('libs/php-jwt-master/src/BeforeValidException.php');
include ('libs/php-jwt-master/src/ExpiredException.php');
include ('libs/php-jwt-master/src/SignatureInvalidException.php');
include ('libs/php-jwt-master/src/JWT.php');
use \Firebase\JWT\JWT;

if ($user_exist) {
 
	$token = array(
		"iss" => $iss,
		"aud" => $aud,
		"iat" => $iat,
		"nbf" => $nbf,
		"data" => array(
			"id" => $user->id,
			"name" => $user->name,
			"email" => $user->email
		)
	);
 
	http_response_code(200);
 
	$jwt = JWT::encode($token, $key);
	echo json_encode(
		array(
			"message" => "Успешный вход в систему.",
			"jwt" => $jwt
		)
	);
} else {
	http_response_code(401);
	echo json_encode(array("message" => "Ошибка входа."));
};
?>