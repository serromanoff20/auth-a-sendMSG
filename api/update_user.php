<?php
	header("Access-Control-Allow-Origin: *");
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
	 
	$jwt = isset ($data->jwt) ?($data->jwt) : "";
	 
	if($jwt) {
	 
		try {
	 
			$decoded = JWT::decode($jwt, $key, array('HS256'));
			$user->name = $data->name;
			$user->email = $data->email;
			$user->id = $decoded->data->id;
	        
			if($user->update()) {
				// нам нужно заново сгенерировать JWT, потому что данные пользователя могут отличаться 
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
				$jwt = JWT::encode($token, $key);
				 
				http_response_code(200);
				 
				echo json_encode(
					array(
						"message" => "Пользователь был обновлён",
						"jwt" => $jwt
					)
				);
			}
	        
			else {
				http_response_code(401);
				echo json_encode(array("message" => "Невозможно обновить пользователя."));
			}
		}
	 
		// если декодирование не удалось, это означает, что JWT является недействительным 
		catch (Exception $e){
	    	http_response_code(401);
	    
			echo json_encode(array(
				"message" => "Доступ закрыт",
				"error" => $e->getMessage()
			));
		}
	}

	// показать сообщение об ошибке, если jwt пуст 
	else {
		http_response_code(401);
		echo json_encode(array("message" => "Доступ закрыт."));
	}
?>