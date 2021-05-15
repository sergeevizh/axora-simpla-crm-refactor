{* Вкладки *}
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
		<li class="active">
			<a href="index.php?module=BannersAdmin">Баннеры</a>
		</li>
	{/if}
	{if in_array('tags', $manager->permissions)}<li><a href="index.php?module=TagsAdmin">Теги</a></li>{/if}

{/capture}

{$meta_title='Баннеры' scope=root}

<div id="header">
	<h1>Баннеры</h1>
	<a class="add" href="{url module=BannerAdmin return=$smarty.server.REQUEST_URI}">Добавить баннер</a>
</div>

{if $banners}
<div id="main_list" class="banners">


	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list">
			{foreach $banners as $banner}
			<div class="{if $banner->visible}visible{/if} row">
				<input type="hidden" name="positions[{$banner->id}]" value="{$banner->position}">
				<div class="move cell"><div class="move_zone"></div></div>
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$banner->id}" />
				</div>
				<div class="cell">
					{if $banner->image}<img src="../files/sliders/mini/{$banner->image}" alt="">{/if}
					<a href="{url module=BannerAdmin id=$banner->id return=$smarty.server.REQUEST_URI}">{$banner->name|escape}</a>
					<small style="color: #a9adb4;">{$typesNames[$banner->type]}</small>
				</div>
				<div class="icons cell">

					<a title="Удалить" class="delete" href='#' ></a>
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



</div>
{else}
	Нет банннеров
{/if}





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
		items:             ".row",
		tolerance:         "pointer",
		handle:            ".move_zone",
		axis: 'y',
		scrollSensitivity: 40,
		opacity:           0.7,
		forcePlaceholderSize: true,

		helper: function(event, ui){
			if($('input[type="checkbox"][name*="check"]:checked').size()<1) return ui;
			var helper = $('<div/>');
			$('input[type="checkbox"][name*="check"]:checked').each(function(){
				var item = $(this).closest('.row');
				helper.height(helper.height()+item.innerHeight());
				if(item[0]!=ui[0]) {
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
 		start: function(event, ui) {
  			if(ui.helper.children('.row').size()>0)
				$('.ui-sortable-placeholder').height(ui.helper.height());
		},
		beforeStop:function(event, ui){
			if(ui.helper.children('.row').size()>0){
				ui.helper.children('.row').each(function(){
					$(this).insertBefore(ui.item);
				});
				ui.item.remove();
			}
		},
		update:function(event, ui)
		{
			$("#list_form input[name*='check']").attr('checked', false);
			$("#list_form").ajaxSubmit(function() {
				colorize();
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

	// Подтверждение удаления
	$("form").submit(function() {
		if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
			if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
				return false;
	});

});
</script>
{/literal}
