<div class="catalog-section__content">
    <div class="catalog" data-ajax-products>
        {if $products && $products|count > 0}
            {foreach $products as $product}
                <div class="catalog__item">
                    {include file="partials/product.tpl" is_catalog=true}
                </div>
            {/foreach}
        {else}
            {if !$is_favorites}
                <p>Данный раздел наполняется! Приносим свои извинения.</p>
            {else}
                <p>Товаров нет</p>
            {/if}
        {/if}
    </div>

    {*  если не на странице мзбранных товаров  *}
    {if !$is_favorites}
        <div data-ajax-pagination>
            {include file='partials/pagination.tpl'}
        </div>
    {/if}
</div>
