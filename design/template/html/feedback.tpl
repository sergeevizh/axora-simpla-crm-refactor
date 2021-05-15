{* Страница с формой обратной связи *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">{$page->name}</li>
	</ul>

	<h1>{$page->name|escape}</h1>

	{$page->body}

	<div style="max-width: 600px;">
		{if $message_sent}
			<div class="alert alert-success" role="alert">
				{$name|escape}, ваше сообщение отправлено.
			</div>
		{else}
		<form class="js-feedback_form" method="post">
			{if $error}
				<div class="alert alert-danger" role="alert">
					{if $error=='captcha'}
						Неверно введена капча
					{elseif $error=='empty_name'}
						Введите имя
					{elseif $error=='empty_email'}
						Введите email
					{elseif $error=='empty_text'}
						Введите сообщение
					{/if}
				</div>
			{/if}

			<div class="form-row">
				<div class="col-sm-6 form-group">
					<label for="name">Имя</label>
					<input class="form-control" id="name" data-format=".+" data-notice="Введите имя" value="{$name|escape}" name="name" maxlength="255" type="text" required/>
				</div>

				<div class="col-sm-6 form-group">
					<label for="email">Email</label>
					<input class="form-control" id="email" data-format="email" data-notice="Введите email" value="{$email|escape}" name="email" maxlength="255" type="text" required/>
				</div>
			</div>

			<div class="form-group">
				<label for="msg">Сообщение</label>
				<textarea class="form-control" rows="7" id="msg" data-format=".+" data-notice="Введите сообщение"  name="message" required>{$message|escape}</textarea>
			</div>

			<div class="form-group">
				<label for="comment_captcha">Введите капчу</label>
				<div class="form-row">
					<div class="col flex-grow-0 captcha">
						<img src="captcha/image.php?{math equation='rand(10,10000)'}"/>
					</div>
					<div class="col">
						<input class="form-control" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу" required/>
					</div>
				</div>
			</div>

			<div class="form-group text-center">
				<input class="basket-footer__button btn btn-info" type="submit" name="feedback" value="Отправить" />
			</div>
		</form>
		{/if}
	</div>
</div>