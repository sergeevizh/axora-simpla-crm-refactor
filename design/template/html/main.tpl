{* Главная страница магазина *}

{* Для того чтобы обернуть центральный блок в шаблон, отличный от index.tpl *}
{* Укажите нужный шаблон строкой ниже. Это работает и для других модулей *}
{$wrapper = 'index.tpl' scope=root}

{* Канонический адрес страницы *}
{$canonical="" scope=root}
<div class="container">
    {*{api module=banners method=gets var=banners_slider _=['visible' => 1, 'type'=>1]}*}
    {if $banners_slider}
    <div class="main-slider-wrapper">
        {include file="partials/main_slider.tpl"}
    </div>
    {/if}
{*    {api module=banners method=gets var=banners_slider _=['visible' => 1, 'type'=>2]}*}
    {if $banners_slider}
        <div class="main-catalog">
            {include file="partials/main_slider_catalog.tpl"}
        </div>
    {/if}

    {*    {get_new_products var=new_products }*}
    {if $new_products}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title"><a href="{$config->root_url}/new">Новинки</a></h2>
            <div class="catalog-slider">
                {foreach $new_products as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}

    {*    {get_featured_products var=featured_products }*}
    {if $featured_products}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title"><a href="{$config->root_url}/featured">Популярное</a></h2>
            <div class="catalog-slider">
                {foreach $featured_products as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}

    {*    {get_posts var=posts limit=3}*}
    {if $posts}
        <section class="main-news-section">
            <h2><a href="{$config->root_url}/blog">Новости</a></h2>
            <div class="main-news-section__list row">
                {include file="partials/blog_posts.tpl"}
            </div>
        </section>
    {/if}

    {*    {get_discounted_products var=discounted_products limit=10 }*}
    {if $discounted_products}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title"><a href="{$config->root_url}/actions">Акции</a></h2>
            <div class="catalog-slider">
                {foreach $discounted_products as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}

    {* Заголовок страницы *}
    <h1>{$page->header}</h1>

    {* Тело страницы *}
    {$page->body}
</div>
