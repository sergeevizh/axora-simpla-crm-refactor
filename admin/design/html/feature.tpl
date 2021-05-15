{* Вкладки *}
{capture name=tabs}
    {if in_array('products', $manager->permissions)}
        <li>
            <a href="index.php?module=ProductsAdmin">Товары</a>
        </li>
    {/if}
    {if in_array('categories', $manager->permissions)}
        <li>
            <a href="index.php?module=CategoriesAdmin">Категории</a>
        </li>
    {/if}
    {if in_array('brands', $manager->permissions)}
        <li>
            <a href="index.php?module=BrandsAdmin">Бренды</a>
        </li>
    {/if}
    <li class="active">
        <a href="index.php?module=FeaturesAdmin">Свойства</a>
    </li>
    {if in_array('banners', $manager->permissions)}
        <li>
            <a href="index.php?module=BannersAdmin">Баннеры</a>
        </li>
    {/if}

    {if in_array('tags', $manager->permissions)}
        <li><a href="index.php?module=TagsAdmin">Теги</a></li>
    {/if}






{/capture}

{if $feature->id}
    {$meta_title = $feature->name|escape scope=root}
{else}
    {$meta_title = 'Новое свойство' scope=root}
{/if}

{if $message_success}
    <!-- Системное сообщение -->
    <div class="message message_success">
		<span class="text">
			{if $message_success=='added'}
                Свойство добавлено
            {elseif $message_success=='updated'}
                Свойство обновлено
            {else}
                {$message_success}
            {/if}
		</span>
        {if $smarty.get.return}
            <a class="button" href="{$smarty.get.return}">Вернуться</a>
        {/if}
    </div>
    <!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
    <!-- Системное сообщение -->
    <div class="message message_error">
        <span class="text">{$message_error}</span>
        {if $smarty.get.return}
            <a class="button" href="{$smarty.get.return}">Вернуться</a>
        {/if}
    </div>
    <!-- Системное сообщение (The End)-->
{/if}

<!-- Основная форма -->
<form method=post id=product>

    <div id="name">
        <input class="name" name=name type="text" value="{$feature->name|escape}" placeholder="Название свойства"
               required>
        <input name="id" type="hidden" value="{$feature->id|escape}"/>
    </div>

    <div id="column_left">
        <div class="block">
            <h2>Использовать в категориях</h2>
            <input type="hidden" name="session_id" value="{$smarty.session.id}">

            <div id="list" class="sortable">
                {function name=categories_tree level=0}
                    {if $categories}
                        {foreach $categories as $category}
                            <div
                                    class="row {if !in_array($category->id, $in_product_filtered_categories)}invisible{/if}
                                        {if in_array($category->id, $in_filter_filtered_categories)}in_filter{/if}
                                        "
                            >
                                <div class="tree_row">

                                    <div class="move cell" style="padding-left:{$level*20}px">
                                    </div>
                                    <div class="checkbox cell">
                                        <input type="checkbox" name="check[]" data-id="{$feature->id}" value="{$category->id}"/>
                                    </div>
                                    <div class="cell name">
                                        {$category->name|escape}
                                    </div>
                                    <div class="icons cell">
                                        <a class="enable" title="Использовать в товаре" href="#"></a>
                                        <a class="in_filter" title="Использовать в категории" href="#"></a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                {categories_tree categories=$category->subcategories level=$level+1}
                            </div>
                        {/foreach}
                    {/if}
                {/function}
                {categories_tree categories=$categories}
            </div>



            <div id="action">
                <label id="check_all" class="dash_link">Выбрать все</label>

                <span id="select">
    				<select name="action">
    					<option value="none">Выберите действие</option>
    					<option value="use-in-category">Использовать в категории</option>
    					<option value="use-not-in-category">Не использовать в категории</option>
    					<option value="use-in-filter">Использовать в товаре</option>
    					<option value="use-not-in-filter">Не использовать в товаре</option>
    				</select>
    			</span>
            </div>
            <input id="apply_action" class="button_green" type="submit" value="Применить">
        </div>
    </div>


    <!-- Правая колонка свойств товара -->
    {*    <div id="column_right">*}

    {*        <!-- Параметры страницы -->*}
    {*        <div class="block">*}
    {*            <h2>Настройки свойства</h2>*}
    {*            <ul>*}
    {*                <li>*}
    {*                    <input type="checkbox" name="in_filter" id="in_filter"{if $feature->in_filter} checked{/if}*}
    {*                           value="1">*}
    {*                    <label for="in_filter">Использовать в фильтре</label>*}
    {*                </li>*}
    {*            </ul>*}
    {*        </div>*}
    {*        <!-- Параметры страницы (The End)-->*}
    {*        <input type="hidden" name="session_id" value='{$smarty.session.id}'>*}
    {*        <input class="button_green" type="submit" name="" value="Сохранить"/>*}

    {*    </div>*}
    <!-- Правая колонка свойств товара (The End)-->


</form>
<!-- Основная форма (The End) -->


{literal}
<script>
    // Выделить все
    $("#check_all").click(function () {
        $('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length > 0);
    });


    $("a.enable").click(function () {
        var icon = $(this);
        var line = icon.closest("div.row");
        var category_id = line.find('input[type="checkbox"][name*="check"]').val();
        var feature_id = line.find('input[type="checkbox"][name*="check"]').data('id');
        var state = line.hasClass('invisible') ? 1 : 0;
        icon.addClass('loading_icon');
        $.ajax({
            type: 'POST',
            url: 'ajax/update_object.php',
            data: {
                'object': 'categories_features',
                'values': {'category_id': category_id, 'feature_id': feature_id, 'in_product': state},
                'session_id': '{/literal}{$smarty.session.id}{literal}'
            },
            success: function (data) {
                icon.removeClass('loading_icon');
                if (state)
                    line.removeClass('invisible');
                else
                    line.addClass('invisible');
            },
            dataType: 'json'
        });
        return false;
    });

    $("a.in_filter").click(function () {
        var icon = $(this);
        var line = icon.closest("div.row");

        var category_id = line.find('input[type="checkbox"][name*="check"]').val();
        var feature_id = line.find('input[type="checkbox"][name*="check"]').data('id');
        var state = line.hasClass('in_filter') ? 0 : 1;
        icon.addClass('loading_icon');
        $.ajax({
            type: 'POST',
            url: 'ajax/update_object.php',
            data: {
                'object': 'categories_features',
                'values': {'category_id': category_id, 'feature_id': feature_id, 'in_filter': state},
                'session_id': '{/literal}{$smarty.session.id}{literal}'
            },
            success: function (data) {
                icon.removeClass('loading_icon');

                if (!state)
                    line.removeClass('in_filter');
                else
                    line.addClass('in_filter');
            },
            dataType: 'json'
        });
        return false;
    });


</script>
{/literal}
