$(document).ready(function() {

	/**
	 * Rating
	 */
	$('.rating__star').on('click', function(e){
		e.preventDefault();

		var self = $(this),
			product_id = self.closest('.rating').find('[name=productRating]').data('product-id'),
			user_id = self.closest('.rating').find('[name=productRating]').data('user-id'),
			rating = self.data('value') ;

		$.ajax({
			url: "ajax/product/add-rating",
			method: 'post',
			data: {
				product_id: product_id,
				user_id: user_id,
				rating: rating
			},
			dataType: 'json',
			success: function(data){

				//если пользователь не зарегистрирован
				if (data.msg.status == 3) {
					self.closest('.rating').closest('.rating-area').find('.js-rating-msg').text(data.msg.text).show(0).delay(2000).hide(0);
				} else {
					if(!self.hasClass('is-active')){
						self.closest('.rating').find('.rating__star.is-active').removeClass('is-active');
						// self.addClass('is-active');
						self.closest('.rating').closest('.rating-area').find('.js-rating-msg').text(data.msg.text).show(0).delay(2000).hide(0);
						self.closest('.rating').find('[data-value='+data.avg_rating+']').addClass('is-active');
					}
				}
			},
			error: function (request, status, error) {
				console.log(error);
			}
		});
	});

	/**
	 * Add Basket
	 */
	$('.js-submit-to-cart').on('submit', function (e) {
		e.preventDefault();

		$(this).find(':submit').addClass('is-active');

		var variant, amount = 1, form = $(this);

		if(form.find('.js-add-to-cart-in-product-page').length > 0){
			form.find('.js-add-to-cart-in-product-page').text('В корзине');
		}

		if(form.find('input[name=variant]:checked').length>0) {
			variant = form.find('input[name=variant]:checked').val();
		} else if(form.find('select[name=variant]').length>0) {
			variant = form.find('select[name=variant]').val();
		} else if(form.find('input[name=variant]:hidden').length>0) {
			variant = form.find('input[name=variant]:hidden').val();
		}
		if(form.find('input[name=amount]').length>0) {
			amount = form.find('input[name=amount]').val();
		} else {
			amount =  $('input[name=amount]').val();
		}

		$.ajax({
			url: "ajax/product/cart",
			data: {variant: variant, amount: amount},
			dataType: 'json',
			success: function(data){
				$('.js-cart-informer').html(data);

				//добавляем айдишник добавленного варианта, используется для вывода разных  кнопок добавления в корзину
				var cart_product_ids_el = $('[name=cart-product-ids]');

				if( cart_product_ids_el.length > 0) {
					var  arrIds;

					arrIds = cart_product_ids_el.val().split(',');
					arrIds.push(variant);
					cart_product_ids_el.val(arrIds.join(','));

				}

			},
			error: function (request, status, error) {
				console.log(error);
			}
		});


		var $this = $(this),
			$basketBtn = $('.control__btn_basket'),
			$img = $this.closest('.item').length ? $this.closest('.item').find('.item__image') : $this.closest('.product').find('.product-gallery__slider .slick-slide.slick-current img'),
			$tempImg = $img.clone().css({
				pointerEvents: 'none',
				position: 'absolute',
				zIndex: 10001,
			});

		$('body').append($tempImg);

		$tempImg.css({
			left: $img.offset().left,
			top: $img.offset().top,
			width: $img.width(),
			height: $img.height(),
		}).animate({
			left: $basketBtn.offset().left,
			top: $basketBtn.offset().top,
			width: $basketBtn.outerWidth(),
			height: $basketBtn.outerHeight(),
		}, 500, function() {
			$tempImg.remove();
		});
	});

	/**
	 * Change variant
	 */
	$(document).on('change', '.js-change-variant', function(){
		$('.price_lg').empty();

		/**
		 * получаем айдишники вариантов товаров в корзине
		 */
		var cart_product_ids = $('[name=cart-product-ids]').val().split(',');
		var price, compare_price, sku;
		var currency = $('input[name=currency]').val();

		//если варианты товара выводятся списком
		if ($(this).is('select')) {
			var selected = $(this).find('option:selected');

			console.log(selected.data('variant-infinity'));

			sku = selected.data('sku');
			price = selected.data('price');
			compare_price = selected.data('compare-price');

			renderPrices(price, compare_price, currency, sku);
			renderCartButton(cart_product_ids,selected.data('variant-id'), selected.data('variant-infinity'), selected.data('variant-stock'))
		}

		//если варианты товара выводятся радиокнопками
		if ($(this).is('input')) {
			var checked = $(this).filter(':checked');

			sku = checked.data('sku');
			price = checked.data('price');
			compare_price = checked.data('compare-price');

			renderPrices(price, compare_price, currency, sku);
			renderCartButton(cart_product_ids,$(this).data('variant-id'), $(this).data('variant-infinity'), $(this).data('variant-stock'))
		}
	});

	/**
	 * Add Favorite
	 */
	$(document).on('click', '.js-add-favorites', function(event) {
		event.preventDefault();
		/* Act on the event */
		var product_id = $(this).data('product-id'),
			$this = $(this);
		$.ajax({
			url: 'ajax/product/favorites',
			data: { product_id: product_id, action: ($this.hasClass('is-active') ? 'remove' : 'add')},
			dataType: 'json',
			cache: false,
			success: function(data) {


				if($this.hasClass('is-active')) {
					$('.is-active.js-favorites-'+product_id).removeClass('is-active');
					$this.removeClass('is-active');
				} else {
					$('.js-favorites-'+product_id).addClass('is-active');
				}


				$('.js-favorites-count').html(data.count);
			}
		});
	});

	/**
	 *-------------------- CART PAGE
	 */
	$('.js-validation-cart-form').each(function() {
		var $form = $(this);
		$form.validate({
			errorPlacement: function(error, element) {
				if ($(element).is(':checkbox') || $(element).is(':radio')) {
					error.insertAfter($(element).closest('label'));
				} else {
					error.insertAfter(element);
				}
			}
		});
	});

	$(document).ready(function () {
		$(document).on('click', '[name=delivery_id]' , function () {

			$.ajax({
				url: "ajax/product/deliveryDiscount",
				method: 'post',
				data: {
					delivery_id: $(this).val(),
					name    : $('#orderName').val(),
					email   : $('#orderEmail').val(),
					phone   : $('#orderPhone').val(),
					address : $('[name=address]').val(),
					comment : $('#order_comment').val(),
				},
				dataType: 'json',
				success: function () {


					location.reload();

				},
				error: function (request, status, error) {
					console.log(error);
				}
			});
		});
	});
	/**
	 *----------------- END CART PAGE
	 */
});

