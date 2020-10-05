<!doctype html>
<html lang="ru">
<head>
		<meta charset="utf-8">
		<title> Проект для ООО "Юрист" </title>

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">
 
</head>
<body>
 
	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link" href="#" id='update_account'>Учётка</a>
				<a class="nav-item nav-link" href="#" id='home'>Чат</a>
				<a class="nav-item nav-link" href="#" id='logout'>Выход</a>
				<a class="nav-item nav-link" href="#" id='login'>Вход</a>
				<a class="nav-item nav-link" href="#" id='sign_up'>Регистрация</a>
			</div>
		</div>

	</nav>
	
	<main role="main" class="container starter-template">
	 
		<div class="row">
			<div class="col">

				<div id="response"></div>

				<?php include('api/query/HistoryMessages.php') ?>

				<div id="content"></div>
			</div>
		</div>

	</main>
 
	<script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

	<script>
	jQuery(function($) {

		$(document).on('click', '#sign_up', function(){
			let html = `
				<h2>Регистрация</h2>
				<form id='sign_up_form'>
					<div class="form-group">
						<label for="name">Имя</label>
						<input type="text" class="form-control" name="name" id="name" required>
					</div>

					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" name="email" id="email" required>
					</div>

					<button type='submit' class='btn btn-primary'>Зарегистрироваться</button>
				</form>
			`;

			clearResponse();
			hideChat();
			$('#content').html(html);
		});

		$(document).on('submit', '#sign_up_form', function(){

			let sign_up_form=$(this);
			let form_data=JSON.stringify(sign_up_form.serializeObject());

			$.ajax({
				url: "api/create_user.php",
				type: "POST",
				contentType: 'application/json',
				data: form_data,
				success: function(result) {
					$('#response').html("<div class='alert alert-success'>Регистрация завершена успешно. Пожалуйста, войдите.</div>");
					sign_up_form.find('input').val('');
		        },
				error: function(xhr, resp, text){
					$('#response').html("<div class='alert alert-danger'>Невозможно зарегистрироваться. Пожалуйста, свяжитесь с администратором.</div>");
				}
			});

			return false;
		}); 

		$(document).on('click', '#login', function(){
		    showLoginPage();
		    hideChat();
		});

		$(document).on('submit', '#login_form', function(){

			let login_form = $(this);
			let form_data = JSON.stringify(login_form.serializeObject());

			$.ajax({
				url: "api/login.php",
				type : "POST",
				contentType : 'application/json',
				data : form_data,
				success : function(result){

					setCookie("jwt", result.jwt, 1);

					showHomePage();
					$('#response').html("<div class='alert alert-success'>Успешный вход в систему.</div>");

				},
				error: function(xhr, resp, text){
					// при ошибке сообщим пользователю, что вход в систему не выполнен и очистим поля ввода 
					$('#response').html("<div class='alert alert-danger'>Ошибка входа. Email указан неверно.</div>");
					login_form.find('input').val('');
				}
			});

			return false;
		});

		// показать домашнюю страницу 
		$(document).on('click', '#home', function(){
			showHomePage();
			clearResponse();
		});

		$(document).on('click', '#update_account', function(){
			showUpdateAccountForm();
			hideChat();
		});

		$(document).on('submit', '#update_account_form', function(){

			let update_account_form=$(this);

			let jwt = getCookie('jwt');

			let update_account_form_obj = update_account_form.serializeObject();

			update_account_form_obj.jwt = jwt;

			let form_data=JSON.stringify(update_account_form_obj);

			$.ajax({
				url: "api/update_user.php",
				type : "POST",
				contentType : 'application/json',
				data : form_data,
				success : function(result) {

					$('#response').html("<div class='alert alert-success'>Учетная запись обновлена.</div>");

					setCookie("jwt", result.jwt, 1);
					// console.log(result);
				},

				error: function(xhr, resp, text){
					if(xhr.responseJSON.message=="Невозможно обновить пользователя."){
						$('#response').html("<div class='alert alert-danger'>Невозможно обновить пользователя.</div>");
					}

					else if(xhr.responseJSON.message=="Доступ закрыт."){
						showLoginPage();
						$('#response').html("<div class='alert alert-success'>Доступ закрыт. Пожалуйста войдите</div>");
					}
				}
			});

			return false;
			hideChat();
		});

		// выйти из системы 
		$(document).on('click', '#logout', function(){
			showLoginPage();
			hideChat();

			$('#response').html("<div class='alert alert-info'>Вы вышли из системы.</div>");
		});

		function clearResponse(){
			$('#response').html('');
		}

		function showLoginPage(){

			setCookie("jwt", "", 1);

			let html = `
				<h2>Вход</h2>
				<form id='login_form'>
					<div class='form-group'>
						<label for='email'>Email адрес</label>
						<input type='email' class='form-control' id='email' name='email' placeholder='Введите email'>
					</div>

					<button type='submit' class='btn btn-primary'>Войти</button>
				</form>
			`;

			$('#content').html(html);
			clearResponse();
			showLoggedOutMenu();
		}

		function setCookie(cname, cvalue, exdays) {
			let d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			let expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function showLoggedOutMenu(){
			$("#login, #sign_up").show();
			$("#logout").hide(); 
		}

		function hideChat() {
			//if ($("#chat").parent() !== "showHomePage()") {
				$("#chat").css("display","none");
			//}
		}

		function showHomePage(){
			let jwt = getCookie('jwt');

			$.post("api/validate_token.php", JSON.stringify({ jwt:jwt })).done(function(result) {
				
				let html = `
					<div class="card">
						<h5 class="card-header">Добро пожаловать в чат!</h5>
						<div class="card-body">
							<form id="home_form">
								<input class="card-text" size="30" name="user" id="user" value="`+
									result.data.email
								+`">
								<ul name="message" id="message"></ul>
								<textarea name="message" id="message" rows="5" class="form-control" required></textarea>
								<br>
								<input type="submit" class='btn btn-primary'>
							</form>
						</div>
					</div>
				`;

				// console.log(result);

				$('#content').html(html);
				showLoggedInMenu();
				$("#chat").css("display","block");
				
				// $(document).ready(function(){
				// 	setInterval(location.reload(), 10000);
				// })
				
			})

			.fail(function(result){
				showLoginPage();
				$('#response').html("<div class='alert alert-danger'>Пожалуйста войдите, чтобы получить доступ к домашней станице</div>");
			});
		}

		$(document).on('submit', '#home_form', function(){
			let home_form=$(this);
			let form_data=JSON.stringify(home_form.serializeObject());

			$.ajax({
				url: "api/chat.php",
				type: "POST",
				contentType: 'application/json',
				data: form_data,
				success: function(result) {
					$('#response').html("<div class='alert alert-success'>Отправлено сообшение</div>");
					//home_form.find('input').val('');
					$('textarea').before('<ul>'+result.data.message+'</ul>');
					
					console.log(result);
		        },
				error: function(xhr, resp, text){
					$('#response').html("<div class='alert alert-danger'>Не отправлено сообщение</div>");
				}
			});
			// alert(form_data);
			return false;
		});
		 
		// Функция поможет нам прочитать JWT, который мы сохранили ранее. 
		function getCookie(cname) {
			let name = cname + "=";
			let decodedCookie = decodeURIComponent(document.cookie);
			let ca = decodedCookie.split(';');
			for(let i = 0; i <ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' '){
					c = c.substring(1);
				}

				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}

		function showLoggedInMenu(){
			$("#login, #sign_up").hide();
			$("#logout").show();
		}

		function showUpdateAccountForm(){
		  let jwt = getCookie('jwt');
		  $.post("api/validate_token.php", JSON.stringify({ jwt:jwt })).done(function(result) {

			let html = `
				<h2>Обновление аккаунта</h2>
				<form id='update_account_form'>
					<div class="form-group">
						<label for="firstname">Имя</label>
		    			<input type="text" class="form-control" name="name" id="name" required value="` + result.data.name + `">
						</div>

						<div class="form-group">
							<label for="email">Email</label>
							<input type="email" class="form-control" name="email" id="email" required value="` + result.data.email + `" />
		                </div>

						<button type='submit' class='btn btn-primary'>
							Сохранить
						</button>
				</form>
			`;

			clearResponse();
			$("#chat").css("display","none");
			$('#content').html(html);
		  }) 
		  .fail(function(result){
			showLoginPage();
			$('#response').html("<div class='alert alert-danger'>Пожалуйста, войдите, чтобы получить доступ к странице учетной записи.</div>");
		  });
		}

		// функция для преобразования значений формы в формат JSON 
		$.fn.serializeObject = function(){

			let o = {};
			let a = this.serializeArray();
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};
	});
	</script>

</body>
</html>