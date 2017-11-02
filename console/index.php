<?php

	include $_SERVER[ 'DOCUMENT_ROOT' ] . '/config.php';
	require 'common.php';

	$Common = new Common();
	if ($Common->Is_User_Logged()) {
		header( "Location: ./platform" );
		die();
	}

	include $Common->Get_Language();

	$demo = (isset($_GET[ 'demo' ])) ? true : false;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login | Webair</title>
		<?php echo $Common->Get_Header(); ?>
	</head>
	<body ng-app="Webair" ng-cloak>
	<div class="container text-center">
		<div class="row">
			<div class="col-sm-6 col-md-3 pull-right">
				<md-button ng-href="/" class="md-raised md-warn">HOME</md-button>
			</div>
		</div>
		<div class="row hidden-xs">
			<div class="col-sm-12 col-md-6 col-md-offset-3">
				<img src="fire.png" style="max-height: 100px"/>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 col-md-6 col-md-offset-3">
				<p class="title"><?= $login_title ?></p>
			</div>
		</div>
		<form name="loginForm" id="loginForm" onsubmit="return checkForm(this);" method="post" action="#"
			  ng-controller="utenteCTRL">
			<div class="row col-sm-10 col-sm-offset-1 col-md-4 col-md-offset-4">
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-warning" style="display: none">
							I dati di login sono errati.
						</div>
						<div class="alert alert-warning json" style="display: none">
							La login ha qualche problema, riprova fra poco o contatta un amministratore.
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" ng-cloak>
						<md-input-container class="md-block">
							<label><?= $login_input_username ?></label>
							<input name="username" ng-model="user.username" md-no-asterisk required>

							<div ng-messages="loginForm.username.$error" ng-show="loginForm.username.$dirty">
								<div ng-message="required">La username &egrave; obbligatoria!</div>
							</div>
						</md-input-container>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" ng-cloak>
						<md-input-container class="md-block">
							<label><?= $login_input_password ?></label>
							<input type="password" name="password" ng-model="user.password" md-no-asterisk required>

							<div ng-messages="loginForm.password.$error" ng-show="loginForm.password.$dirty">
								<div ng-message="required">La password &egrave; obbligatoria!</div>
							</div>
						</md-input-container>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-9 col-md-3 pull-left" ng-cloak>
						<md-progress-circular class="md-hue-2" md-diameter="30px" id="progress"
											  style="display: none"></md-progress-circular>
					</div>
					<div class="col-sm-3 col-md-3 pull-right" ng-cloak>
						<md-button type="submit" name="invia" id="inviaForm"
								   class="md-raised md-primary"><?= (!$demo) ? $login_button_accedi : "Procedi con la demo" ?>
						</md-button>
					</div>
				</div>
			</div>
		</form>

		<input type="hidden" name="language-value" value="<?= $Common->Is_Language_Set() ?>">

		<script>

			function checkForm(form) {
				$("#progress")[0].style.display = "block";
				document.getElementsByClassName("alert alert-warning")[0].style.display = "none";
				form.elements[2].setAttribute("disabled", "disabled");

				var username = form.elements[0].value;
				var password = form.elements[1].value;
				sendRequest(username, password);

				return false;
			}

			function sendRequest(USER, PASS) {

				$.ajax({
					type: "POST",
					url: "<?=$api_path?>/LoginAPI.php",
					data: {username: USER, password: PASS, platform: "WEB"},
					dataType: 'text',

					success: function (response) {

						//alert(response);
						var json;
						try {
							json = JSON.parse(response);
						}
						catch (e) {
							$("#progress")[0].style.display = "none";
							document.getElementsByClassName("alert alert-warning json")[0].style.display = "block";
							$("#inviaForm")[0].removeAttribute("disabled");
							return;
						}
						//alert(json.resultCode);
						if (json.resultCode == 'OK') {

							$.ajax({
								type: "POST",
								url: "session.php",
								data: {json: response, type: "login"},
								dataType: 'text',

								success: function (response2) {
									//alert(response2);
									window.location.replace("./platform");
								}
							});


							return;
						}

						$("#progress")[0].style.display = "none";
						document.getElementsByClassName("alert alert-warning")[0].style.display = "block";
						$("#inviaForm")[0].removeAttribute("disabled");

					}

				});
			}

			if (document.getElementsByName("language-value")[0].value == "not_set") {
				$.ajax({
					type: "POST",
					url: "<?=$api_path?>/GetConfiguration.php",
					data: {},
					dataType: 'text',

					success: function (response) {

						//alert(response);
						var json;
						try {
							json = JSON.parse(response);
						}
						catch (e) {
							alert("Malformed login json");
							return;
						}
						if (json.resultCode == 'OK') {
							for (var i = 0; i < json.resultObj.length; i++) {
								if (json.resultObj[i].parameterName == "LANGUAGE") {
									$.ajax({
										type: "POST",
										url: "cookie.php",
										data: {action: "create_language", value: json.resultObj[i].parameterValue},
										dataType: 'text',

										success: function (response) {

										}
									});
								}
							}
						}
					}
				});
			}

			$(document).keypress(function (e) {
				if (e.which == 13) {
					$("#inviaForm")[0].click();
				}
			});
		</script>

		<?php echo $Common->getFooterScript(); ?>

		<script type="text/javascript">
			angular
				.module("Webair", ["ngMaterial", "ngMessages"])
				.controller('utenteCTRL', function ($scope) {
					$scope.user = {
						<?= ($demo) ? "username: 'demo',password: 'demo'" : "" ?>
					}
				});
		</script>

	</body>
</html>
