{* Страница заказа *}
{$meta_title = "Ваш заказ №`$order->id`" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">
			<a href="cart/" class="breadcrumbs__link">Корзина</a>
		</li>
		<li class="breadcrumbs__item">Детали заказа</li>
	</ul>

	<h1>Заказ №{$order->id}
		{if $order->status == 0}принят{/if}
		{if $order->status == 1}в обработке{elseif $order->status == 2}выполнен{/if}
		{if $order->paid == 1}, оплачен{else}{/if}
	</h1>

	<p class="date">{$order->date|date} в {$order->date|time}</p>
	<p><strong>Получатель:</strong> {$order->name|escape}</p>
	{if $order->phone}
		<p><strong>Телефон:</strong> {$order->phone|escape}</p>
	{/if}
	<p><strong>Электронная почта:</strong> {$order->email|escape}</p>
	<p><strong>Способ доставки:</strong> {$delivery->name}</p>
	{if $order->address }
		<p><strong>Адрес:</strong> {$order->address|escape}</p>
	{/if}

	{if $order->discount > 0 }
		<p><strong>Cкидка:</strong> {$order->discount}&nbsp;%</p>
	{/if}

	{if $order->coupon_discount > 0}
		<p><strong>Купон:</strong> {$order->coupon_discount|convert}&nbsp;{$currency->sign}</p>
	{/if}

	{if !$order->paid}
		{* Выбор способа оплаты *}
		{if $payment_methods && !$payment_method && $order->total_price>0}
			<form method="post" class="change-payment-type-form" >
				<div class="payment-type ">
					{foreach $payment_methods as $payment_method}
						<div class="form-check">
							<label class="form-check__label">
								<input type="radio" name="payment_method_id" class="form-check__input " value='{$payment_method->id}' {if $payment_method@first}checked{/if} id=payment_{$payment_method->id}>
								<span class="form-check__text">
								{$payment_method->name}, к оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
								</span>
							</label>
						</div>
						{if $payment_method->description}
							<div class="form-check">
								{$payment_method->description}
							</div>
						{/if}
					{/foreach}
				</div>
			</form>
			{* Выбраный способ оплаты *}
		{elseif $payment_method}
			<p>
				<strong>Способ оплаты:</strong>
				<span class="">{$payment_method->name}</span>&nbsp;&nbsp;
				<span class="">{$payment_method->description}</span>&nbsp;&nbsp;
				<form method=post>
					<button type="submit" name='reset_payment_method' class="link " tabindex="-1"><i class="fal fa-edit"></i> Изменить способ оплаты</button>
				</form>
			</p>
		{/if}
	{/if}

	{if $order->comment}
		<p><strong>Комментарий:</strong> {$order->comment|escape|nl2br}</p>
	{/if}

	{include file="partials/order_table.tpl"}

	<div class="basket-footer">
		<div class="basket-footer__total">
			{if $payment_method}
				<div class="basket-footer__total-main">
					Итого к оплате: {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
				</div>
			{else}
				<div class="basket-footer__total-main">Итого к оплате: {$order->total_price|convert}&nbsp;{$currency->sign}</div>
			{/if}
		</div>
	 {if $order->paid == 0}
		<div class="basket-footer__buttons">
			<div class="basket-footer__buttons-item">
				{*  Форма оплаты, генерируется модулем оплаты*}
				{checkout_form order_id=$order->id module=$payment_method->module}
			</div>
		</div>
		        {/if}

	</div>
</div>
