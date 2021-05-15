{* Страница отдельной записи блога *}

{* Канонический адрес страницы *}
{$canonical="/blog/{$post->url}" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="/" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">
			<a href="/blog/" class="breadcrumbs__link">Новости</a>
		</li>

		<li class="breadcrumbs__item">
			{$post->name|escape}
		</li>
	</ul>

	<div class="text" style="max-width: 800px;">
		<h1 data-post="{$post->id}">{$post->name|escape}</h1>
		<p class="date">{$post->date|date}</p>
		{if $post->image}
			<img src="{$config->blog_images_dir}{$post->image}" alt="{$post->name|escape}" title="{$post->name|escape}">
		{/if}
		<!-- Тело поста /-->
		{$post->text}
	</div>

	<div class="article-pagination">
		{if $prev_post->url }
			<a style="float: left;" href="blog/{$prev_post->url}"><i class="fal fa-long-arrow-left"></i> {$prev_post->name}</a>
		{/if}

		{if $next_post->url }
		<a style="float: right;"  href="blog/{$next_post->url}">  {$next_post->name} <i class="fal fa-long-arrow-right"> </i></a>
		{/if}
	</div>
</div>