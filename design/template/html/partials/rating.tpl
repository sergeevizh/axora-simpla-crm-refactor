<div class="rating-area">
    <div class="js-rating-msg alert alert-primary" role="alert" style="display: none;"></div>
    <div class="rating" >


        {if $is_product_page}
            {$rating=$in_product_page_rating}

        {elseif $is_product_card}
            {$rating=$product->rating}
        {/if}

        <input type="hidden"
               class="rating__value"
               name="productRating"
               data-product-id="{$product->id}"
               {if $user}data-user-id="{$user->id}"{/if}
               value="{$rating}">
        {for $i=1 to 5}
            <button type="button" class="rating__star rating__star_{$i} {if $i == $rating}is-active{/if}" data-value="{$i}"></button>
        {/for}
        <div class="rating__bg">
            <span style="width: 0"></span>
        </div>
    </div>
</div>