{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	<li class="active"><a href="index.php?module=BannersAdmin">Баннеры</a></li>
	{if in_array('tags', $manager->permissions)}<li><a href="index.php?module=TagsAdmin">Теги</a></li>{/if}
{/capture}
{if $banner->id}
	{$meta_title = $banner->name scope=root}
{else}
	{$meta_title = 'Новый баннер' scope=root}
{/if}
<style>
	#product .images li {
		width: 400px;
		position: relative;
	}
	#product .images li img{
		max-width: 400px;
	}
	#product .images a.delete {
		right: 5px;
		top: 5px;
	}
</style>

<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$banner->name|escape}" placeholder="название" required/>
		<input name=id type="hidden" value="{$banner->id|escape}"/>
		<div class="checkbox">
			<input name=visible value='1' type="checkbox" id="visible_checkbox"{if $banner->visible} checked{/if}/> <label for="visible_checkbox">Активен</label>
		</div>
	</div>

	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<ul>
				<li><label class=property>Ссылка</label><input name="link" class="simpla_inp" type="text" value="{$banner->link|escape}" /></li>
				<li><label class=property>Текст</label><input name="sub_text" class="simpla_inp" type="text" value="{$banner->sub_text|escape}" /></li>
				<li>
					<label class=property>Место:</label>
				</li>
				{foreach $types as $type => $id}
					<li>
						<label class="">
							<input name="type" type="radio" value="{$id}" {if $banner->type == $id} checked{elseif !$banner->type && $id@first} checked{/if}> {$typesNames[$id]}
						</label>
					</li>
				{/foreach}
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->



		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">

		<!-- Изображение категории -->
		<div class="block layer images">
			<h2>Изображение баннера</h2>
			<input class='upload_image' name=image type=file>
			<input type=hidden name="delete_image" value="">
			{if $banner->image}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->banners_dir}{$banner->image}" alt="" />
				</li>
			</ul>
			{/if}
			<small>
{*				<p><b>Рекомендации к размерам:</b></p>
				<p>Основной слайдер 950px х 310px</p>
				<p>Дополнительные на главной 445px х 145px</p>*}
			</small>
		</div>

	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<input class="button_green button_save" type="submit" name="" value="Сохранить" />


</form>
<!-- Основная форма (The End) -->

