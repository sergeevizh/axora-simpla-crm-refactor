<table class="basket">
    <thead class="basket__header">
        <tr class="basket__header-row">
            <th class="basket__cell basket__cell_head basket__cell_title" colspan="2">Наименование товара</th>
            <th class="basket__cell basket__cell_head basket__cell_price">Цена</th>
            <th class="basket__cell basket__cell_head basket__cell_count">К‑во,&nbsp;шт.</th>
            <th class="basket__cell basket__cell_head basket__cell_total">Сумма,&nbsp;р.</th>
            <th class="basket__cell basket__cell_head basket__cell_remove">Удалить</th>
        </tr>
    </thead>

    <tbody class="basket__content">
        {foreach $cart->purchases as $purchase}
            <tr class="basket__item">
                <td class="basket__cell basket__cell_image">
                    {if $purchase->variant->attachment}
                        <a href="products/{$purchase->product->url}" class="basket__image-link">
                            <img src="{$purchase->variant->attachment|resize:75:75}" class="basket__image" alt="{$product->name|escape}">
                        </a>
                    {elseif $purchase->product->image}
                        <a href="products/{$purchase->product->url}" class="basket__image-link">
                            <img src="{$purchase->product->image->filename|resize:75:75}" class="basket__image" alt="{$product->name|escape}">
                        </a>
                    {/if}
                </td>

                <td class="basket__cell basket__cell_title">
                    <div class="basket__title">
                        <a href="products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
                    </div>
                    <div class="basket__subtitle">{$purchase->variant->name|escape}</div>
                </td>

                <td class="basket__cell basket__cell_price">
                    {if $purchase->variant->compare_price > 0}
                        <div class="basket__price-old">
                            {$purchase->variant->compare_price} {$currency->sign|escape}
                        </div>
                    {/if}
                    <div class="basket__price">{($purchase->variant->price)} {$currency->sign}</div>
                </td>

                <td class="basket__cell basket__cell_count">
                    <input
                            name="amounts[{$purchase->variant->id}]"
                            onchange="document.cart.submit();"
                            type="number"
                            class="form-control basket__count"
                            value="{$purchase->amount}"
                            min="1"
                            max="{$purchase->variant->stock}"
                    >
                </td>

                <td class="basket__cell basket__cell_total">
                    <div class="basket__total-price">
                        {($purchase->variant->price*$purchase->amount)}&nbsp;{$currency->sign}
                    </div>
                </td>

                <td class="basket__cell basket__cell_remove">
                    <a href="cart/remove/{$purchase->variant->id}" class="basket__remove close-btn"><i class="fal fa-times"></i></a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
