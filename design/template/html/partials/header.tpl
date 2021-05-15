<!doctype html>
<html lang="ru">
<head>
    <base href="{$config->root_url}/"/>
    <title>{$meta_title|escape}</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="{$meta_description|escape}"/>
    <meta name="keywords" content="{$meta_keywords|escape}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {* добавляем мета тег если пагинация *}
    {if $is_page_get_parameter}
        <meta name='robots' content='noindex,follow'/>
    {/if}

    {* Канонический адрес страницы *}
    {if isset($canonical)}
        <link rel="canonical" href="{$config->root_url}{$canonical}"/>{/if}
    {if isset($prev)}
        <link rel="prev" href="{$config->root_url}{$prev}">{/if}
    {if isset($next)}
        <link rel="next" href="{$config->root_url}{$next}">{/if}

    <link href="{asset('lib/jquery-ui-1.12.1/jquery-ui.min.css')}" rel="stylesheet">
    <link href="{asset('lib/bootstrap-4.2.1/bootstrap.min.css')}" rel="stylesheet">
    <link href="{asset('lib/fontawesome-pro-5.7.2/css/all.min.css')}" rel="stylesheet">
    <link href="{asset('lib/jquery.fancybox-3.5.6/jquery.fancybox.min.css')}" rel="stylesheet">
    <link href="{asset('lib/slick-1.9.0/slick.css')}" rel="stylesheet">

    <link href="{asset('css/style.css')}" rel="stylesheet" type="text/css"/>
    <link href="{asset('css/media.css')}" rel="stylesheet" type="text/css"/>
    <script src="{asset('lib/jquery-3.3.1/jquery-3.3.1.min.js')}"></script>

    {*========== icons ====================	*}
    <script type="text/javascript" src="{asset('fontIconPicker-2.0.0/jquery.fonticonpicker.min.js')}"></script>

    <!-- fontIconPicker core CSS -->
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/css/jquery.fonticonpicker.min.css')}"/>

    <!-- required default theme -->
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/themes/grey-theme/jquery.fonticonpicker.grey.min.css')}"/>

    <!-- optional themes -->
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/themes/dark-grey-theme/jquery.fonticonpicker.darkgrey.min.css')}"/>
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css')}"/>
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/themes/inverted-theme/jquery.fonticonpicker.inverted.min.css')}"/>

    <!-- Font -->
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/demo/fontello-7275ca86/css/fontello.css')}"/>
    <link rel="stylesheet" type="text/css"
          href="{asset('fontIconPicker-2.0.0/demo/icomoon/style.css')}"/>
    {* ================== /icons ========================	*}
    {if isset($debugbarRenderer)}
        {$debugbarRenderer->renderHead()}
    {/if}
</head>

