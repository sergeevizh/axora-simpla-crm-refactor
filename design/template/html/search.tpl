{$meta_title = "Результаты поиска {$keyword}" scope=root}
<div class="container">
	<h1>Результаты поиска - {$keyword|escape}</h1>

	<div class="catalog-section">
		<div class="catalog-section__aside">
			{if $search_categories}
				<div class="form-check">
					<h3>Категории</h3>
					{foreach $search_categories as $search_category}
						<li>
							<a href="{$search_category->url}" class="catalog-section__sort-link ">{$search_category->name|escape}</a>
						</li>
					{/foreach}
				</div>
			{/if}
			{if $search_brands}
				<div class="form-check">
					<h3>Бренды</h3>
					{foreach $search_brands as $search_brand}
						<li>
							<a href="{$search_brand->url}" class="catalog-section__sort-link ">{$search_brand->name|escape}</a>
						</li>
					{/foreach}
				</div>
			{/if}
		</div>

		<div class="catalog-section__main">
			<div class="catalog-section__header">
				<div class="catalog-section__sort catalog-section__sort_right">
					<span class="catalog-section__sort-item" style="font-size: 18px">Найдено {$total_products_num} {$total_products_num|plural:'товар':'товаров':'товара'}</span>
				</div>
			</div>

			{* КАТАЛОГ *}
				{include file='partials/catalog_products.tpl' }
			{* КАТАЛОГ END	*}
		</div>
	</div>
</div>
