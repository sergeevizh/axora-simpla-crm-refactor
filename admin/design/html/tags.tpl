{capture name=tabs}
	{if in_array('products', $manager->permissions)}
		<li>
			<a href="index.php?module=ProductsAdmin">Товары</a>
		</li>
	{/if}
	{if in_array('categories', $manager->permissions)}
		<li>
			<a href="index.php?module=CategoriesAdmin">Категории</a>
		</li>
	{/if}
	<li >
		<a href="index.php?module=BrandsAdmin">Бренды</a>
	</li>
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
	{if in_array('tags', $manager->permissions)}<li class="active"><a href="index.php?module=TagsAdmin">Теги</a></li>{/if}

{/capture}

{* Title *}
{$meta_title='Теги' scope=root}
<style>
	.b_views {
		border: 2px solid blue;
		width: 16px;
		padding: 0 2px;
		display: inline-block;
		text-align: center;
		font-size: 10px;
		height:16px;
		line-height: 16px;
	}
</style>
{* Заголовок *}
<div id="header">
	<h1>Теги</h1>
	<a class="add" href="{url module=TagAdmin return=$smarty.server.REQUEST_URI}">Добавить тег</a>
</div>


<div id="main_list" class="brands">
{if $tags}
	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list" class="brands">
			{foreach $tags as $tag}
			<div class="row{if !$tag->visible} invisible{/if}">
				<input type="hidden" name="positions[{$tag->id}]" value="{$tag->position}">
				<div class="move cell">
					<div class="move_zone"></div>
				</div>
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$tag->id}" />
				</div>
				<div class="cell">
					<a href="{url module=TagAdmin id=$tag->id return=$smarty.server.REQUEST_URI}">{$tag->name|escape}</a>
				</div>
				<div class="icons cell">
					{*<span class="b_views">{$tag->views}</span>*}
					<a class="enable" title="Активен" href="#"></a>
					<a class="preview" title="Предпросмотр в новом окне" href="../{$tag->url}" target="_blank"></a>
					<a class="delete"  title="Удалить" href="#"></a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>

		<div id="action">
			<label id="check_all" class="dash_link">Выбрать все</label>

			<span id="select">
				<select name="action">
					<option value="delete">Удалить</option>
				</select>
			</span>
			<input id="apply_action" class="button_green" type="submit" value="Применить">
		</div>

	</form>
{else}
Нет тегов
{/if}
</div>
<div id="right_menu">

	<!-- Категории товаров -->
	{function name=categories_tree}
	{if $categories}
	<ul>
		{if $categories[0]->parent_id == 0}
		<li {if !$category->id}class="selected"{/if}><a href="{url category_id=null}">Все категории</a></li>
		{/if}
		{foreach $categories as $c}
		<li category_id="{$c->id}" {if $category->id == $c->id}class="selected"{else}class="droppable category"{/if}><a href='{url keyword=null brand_id=null page=null category_id={$c->id}}'>{$c->name}</a></li>
		{categories_tree categories=$c->subcategories}
		{/foreach}
	</ul>
	{/if}
	{/function}
	{categories_tree categories=$categories}
	<!-- Категории товаров (The End)-->

</div>


{literal}
<script>
$(function() {

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();
	// Сортировка списка
	$("#list").sortable({
		items: ".row",
		tolerance: "pointer",
		handle: ".move_zone",
		scrollSensitivity: 40,
		opacity: 0.7,
		forcePlaceholderSize: true,
		axis: 'y',

		helper: function (event, ui) {
			if ($('input[type="checkbox"][name*="check"]:checked').size() < 1) return ui;
			var helper = $('<div/>');
			$('input[type="checkbox"][name*="check"]:checked').each(function () {
				var item = $(this).closest('.row');
				helper.height(helper.height() + item.innerHeight());
				if (item[0] != ui[0]) {
					helper.append(item.clone());
					$(this).closest('.row').remove();
				}
				else {
					helper.append(ui.clone());
					item.find('input[type="checkbox"][name*="check"]').attr('checked', false);
				}
			});
			return helper;
		},
		start: function (event, ui) {
			if (ui.helper.children('.row').size() > 0)
				$('.ui-sortable-placeholder').height(ui.helper.height());
		},
		beforeStop: function (event, ui) {
			if (ui.helper.children('.row').size() > 0) {
				ui.helper.children('.row').each(function () {
					$(this).insertBefore(ui.item);
				});
				ui.item.remove();
			}
		},
		update: function (event, ui) {
			$("#list_form input[name*='check']").attr('checked', false);
			$("#list_form").ajaxSubmit(function () {
				$("#list div.row").colorize();
			});
		}
	});
	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});

	// Удалить
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest("div.row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	// Показать товар
	$("a.enable").click(function () {
		var icon = $(this);
		var line = icon.closest("div.row");
		var id = line.find('input[type="checkbox"][name*="check"]').val();
		var state = line.hasClass('invisible') ? 1 : 0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {
				'object': 'tags',
				'id': id,
				'values': {'visible': state},
				'session_id': '{/literal}{$smarty.session.id}{literal}'
			},
			success: function (data) {
				icon.removeClass('loading_icon');
				if (state)
					line.removeClass('invisible');
				else
					line.addClass('invisible');
			},
			dataType: 'json'
		});
		return false;
	});
	// Подтверждение удаления
	$("form").submit(function() {
		if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
			if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
				return false;
	});

});
</script>
{/literal}
