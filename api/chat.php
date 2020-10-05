<?php 
	header("Access-Control-Allow-Origin: http://authentication-jwt/");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	include ('config/core.php');
    include ('libs/php-jwt-master/src/BeforeValidException.php');
    include ('libs/php-jwt-master/src/ExpiredException.php');
    include ('libs/php-jwt-master/src/SignatureInvalidException.php');
    include ('libs/php-jwt-master/src/JWT.php');
    use \Firebase\JWT\JWT; 
	

	include ('config/database.php');
	include ('objects/user.php');

	$database = new Database();
	$db = $database->getConnection();
	
	$user = new User($db);
	$data = json_decode(file_get_contents("php://input"));

	$user->email = $data->user;
	$user->message = $data->message;

	if ($user->sendMessage()) {

		echo json_encode(array(
			"message" => "Есть доступ.",
			"data" => $user
		));
	} else {
		http_response_code(401);
		echo json_encode(array("message" => "Ошибка отправки сообщения."));
	};
	
?>