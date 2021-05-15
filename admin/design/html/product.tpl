{capture name=tabs}
	<li class="active">
		<a href="{url module=ProductsAdmin category_id=$product->category_id return=null brand_id=null id=null}">Товары</a>
	</li>
	{if in_array('categories', $manager->permissions)}
		<li>
			<a href="index.php?module=CategoriesAdmin">Категории</a>
		</li>
	{/if}
	{if in_array('brands', $manager->permissions)}
		<li>
			<a href="index.php?module=BrandsAdmin">Бренды</a>
		</li>
	{/if}
	{if in_array('features', $manager->permissions)}
		<li>
			<a href="index.php?module=FeaturesAdmin">Свойства</a>
		</li>
	{/if}

	{if in_array('banners', $manager->permissions)}
		<li>
			<a href="index.php?module=BannersAdmin">Баннеры</a>
		</li>
	{/if}

	{if in_array('tags', $manager->permissions)}
		<li><a href="index.php?module=TagsAdmin">Теги</a></li>
	{/if}

{/capture}

{if $product->id}
	{$meta_title = $product->name scope=root}
{else}
	{$meta_title = 'Новый товар' scope=root}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script>
		$(function () {
		
		     var new_document = $('#new_document').clone(true);
            $('#new_document').remove().removeAttr('id');
            $("#add_document").on('click', function () {
                $(new_document).clone(true).appendTo('.js-document-list').fadeIn('slow').find("input[name*=document]").focus();
                return false;
            });
            $('.delete_document_values').live('click', function () {
                $(this).closest('.new_document').remove();
            });

            $('.delete_document').live('click', function (e) {

                e.preventDefault();

                let docId = $(this).data('document-id'),
                    self = $(this)
                ;


                $.ajax({
                    url: "ajax/delete_product_document.php",
                    data: {
                        id: docId
                    },
                    dataType: 'json',
                    success: function (data) {

                        if (data.success === true) {
                            self.closest('ul').remove();
                        } else {
                            console.log(data.error);
                        }
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    }
                });

            });

			// Добавление категории
			$('#product_categories .add').click(function () {
				$("#product_categories ul li:last").clone(false).appendTo('#product_categories ul').fadeIn('slow').find("select[name*=categories]:last").focus();
				$("#product_categories ul li:last span.add").hide();
				$("#product_categories ul li:last span.delete").show();
				return false;
			});

			// Удаление категории
			$("#product_categories .delete").live('click', function () {
				$(this).closest("li").fadeOut(200, function () {
					$(this).remove();
				});
				return false;
			});

			// Сортировка вариантов
			$("#variants_block").sortable({items: '#variants ul', axis: 'y', cancel: '#header', handle: '.move_zone'});
			// Сортировка вариантов
			$("table.related_products").sortable({items: 'tr', axis: 'y', cancel: '#header', handle: '.move_zone'});


			// Сортировка связанных товаров
			$(".sortable").sortable({
				items: "div.row",
				tolerance: "pointer",
				scrollSensitivity: 40,
				opacity: 0.7,
				handle: '.move_zone'
			});


			// Сортировка изображений
			$(".images ul").sortable({tolerance: 'pointer'});

			// Удаление изображений
			$(".images a.delete").live('click', function () {
				$(this).closest("li").fadeOut(200, function () {
					$(this).remove();
				});
				return false;
			});
			// Загрузить изображение с компьютера
			$('#upload_image').click(function () {
				$("<input class='upload_image' name=images[] type=file multiple  accept='image/*'>").appendTo('div#add_image').focus().click();
			});
			// Или с URL
			$('#add_image_url').click(function () {
				$("<input class='remote_image' name=images_urls[] type=text value='http://'>").appendTo('div#add_image').focus().select();
			});
			// Или перетаскиванием
			if (window.File && window.FileReader && window.FileList) {
				$("#dropZone").show();
				$("#dropZone").on('dragover', function (e) {
					$(this).css('border', '1px solid #8cbf32');
				});
				$(document).on('dragenter', function (e) {
					$("#dropZone").css('border', '1px dotted #8cbf32').css('background-color', '#c5ff8d');
				});

				dropInput = $('.dropInput').last().clone();

				function handleFileSelect(evt) {
					var files = evt.target.files; // FileList object
					// Loop through the FileList and render image files as thumbnails.
					for (var i = 0, f; f = files[i]; i++) {
						// Only process image files.
						if (!f.type.match('image.*')) {
							continue;
						}
						var reader = new FileReader();
						// Closure to capture the file information.
						reader.onload = (function (theFile) {
							return function (e) {
								// Render thumbnail.
								$("<li class=wizard><a href='' class='delete'><img src='design/images/cross-circle-frame.png'></a><img onerror='$(this).closest(\"li\").remove();' src='" + e.target.result + "' /><input name=images_urls[] type=hidden value='" + theFile.name + "'></li>").appendTo('div .images ul');
								temp_input = dropInput.clone();
								$('.dropInput').hide();
								$('#dropZone').append(temp_input);
								$("#dropZone").css('border', '1px solid #d0d0d0').css('background-color', '#ffffff');
								clone_input.show();
							};
						})(f);

						// Read in the image file as a data URL.
						reader.readAsDataURL(f);
					}
				}

				$('.dropInput').live("change", handleFileSelect);
			}
			;

			// Удаление варианта
			$('a.del_variant').click(function () {
				if ($("#variants ul").size() > 1) {
					$(this).closest("ul").fadeOut(200, function () {
						$(this).remove();
					});
				}
				else {
					$('#variants_block .variant_name input[name*=variant][name*=name]').val('');
					$('#variants_block .variant_name').hide('slow');
					$('#variants_block').addClass('single_variant');
				}
				return false;
			});

			// Загрузить файл к варианту
			$('#variants_block a.add_attachment').click(function () {
				$(this).hide();
				$(this).closest('li').find('div.browse_attachment').show('fast');
				$(this).closest('li').find('input[name*=attachment]').attr('disabled', false);
				return false;
			});

			// Удалить файл к варианту
			$('#variants_block a.remove_attachment').click(function () {
				closest_li = $(this).closest('li');
				closest_li.find('.attachment_name').hide('fast');
				$(this).hide('fast');
				closest_li.find('input[name*=delete_attachment]').val('1');
				closest_li.find('a.add_attachment').show('fast');
				return false;
			});


			// Добавление варианта
			var variant = $('#new_variant').clone(true);
			$('#new_variant').remove().removeAttr('id');
			$('#variants_block span.add').click(function () {
				if (!$('#variants_block').is('.single_variant')) {
					$(variant).clone(true).appendTo('#variants').fadeIn('slow').find("input[name*=variant][name*=name]").focus();
				}
				else {
					$('#variants_block .variant_name').show('slow');
					$('#variants_block').removeClass('single_variant');
				}
				return false;
			});

			function optionsAutocompleteInit() {
				// Автодополнение свойств
				$('ul.prop_ul input[name*=options]:not(.init-autocomplete)').each(function(index) {
					feature_id = $(this).closest('li').attr('feature_id');
					$(this).autocomplete({
						serviceUrl:'ajax/options_autocomplete.php',
						minChars:0,
						params: {feature_id:feature_id},
						noCache: false
					});
				});
				$('ul.prop_ul input[name*=options]:not(.init-autocomplete)').addClass('init-autocomplete');
			}
			function show_category_features(category_id) {
				$('ul.prop_ul input[value=""].init-autocomplete').autocomplete('dispose');
				$('ul.prop_ul input[value=""]').closest('li[feature_id]').remove();
				//$('ul.prop_ul').empty();
				$.ajax({
					url: "ajax/get_features.php",
					data: {category_id: category_id, product_id: $("input[name=id]").val()},
					dataType: 'json',
					success: function (data) {
						for (i = 0; i < data.length; i++) {
							feature = data[i];

							if($('[feature_id='+feature.id+']').length > 0){
								continue;
							}

							line = $("<li feature_id='"+ feature.id +"'><label class=property></label><span class='values i'></span>" +
									"<div class='add_values'><span class='add'><i class='dash_link' data-feature-id='" + feature.id + "' id='add_value'>Добавить</i></span></div>" +
									"</li>");

							var new_line = line.clone(true);
							new_line.find("label.property").text(feature.name);
							for (j = 0; j < feature.values.length; j++) {
								new_line.find(".values").append("<input class='simpla_inp' name='options[" + feature.id + "][]' value='"+feature.values[j]+"' type='text'> <button class='delete_values' style='display: inline;'>x</button>");
							}

							new_line.appendTo('ul.prop_ul');

						}

						optionsAutocompleteInit();
					}
				});
				return false;
			}

			// Изменение набора свойств при изменении категории
			$('select[name="categories[]"]:first').change(function () {
				show_category_features($("option:selected", this).val());
			});

			optionsAutocompleteInit();
			// Добавление нового свойства товара
			var new_feature = $('#new_feature').clone(true);
			$('#new_feature').remove().removeAttr('id');
			$('#add_new_feature').click(function () {
				$(new_feature).clone(true).appendTo('ul.new_features').fadeIn('slow').find("input[name*=new_feature_name]").focus();
				return false;
			});

			$('#add_value').live('click', function() {


				$( '<input class="simpla_inp" type="text" name="options['+$(this).attr('data-feature-id')+'][]" value="" />' ).clone()
						.after(' <button class="delete_values" style="display: inline;">x</button>')
						.appendTo($(this).closest('li').find('.values'));
//
				optionsAutocompleteInit();
			});

			$('.delete_values').live('click', function() {

				$(this).prev().remove();
				$(this).remove();
			});



			// Удаление связанного товара
			$(".related_products1 a.delete").live('click', function () {
				$(this).closest("div.row").fadeOut(200, function () {
					$(this).remove();
				});
				return false;
			});


			// Удаление связанного товара
			$(".related_products2 a.delete").live('click', function () {
				$(this).closest("div.row").fadeOut(200, function () {
					$(this).remove();
				});
				return false;
			});

			// Добавление связанного товара
			var new_related_product = $('#new_related_product').clone(true);
			$('#new_related_product').remove().removeAttr('id');

			$("input#related_products").autocomplete({
				serviceUrl: 'ajax/search_products.php',
				minChars: 0,
				noCache: false,
				onSelect: function (suggestion) {
					$("input#related_products").val('').focus().blur();
					new_item = new_related_product.clone().appendTo('.related_products1');
					new_item.removeAttr('id');
					new_item.find('a.related_product_name').html(suggestion.data.name);
					new_item.find('a.related_product_name').attr('href', 'index.php?module=ProductAdmin&id=' + suggestion.data.id);
					new_item.find('input[name*="related_products"]').val(suggestion.data.id);
					if (suggestion.data.image)
						new_item.find('img.product_icon').attr("src", suggestion.data.image);
					else
						new_item.find('img.product_icon').remove();
					new_item.show();
				},
				formatResult: function (suggestions, currentValue) {
					var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
					var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
					return (suggestions.data.image ? "<img align=absmiddle src='" + suggestions.data.image + "'> " : '') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
				}

			});



			// Добавление связанного товара
			var new_related_product2 = $('#new_related_product2').clone(true);
			$('#new_related_product2').remove().removeAttr('id');

			$("input#related_products2").autocomplete({
				serviceUrl: 'ajax/search_products.php',
				minChars: 0,
				noCache: false,
				onSelect: function (suggestion) {
					$("input#related_products2").val('').focus().blur();
					new_item = new_related_product2.clone().appendTo('.related_products2');
					new_item.removeAttr('id');
					new_item.find('a.related_product_name').html(suggestion.data.name);
					new_item.find('a.related_product_name').attr('href', 'index.php?module=ProductAdmin&id=' + suggestion.data.id);
					new_item.find('input[name*="related_products2"]').val(suggestion.data.id);
					if (suggestion.data.image)
						new_item.find('img.product_icon').attr("src", suggestion.data.image);
					else
						new_item.find('img.product_icon').remove();
					new_item.show();
				},
				formatResult: function (suggestions, currentValue) {
					var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
					var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
					return (suggestions.data.image ? "<img align=absmiddle src='" + suggestions.data.image + "'> " : '') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
				}

			});


			// infinity
			$("input[name*=variant][name*=stock]").focus(function () {
				if ($(this).val() == '∞')
					$(this).val('');
				return false;
			});

			$("input[name*=variant][name*=stock]").blur(function () {
				if ($(this).val() == '')
					$(this).val('∞');
			});

			// Автозаполнение мета-тегов
			meta_title_touched = true;
			meta_keywords_touched = true;
			meta_description_touched = true;
			url_touched = true;

			if ($('input[name="meta_title"]').val() == generate_meta_title() || $('input[name="meta_title"]').val() == '')
				meta_title_touched = false;
			if ($('input[name="meta_keywords"]').val() == generate_meta_keywords() || $('input[name="meta_keywords"]').val() == '')
				meta_keywords_touched = false;
			if ($('textarea[name="meta_description"]').val() == generate_meta_description() || $('textarea[name="meta_description"]').val() == '')
				meta_description_touched = false;
			if ($('input[name="url"]').val() == generate_url() || $('input[name="url"]').val() == '')
				url_touched = false;

			$('input[name="meta_title"]').change(function () {
				meta_title_touched = true;
			});
			$('input[name="meta_keywords"]').change(function () {
				meta_keywords_touched = true;
			});
			$('textarea[name="meta_description"]').change(function () {
				meta_description_touched = true;
			});
			$('input[name="url"]').change(function () {
				url_touched = true;
			});

			$('input[name="name"]').keyup(function () {
				set_meta();
			});
			$('select[name="brand_id"]').change(function () {
				set_meta();
			});
			$('select[name="categories[]"]').change(function () {
				set_meta();
			});

		});

		function set_meta() {
			if (!meta_title_touched)
				$('input[name="meta_title"]').val(generate_meta_title());
			if (!meta_keywords_touched)
				$('input[name="meta_keywords"]').val(generate_meta_keywords());
			if (!meta_description_touched)
				$('textarea[name="meta_description"]').val(generate_meta_description());
			if (!url_touched)
				$('input[name="url"]').val(generate_url());
		}

		function generate_meta_title() {
			return $('input[name="name"]').val();
		}

		function generate_meta_keywords() {
			name = $('input[name="name"]').val();
			result = name;
			brand = $('select[name="brand_id"] option:selected').attr('brand_name');
			if (typeof(brand) == 'string' && brand != '')
				result += ', ' + brand;
			$('select[name="categories[]"]').each(function (index) {
				c = $(this).find('option:selected').attr('category_name');
				if (typeof(c) == 'string' && c != '')
					result += ', ' + c;
			});
			return result;
		}

		function generate_meta_description() {
			if (typeof(tinyMCE.get("annotation")) == 'object') {
				description = tinyMCE.get("annotation").getContent().replace(/(<([^>]+)>)/ig, " ").replace(/(\&nbsp;)/ig, " ").replace(/^\s+|\s+$/g, '').substr(0, 512);
				return description;
			}
			else
				return $('textarea[name=annotation]').val().replace(/(<([^>]+)>)/ig, " ").replace(/(\&nbsp;)/ig, " ").replace(/^\s+|\s+$/g, '').substr(0, 512);
		}
		function generate_url() {
			var name = $('input[name="name"]').val();
			return Simpla.translit_url(name);
		}

	</script>
	<style>
		.autocomplete-suggestions {
			background-color: #ffffff;
			overflow: hidden;
			border: 1px solid #e0e0e0;
			overflow-y: auto;
		}

		.autocomplete-suggestions .autocomplete-suggestion {
			cursor: default;
		}

		.autocomplete-suggestions .selected {
			background: #F0F0F0;
		}

		.autocomplete-suggestions div {
			padding: 2px 5px;
			white-space: nowrap;
		}

		.autocomplete-suggestions strong {
			font-weight: normal;
			color: #3399FF;
		}
	</style>
{/literal}



