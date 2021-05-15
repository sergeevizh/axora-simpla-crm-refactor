{* Шаблон страницы зарегистрированного пользователя *}
<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">Личный кабинет</li>
	</ul>

	<h1>Личный кабинет </h1>

	<div class="account">
		<h3 class="account__title">Личные данные</h3>
		<input type="hidden" name="id" value="{$user->id}">
		<p><span id="current_user_name">{$user->name|escape}</span></p>
		<p><strong>Телефон:</strong> <span
					id="current_user_phone">{if $user->phone}{$user->phone|escape}{else}не указан{/if} </span></p>
		<p><strong>Электронная почта:</strong> <span id="current_user_email">{$user->email|escape}</span></p>
		<p><strong>Адрес:</strong> <span id="current_user_address">{if $user->address}{$user->address|escape} {else}не указан {/if}
		</p>

		<div class="form-row">
			<div class="col-auto">
				<p>
					<a href="{$config->root_url}/user/edit" class="btn btn-info">
						Редактировать данные
					</a>
				</p>
				{include file='partials/account-data-window.tpl'}
			</div>
			<div class="col-auto">
				<p>
					<button type="button" class="btn btn-info js-popup-open-psw-change" data-type="ajax"
							data-src="#password-change-window">Изменить пароль
					</button>
				</p>
				{include file='partials/password-change-window.tpl'}
			</div>
			<div class="col-auto">
				<p><a href="user/logout" class="btn btn-outline-secondary">Выйти</a></p>
			</div>
		</div>
	</div>

	<div class="history">
		<h3 class="history__title">История заказов</h3>

		<div class="history__list">
			{if $orders}
				{foreach $orders as $order}
					<div class="history__item">
						<header class="history__item-header">
							<div class="history__item-info history__item-id">#{$order->id}</div>
							<div class="history__item-info history__item-date">{$order->date|date_format:"%e.%m.%Y %k:%M"}
							</div>
							<div class="history__item-info history__item-status">
								{if $order->paid == 1}оплачен,{/if}
								{if $order->status == 0}ждет обработки
								{elseif $order->status == 1}в обработке
								{elseif $order->status == 2}выполнен
								{elseif $order->status == 3}отменен{/if}
							</div>
							<div class="history__item-info  history__item-price">{$order->delivery->name}</div>
							<div class="history__item-info  history__item-price">{$order->payment_method->name}</div>
							<div class="history__item-info history__item-price">{$order->total_price} {$currency->sign}</div>
						</header>
						<div class="history__item-content">
							{include file="partials/user_orders_table.tpl"}
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
	</div>
</div>
