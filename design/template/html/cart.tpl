{* Шаблон корзины *}

{$meta_title = "Корзина" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">Корзина</li>
	</ul>

	<h1>Корзина</h1>

	{if $cart->purchases}
		<form class="js-validation-cart-form"  method="post" name="cart">
			{*	Товары	*}
			{include file="partials/cart_product_table.tpl"}

			<div class="basket-footer">
				<div class="basket-footer__total">
					<div class="basket-footer__total-main">Итого к оплате: {if $cart->total_price_without_delivery}{$cart->total_price_without_delivery}{else}{$cart->total_price}{/if}&nbsp;{$currency->sign}</div>
					<div class="basket-footer__total-info">Цена без учета доставки</div>
				</div>

				<div class="basket-footer__buttons">
					{*	Купоны	*}
					{if $coupon_request}
						<div class="basket-footer__buttons-item">
							<div class="basket-footer__cupon">
								{if $coupon_error}
									{if $coupon_error == 'invalid'}
										<div class="alert alert-danger" role="alert">
											Купон недействителен
										</div>
									{/if}
								{/if}

								{if $cart->coupon->min_order_price > 0 &&  $cart->total_price|floatval < $cart->coupon->min_order_price|floatval   }
									<div class="alert alert-danger" role="alert">
										Купон {$cart->coupon->code|escape} действует для заказов от {$cart->coupon->min_order_price|convert} {$currency->sign}
									</div>
								{/if}

								{if $cart->coupon->min_order_price  &&   $cart->total_price|floatval >= $cart->coupon->min_order_price|floatval }
									<div class="alert alert-success" role="alert">
									<span style="font-size: 12px">

										Купон принят, ваша скидка&nbsp;{$cart->coupon_discount|convert}&nbsp;{$currency->sign}
									</span>
									</div>
								{/if}

								<div class="form-row">
									<div class="col flex-grow-1">
										<input type="text" class=" form-control" placeholder="Код купона" name="coupon_code" value="{$cart->coupon->code|escape}">
									</div>
									<div class="col-auto">
										<button type="submit" class="search__btn btn btn-info" onclick="document.cart.submit();"><i class="fas fa-chevron-right"></i></button>
									</div>
								</div>
							</div>
						</div>
					{/if}

					<div class="basket-footer__buttons-item">
						<a type="submit"  href="cart/remove/all"   class="basket-footer__button btn btn-light">Очистить корзину</a>
					</div>

				</div>
			</div>

		<section class="order" id="order">
				<h2 class="order__title">Оформление заказа</h2>

				<form method="post" class="order__form js-validation-cart-form">
					<div class="order__content row js-cart-info-area">
						<section class="order__step col-md">
							<h3 class="order__step-title">Данные покупателя</h3>

							<div class="form-group">
								<label for="orderName">Ваше Ф.И.О. <span class="color-red">*</span></label>
								<input type="text" class="form-control" id="orderName" name="name" value="{$name|escape}" required>
							</div>
							<div class="form-group">
								<label for="orderEmail">E-mail <span class="color-red">*</span></label>
								<input type="email" class="form-control" id="orderEmail" name="email" value="{$email|escape}" required>
							</div>
							<div class="form-group">
								<label for="orderPhone">Телефон <span class="color-red">*</span></label>
								<input type="text" class="form-control" id="orderPhone" name="phone"  value="{$phone|escape}" required>
							</div>

							<div class="form-group">
								<label for="address">Адрес <span class="color-red">*</span></label>
								<input name="address" value="{$address|escape}" type="text" class="form-control" required>
							</div>

							<div class="form-group">
								<label for="order_comment">Комментарий к&nbsp;заказу</label>
								<textarea class="form-control" name="comment" id="order_comment">{$comment|escape}</textarea>
							</div>
							<p><span class="color-red">*</span> — поля, обязательно для заполнения</p>
						</section>

						<section class="order__step col-md">
							<h3 class="order__step-title">Доставка</h3>

							<div class="order__delivery-type">
								<div class="order__delivery-type-title">Выберите способ доставки</div>
								{if $deliveries}
									<div class="order__delivery-type-section is-open">

										{foreach  $deliveries as $delivery}
											<div class="form-check">
												<label class="form-check__label">

													<input class="order__delivery-type-input form-check__input "
														   type="radio"
														   name="delivery_id"
														   value="{$delivery->id}"
														   id="deliveries_{$delivery->id}"
														   {if $current_delivery_id == $delivery->id}checked{/if}
													>

													<span class="form-check__text">
													{$delivery->name}
														{if $cart->total_price < $delivery->free_from && $delivery->price>0}
															({$delivery->price}&nbsp;{$currency->sign})
														{elseif $cart->total_price >= $delivery->free_from}
															(бесплатно)
														{/if}
												</span>
													<span class="form-check__subtext">{$delivery->description}</span>
												</label>
											</div>
										{/foreach}

									</div>
								{/if}
							</div>
						</section>

						<section class="order__step col-md">
							<h3 class="order__step-title">Подтверждение заказа</h3>

							<div class="form-group">
								<label for="orderPaymentType">Выберите способ оплаты</label>
								{foreach $payment_methods as $payment_method}
										<div class="form-check">
											<label class="form-check__label">
												<input class="order__delivery-type-input form-check__input "
													   type="radio"
													   name="payment_method_id"
													   value="{$payment_method->id}"
														{if $payment_method_id==$payment_method->id} checked{elseif $payment_method@first} checked {/if}
												>
												<span class="form-check__text">{$payment_method->name|escape}</span>
											</label>
										</div>
									{/foreach}
							</div>

							<div class="order__total">
								<div class="order__total-title">Общая сумма заказа:&nbsp;</div>
								<div class="order__total-value">{$cart->total_price}&nbsp;{$currency->sign}</div>
							</div>

							<div class="form-check">
								<label class="form-check__label">
									<input type="checkbox" class="form-check__input" name="orderAgreement" data-msg-required="Обязательный пункт" required>
									<span class="form-check__text">Я соглашаюсь на обработку моих персональных данных и принимаю <a href="/politika-konfidentsialnosti">Политику конфиденциальности</a></span>
								</label>
							</div>

							<div class="order__btn-row">
								<button name="checkout" type="submit" class="order__btn btn btn-info btn-lg btn-block">Подтвердить и заказать</button>
							</div>
						</section>
					</div>
				</form>
			</section>


					</div>

			</section>
		</form>
	{else}
		<p>В корзине нет товаров</p>
	{/if}
</div>
