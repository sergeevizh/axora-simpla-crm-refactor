{* Шаблон страницы зарегистрированного пользователя *}
<div class="container">
    <ul class="breadcrumbs">
        <li class="breadcrumbs__item">
            <a href="/" class="breadcrumbs__link">Главная</a>
        </li>
        <li class="breadcrumbs__item">
            <a href="/user" class="breadcrumbs__link">Личный кабинет</a>
        </li>
        <li class="breadcrumbs__item">Редактирование пользователя</li>
    </ul>

    <h1>Личный кабинет </h1>

    <div class="account">
        <h3 class="account__title">Редактировать данные пользователя</h3>
        {if session()->has('errors')}
            {foreach session()->get('errors') as $error}
                {array_shift($error)}
            {/foreach}
        {/if}
        <form class="js-validation-form" action="{$config->root_url}/user/update"  method="POST">
            <div class="form-group">
                <label for="accountName">Ваше Ф.И.О.</label>
                <input type="text" class="form-control" id="accountName" name="name" value="{$user->name}" required>

            </div>

            <div class="form-group">
                <label for="accountPhone">Телефон</label>
                <input type="text" class="form-control" id="accountPhone" name="phone" value="{$user->phone}" >
            </div>

            <div class="form-group">
                <label for="accountEmail">Электронная почта</label>
                <input type="email" class="form-control" id="accountEmail" name="email" value="{$user->email}" required>
            </div>

            <div class="form-group">
                <label for="accountAddress">Адрес</label>
                <input type="text" class="form-control" id="accountAddress" name="address" value="{$user->address}" >
            </div>
            <br>

            <button type="submit" class="btn btn-info btn-lg">Сохранить</button>
        </form>
    </div>

</div>
