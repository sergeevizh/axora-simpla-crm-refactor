<div id="password-change-window" class="popup-window">
	<h2 class="popup-window__title">Изменить пароль</h2>

	<form class="js-validation-form-user-password-edit" method="post">
		<input name="user_id" type="hidden">

		<div class="form-group">
			<label for="accountPassword">Новый пароль</label>
			<input type="password" class="form-control" id="accountPassword" name="password" required>
		</div>

		<div class="form-group">
			<label for="accountRePassword">Повторить новый пароль</label>
			<input type="password" class="form-control" id="accountRePassword" name="accountRePassword" equalTo="#accountPassword" required>
		</div>

		<br>

		<button type="submit" class="btn btn-info btn-lg">Сохранить</button>
	</form>
</div>