{if $message_success}
	<!-- Системное сообщение -->
	<div class="message message_success">
		<span class="text">
			{if $message_success=='added'}Товар добавлен
			{elseif $message_success=='updated'}Товар изменен
			{else}{$message_success|escape}{/if}
		</span>

		<a class="link" target="_blank" href="../products/{$product->url}">Открыть товар на сайте</a>
		{if $smarty.get.return}
			<a class="button" href="{$smarty.get.return}">Вернуться</a>
		{/if}

		<span class="share">
			<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/products/{$product->url|urlencode}&title={$product->name|urlencode}&description={$product->annotation|urlencode}&image={$product_images.0->filename|resize:1000:1000|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  				<img src="{$config->root_url}/simpla/design/images/vk_icon.png"/>
			</a>
			<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/products/{$product->url|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
				<img src="{$config->root_url}/simpla/design/images/facebook_icon.png"/>
			</a>
			<a href="#"onClick='window.open("http://twitter.com/share?text={$product->name|urlencode}&url={$config->root_url|urlencode}/products/{$product->url|urlencode}&hashtags={$product->meta_keywords|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
				<img src="{$config->root_url}/simpla/design/images/twitter_icon.png"/>
			</a>
		</span>

	</div>
	<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
	<!-- Системное сообщение -->
	<div class="message message_error">
		<span class="text">
			{if $message_error=='url_exists'}Товар с таким адресом уже существует
			{elseif $message_error=='empty_name'}Введите название
			{else}{$message_error|escape}{/if}
		</span>
		{if $smarty.get.return}
			<a class="button" href="{$smarty.get.return}">Вернуться</a>
		{/if}
	</div>
	<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method="post" id="product" enctype="multipart/form-data">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

	<div id="name">
		<input class="name" name="name" type="text" value="{$product->name|escape}" placeholder="Название товара" required>
		<input name="id" type="hidden" value="{$product->id|escape}"/>
		<div class="checkbox">
			<input name=visible value="1" type="checkbox" id="active_checkbox"{if $product->visible} checked{/if}/>
			<label for="active_checkbox">Активен</label>
		</div>
		 <div class="checkbox">
            <input name="discounted" value="1" type="checkbox"
                   id="discounted_checkbox"{if $product->discounted} checked{/if}/>
            <label for="discounted_checkbox">Акция</label>
        </div>
		
		<div class="checkbox">
			<input name="featured" value="1" type="checkbox" id="featured_checkbox"{if $product->featured} checked{/if}/>
			<label for="featured_checkbox">Рекомендуемый</label>
		</div>

		<div class="checkbox">
			<input name="new" value="1" type="checkbox" id="new_checkbox"{if $product->new} checked{/if}/>
			<label for="new_checkbox">Новинка</label>
		</div>

	</div>

	<div id="product_brand"{if !$brands} style="display:none;"{/if}>
		<label>Бренд</label>
		<select name="brand_id">
			<option value="0"{if !$product->brand_id} selected{/if} brand_name="">Не указан</option>
			{foreach $brands as $brand}
				<option value="{$brand->id}"{if $product->brand_id == $brand->id} selected{/if} brand_name="{$brand->name|escape}">{$brand->name|escape}</option>
			{/foreach}
		</select>
	</div>


	<div id="product_categories" {if !$categories}style="display:none;"{/if}>
		<label>Категория</label>
		<div>
			<ul>
				{function name=category_select categories=false selected_id=0 level=0}
					{if $categories}
						{foreach $categories as $category}
							<option value="{$category->id}"{if $category->id == $selected_id} selected{/if} category_name="{$category->name|escape}">{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
							{category_select categories=$category->subcategories selected_id=$selected_id level=$level+1}
						{/foreach}
					{/if}
				{/function}
				{foreach $product_categories as $product_category}
					<li>
						<select name="categories[]">
							{category_select categories=$categories selected_id=$product_category->category_id}
						</select>
						<span {if !$product_category@first}style="display:none;"{/if} class="add">
							<i class="dash_link">Дополнительная категория</i>
						</span>
						<span {if $product_category@first}style="display:none;"{/if} class="delete">
							<i class="dash_link">Удалить</i>
						</span>
					</li>
				{/foreach}
			</ul>
		</div>
	</div>


	<!-- Варианты товара -->
	<div id="variants_block" {assign var=first_variant value=$product_variants|@first}{if $product_variants|@count <= 1 && !$first_variant->name} class="single_variant"{/if}>
		<ul id="header">
			<li class="variant_move"></li>
			<li class="variant_name">Название варианта</li>
			<li class="variant_sku">Артикул</li>
			<li class="variant_price">Цена, {$currency->sign}</li>
			<li class="variant_discount">Старая, {$currency->sign}</li>
			<li class="variant_amount">Кол-во</li>
		</ul>
		<div id="variants">
			{foreach $product_variants as $variant}
				<ul>
					<li class="variant_move">
						<div class="move_zone"></div>
					</li>
					<li class="variant_name">
						<input name="variants[id][]" type="hidden" value="{$variant->id|escape}"/>
						<input name="variants[name][]" type="text" value="{$variant->name|escape}"/>
						<a class="del_variant" href="#">
							<img src="design/images/cross-circle-frame.png" alt=""/>
						</a>
					</li>
					<li class="variant_sku">
						<input name="variants[sku][]" type="text" value="{$variant->sku|escape}"/>
					</li>
					<li class="variant_price">
						<input name="variants[price][]" type="text" value="{$variant->price|escape}"/>
					</li>
					<li class="variant_discount">
						<input name="variants[compare_price][]" type="text" value="{$variant->compare_price|escape}"/>
					</li>
					<li class="variant_amount">
						<input name="variants[stock][]" type="text" value="{if $variant->infinity || $variant->stock == ''}∞{else}{$variant->stock|escape}{/if}"/>{$settings->units}
					</li>
					<li class="variant_download">

						{if $variant->attachment}


							<img src="{$variant->attachment|resize:30:30}" class="" alt="{$variant->name|escape}">

{*							<span class="attachment_name">{$variant->attachment|truncate:25:'...':false:true}</span>*}
							<a href="#" class="remove_attachment">
								<img src="design/images/bullet_delete.png" title="Удалить цифровой товар">
							</a>

							<a href="#" class="add_attachment" style="display:none;">
								<img src="design/images/cd_add.png" title="Добавить цифровой товар"/>
							</a>
						{else}
							<a href="#" class="add_attachment">
								<img src="design/images/cd_add.png" title="Добавить цифровой товар"/>
							</a>
						{/if}
						<div class="browse_attachment" style="display:none;">
							<input type="file" name="attachment[]">
							<input type="hidden" name="delete_attachment[]">
						</div>
					</li>
				</ul>
			{/foreach}
		</div>
		<ul id="new_variant" style="display:none;">
			<li class="variant_move">
				<div class="move_zone"></div>
			</li>
			<li class="variant_name">
				<input name="variants[id][]" type="hidden" value=""/>
				<input name="variants[name][]" type="text" value=""/>
				<a class="del_variant" href="#">
					<img src="design/images/cross-circle-frame.png" alt=""/>
				</a>
			</li>
			<li class="variant_sku">
				<input name="variants[sku][]" type="text" value=""/>
			</li>
			<li class="variant_price">
				<input name="variants[price][]" type="text" value=""/>
			</li>
			<li class="variant_discount">
				<input name="variants[compare_price][]" type="text" value=""/>
			</li>
			<li class="variant_amount">
				<input name="variants[stock][]" type="text" value="∞"/>{$settings->units}
			</li>
			<li class="variant_download">
				<a href="#" class="add_attachment">
					<img src="design/images/cd_add.png" alt=""/>
				</a>
				<div class="browse_attachment" style="display:none;">
					<input type="file" name="attachment[]">
					<input type="hidden" name="delete_attachment[]">
				</div>
			</li>
		</ul>

		<input class="button_green button_save" type="submit" name="" value="Сохранить"/>
		<span class="add" id="add_variant">
			<i class="dash_link">Добавить вариант</i>
		</span>
	</div>
	<!-- Варианты товара (The End)-->

	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li>
					<label for="url" class="property">Адрес</label>
					<div class="page_url"> /products/</div>
					<input id="url" name="url" class="page_url" type="text" value="{$product->url|escape}" required>
				</li>
				<li>
					<label for="meta_title" class="property">Заголовок</label>
					<input id="meta_title" name="meta_title" class="simpla_inp" type="text" value="{$product->meta_title|escape}"/>
				</li>
				<li>
					<label for="meta_keywords" class="property">Ключевые слова</label>
					<input id="meta_keywords" name="meta_keywords" class="simpla_inp" type="text" value="{$product->meta_keywords|escape}"/>
				</li>
				<li>
					<label for="meta_description" class="property">Описание</label>
					<textarea id="meta_description" name="meta_description" class="simpla_inp">{$product->meta_description|escape}</textarea>
				</li>
				<li>
					<label for="youtube_link" class="property">Код ссылки на youtube</label>
					<input id="youtube_link" name="youtube_link" class="simpla_inp" type="text" value="{$product->youtube_link|escape}"/>
				</li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->

		<div class="block layer"{if !$categories} style="display:none;"{/if}>
			<h2>Свойства товара</h2>

			<ul class="prop_ul">
				{foreach $features as $feature}

					{assign var=feature_id value=$feature->id}

					<li feature_id={$feature_id}>
						<label class="property">{$feature->name}</label>
						<span class="values i">
						{if $options[$feature->id]->values}
							{foreach $options[$feature->id]->values as $value}
								<input class="simpla_inp" type="text" name="options[{$feature->id}][]" value="{$value|escape}" />
								<button class="delete_values" style="display: inline;">x</button>
							{/foreach}
						{else}
							<input class="simpla_inp" type="text" name="options[{$feature->id}][]" value="" />
							<button class="delete_values" style="display: inline;">x</button>
						{/if}

						</span>
						<div class="add_values">
							<span class="add">
								<i class="dash_link" data-feature-id="{$feature_id}" id="add_value">Добавить</i>
							</span>
						</div>
						{*<input class="simpla_inp" type="text" name="options[{$feature_id}]" value="{$options.$feature_id->value|escape}"/>*}
					</li>
				{/foreach}
			</ul>

			<input class="button_green button_save" type="submit" name="save" value="Сохранить"/>
		</div>
		<style>
			.prop_ul .values {
				width: 265px;
				display: block;
				float: left;
			}
			.prop_ul .values .simpla_inp{
				margin-bottom: 4px;

			}
			#product .block li .values input[type=text]  {
				width: 220px;
			}
			.add_values {
				float: left;
				margin-left: 350px;
			}

		</style>

		<!-- Свойства товара (The End)-->
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">

		<!-- Изображения товара -->
		<div class="block layer images">
			<h2>Изображения товара</h2>
			<ul>
				{foreach $product_images as $image}
					<li>
						<a href="#" class="delete">
							<img src="design/images/cross-circle-frame.png">
						</a>
						<img src="{$image->filename|resize:100:100}" alt=""/>
						<input type="hidden" name="images[]" value="{$image->id}">
					</li>
				{/foreach}
			</ul>
			<div id="dropZone">
				<div id="dropMessage">Перетащите файлы сюда</div>
				<input type="file" name="dropped_images[]" multiple class="dropInput" accept="image/*">
			</div>
			<div id="add_image"></div>
			<span class="upload_image">
				<i class="dash_link" id="upload_image">Добавить изображение</i>
			</span>
			или
			<span class="add_image_url">
				<i class="dash_link" id="add_image_url">загрузить из интернета</i>
			</span>
		</div>

		<div class="block layer">
			<h2>Необходимые аксессуары</h2>
			<div id="list" class="sortable related_products related_products1">
				{foreach $related_products as $related_product}
					<div class="row">
						<div class="move cell">
							<div class="move_zone"></div>
						</div>
						<div class="image cell">
							<input type="hidden" name="related_products[]" value="{$related_product->id}">
							<a href="{url id=$related_product->id}">
								<img class="product_icon" src="{$related_product->images[0]->filename|resize:35:35}">
							</a>
						</div>
						<div class="name cell">
							<a href="{url id=$related_product->id}">{$related_product->name}</a>
						</div>
						<div class="icons cell">
							<a href="#" class="delete"></a>
						</div>
						<div class="clear"></div>
					</div>
				{/foreach}
				<div id="new_related_product" class="row" style="display:none;">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
						<input type="hidden" name="related_products[]" value="">
						<img class="product_icon" src="">
					</div>
					<div class="name cell">
						<a class="related_product_name" href=""></a>
					</div>
					<div class="icons cell">
						<a href="#" class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<input type="text" name="related" id="related_products" class="input_autocomplete" placeholder="Выберите товар чтобы добавить его">
		</div>

		<div class="block layer">
			<h2>Похожие товары</h2>
			<div id="list" class="sortable related_products related_products2">
				{foreach $related_products2 as $related_product2}
					<div class="row">
						<div class="move cell">
							<div class="move_zone"></div>
						</div>
						<div class="image cell">
							<input type="hidden" name="related_products2[]" value="{$related_product2->id}">
							<a href="{url id=$related_product2->id}">
								<img class="product_icon" src="{$related_product2->images[0]->filename|resize:35:35}">
							</a>
						</div>
						<div class="name cell">
							<a href="{url id=$related_product2->id}">{$related_product2->name}</a>
						</div>
						<div class="icons cell">
							<a href="#" class="delete"></a>
						</div>
						<div class="clear"></div>
					</div>
				{/foreach}
				<div id="new_related_product2" class="row" style="display:none;">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
						<input type="hidden" name="related_products2[]" value="">
						<img class="product_icon" src="">
					</div>
					<div class="name cell">
						<a class="related_product_name" href=""></a>
					</div>
					<div class="icons cell">
						<a href="#" class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<input type="text" name="related" id="related_products2" class="input_autocomplete" placeholder="Выберите товар чтобы добавить его">
		</div>


        <div class="block layer">
            <h2>Документы <span class="add" id="add_document"><i class="dash_link"></i></span></h2>

            <div class="js-document-list">

                {if $documents }
                    {foreach $documents as $document }
                        <ul id="" class="new_document">

                            <li class="variant_sku">
                                <input name="exist_document[{$document->id}]" type="text" value="{$document->name}"/>
                                <button type="button" data-document-id="{$document->id}" class="delete_document"
                                        style="display: inline;">x
                                </button>
                            </li>
                            <li class="variant_price">
                                <a href="{$config->root_url}/files/documents/{$document->document}">Просмотр</a>
                            </li>

                        </ul>
                    {/foreach}
                {/if}

            </div>

        </div>


        <ul id="new_document" class="new_document" style="display:none;">

            <li class="variant_sku">
                <input name="document[name][]" type="text" value=""/>
                <button class="delete_document_values" style="display: inline;">x</button>
            </li>
            <li class="variant_price">
                <input type="file" name="documents[]">
            </li>

        </ul>


		<input class="button_green button_save" type="submit" name="save" value="Сохранить"/>

	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Краткое описание</h2>
		<textarea name="annotation" class="editor_small">{$product->annotation|escape}</textarea>
	</div>

	<div class="block">
		<h2>Полное описание</h2>
		<textarea name="body" class="editor_large">{$product->body|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="save" value="Сохранить"/>

</form>
<!-- Основная форма (The End) -->

