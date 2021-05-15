<div class="footer">
    <div class="footer__content">
        <div class="container">
            <div class="footer__content-inner row">
                <div class="footer__col col-sm-6 col-md-3">
                    <div class="footer__nav">
                        <div class="footer__nav-title">
                            <a href="#">Информация</a>
                        </div>
                        <ul class="footer__nav-list">
                            {foreach $pages as $p}
                                {if $p->menu_id == 1}
                                    <li class="footer__nav-item ">
                                        <a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                </div>

                <div class="footer__col col-sm-6 col-md-3">
                    <div class="footer__section">
                        <div class="footer__section-title">Телефон</div>
                        <p>
                            <a href="tel:+375333920788">+375 33 392-07-88</a><br/>
                            <a href="tel:+375291145925">+375 29 114-59-25</a><br/>
                            {* <button type="button" class="footer__callback-btn link js-popup-open" data-type="ajax" data-src="ajax/callback-window.php">Заказать звонок</button> *}
                        </p>
                    </div>
                </div>

                <div class="footer__col col-sm-6 col-md-3">
                    <div class="footer__section">
                        <div class="footer__section-title">Адрес</div>
                        <p>
                            ЧП «Аксора»<br/>
                            УНП 192491124<br/>
                            г.Минск, ул.Либкнехта 66-310
                        </p>
                    </div>
                </div>

                <div class="footer__col col-sm-6 col-md-3">
                    <div class="footer__section">
                        <div class="footer__section-title">Мы в социальных сетях</div>
                        <div class="social-list">
                            <a href="https://www.instagram.com/axora.by/"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook-square"></i></a>
                            <a href="#"><i class="fab fa-vk"></i></a>
                            <a href="#"><i class="fab fa-odnoklassniki-square"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                            <a href="#"><i class="fab fa-viber"></i></a>
                        </div>
                    </div>

                    <div class="footer__section">
                        <div class="footer__section-title">Мы принимаем к оплате</div>
                        <div class="payment-list">
                            <a href="#"><i class="fab fa-cc-mastercard"></i></a>
                            <a href="#"><i class="fab fa-cc-visa"></i></a>
                            {* <a href="#"><i class="fab fa-cc-paypal"></i></a> *}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer__bottom">
        <div class="container">
            <div class="footer__bottom-inner">
                <div class="footer__copyright">&copy; Copyright 2020</div>
                <div class="footer__dev">
                    <a target="_blank" href="https://axora.by/internet-magazin.html">axora.by</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{asset('lib/jquery-ui-1.12.1/jquery-ui.min.js')}"></script>
<script src="{asset('lib/jquery-ui-touch-punch-0.2.3/jquery.ui.touch-punch.min.js')}"></script>
<script src="{asset('lib/jquery.fancybox-3.5.6/jquery.fancybox.min.js')}"></script>
<script src="{asset('lib/jquery.sticky-kit-1.1.4/jquery.sticky-kit.min.js')}"></script>
<script src="{asset('lib/jquery.validation-1.19.0/jquery.validate.min.js')}"></script>
<script src="{asset('lib/slick-1.9.0/slick.min.js')}"></script>
<script src="{asset('js/jquery.autocomplete.min.js')}" type="text/javascript"></script>

<script src="{asset('js/filter.js')}"></script>
<script src="{asset('js/compare.js')}"></script>
<script src="{asset('js/helpers.js')}"></script>
<script src="{asset('js/search.js')}"></script>
<script src="{asset('js/authOperations.js')}"></script>
<script src="{asset('js/userOperations.js')}"></script>
<script src="{asset('js/productOperations.js')}"></script>

<script src="{asset('js/scripts.js')}"></script>
{if isset($debugbarRenderer)}
    {$debugbarRenderer->render()}
{/if}
</body>
</html>
