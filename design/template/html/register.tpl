{* Страница регистрации *}

{* Канонический адрес страницы *}
{$canonical="/user/register" scope=root}

{$meta_title = "Регистрация" scope=root}
<div class="container">
	<h1>Регистрация</h1>

	{if $error}
	<div class="message_error">
		{if $error == 'empty_name'}Введите имя
		{elseif $error == 'empty_email'}Введите email
		{elseif $error == 'empty_password'}Введите пароль
		{elseif $error == 'user_exists'}Пользователь с таким email уже зарегистрирован
		{elseif $error == 'captcha'}Неверно введена капча
		{else}{$error}{/if}
	</div>
	{/if}

	<form class="form register_form" method="post">
		<label for="name">Имя</label>
		<input id="name" type="text" name="name" data-format=".+" data-notice="Введите имя" value="{$name|escape}" maxlength="255" />

		<label for="email">Email</label>
		<input id="email" type="text" name="email" data-format="email" data-notice="Введите email" value="{$email|escape}" maxlength="255" />

	    <label for="password">Пароль</label>
	    <input id="password" type="password" name="password" data-format=".+" data-notice="Введите пароль" value="" />

		<div class="captcha"><img src="captcha/image.php?{math equation='rand(10,10000)'}"/></div>
		<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу"/>

		<input type="submit" class="button" name="register" value="Зарегистрироваться">
	</form>
</div>