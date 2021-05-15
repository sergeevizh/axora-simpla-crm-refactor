{* Список записей блога *}

{* Канонический адрес страницы *}
{$canonical="/blog" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">
			<a href="/blog/" class="breadcrumbs__link">{$page->name}</a>

		</li>
	</ul>

	<h1>{$page->name}</h1>

	{if $posts}
		<div class="row">
			{include file="partials/blog_posts.tpl"}
		</div>
	{else}
		<p>Новостей нету</p>
	{/if}

	{include file='partials/pagination.tpl'}
</div>
