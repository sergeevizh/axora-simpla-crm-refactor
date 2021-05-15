{* Страница товара *}

{* Канонический адрес страницы *}
{$canonical="/products/{$product->url}" scope=root}

<div class="container">
    <ul class="breadcrumbs">
        <li class="breadcrumbs__item">
            <a href="/" class="breadcrumbs__link">Главная</a>
        </li>
        {foreach $category->path as $cat}
            <li class="breadcrumbs__item">
                <a href="catalog/{$cat->url}" class="breadcrumbs__link">{$cat->name|escape}</a>
            </li>
        {/foreach}
        {if $brand}
            <li class="breadcrumbs__item">
                <a href="brands/{$brand->url}" class="breadcrumbs__link">{$brand->name|escape}</a>
            </li>
        {/if}
        <li class="breadcrumbs__item">{$product->name|escape}</li>
    </ul>

    <h1 data-product="{$product->id}">{$product->name|escape}</h1>

    <div class="product">
        <div class="product__header">
            <div class="product__meta">
                <div class="meta">
                    <div class="meta__item">
                        {include file="partials/rating.tpl" is_product_page=true}
                    </div>
                    <div class="meta__item">
                        <a href="#reviews" class="comment-count js-target-link"><i class="fal fa-comment"></i> <span
                                    class="comment-count__val">{$comments|count}</span></a>
                    </div>
                    <div class="meta__item">
                        {if $brand}
                            <a href="{$config->root_url}/brands/{$brand->url}" class="meta__link">{$brand->name|escape}</a>
                        {/if}
                    </div>
                    {if $product->variant->sku}
                        <div class="meta__item sku">Арт. {$product->variant->sku}</div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="product__main">
            <div class="product__image-section">
                <div class="product__gallery">
                    <div class="product__gallery-header">
                        {include file="partials/stickers.tpl" is_product_page=true}

                        <div class="product__control">
                            <button type="button" data-product-id="{$product->id}"
                                    class="favorite-btn js-add-favorites js-favorites-{$product->id} {if $favorites &&  in_array($product->id, $favorites)}is-active{/if} ">
                                <i class="fal fa-heart"></i></button>
                            <button type="button"
                                    class="compare-btn js-add-compare js-add-compare-{$product->id} {if in_array($product->id, $compares)} is-active{/if}"
                                    data-product-id="{$product->id}">
                                <i class="fal fa-balance-scale "></i>
                            </button>
                        </div>
                    </div>

                    <div class="product-gallery">
                        <div class="product-gallery__slider">
                            {foreach $product->images as $i=>$image}
                                <a href="{$image->filename|resize:800:600:w}" class="product-gallery__item"
                                   data-value="{$i}" data-thumb="{$image->filename|resize:100:100}"
                                   data-fancybox="product-gallery">
    								<span class="product-gallery__item-inner">
    									<img src="{$image->filename|resize:480:340}" alt="">
    								</span>
                                </a>
                            {/foreach}

                            {if $product->variants|count > 0}
                                {foreach $product->variants as $v}
                                    {if $v->attachment}
                                        <a href="{$v->attachment|resize:800:600:w}" class="product-gallery__item"
                                           data-value="{$v->id}" data-thumb="{$v->attachment|resize:100:100}"
                                           data-fancybox="product-gallery">
    										<span class="product-gallery__item-inner">
    											<img src="{$v->attachment|resize:480:340}" alt="{$v->name}">
    										</span>
                                        </a>
                                    {/if}
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div class="product__content">
                {$product->annotation}

                <div class="product__price-row row">
                    <div class="col-sm-6">
                        <div class="product__price">
                            <input type="hidden" name="currency" value="{$currency->sign|escape}">
                            <div class="price price_lg">
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
                    </div>

                    <div class="col-sm-6">
                        <div class="product__count-field">
                            <div class="product__count-title">Количество, шт.</div>
                            <input type="number" class="product__count form-control" name="amount" value="1" min="1">
                        </div>
                    </div>
                </div>

                <form action="/cart" class="js-submit-to-cart" method="post">
                    {if $product->variants|count < 5}
                        {if $product->variants|count == 1}
                            {foreach $product->variants as $v}
                                <input
                                        style="display: none"
                                        type="radio"
                                        name="variant"
                                        class="form-check__input product__radio js-change-variant"
                                        value="{$v->id}"
                                        data-variant-id="{$v->id}"
                                        data-variant-infinity="{$v->infinity}"
                                        data-variant-stock="{$v->stock}"
                                        {if $v->sku}data-sku="{$v->sku|escape}"{/if}
                                        {if $product->variant->id == $v->id}checked{/if}
                                        data-price="{$v->price}"
                                        {if $v->compare_price > 0 }data-compare-price="{$v->compare_price}"{/if}
                                >
                            {/foreach}
                        {else}
                            <div class="product__variants">
                                <div class="variants">
                                    <div class="variants__title">Выбрать вариант</div>
                                    {foreach $product->variants as $v}
                                        <div class="form-check form-check_inline">
                                            <label class="form-check__label">
                                                <input
                                                        type="radio"
                                                        name="variant"
                                                        class="form-check__input product__radio js-change-variant"
                                                        value="{$v->id}"
                                                        data-variant-id="{$v->id}"
                                                        data-variant-infinity="{$v->infinity}"
                                                        data-variant-stock="{$v->stock}"
                                                        {if $v->sku}data-sku="{$v->sku|escape}"{/if}
                                                        {if $product->variant->id == $v->id}checked{/if}
                                                        data-price="{$v->price}"
                                                        {if $v->compare_price > 0 }data-compare-price="{$v->compare_price}"{/if}
                                                >
                                                <span class="form-check__text">{$v->name}</span>
                                            </label>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        {/if}
                    {else}
                        <div class="product__variants">
                            <div class="variants">
                                <div class="variants__title">Выбрать характеристику</div>
                                <select class="form-control product__select js-change-variant" name="variant">
                                    {foreach $product->variants as $v}
                                        <option
                                                data-image="{$config->downloads_dir}{$v->attachment}"
                                                data-price="{$v->price}"
                                                {if $v->compare_price > 0 }data-compare-price="{$v->compare_price}"{/if}
                                                {if $v->sku}data-sku="{$v->sku|escape}"{/if}
                                                data-variant-id="{$v->id}"
                                                data-variant-infinity="{$v->infinity}"
                                                data-variant-stock="{$v->stock}"

                                                {foreach $currencies as $c}{if $c->enabled}data-price-currency-{$c->id|escape}="{$v->price|convert:$c->id}"{/if}{/foreach}
                                                value="{$v->id}" {if $product->variant->id == $v->id}selected{/if}>{$v->name|escape}
                                        </option>
                                    {/foreach}
                                </select>

                                <div class="variants__thumbs product__select-thumbs">
                                    {foreach $product->variants as $v}
                                        {if $v->attachment}
                                            <span class="variants__thumbs-item {if $product->variant->id == $v->id}is-selected{/if}"
                                                  data-value="{$v->id}"
                                                  style="background-image: url({$v->attachment|resize:800:600});"></span>
                                        {/if}
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/if}

                    <input type="hidden" name="cart-product-ids" value="{$cart_product_ids_str}">

                    <div class="product__btn-field js-cart_button-render-area">
                        {assign var="is_cart" value=in_array($product->variant->id, $cart_product_ids)}

                        {if $product->variant->stock == 0}
                            <button disabled type="submit" class="basket-btn btn btn-warning ">
                                <span class="basket-btn__text ">Нет в наличии</span>
                            </button>
                        {else}
                            <button type="submit" class="basket-btn btn btn-warning {if $is_cart}is-active{/if}"
                                    data-alt-text="В корзине">
                                <i class="fal fa-shopping-cart"></i>
                                <span class="basket-btn__text js-add-to-cart-in-product-page">{if $is_cart}В корзине{else} {if $product->variant->infinity == 1}Под Заказ{else}Добавить в корзину{/if} {/if}</span>
                            </button>
                        {/if}
                    </div>
                </form>
            </div>
        </div>

        <div class="tabs">
            <ul class="tabs__nav">
                <li class="tabs__nav-item">
                    <a href="#description" class="tabs__nav-link is-active">Обзор</a>
                </li>
                <li class="tabs__nav-item">
                    <a href="#specifications" class="tabs__nav-link">Характеристики</a>
                </li>

                {if $documents}
                    <li class="tabs__nav-item">
                        <a href="#documents" class="tabs__nav-link">Документы</a>
                    </li>
                {/if}

                {if $product->youtube_link}
                    <li class="tabs__nav-item">
                        <a href="#video" class="tabs__nav-link">Видео</a>
                    </li>
                {/if}
                <li class="tabs__nav-item">
                    <a href="#reviews" class="tabs__nav-link">Отзывы</a>
                </li>
            </ul>
            <div class="tabs__content">
                <div class="tabs__item is-open" id="description">
                    <div class="text" style="max-width: 800px;">
                        {if $product->body}
                            {$product->body}
                        {/if}
                    </div>
                </div>

                <div class="tabs__item" id="specifications">
                    {if $product->features}
                        <table class="data-table" style="max-width: 600px;">
                            {foreach $product->features as $f}
                                <tr>
                                    <td><span><span>{$f->name}</span></span></td>
                                    <td>{$f->value}</td>
                                </tr>
                            {/foreach}
                        </table>
                    {/if}
                </div>

                {if $documents}
                    <div class="tabs__item" id="documents">
                        <div class="table_description">Сопроводительная документация</div>
                        <table class="table-description" style="max-width: 600px;" itemprop="additionalProperty"
                               itemscope="" itemtype="http://schema.org/PropertyValue">
                            {foreach $documents as $document}
                                <tr>
                                    <td><strong itemprop="name">{$document->name}</strong></td>
                                    <td itemprop="value"><a
                                                href="{$config->root_url}/files/documents/{$document->document}"><i
                                                    class="fa fa-download"></i> Скачать</a></td>
                                </tr>
                            {/foreach}
                        </table>

                    </div>
                {/if}

                {if $product->youtube_link}
                    <div class="tabs__item" id="video">

                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$product->youtube_link|escape}"
                                frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>

                    </div>
                {/if}

                <div class="tabs__item" id="reviews">
                    <div class="add-review">
                        <div class="add-review__header" style="{if $error}display:none{/if}">
                            <button type="button" class="add-review__open-btn btn btn-info"><i class="fal fa-edit"></i>
                                Оставить отзыв
                            </button>
                        </div>
                        <div class="add-review__content" style="{if $error}display:block{/if}">
                            <form class="add-review__form" method="post">
                                <h2>Оставить отзыв</h2>
                                {if $error}
                                    <div class="alert alert-danger" role="alert">
                                        {if $error=='captcha'}
                                            Неверно введена капча
                                        {elseif $error=='empty_name'}
                                            Введите имя
                                        {elseif $error=='empty_comment'}
                                            Введите комментарий
                                        {/if}
                                    </div>
                                {/if}
                                <div class="form-group">
                                    <label for="comment_name">Ваше имя</label>
                                    <input type="text" class="form-control" id="comment_name" name="name" required
                                           value="{$comment_name}">
                                </div>

                                <div class="form-group">
                                    <label for="text">Отзыв</label>
                                    <textarea class="form-control" id="reviewText" name="text" rows="8"
                                              required>{$comment_text}</textarea>
                                </div>

                                {*
                                <div class="form-group">
                                    <label>Оценка</label>
                                    <div class="rating rating_lg">
                                        <input type="hidden" class="rating__value" name="reviewRating" value="0">
                                        <button type="button" class="rating__star" data-value="1"></button>
                                        <button type="button" class="rating__star" data-value="2"></button>
                                        <button type="button" class="rating__star" data-value="3"></button>
                                        <button type="button" class="rating__star" data-value="4"></button>
                                        <button type="button" class="rating__star" data-value="5"></button>
                                        <div class="rating__bg">
                                            <span style="width: 0%;"></span>
                                        </div>
                                    </div>
                                </div>
                                *}

                                <div class="form-row">
                                    <div class="col flex-grow-0 captcha">
                                        <img src="captcha/image.php?{math equation='rand(10,10000)'}" alt='captcha'/>
                                    </div>
                                    <div class="col">
                                        <input class="input_captcha form-control" id="comment_captcha" type="text"
                                               name="captcha_code" value="" data-format="\d\d\d\d"
                                               data-notice="Введите капчу" placeholder="Введите капчу"/>
                                    </div>
                                    <div class="col flex-grow-0">
                                        <button type="submit" name="comment" class="btn btn-info">Отправить</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="reviews">
                        <div class="reviews__content">
                            {if $comments}
                                {foreach $comments as $comment}
                                    {if $comment->approved}
                                        <div class="review">
                                            <div class="review__main">
                                                <div class="review__header">
                                                    <div class="review__author">{$comment->name|escape}</div>
                                                    {*
                                                    <div class="review__rating">
                                                        <div class="rating">
                                                            <div class="rating__bg">
                                                                <span style="width: 80%;"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    *}
                                                    <div class="review__date">{$comment->date|date}
                                                        , {$comment->date|time}</div>
                                                    {if !$comment->approved}
                                                        <div class="review__date"><b>ожидает модерации</b></div>
                                                    {/if}
                                                </div>
                                                <div class="review__content">
                                                    <!-- Комментарий -->
                                                    {$comment->text|escape|nl2br}
                                                    <!-- Комментарий (The End)-->
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                {/foreach}
                            {else}
                                <p>Пока нет комментариев</p>
                            {/if}
                        </div>

                        <div class="reviews__footer">
                            {*<button type="button" class="reviews__more-btn loading-btn btn btn-outline-secondary"><span class="spinner-border spinner-border-sm"></span> Показать еще</button>*}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*	Аналоги	*}
    {get_related_products var=related_products product_id=$product->id limit=10 }
    {if $related_products}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title">Аналоги</h2>
            <div class="catalog-slider">
                {foreach $related_products as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}

    {*	Подходит к изделиям	*}
    {get_related_schemes var=related_schemes product_id=$product->id limit=10 }
    {if $related_schemes}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title">Подходит к изделиям </h2>
            <div class="catalog-slider">
                {foreach $related_schemes as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}

    {*	Ранее просматривали	*}
    {get_browsed_products var=browsed_products limit=10 }
    {if $browsed_products}
        <section class="catalog-slider-section">
            <h2 class="catalog-slider-section__title">Ранее просматривали</h2>
            <div class="catalog-slider">
                {foreach $browsed_products as $product}
                    <div class="catalog-slider__item">
                        {include file="partials/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
    {/if}
</div>
