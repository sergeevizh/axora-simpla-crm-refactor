<div class="{if $is_product_page}product__stickers{else}item__stickers{/if}">
{*    {$url = $is_product_page ? $category[0]->path->url : $product->type->url}*}


    {if $is_product_page }
        {$url =  $category->path->url}
    {else}
        {$url =  $product->type->url}
    {/if}


    {if $product->new == 1 }
        <a href="" class="sticker">new</a>
    {/if}

    {foreach $product->variants as $v}
        {if $v->compare_price > 0}
            <a href="/catalog/{$url}/?discounted=1" class="sticker">Sale</a>
            <a href="/catalog/{$url}/?discounted=1" class="sticker"> {round(100 - ({$v->price|convert} * 100 / {$v->compare_price|convert})  )  }%</a>
        {/if}
        {break}
    {/foreach}

    {if $product->featured == 1}
        <a href="" class="sticker">Hit</a>
    {/if}
</div>