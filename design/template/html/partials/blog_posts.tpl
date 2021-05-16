{foreach $posts as $post}
<div class="col-sm-6 col-lg-4">
    <div class="exceprt">
        <a title="{$post['name']}" class="exceprt__image" style="background-image: url('{if $post['image']}{asset($post['image'])}{else}{'design/template/images/no-image.png'}{/if}')"></a>
        <h3 class="exceprt__title">
            <a href="{$config->root_url}/blog/{$post['url']}">{$post['name']}</a>
        </h3>
		<div class="exceprt__text">{$post['annotation']}</div>
        <div class="exceprt__date">{$post['date']}</div>
    </div>
</div>
{/foreach}