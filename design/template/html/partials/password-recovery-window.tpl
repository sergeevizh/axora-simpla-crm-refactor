<div id="password-recovery-window" class="popup-window">
	<h2 class="popup-window__title">Восстановление пароля</h2>

	<div id="recovery-error" style="text-align: center; color: red;" class="form-group"></div>
	<div id="recovery-success" style="text-align: center; color: limegreen;" class="form-group"></div>

	<form class="js-validation-recovery-form" method="post">
		<div class="form-group">
			<label for="recoveryEmail">E-mail</label>
			<input type="email" class="form-control" id="recoveryEmail" name="email" required>
		</div>
		<br>

		<div class="form-row">
			<div class="form-group col-sm-6">
				<button type="submit" class="btn btn-block btn-info">Выслать</button>
			</div>
			<div class="form-group col-sm-6">
				<button type="button" class="btn btn-block btn-outline-secondary js-popup-open-login" data-type="inline">Войти</button>
			</div>
		</div>
	</form>
</div>