<body>
<div class="header">
    <div class="header__content">
        <div class="container">
            <div class="header__content-inner">
                <div class="header__top">
                    <div class="header__nav">
                        <div class="nav-popup">
                            <div class="nav-popup__bg"></div>
                            <div class="nav-popup__inner">
                                <button type="button" class="nav-popup__close close-btn"><i class="fal fa-times"></i>
                                </button>
                                <div class="nav-popup__content">
                                    <ul class="nav">
                                        {foreach $pages as $p}
                                            {* Выводим только страницы из первого меню *}
                                            {if $p->menu_id == 1}
                                                <li class="nav__item">
                                                    <a class="nav__link {if $page && $page->id == $p->id}is-active{/if}"
                                                       data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
                                                </li>
                                            {/if}
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="header__contacts">
                        <div class="top-contacts">
                            <div class="top-contacts__item">
                                <a href="#" class="top-contacts__link"><i class="fal fa-map"></i> Адреса магазинов</a>
                            </div>

                            <div class="top-contacts__item">
                                <ul class="top-contacts__list">
                                    <li class="top-contacts__list-item">
                                        <a href="tel:+375333920788" class="top-contacts__link">+375 33 392-07-88</a>
                                    </li>
                                    <li class="top-contacts__list-item">
                                        <a href="tel:+375291145925" class="top-contacts__link">+375 29 114-59-25</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header__middle">
                    <div class="header__logo">
                        <a href="./" class="logo">
                            <img src="{asset('images/logo.svg')}" alt=""
                                 class="logo__img">
                        </a>
                    </div>

                    <div class="header__search">
                        <div class="header__search-wrapper">
                            <input value="{$keyword|escape}" type="text"
                                   class="header__search-input form-control js-autocomplete-search" placeholder="Поиск">
                            <button type="button" class="header__search-btn btn btn-info"><i class="far fa-search"></i>
                                Поиск
                            </button>
                        </div>
                    </div>

                    <div class="header__user">
                        <div class="user-field">
                            <div class="user-field__icon">
                                <i class="fal fa-user"></i>
                            </div>
                            <div class="user-field__content">
                                {if $user}
                                    <div class="user-field__item">
                                        <a href="{route('user')}" class="user-field__link link">{$user['name']}</a>
                                    </div>
                                    <div class="user-field__item">
                                        <a onclick="document.getElementById('logout-form').submit();" href="#" class="user-field__link link" id="logout">выйти</a>
                                        <form  style="display: none" id="logout-form" action="{route('user/logout')}" method="post">
                                            <button type="submit">logout</button>
                                        </form>
                                    </div>
                                {else}
                                    <div class="user-field__item">
                                        <button type="button" class="user-field__link link js-popup-open-login"
                                                data-src="#login-window">Войти
                                        </button>
                                        {include file='partials/login-window.tpl'}
                                    </div>
                                    <div class="user-field__item">
                                        <button type="button" class="user-field__link link js-popup-open-register"
                                                data-src="#register-window">Регистрация
                                        </button>
                                        {include file='partials/register-window.tpl'}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="header__menu-line">
        <div class="container">
            <div class="header__menu-line-inner">
                {* Рекурсивная функция вывода дерева категорий *}
                <div class="header__menu">
                    <div class="menu-popup">
                        <div class="menu-popup__bg"></div>
                        <div class="menu-popup__inner">
                            <button type="button" class="menu-popup__close close-btn"><i class="fal fa-times"></i>
                            </button>
                            <div class="menu-popup__content">
                                {include file="partials/nav.tpl"}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header__control">
                    <div class="control">
                        <button type="button" class="control__btn control__btn_menu">
                            <span class="control__btn-icon"><i class="far fa-bars"></i></span>
                            <span class="control__btn-text">Каталог</span>
                        </button>

                        <button type="button" class="control__btn control__btn_nav">
                            <span class="control__btn-icon"><i class="fal fa-ellipsis-v"></i></span>
                        </button>

                        <button type="button" class="control__btn control__btn_search">
                            <span class="control__btn-icon"><i class="fal fa-search"></i></span>
                        </button>

                        <button type="button" class="control__btn control__btn_user" data-src="#login-window">
                            <span class="control__btn-icon"><i class="fal fa-user"></i></span>
                        </button>

                        <a href="favorites" class="control__btn control__btn_favorite">
                            <span class="control__btn-icon"><i class="fal fa-heart"></i></span>
                            <span class="control__btn-count js-favorites-count">{if $favorites}{$favorites|count}{else}0{/if}</span>
                        </a>

                        <a href="compares" class="control__btn control__btn_compare">
                            <span class="control__btn-icon"><i class="fal fa-balance-scale"></i></span>
                            <span class="control__btn-count js-compares-informer">{$compares|count}</span>
                        </a>

                        <a href="/cart/" class="control__btn control__btn_basket">
                            <span class="control__btn-icon"><i class="fal fa-shopping-basket"></i></span>
                            <span class="control__btn-count js-cart-informer">{include file="cart_informer.tpl"}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="search">
    <div class="search__main">
        <div class="container">
            <div class="search__main-inner">
                <form action="search" class="b-search__form search__form js-validation-form" novalidate="novalidate">
                    <div class="form-row">
                        <div class="col flex-grow-1">
                            <input type="text"
                                   class="search__input form-control form-control-lg js-autocomplete-search valid"
                                   placeholder="Поиск" name="keyword" required>
                        </div>

                        <div class="col-auto">
                            <button type="button" class="search__clear btn btn-outline-info btn-lg"><i
                                        class="fal fa-times"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="search__content">
        <div class="container">
            <div class="search__content-inner">
                <div class="search__result js-autocomplete-search-results" style="position: relative">

                </div>
                <div class="search__result" style="display: none;">
                    <div class="search__result-section">
                        <div class="search__result-title">Каталог</div>

                    </div>
                    <div class="search__result-section">
                        <div class="search__result-title">Бренды</div>

                    </div>

                    <div class="search__result-section">
                        <div class="search__result-title">Товары</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
