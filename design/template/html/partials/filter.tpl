<div class="filter">
    <form class="filter__form js-filter"  action="catalog/{$category->url}" data-ajax-filter>
        <div class="filter__tags">
            <div class="filter__tags-title">Выбранные фильтры</div>
            <div class="filter__tags-content"></div>
            <button type="reset" class="filter__reset-btn btn btn-outline-secondary btn-sm">Очистить фильтр</button>
        </div>

        {* Вложенные категории *}
        {$category_sidebar = false}
        {if $category->subcategories}
            {$category_sidebar = $category}
        {elseif $category->path[count($category->path)-2]->subcategories}
            {$category_sidebar = $category->path[count($category->path)-2]}
        {elseif $category->path.0->subcategories}
            {$category_sidebar = $category->path.0}
        {/if}
        {if $category_sidebar}
            <div class="filter__section is-open">
                <div class="filter__section-title">{$category_sidebar->name|escape}</div>
                <div class="filter__section-content">
                    {foreach $category_sidebar->subcategories as $s}
                        {if $s->visible}
                            <div class="form-check">
                                <a class="{if isset($category) && $s->id == $category->id}link is-active{/if} " href="{$config->root_url}/catalog/{$s->url}">
                                    <span>{$s->name|escape}{if isset($s->products_count)} <span class="b-filter__count">{$s->products_count|escape}</span>{/if}</span>
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        {/if}
        {* Вложенные категории END *}

        {* БРЕНДЫ *}
        {if $category->brands}
        <div class="filter__section is-open">
            <div class="filter__section-title">Производитель</div>
            <div class="filter__section-content">
                {foreach $category->brands as $b}
                <div class="form-check">
                    <label class="form-check__label">
                        <input type="checkbox" class="form-check__input js-filter-checkbox"  id="b-{$b->id}"{if $b->checked} checked{/if} name="b[]" value="{$b->id}">
                        <span class="form-check__text"><span class="filter__check-text">{$b->name|escape}</span>
                        {*<small>(218)</small>*}
                        </span>
                    </label>
                </div>
                {/foreach}
            </div>
        </div>
        {/if}
        {* БРЕНДЫ END *}

        {* АКЦИИ *}
        <div class="filter__section is-open">
            <div class="filter__section-content">
                <div class="form-check">
                    <label class="form-check__label">
                        <input type="checkbox" name="discounted" value="1" class="form-check__input js-filter-checkbox"  id="discounted" {if $filter['discounted']} checked{/if}">
                        <span class="form-check__text"><span class="filter__check-text">только акционные</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        {* АКЦИИ  END *}

        {* PRICE_RANGE *}
         <div class="filter__section is-open">
            <div class="filter__section-title">Цена, руб.</div>
            <div class="filter__section-content">
                <div class="js-filter-range" id="r01">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="filterPriceStart" style="margin: 0;">от</label>
                            <input {if isset($smarty.get.min_price)} value="{$smarty.get.min_price}" {/if}  type="text" class="form-control form-control-sm js-integar-input js-filter-range-start" id="filterPriceStart" name="min_price" data-min="{$min_price}"  required>
                        </div>
                        <div class="form-group col-6">
                            <label for="filterPriceEnd" style="margin: 0;">до</label>
                            <input {if isset($smarty.get.max_price)} value="{$smarty.get.max_price}" {/if} type="text" class="form-control form-control-sm js-integar-input js-filter-range-end" id="filterPriceEnd" name="max_price" data-max="{$max_price}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {* PRICE_RANGE END *}

        {* Дополнительные свойства *}
        {if $features}
            {foreach $features as $key=>$f}
                {if $category->top_features_id == $f->id}
                    {break}
                {/if}
                {if $f->options|count > 1}
                    <div class="filter__section is-open">
                        <div class="filter__section-title">{$f->name}</div>
                        <div class="filter__section-content">
                            {foreach $f->options as $k => $o}
                                <div class="form-check">
                                    <label class="form-check__label">
                                        <input type="checkbox" class="form-check__input js-filter-checkbox" name="{$f->id}[]" value="{$o->value}" id="f-{$f->id}-{$k}"{if $o->checked} checked{/if}  >
                                        <span class="form-check__text"><span class="filter__check-text">{$o->value|escape}</span>
                                            <small>({$o->count})</small>
                                        </span>
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {/if}
            {/foreach}
        {/if}
        {* Дополнительные свойства END *}
    </form>
</div>