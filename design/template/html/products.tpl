{* Список товаров *}

{* Канонический адрес страницы *}
{if $category && $brand}
	{$canonical="/catalog/{$category->url}/{$brand->url}" scope=root}
{elseif $category}
	{$canonical="/catalog/{$category->url}" scope=root}
{elseif $brand}
	{$canonical="/brands/{$brand->url}" scope=root}
{elseif $keyword}
	{$canonical="/products?keyword={$keyword|escape}" scope=root}
{else}
	{$canonical="/products" scope=root}
{/if}

<div class="container">
	<!-- Хлебные крошки -->
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>

		{if $category}
			{foreach $category->path as $cat}
				<li class="breadcrumbs__item">
					<a href="catalog/{$cat->url}" class="breadcrumbs__link">{$cat->name|escape}</a>
				</li>
			{/foreach}
			{if $brand}
				<li class="breadcrumbs__item">
					<a href="catalog/{$cat->url}/{$brand->url}" class="breadcrumbs__link">{$brand->name|escape}</a>
				</li>
			{/if}
		{elseif $brand}
			<li class="breadcrumbs__item">
				<a href="brands/{$brand->url}" class="breadcrumbs__link">{$brand->name|escape}</a>
			</li>
		{elseif $keyword}
			<li class="breadcrumbs__item">Поиск</li>
		{/if}
	</ul>
	<!-- Хлебные крошки /-->

	{* Заголовок страницы *}
	{if $tag}
		<h1 class="page-title">{$tag->h1|escape}</h1>
	{elseif $keyword}
		<h1 class="b-page-title">Поиск {$keyword|escape}</h1>
	{elseif $page}
		<h1 class="b-page-title">{$page->name|escape}</h1>
	{elseif $category->h1}
		<h1 class="b-page-title">{$category->h1|escape}</h1>
	{elseif $category}
		<h1 class="b-page-title">{$category->name|escape}</h1>
	{elseif $brand->h1}
		<h1 class="b-page-title">{$brand->h1|escape}</h1>
	{elseif $brand}
		<h1 class="b-page-title">{$brand->name|escape}</h1>
	{/if}

	<div class="catalog-section">
		{* Фильтр *}
		{* если мы не в каталоге а на страницах определенных групп товаров (акции, новинки и тд.) выводим вместо фильтра категории в которых эти товары находятся *}
		{if ($type)}
			{if $results_categories}
				<div class="catalog-section__aside">
					<ul class="catalog-section__aside-list">
						{foreach $results_categories as $category}
							<li>
								<a href="/catalog/{$category->url}">{$category->name}</a><small> ({$category->products_count})</small>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		{*	если мы на странице бренда выводим вместо фильтра категории в которых эти товары находятся	*}
		{elseif ($brand)}
			{if $results_categories}
				<div class="catalog-section__aside">
					<ul class="catalog-section__aside-list">
						{foreach $results_categories as $category}
							<li>
								<a href="/catalog/{$category->url}">{$category->name}</a><small> ({$category->products_count})</small>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		{else}
			<div data-ajax-filter class="catalog-section__aside">
				<div class="filter-popup">
				    <div class="filter-popup__bg"></div>
				    <div class="filter-popup__inner">
				        <button type="button" class="filter-popup__close close-btn"><i class="fal fa-times"></i></button>
				        <div class="filter-popup__content">
							{include file="partials/filter.tpl"}
						</div>
				    </div>
				</div>
			</div>
		{/if}
		{* Фильтр *}

		<div class="catalog-section__main">
			{if !$type}
				<div class="catalog-section__header">
					<div class="catalog-section__filter-btn">
						<button type="button" class="filter-btn"><i class="fal fa-sliders-v"></i></button>
					</div>
					{* СОРТИРОВКА *}
					{include file='partials/sort.tpl'}
				</div>
			{/if}

			{* КАТАЛОГ *}
				{include file='partials/catalog_products.tpl' }
			{* КАТАЛОГ	END	*}
		</div>
	</div>

	{* ПРОСМОТРЕННЫЕ ТОВАРЫ *}
	{get_browsed_products var=browsed_products limit=10 }
	{if $browsed_products}
	    <section class="catalog-slider-section">
	        <h2 class="catalog-slider-section__title">Ранее просматривали</h2>
	        <div class="catalog-slider">
	            {foreach $browsed_products as $product}
		            <div class="catalog-slider__item">
		                {include file="partials/product.tpl"}
		            </div>
	            {/foreach}
	        </div>
	    </section>
	{/if}
	{* ПРОСМОТРЕННЫЕ ТОВАРЫ END *}

	{include file='partials/page_description.tpl'}
</div>
