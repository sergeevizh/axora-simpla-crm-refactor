<div class="container">
    <ul class="breadcrumbs">
        <li class="breadcrumbs__item">
            <a href="/" class="breadcrumbs__link">Главная</a>
        </li>
        <li class="breadcrumbs__item">Избранное</li>
    </ul>

    <h1>Избранное</h1>

    <h1>{$page->name|escape}</h1>

    <div class="catalog-section">
        <div class="catalog-section__main">
            {include file="partials/catalog_products.tpl" is_favorites=true}
        </div>
    </div>
</div>
