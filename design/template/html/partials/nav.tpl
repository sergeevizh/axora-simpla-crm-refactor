{if $categories}
    <ul class="menu">
        {foreach $categories as $c}
            {* Показываем только видимые категории *}
            {if $c->visible}
                <li class="menu__item">
                    <div class="menu__row {if $c->subcategories}has-dropdown{/if}">
                        <a href="catalog/{$c->url}" data-category="{$c->id}" class="menu__link">
                            {if $c->image}
                                <span class="menu__icon"><img src="{$config->categories_images_dir}{$c->image}" alt="{$c->name|escape}"></span>
                            {elseif $c->icon}
                                <span class="menu__icon"><i  class="{$c->icon}"></i></span>
                            {/if}
                            <span class="menu__text">{$c->name|escape}</span>
                        </a>
                        {if $c->subcategories}
                        <button type="button" class="menu__toggle-btn"></button>
                        {/if}
                    </div>

                    {if $c->subcategories}
                        <div class="menu__dropdown">
                            {foreach $c->subcategories as $sub}
                                {if $sub->visible}
                                <div class="menu__group">
                                    <div class="menu__group-title">
                                        <a href="catalog/{$sub->url}" class="menu__group-link">{$sub->name|escape}</a>
                                        {if $sub->subcategories}
                                        <button type="button" class="menu__toggle-btn"></button>
                                        {/if}
                                    </div>

                                    {if $sub->subcategories}
                                        <div class="menu__group-content">
                                            <ul class="menu__group-list">
                                                {foreach $sub->subcategories as $s}
                                                    {if $s->visible}
                                                        <li class="menu__group-item">
                                                            <a href="catalog/{$s->url}" class="menu__group-link">{$s->name|escape}</a>
                                                        </li>
                                                    {/if}
                                                {/foreach}
                                            </ul>
                                            {*<a href="catalog.php" class="menu__group-more">Все категории ></a>*}
                                        </div>
                                    {/if}
                                </div>
                                {/if}
                            {/foreach}
                        </div>
                    {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
{/if}