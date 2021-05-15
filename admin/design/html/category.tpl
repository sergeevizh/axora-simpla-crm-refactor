
{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}
		<li>
			<a href="index.php?module=ProductsAdmin">Товары</a>
		</li>
	{/if}
	<li class="active">
		<a href="index.php?module=CategoriesAdmin">Категории</a>
	</li>
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

	{if in_array('tags', $manager->permissions)}<li><a href="index.php?module=TagsAdmin">Теги</a></li>{/if}


{/capture}

{if $category->id}
	{$meta_title = $category->name scope=root}
{else}
	{$meta_title = 'Новая категория' scope=root}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
	<script src="design/js/jquery/jquery.js"></script>
	<script src="design/js/jquery/jquery-ui.min.js"></script>
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<style>
		.autocomplete-w1 {
			background: url(img/shadow.png) no-repeat bottom right;
			position: absolute;
			top: 0px;
			left: 0px;
			margin: 6px 0 0 6px; /* IE6 fix: */
			_background: none;
			_margin: 1px 0 0 0;
		}

		.autocomplete {
			border: 1px solid #999;
			background: #FFF;
			cursor: default;
			text-align: left;
			overflow-x: auto;
			min-width: 300px;
			overflow-y: auto;
			margin: -6px 6px 6px -6px; /* IE6 specific: */
			_height: 350px;
			_margin: 0;
			_overflow-x: hidden;
		}

		.autocomplete .selected {
			background: #F0F0F0;
		}

		.autocomplete div {
			padding: 2px 5px;
			white-space: nowrap;
		}

		.autocomplete strong {
			font-weight: normal;
			color: #3399FF;
		}
	</style>
	<script>
		$(function () {


			// Удаление изображений
			$(".images a.delete").click(function () {
				$("input[name='delete_image']").val('1');
				$(this).closest("ul").fadeOut(200, function () {
					$(this).remove();
				});
				return false;
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
			return $('input[name="name"]').val();
		}

		function generate_meta_description() {
			if (typeof(tinyMCE.get("description")) == 'object') {
				description = tinyMCE.get("description").getContent().replace(/(<([^>]+)>)/ig, " ").replace(/(\&nbsp;)/ig, " ").replace(/^\s+|\s+$/g, '').substr(0, 512);
				return description;
			}
			else
				return $('textarea[name=description]').val().replace(/(<([^>]+)>)/ig, " ").replace(/(\&nbsp;)/ig, " ").replace(/^\s+|\s+$/g, '').substr(0, 512);
		}

		function generate_url() {
			var name = $('input[name="name"]').val();
			return Simpla.translit_url(name);
		}
	</script>
{/literal}


{if $message_success}
	<!-- Системное сообщение -->
	<div class="message message_success">
		<span class="text">
			{if $message_success=='added'}
				Категория добавлена
			{elseif $message_success=='updated'}
				Категория обновлена
			{else}
				{$message_success}
			{/if}
		</span>
		<a class="link" target="_blank" href="../catalog/{$category->url}">Открыть категорию на сайте</a>
		{if $smarty.get.return}
			<a class="button" href="{$smarty.get.return}">Вернуться</a>
		{/if}

		<span class="share">
			<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/catalog/{$category->url|urlencode}&title={$category->name|urlencode}&description={$category->description|urlencode}&image={$config->root_url|urlencode}/files/categories/{$category->image|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
				<img src="{$config->root_url}/simpla/design/images/vk_icon.png"/>
			</a>
			<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/catalog/{$category->url|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
				<img src="{$config->root_url}/simpla/design/images/facebook_icon.png"/>
			</a>
			<a href="#" onClick='window.open("http://twitter.com/share?text={$category->name|urlencode}&url={$config->root_url|urlencode}/catalog/{$category->url|urlencode}&hashtags={$category->meta_keywords|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
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
			{if $message_error=='url_exists'}
				Категория с таким адресом уже существует
			{elseif $message_error=='name_empty'}
				У категории должно быть название
			{elseif $message_error=='url_empty'}
				URl адрес не может быть пустым
			{/if}
		</span>
		{if $smarty.get.return}
			<a class="button" href="{$smarty.get.return}">Вернуться</a>
		{/if}
	</div>
	<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method="post" id="product" enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">

		<input class="name" name="name" type="text" value="{$category->name|escape}" placeholder="Название категории" required>
		<input name="id" type="hidden" value="{$category->id|escape}"/>

		<div class="checkbox">
			<input id="active_checkbox" name="visible" value="1" type="checkbox"{if $category->visible} checked{/if}/>
			<label for="active_checkbox">Активна</label>
		</div>
	</div>

	<div id="product_categories">
		<select name="parent_id">
			<option value="0">Корневая категория</option>
			{function name=category_select level=0}
				{foreach $cats as $cat}
					{if $category->id != $cat->id}
						<option value="{$cat->id}" {if $category->parent_id == $cat->id}selected{/if}>
							{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$cat->name}
						</option>
						{category_select cats=$cat->subcategories level=$level+1}
					{/if}
				{/foreach}
			{/function}
			{category_select cats=$categories}
		</select>
	</div>

	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li>
					<label for="url" class="property">Адрес</label>
					<div class="page_url">/catalog/</div>
					<input id="url" name="url" class="page_url" type="text" value="{$category->url|escape}" required>
				</li>
				<li>
					<label for="h1" class="property">Заголовок H1</label>
					<input id="h1" name="h1" class="simpla_inp" type="text" value="{$category->h1|escape}"/>
				</li>
				<li>
					<label for="meta_title" class="property">Заголовок</label>
					<input id="meta_title" name="meta_title" class="simpla_inp" type="text" value="{$category->meta_title|escape}"/>
				</li>
				<li>
					<label for="meta_keywords" class="property">Ключевые слова</label>
					<input id="meta_keywords" name="meta_keywords" class="simpla_inp" type="text" value="{$category->meta_keywords|escape}"/>
				</li>

				<li>
					<label for="meta_description" class="property">Описание</label>
					<textarea id="meta_description" name="meta_description" class="simpla_inp">{$category->meta_description|escape}</textarea>
				</li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">
		<!-- Изображение категории -->
		<div class="block layer images">
			<h2>Изображение категории</h2>


			<input class="upload_image" name="image" type="file" accept="image/*">
			<input type="hidden" name="delete_image" value="">
			{if $category->image}
				<ul>
					<li>
						<a href="#" class="delete">
							<img src="design/images/cross-circle-frame.png">
						</a>
						<img src="../{$config->categories_images_dir}{$category->image}" alt=""/>
					</li>
				</ul>
			{/if}
		</div>

		<div class="block layer images">
			<h2>Иконка категории</h2>
			<span>
			Каталог иконок:	<a target="_blank" style="color: blue" href="https://fontawesome.com/icons?d=gallery">ссылка</a>
			</span> <br>
{*			<input type="text" class="trigger_change" id="e10_element_2" name="icon"   />*}
			<input style="margin-top: 5px" class="icp demo" value="{$category->icon}" name="icon" type="text">
			{if $category->icon}<span>Выбранная иконка: <i style="font-size: 18px" class="{$category->icon}"></i></span>{/if}

		</div>


	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<!-- Описагние категории -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="description" class="editor_large">{$category->description|escape}</textarea>
	</div>
	<!-- Описание категории (The End)-->
	<input class="button_green button_save" type="submit" name="save" value="Сохранить"/>

</form>
<!-- Основная форма (The End) -->
