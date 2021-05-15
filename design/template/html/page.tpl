{* Шаблон текстовой страницы *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=root}

<div class="container">
    <!-- Заголовок страницы -->
    <h1 data-page="{$page->id}">{$page->header|escape}</h1>

    <!-- Тело страницы -->
    {$page->body}
</div>