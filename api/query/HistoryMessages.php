<?php 
	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$database = 'auth_a_chat';

	$link = mysqli_connect($host, $user, $pass, $database);

	$query_toMSGs = "SELECT message, email_usr FROM messages";
	$result_toMSGs = mysqli_query($link, $query_toMSGs);

	if ($result_toMSGs) {
		echo "<div id='chat' style='display: block;'>";
		while (list($message, $email_usr) = mysqli_fetch_row($result_toMSGs)) {
			echo "<ul>".$email_usr."</ul>
				  <ul>".$message."</ul>";
		}
		echo "</div>";
	}


?>