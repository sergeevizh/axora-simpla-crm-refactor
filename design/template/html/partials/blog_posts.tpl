{foreach $posts as $post}
<div class="col-sm-6 col-lg-4">
    <div class="exceprt">
        <a title="{$post->name|escape}" class="exceprt__image" style="background-image: url('{$config->blog_images_dir}{$post->image}')"></a>
        <h3 class="exceprt__title">
            <a href="blog/{$post->url}">{$post->name|escape}</a>
        </h3>
		<div class="exceprt__text">{$post->annotation}</div>
        <div class="exceprt__date">{$post->date|date}</div>
    </div>
</div>
{/foreach}