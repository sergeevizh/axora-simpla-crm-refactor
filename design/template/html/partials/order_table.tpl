<table class="basket">
    <thead class="basket__header">
        <tr class="basket__header-row">
            <th class="basket__cell basket__cell_head basket__cell_title" colspan="2">Наименование товара</th>
            <th class="basket__cell basket__cell_head basket__cell_price">Цена</th>
            <th class="basket__cell basket__cell_head basket__cell_count">К‑во,&nbsp;шт.</th>
            <th class="basket__cell basket__cell_head basket__cell_total">Сумма,&nbsp;р.</th>
        </tr>
    </thead>

    <tbody class="basket__content">
        {foreach $purchases as $purchase}
            <tr class="basket__item">
                <td class="basket__cell basket__cell_image">
                    {if $purchase->product->image}
                        <a href="products/{$purchase->product->url}" class="basket__image-link">
                            <img src="{$purchase->product->image->filename|resize:75:75}" class="basket__image" alt="{$product->name|escape}">
                        </a>
                    {/if}
                </td>

                <td class="basket__cell basket__cell_title">
                    <div class="basket__title">
                        <a href="products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
                        {$purchase->variant->name|escape}
                    </div>
                </td>

                <td class="basket__cell basket__cell_price">
                    {if $purchase->variant->compare_price > 0}
                        <div class="basket__price-old">
                            {$purchase->variant->compare_price|convert} {$currency->sign|escape}
                        </div>
                    {/if}
                    <div class="basket__price">{($purchase->variant->price)|convert} {$currency->sign}</div>
                </td>

                <td class="basket__cell basket__cell_count">
                    <span>{$purchase->amount}</span>
                </td>

                <td class="basket__cell basket__cell_total">
                    <div class="basket__total-price">
                        {($purchase->variant->price*$purchase->amount)|convert}&nbsp;{$currency->sign}
                    </div>
                </td>

            </tr>
        {/foreach}
    </tbody>
</table>