/**
 * Меняем кнопку добавления корзину в зависимости от ситуаций
 * 1. Товаров бесконечность, значить выводим Под Заказ
 * 2. Если выбранный вариант товара уже находится в корзине, то выводим сообщение в корзине
 * @param cart_product_ids
 * @param variant_id
 * @param variant_infinity
 * @param variant_stock
 */
function renderCartButton(cart_product_ids,variant_id, variant_infinity, variant_stock) {

	var btn_area = $('.js-cart_button-render-area');

	if (variant_stock == 0) {
		btn_area.html('<button disabled type="submit" class="basket-btn btn btn-warning " > <span class="basket-btn__text ">Нет в наличии</span></button>');
	}  else  {

		var btn_name = '';
		var is_active = '';

		if (variant_infinity == 1) {
			btn_name = 'Под Заказ';
		}  else {
			btn_name= 'Добавить в корзину';
		}


		if ( cart_product_ids.indexOf(variant_id.toString()) >= 0 ) {
			btn_name = 'В корзине';
			is_active = 'is-active';
		}

		btn_area.html(
			' <button type="submit" class="basket-btn btn btn-warning ' + is_active + '" data-alt-text="В корзине">\n' +
			' <i class="fal fa-shopping-cart"></i>\n' +
			' <span class="basket-btn__text js-add-to-cart-in-product-page">'+ btn_name +'</span>\n' +
			' </button>'
		);

	}
}

function renderPrices(price, compare_price, currency, sku) {
	if (compare_price !== undefined) {
		$('.price_lg').html("<div class='price__old'>" + compare_price +' ' + currency + "</div><div class='price__discount'>" + (compare_price - price) + currency + "</div>");
	}
	$('.price_lg').append("<div class='price__value'>" +price+ ' ' + currency + "</div>");
	$('.sku').text('Арт. ' + sku);
}
