{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	{if in_array('banners', $manager->permissions)}<li><a href="index.php?module=BannersAdmin">Баннеры</a></li>{/if}
	<li class="active"><a href="index.php?module=TagsAdmin">Теги</a></li>
{/capture}

{if $tag->id}
{$meta_title = $tag->name scope=root}
{else}
{$meta_title = 'Новый тег' scope=root}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<style>
.autocomplete-w1 {position:absolute; top:0px; left:0px; margin:6px 0 0 6px; /* IE6 fix: */ _background:none; _margin:1px 0 0 0; }
.autocomplete { border:1px solid #999; background:#FFF; cursor:default; text-align:left; overflow-x:auto; min-width: 300px; overflow-y: auto; margin:-6px 6px 6px -6px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; }
.autocomplete .selected { background:#F0F0F0; }
.autocomplete div { padding:2px 5px; white-space:nowrap; }
.autocomplete strong { font-weight:normal; color:#3399FF; }
</style>
<script>
$(function() {


	// Автозаполнение мета-тегов
	meta_title_touched = true;
	meta_keywords_touched = true;
	meta_description_touched = true;
	url_touched = true;

	if($('input[name="meta_title"]').val() == generate_meta_title() || $('input[name="meta_title"]').val() == '')
		meta_title_touched = false;
	if($('input[name="meta_keywords"]').val() == generate_meta_keywords() || $('input[name="meta_keywords"]').val() == '')
		meta_keywords_touched = false;
	if($('textarea[name="meta_description"]').val() == generate_meta_description() || $('textarea[name="meta_description"]').val() == '')
		meta_description_touched = false;
	if($('input[name="url"]').val() == generate_url() || $('input[name="url"]').val() == '')
		url_touched = false;

	$('input[name="meta_title"]').change(function() { meta_title_touched = true; });
	$('input[name="meta_keywords"]').change(function() { meta_keywords_touched = true; });
	$('textarea[name="meta_description"]').change(function() { meta_description_touched = true; });
	$('input[name="url"]').change(function() { url_touched = true; });

	$('input[name="name"]').keyup(function() { set_meta(); });
	$('select[name="brand_id"]').change(function() { set_meta(); });
	$('select[name="categories[]"]').change(function() { set_meta(); });

});

function set_meta()
{
	if(!meta_title_touched)
		$('input[name="meta_title"]').val(generate_meta_title());
	if(!meta_keywords_touched)
		$('input[name="meta_keywords"]').val(generate_meta_keywords());
	if(!meta_description_touched)
	{
		descr = $('textarea[name="meta_description"]');
		descr.val(generate_meta_description());
		descr.scrollTop(descr.outerHeight());
	}
	if(!url_touched)
		$('input[name="url"]').val(generate_url());
}

function generate_meta_title()
{
	name = $('input[name="name"]').val();
	return name;
}

function generate_meta_keywords()
{
	name = $('input[name="name"]').val();
	return name;
}

function generate_meta_description()
{
	// if(typeof(tinyMCE.get("annotation")) =='object')
	// {
	// 	description = tinyMCE.get("annotation").getContent().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
	// 	return description;
	// }
	// else
	// 	return $('textarea[name=annotation]').val().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
}

function generate_url()
{
	url = $('input[name="name"]').val();
	url = url.replace(/[\s]+/gi, '-');
	url = translit(url);
	url = url.replace(/[^0-9a-z_\-]+/gi, '').toLowerCase();
	return url;
}

function translit(str)
{
	var ru=("А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я").split("-")
	var en=("A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch-'-'-Y-y-'-'-E-e-YU-yu-YA-ya").split("-")
 	var res = '';
	for(var i=0, l=str.length; i<l; i++)
	{
		var s = str.charAt(i), n = ru.indexOf(s);
		if(n >= 0) { res += en[n]; }
		else { res += s; }
    }
    return res;
}

</script>
{/literal}

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'added'}Запись добавлена{elseif $message_success == 'updated'}Запись обновлена{/if}</span>

	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error == 'url_exists'}Запись с таким адресом уже существует{/if}</span>
	{if $smarty.get.return}
		<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
	</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$tag->name|escape}" required/>
		<input name=id type="hidden" value="{$tag->id|escape}"/>
		<div class="checkbox">
			<input name=visible value="1" type="checkbox" id="active_checkbox"{if $tag->visible} checked{/if}/>
			<label for="active_checkbox">Активен</label>
		</div>
	</div>

	<!-- Левая колонка свойств товара -->
	<div id="column_left">


		<div class="block layer">
			<h2>Параметры страницы</h2>
		<!-- Параметры страницы -->
			<ul>
				<li><label class=property>Адрес</label><input name="url" class="" type="text" value="{$tag->url|escape}" required/></li>
				<li><label class=property>H1</label><input name="h1" type="text" value="{$tag->h1|escape}" required/></li>
{*
				<li>
					<label class=property>
						 Показывать в меню
					</label>
					<input name=in_menu value="1" type="checkbox"{if $tag->in_menu} checked{/if}/>
				</li>
				<li><label class=property>Название в меню</label><input name="in_menu_name" type="text" value="{$tag->in_menu_name|escape}" /></li>
*}

{*				<li>
					<label class=property>
						 Показывать в категории
					</label>
					<input name=in_category value="1" type="checkbox"{if $tag->in_category} checked{/if}/>
				</li>*}
{*				<li><label class=property>Название в категории</label><input name="in_category_name" type="text" value="{$tag->in_category_name|escape}" /></li>*}
{*
				<li><label class=property>Адрес</label> <div class="page_url">/</div> <input name="url" class="page_url" type="text" value="{$tag->url|escape}"/></li>
*}
				<li><label class=property>Заголовок</label><input name="meta_title" type="text" value="{$tag->meta_title|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords"  type="text" value="{$tag->meta_keywords|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description">{$tag->meta_description|escape}</textarea></li>
{*				<li>
					{$sorts = [
						'position'=>'по умолчанию',
						'views'=>'по просмотрам',
						'likes'=>'по избранным',
						'orders'=>'по заказам',
						'created'=>'по дате создания',
						'budget'=>'по рейтингу',
						'name'=>'по названию',
						'cheap'=>'по цене, сначала дешевые',
						'expensive'=>'по цене, сначала дорогие'
					]}
					<label class="property">Сортировка</label>
					<select name="sort">
						<option value="">Не выбрана</option>
						{foreach $sorts as $sort => $name}
							<option value="{$sort}"{if $tag->sort == $sort} selected{/if}>{$name}</option>
						{/foreach}
					</select>
				</li>*}
				<li>
					<label class=property>Категория</label>
					<select name="category_id">
					{function name=category_select level=0}
						{foreach from=$categories item=category}
							<option value='{$category->id}' {if $category->id == $tag->category_id}selected{/if}>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
							{category_select categories=$category->subcategories level=$level+1}
						{/foreach}
					{/function}
					{category_select categories=$categories}
					</select>
				</li>
{*				<li class="layer">
					<label class=property>Ключевое слово</label>
					<input name="keywords" type="text" value="{$tag->keywords|escape}" />
				</li>*}
				{if $features}
					{foreach $features as $f}
						{if $f->options && !$f->is_slider}
							<li class="layer">
								<label class=property>{$f->name}</label>
								<ul class="features">
									{foreach $f->options as $o}
										<li>
											<label><input type="checkbox" name="features[{$f->id}][]" value="{$o->value|escape}"{if $tag->features[$f->id] && in_array($o->value, $tag->features[$f->id])} checked{/if}> {$o->value|escape}</label>
										</li>
									{/foreach}
								</ul>
							</li>
						{/if}
					{/foreach}
				{/if}
{*				<li class="layer">*}
{*					<label class=property>Цена</label>*}
{*					от <input style="width:70px;" name="settings[min_price]" type="text" value="{$tag->settings.min_price|escape}" /> до <input style="width: 70px;" name="settings[max_price]" type="text" value="{$tag->settings.max_price|escape}" />*}
{*				</li>*}
				{if $colors}
				<li class="layer">
					<label class=property>Цвета</label>
					<ul class="features">
						{foreach from=$colors item=color}
						<li>
							<label><input type="checkbox" name="colors[{$color->id}]" value="{$color->id}"{if $tag->colors && in_array($color->id, $tag->colors)} checked{/if}> {$color->name|escape} <span style="background: #{$color->color};	width: 14px;height: 14px;display: inline-block;"></span></label>
						</li>
						{/foreach}
					</ul>
				</li>
				{/if}

				<li class="layer">
					<label class=property>Бренд</label>
					<ul class="features">
						{foreach from=$brands item=brand}
							<li>
								<label><input type="checkbox" name="brands[]" value="{$brand->id|escape}"{if $tag->brands && in_array($brand->id, $tag->brands)} checked{/if}> {$brand->name|escape}</label>
							</li>
						{/foreach}
					</ul>
				</li>
			</ul>

		</div>
		<!-- Параметры страницы (The End)-->



	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">

	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<div class="block">
		<h2>Описание</h2>
		<textarea name="description" class='editor_large'>{$tag->description|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->

	<input class="button_green button_save" type="submit" name="" value="Сохранить" />

</form>
<!-- Основная форма (The End) -->
{literal}
<style>
li .features {
  float: left;
  width: 265px;
  max-height: 200px;
  overflow-y: scroll;
  overflow-x: hidden;
}
#product .block li .features li {
	width: 260px;
}
</style>
{/literal}
