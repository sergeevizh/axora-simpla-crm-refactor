<div class="sidebar-filter-block js-sidebar-filter-block">
    <button type="button" class="sidebar-filter-block__close close js-sidebar-filter-block-close"></button>
    <div class="sidebar-filter-block__content">
        <div class="sidebar-filter">
            <div class="sidebar-filter__header">
                {if $category}
                    <div class="sidebar-filter__section sidebar-filter__section_category">
                        <div class="filter-category js-filter-category">
                            {if $type == 'featured'}
                                <button class="filter-category__link js-filter-category-link is-active">Хиты</button>
                            {else}
                                <a href="{url page=null _=null type=featured}" class="filter-category__link js-filter-category-link">Хиты</a>
                            {/if}
                            {if $type == 'actions'}
                                <button class="filter-category__link js-filter-category-link is-active">Акции</button>
                            {else}
                                <a href="{url page=null _=null type=actions}" class="filter-category__link js-filter-category-link">Акции</a>
                            {/if}
                            {if !$type}
                                <button class="filter-category__link js-filter-category-link is-active">Все</button>
                            {else}
                                <a href="{$config->root_url}/catalog/{$category->url}" class="filter-category__link js-filter-category-link">Все</a>
                            {/if}
                        </div>
                    </div>
                {/if}

                <div class="sidebar-filter__section sidebar-filter__section_sort">
                    <div class="filter filter_mob-fix js-filter-sort-section">

                        {$sorts = [
                        'position'=>'по популярности',
                        'budget'=>'по рейтингу',
                        'name'=>'по названию',
                        'cheap'=>'по цене, сначала дешевые',
                        'expensive'=>'по цене, сначала дорогие'
                        ]}

                        <div class="filter__header">
                            <span class="filter__title">Сортировать</span>
                            <span class="filter__header-info js-filter-sort-section-info">{$sorts[$sort]}</span>
                        </div>

                        <div class="filter__content">
                            <button type="button" class="filter__content-close close"></button>
                            <div class="filter__content-inner">
                                <div class="filter-sort js-filter-sort">
                                    <div class="filter-sort__title">{$sorts[$sort]}</div>
                                    <div class="filter-sort__dropdown">
                                        {foreach $sorts as $key=>$name}
                                            <div class="filter-sort__dropdown-item">
                                                <a href="{url sort=$key page=null}" class="filter-sort__dropdown-link{if $sort == $key} is-active{/if}">{$name}</a>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {$filter_visible = false}
            <form id="filter" action="{$config->root_url}/catalog/{$category->url}" method="get" class="js-filter-form">


                {if $category->brands|count > 1}
                    {$filter_visible = true}
                    {$brand_selected = []}
                    {if $smarty.get.brand}
                        {$brand_selected = array_values($smarty.get.brand)}
                    {/if}
                    <div class="sidebar-filter__section">
                        <div class="filter filter_mob-fix js-filter-brands-section">
                            <div class="filter__header">
                                <span class="filter__title">Бренды</span>
                                <span class="filter__header-info js-filter-brands-section-info">{foreach $category->brands as $b}{if in_array($b->id, $brand_selected)}{$b->name|escape}, {/if}{/foreach}</span>
                            </div>
                            <div class="filter__content">
                                <button type="button" class="filter__content-close close"></button>
                                <div class="filter__content-inner">
                                    <div class="filter-check-list js-filter-check-list">


                                        {foreach $category->brands as $b}
                                            <div class="filter-check-list__row">
                                                <div class="check-row">
                                                    <label>
                                                        <input type="checkbox" class="js-styler js-filter-brands-checkbox" name="brand[]" value="{$b->id}" {if in_array($b->id, $brand_selected)} checked{/if}>
                                                        <span class="check-row__text">{$b->name|escape}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>

                                    {*
                                    <div class="popup-window filter-check-list-all js-filter-check-list-all" id="filter-check-list-all">
                                        {foreach $category->alf_brands as $alf => $brands}
                                        <div class="filter-check-list">
                                            <div class="filter-check-list__letter">{$alf}</div>
                                            {foreach $brands as $b}
                                            <div class="filter-check-list__row">
                                                <div class="check-row">
                                                    <label>
                                                        <input type="checkbox" class="js-styler js-filter-brands-checkbox" name="brand_1">
                                                        <span class="check-row__text">{$b->name|escape}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            {/foreach}
                                        </div>
                                        {/foreach}
                                    </div>
                                    <a href="#" class="filter-check-list-more-link js-more-brands-link">Все 18 вариантов</a>*}

                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                {if ($prices_ranges->min_price < $prices_ranges->max_price) && $prices_ranges->max_price > 1}
                    {$filter_visible = true}
                    <div class="sidebar-filter__section">
                        <div class="filter filter_mob-fix js-filter-price-section">
                            <div class="filter__header">
                                <span class="filter__title">Цена<span class="mobile-hidden">, {$currency->sign|escape}</span></span>
                                <span class="filter__header-info js-filter-price-section-info">
									от <span class="js-filter-range-section-info-min">{if $smarty.get.min_price}{$smarty.get.min_price}{else}{$prices_ranges->min_price|convert:null:false|floor}{/if}</span>
									до <span class="js-filter-range-section-info-max">{if $smarty.get.max_price}{$smarty.get.max_price}{else}{$prices_ranges->max_price|convert:null:false|ceil}{/if}</span> {$currency->sign|escape}
								</span>
                            </div>
                            <div class="filter__content">
                                <button type="button" class="filter__content-close close"></button>
                                <div class="filter__content-inner">
                                    <div class="filter-range js-filter-price">
                                        <div class="filter-range__val">
                                            <span class="filter-range__val-min js-filter-price-min"></span>
                                            <span class="filter-range__val-line">—</span>
                                            <span class="filter-range__val-max js-filter-price-max"></span>
                                        </div>
                                        <div class="filter-range__content">
                                            <div class="filter-range__main">
                                                <div class="range js-filter-price-range" data-min="{$prices_ranges->min_price|convert:null:false|floor}" data-max="{$prices_ranges->max_price|convert:null:false|ceil}" data-values="[{if $smarty.get.min_price}{$smarty.get.min_price}{else}{$prices_ranges->min_price|convert:null:false|floor}{/if}, {if $smarty.get.max_price}{$smarty.get.max_price}{else}{$prices_ranges->max_price|convert:null:false|ceil}{/if}]" data-step="1"></div>
                                            </div>
                                            <span class="filter-range__content-min">от {$prices_ranges->min_price|convert:null:false|floor}</span>
                                            <span class="filter-range__content-max">до {$prices_ranges->max_price|convert:null:false|ceil}</span>
                                        </div>
                                        <input class="js-filter-input-price-min" type="hidden" name="min_price" value="{if $smarty.get.min_price}{$smarty.get.min_price}{else}{$prices_ranges->min_price|convert:null:false|floor}{/if}">
                                        <input class="js-filter-input-price-max" type="hidden" name="max_price" value="{if $smarty.get.max_price}{$smarty.get.max_price}{else}{$prices_ranges->max_price|convert:null:false|ceil}{/if}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                {*
                {if ($prices_ranges->min_price < $prices_ranges->max_price) && $prices_ranges->max_price > 1}
                    <div class="sidebar-filter__section">
                        <div class="filter filter_mob-fix js-filter-price-input-section">
                            <div class="filter__header">
                                <span class="filter__title">Цена<span class="mobile-hidden">, {$currency->sign|escape}</span></span>
                                <span class="filter__header-info js-filter-price-input-section-info">
                                    от <span class="js-filter-price-input-section-info-min">{$prices_ranges->min_price|convert:null:false|floor}</span>
                                    до <span class="js-filter-price-input-section-info-max">{$prices_ranges->max_price|convert:null:false|ceil}</span>
                                    {$currency->sign|escape}
                                </span>
                            </div>
                            <div class="filter__content">
                                <button type="button" class="filter__content-close close"></button>
                                <div class="filter__content-inner">
                                    <div class="filter-input-group">
                                        <div class="filter-input-group__item">
                                            <input name="prices[min]" type="text" class="filter-input-group__input js-filter-price-input-min" placeholder="от" value="{if $smarty.get.prices.min}{$smarty.get.prices.min}{else}{/if}">
                                        </div>
                                        <div class="filter-input-group__item">
                                            <input name="prices[max]" type="text" class="filter-input-group__input js-filter-price-input-max" placeholder="до" value="{if $smarty.get.rices.max}{$smarty.get.prices.max}{else}{/if}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                *}
                {if $features}
                    {$filter_visible = true}
                    {foreach $features as $key=>$f}
                        <div class="sidebar-filter__section">
                            <div class="filter filter_mob-fix js-filter-capacity-section">
                                <div class="filter__header">
                                    <span class="filter__title" data-feature="{$f->id}">{$f->name}</span>
                                    <span class="filter__header-info js-filter-capacity-section-info">{$smarty.get.$key}</span>
                                </div>
                                <div class="filter__content">
                                    <button type="button" class="filter__content-close close"></button>
                                    <div class="filter__content-inner">
                                        <select class="filter-select js-styler js-filter-capacity" name="{$f->id}" {* TODO dev onchange="location = this.value"*}>
                                            <option value=""{if !$smarty.get.$key} selected{/if}>Все</option>
                                            {foreach $f->options as $o}
                                                <option value="{$o->value}"{if $smarty.get.$key == $o->value} selected{/if}>{$o->value|escape}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                {* TODO dev
                <div class="sidebar-filter__section">
                    <div class="filter">
                        <div class="filter__header">
                            <span class="filter__title">Параметр с 2 вариантами</span>
                        </div>
                        <div class="filter__content">
                            <button type="button" class="filter__content-close close"></button>
                            <div class="filter__content-inner">
                                <div class="filter-check-group">
                                    <div class="filter-check-group__item">
                                        <label class="filter-check-group__label">
                                            <input type="radio" name="param" class="filter-check-group__input">
                                            <span class="filter-check-group__text">Да</span>
                                        </label>
                                    </div>
                                    <div class="filter-check-group__item">
                                        <label class="filter-check-group__label">
                                            <input type="radio" name="param" class="filter-check-group__input">
                                            <span class="filter-check-group__text">Нет</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                *}
                {* TODO dev

                <div class="sidebar-filter__section">
                    <div class="filter filter_mob-fix js-filter-colors-section">
                        <div class="filter__header">
                            <span class="filter__title">Цвет</span>
                            <span class="filter__header-info filter-colors-info js-filter-colors-section-info">
                                                            <span class="filter-colors__color" style="background-color: #fff; border: 1px solid #b2b2b2;"></span><span class="filter-colors__color" style="background-color: #be2828;"></span><span class="filter-colors__color" style="background-color: #268e88;"></span><span class="filter-colors__color" style="background-color: #9e59d3;"></span>
                                                        </span>
                        </div>
                        <div class="filter__content">
                            <button type="button" class="filter__content-close close"></button>
                            <div class="filter__content-inner">
                                <div class="filter-colors js-filter-colors">
                                    <div class="filter-colors__content">
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input" checked>
                                            <span class="filter-colors__color" style="background-color: #fff; border: 1px solid #b2b2b2;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input" checked>
                                            <span class="filter-colors__color" style="background-color: #be2828;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input" checked>
                                            <span class="filter-colors__color" style="background-color: #268e88;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input" checked>
                                            <span class="filter-colors__color" style="background-color: #9e59d3;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #5dc3d2;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #42812d;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #577eed;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #622b2b;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #000000;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #ffa200;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #002466;"></span>
                                        </label>
                                        <label class="filter-colors__item">
                                            <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                            <span class="filter-colors__color" style="background-color: #b3b3b3;"></span>
                                        </label>
                                    </div>

                                    <div class="filter-colors__all js-filter-colors-all">
                                        <div class="filter-colors__content">
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #fff; border: 1px solid #b2b2b2;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #be2828;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #268e88;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #9e59d3;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #5dc3d2;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #42812d;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #577eed;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #622b2b;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #000000;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #ffa200;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #002466;"></span>
                                            </label>
                                            <label class="filter-colors__item">
                                                <input type="checkbox" class="filter-colors__input js-filter-colors-input">
                                                <span class="filter-colors__color" style="background-color: #b3b3b3;"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <a href="#" class="filter-colors__more-link js-filter-colors-all-link">Показать все цвета</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                *}

                {* TODO dev
                <div class="sidebar-filter__section">
                    <div class="filter">
                        <div class="filter__header">
                            <span class="filter__title">Размер</span>
                            <span class="filter__header-info"></span>
                        </div>
                        <div class="filter__content">
                            <button type="button" class="filter__content-close close"></button>
                            <div class="filter__content-inner">
                                <div class="filter-check">
                                    <label class="filter-check__item">
                                        <input type="checkbox" class="filter-check__input">
                                        <span class="filter-check__text">S</span>
                                    </label>
                                    <label class="filter-check__item">
                                        <input type="checkbox" class="filter-check__input">
                                        <span class="filter-check__text">M</span>
                                    </label>
                                    <label class="filter-check__item">
                                        <input type="checkbox" class="filter-check__input">
                                        <span class="filter-check__text">L</span>
                                    </label>
                                    <label class="filter-check__item">
                                        <input type="checkbox" class="filter-check__input">
                                        <span class="filter-check__text">XL</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                *}

                {* TODO Паша - что это за ссылка
                    <a href="#" class="sidebar-filter__more-link">Дополнительные параметры</a>
                *}


                <a href="#" class="sidebar-filter__tooltip js-sidebar-filter-tooltip">
                    {include file="_filter_tooltip.tpl"}
                </a>
                {if $filter_visible}
                    <div class="align-center">
                        <button class="btn" type="submit">Подобрать</button>
                    </div>
                {/if}

            </form>
        </div>
    </div>

    <div class="sidebar-filter-block__footer">
        <a href="#" class="btn btn_border js-sidebar-filter-tooltip">
            {include file="_filter_tooltip.tpl"}
        </a>
    </div>
</div>
