<div class="item">
    <div class="item__content">
        <div class="item__header">
            {include file="partials/stickers.tpl"}

            <div class="item__control">
                <button type="button" data-product-id="{$product['id']}" class="favorite-btn js-add-favorites js-favorites-{$product['id']} {if $favorites &&  in_array($product['id'], $favorites)}is-active{/if} "><i class="fal fa-heart"></i></button>

                {* если находимся на странице сравнения *}
                {if $is_compares}
                    <button type="button" class="remove-btn js-remove-compare js-remove-compare-{$product['id']}  {if in_array($product['id'], $compares)} is-active{/if}" data-product-id="{$product['id']}"><i class="fal fa-times"></i></button>
                {else}
                    <button type="button" class="compare-btn js-add-compare js-add-compare-{$product['id']} {if in_array($product['id'], $compares)} is-active{/if}" data-product-id="{$product['id']}"><i class="fal fa-balance-scale "></i></button>
                {/if}
            </div>
        </div>

        <a href="{$config->root_url}/products/{$product['url']}" class="item__image-field">
            {if $product['image']}
                <img src="{$product['image']->filename|resize:242:230}" class="item__image" alt="{$product['name']}">
            {else}
                <img src="{asset('no-image.png')}" class="item__image" alt="{$product['name']}">
            {/if}
        </a>

        <div class="item__meta">
            <div class="meta">
                <div class="meta__item">
                    {include file="{$config->root_url}partials/rating.tpl" is_product_card=true}
                </div>

                {*
                <div class="meta__item">
                    <span class="comment-count"><i class="fal fa-comment"></i> <span class="comment-count__val">0</span></span>
                </div>
                *}

                <div class="meta__item"> {if $product->variant->sku}Арт. {$product->variant->sku}{/if} </div>
            </div>
        </div>

        <div class="item__title">
            <a href="products/{$product->url}">{$product['name']}</a>
        </div>
    </div>

    <form action="cart" class="js-submit-to-cart">
        <div class="item__footer">
            <div class="item__price">
                <div class="price">
                    {foreach $product->variants as $v}
                        {if $v->compare_price > 0}
                            <div class="price__old">{$v->compare_price|convert}</div>
                            <div class="price__discount">{round($v->price - $v->compare_price)} {$currency->sign|escape}</div>
                        {/if}

                        <div class="price__value">{$v->price|convert} {$currency->sign|escape}</div>
                        {break}
                    {/foreach}
                </div>
            </div>

            <div class="item__btn-field">
                {if $product->variants|count == 1}
                    {if $product->variant->stock == 0}
                        <span class="item__btn-not-allow">Нет в наличии</span>
                    {else}
                        <input type="hidden" name="variant" value="{$product->variant->id}">
                        <button {if $product->variant->infinity == 1}  data-toggle="tooltip" data-html="true" title="Под заказ" {/if} type="submit" class="basket-btn btn btn-light {if in_array($product->variant->id, $cart_product_ids)}is-active{/if}">
                            <i class="fal fa-shopping-cart"></i>
                            {if $product->variant->infinity == 1}
                                <span class="basket-btn__text">Под заказ</span>
                            {/if}
                        </button>
                    {/if}
                {elseif $product->variants|count > 1 }
                    <a href="products/{$product->url}" class="btn btn-light"><i class="fal fa-info-circle"></i></a>
                {/if}
            </div>
        </div>
    </form>
</div>

{*
{foreach $product->variants as $v}
    <tr class="variant">
        <td>
            <input id="new_{$v->id}" name="variant" value="{$v->id}" type="radio" class="variant_radiobutton" {if $v@first}checked{/if} {if $product->variants|count<2}style="display:none;"{/if}/>
        </td>
        <td>
            {if $v->name}<label class="variant_name" for="new_{$v->id}">{$v->name}</label>{/if}
        </td>
        <td>
            {if $v->compare_price > 0}<span class="compare_price">{$v->compare_price|convert}</span>{/if}
            <span class="price">{$v->price|convert} <span class="currency">{$currency->sign|escape}</span></span>
        </td>
    </tr>
{/foreach}
*}
