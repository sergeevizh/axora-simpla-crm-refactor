{if $brand}
<div class="page-text">
    <p>{$brand->description}</p>
</div>
{elseif $category}
    <div class="page-text">
        <p>{$category->description}</p>
    </div>
{elseif $tag}
    <div class="page-text">
        <p>{$tag->description}</p>
    </div>
{/if}