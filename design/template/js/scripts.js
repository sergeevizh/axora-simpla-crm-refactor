$(document).ready(function() {

    /**
     * Validation
     */
    $('.js-validation-form').each(function() {
        var $form = $(this);
        $form.validate({
            errorPlacement: function(error, element) {
                if ($(element).is(':checkbox') || $(element).is(':radio')) {
                    error.insertAfter($(element).closest('label'));
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });

    /**
     * Slick
     * @see  http://kenwheeler.github.io/slick/
     */
    $('.main-slider').slick({
        slidesToShow: 1,
        fade: true,
        autoplay: true,
        autoplaySpeed: 7000,
        speed: 1000,
    });

    $('.catalog-slider').slick({
        dots: true,
        slidesToShow: 4,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    $('.product-gallery').each(function(i){
        var $gallery = $(this),
            $gallerySlider =  $gallery.find('.product-gallery__slider');
        $gallerySlider.addClass('product-gallery__slider_' + i);

        if($gallerySlider.children().length > 1){
            var $galleryThumbs = $('<div class="product-gallery__thumbs product-gallery__thumbs_'+ i +'" />');

            $gallerySlider.children().each(function(){
                $galleryThumbs.append($('<div class="product-gallery__thumbs-item"><span class="product-gallery__thumbs-item-inner"><img src="'+ ($(this).data('thumb') || $(this).find('img').attr('src')) +'" /></span></div>'));
            });

            $gallery.append($galleryThumbs);

            $gallerySlider.slick({
                fade: true,
                infinite: false,
                asNavFor: '.product-gallery__thumbs_' + i,
            });

            $galleryThumbs.slick({
                slidesToShow: 6,
                focusOnSelect: true,
                swipeToSlide: true,
                arrows: false,
                infinite: false,
                asNavFor: '.product-gallery__slider_' + i,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 5,
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 4,
                        }
                    }
                ]
            });
        }
    });

    $('.product__radio, .product__select').on('change', function(){
        var value = $(this).val();

        $('.product-gallery__slider').slick('slickGoTo', $('.product-gallery__item[data-value="'+ value +'"]').closest('.slick-slide').data('slick-index'));
    });

    $('.product__select').on('change', function(){
        var value = $(this).val();
        $(this).next('.product__select-thumbs').find('.variants__thumbs-item').removeClass('is-selected');
        $(this).next('.product__select-thumbs').find('.variants__thumbs-item[data-value="'+ value +'"]').addClass('is-selected');
    });

    $('.product__select-thumbs .variants__thumbs-item').on('click', function(){
        var value = $(this).data('value');
        $(this).closest('.product__select-thumbs').prev('.product__select').val(value).trigger('change');
    });

    /**
     * Sticky-Kit
     * @see  http://leafo.net/sticky-kit/
     */
    $('.header__menu-line').stick_in_parent({
        parent: 'body'
    });

    $('.tabs__nav').stick_in_parent({
        offset_top: $('.header__menu-line').outerHeight() - 1
    });

    /**
     * Search
     */
    $('.header__search-btn, .control__btn_search').on('click', function(e){
        noscrollStart();
        $('html').addClass('is-search-open');
        $('.search__input').focus();
    });

    $('.header__search-input').on('input', function(e){
        noscrollStart();
        $('html').addClass('is-search-open');
        $('.search__input').val($(this).val()).focus();
    });

    $('.search__input').on('input', function(e){
        $('.header__search-input').val($(this).val());
    });

    $('.search__clear').on('click', function(e){
        $('html').removeClass('is-search-open');
        noscrollFinish();
    });

    /**
     * Mobile Menu
     */
    $('.control__btn_menu').on('click', function(e){
        noscrollStart();
        $('html').addClass('is-menu-open');
    });

    $('.menu-popup__close, .menu-popup__bg').on('click', function(e){
        $('html').removeClass('is-menu-open');
        noscrollFinish();
    });

    $('.menu__toggle-btn').on('click', function(e){
        e.preventDefault();
        if($(this).closest('.menu__group').length){
            $(this).closest('.menu__group').toggleClass('is-active');
        } else {
            $(this).closest('.menu__item').toggleClass('is-active');
        }
    });

    /**
     * Mobile Nav
     */
    $('.control__btn_nav').on('click', function(e){
        noscrollStart();
        $('html').addClass('is-nav-open');
    });

    $('.nav-popup__close, .nav-popup__bg').on('click', function(e){
        $('html').removeClass('is-nav-open');
        noscrollFinish();
    });

    /**
     * Mobile Filter
     */
    $('.filter-btn').on('click', function(e){
        noscrollStart();
        $('html').addClass('is-filter-open');
    });

    $('.filter-popup__close, .filter-popup__bg').on('click', function(e){
        $('html').removeClass('is-filter-open');
        noscrollFinish();
    });

    /**
     * Tabs
     */
    $(document).on('click','.tabs__nav-link', function(e){
        e.preventDefault();
        var $this = $(this);
        var $tabs = $this.closest('.tabs');
        var hash = this.hash;

        $tabs.find('.tabs__item.is-open').removeClass('is-open');
        $tabs.find('.tabs__item' + hash).addClass('is-open');

        $tabs.find('.tabs__nav-link.is-active').removeClass('is-active');
        $this.addClass('is-active');

        $(document.body).trigger('sticky_kit:recalc');
    });

    /**
     * Target Link
     */
    $('.js-target-link').on('click', function(e){
        e.preventDefault();

        var hash = this.hash,
            $target = $(hash),
            $tabLink = $('.tabs__nav-link[href="' + hash + '"]'),
            offsetTop = $('.header__menu-line').outerHeight();

        if($tabLink.length){
            $tabLink.trigger('click');
        } else if($target.length){
            $('html, body').animate({scrollTop: $target.offset().top - offsetTop}, 500, function (){
                if(history.pushState) {
                    history.pushState(null, null, hash);
                } else {
                    location.hash = hash;
                }
            });
        }
    });

    /**
     * Hash Scroll
     */
    if (window.location.hash) {
        var hash = window.location.hash,
            $target = $(hash),
            $tabLink = $('.tabs__nav-link[href="' + hash + '"]'),
            offsetTop = $('.header__menu-line').outerHeight();

        if($tabLink.length){
            $('.tabs__nav-link[href="' + hash + '"]').trigger('click');
        } else if($target.length){
            $('html, body').scrollTop($target.offset().top - offsetTop);
        }
    }

    /**
     * Add Review
     */
    $('.add-review__open-btn').on('click', function(e){
        e.preventDefault();
        $('.add-review__header').hide();
        $('.add-review__content').show();

        $(document.body).trigger('sticky_kit:recalc');
    });

    /**
     * More Reviews
     */
    $('.reviews__more-btn').on('click', function(e){
        e.preventDefault();
        $(this).addClass('is-loading');
    });

    /**
     * Order Delivery
     */
    $('.order__delivery-type-input').on('change', function(e){
        $('.order__delivery-type-section').removeClass('is-open');
        $(this).closest('.order__delivery-type-section').addClass('is-open');
    });

    /**
     * History
     */
    $('.history__item-header').on('click', function(e){
        e.preventDefault();
        $(this).closest('.history__item').toggleClass('is-open');
    });

    /**
     * Payment Type
     */
    $('.js-payment-type-change-btn').on('click', function(e){
        $('.js-payment-type-list').show();
    });

    $('.js-payment-type-radio').on('change', function(e){
        $('.js-payment-type-text').text($(this).val());
        $('.js-payment-type-list').hide();
    });
});

/**
 * Русификатор Form Validation
 */
jQuery.extend(jQuery.validator.messages, {
    required: "Обязательное поле",
    remote: "Исправьте это поле",
    email: "Некорректный e-mail",
    url: "Некорректный url",
    date: "Некорректная дата",
    dateISO: "Некорректная дата (ISO)",
    number: "Некорректное число",
    digits: "Cимволы 0-9",
    creditcard: "Некорректный номер карты",
    equalTo: "Не совпадает с предыдущим значением",
    accept: "Недопустимое расширение",
    maxlength: jQuery.validator.format("Максимум {0} символов"),
    minlength: jQuery.validator.format("Минимум {0} символов"),
    rangelength: jQuery.validator.format("Минимум {0} и максимумт {1} символов"),
    range: jQuery.validator.format("Допустимо знаечение между {0} и {1}"),
    max: jQuery.validator.format("Допустимо значение меньше или равное {0}"),
    min: jQuery.validator.format("Допустимо значение больше или равное {0}")
});

$.extend(true, $.fancybox.defaults, {
    touch: false,
    autoFocus: false,
});

/**
 * Mobile Scroll Prevent
 */
var noscrollY = 0;

function noscrollStart(){
    noscrollY = $(document).scrollTop();
    $('body').css('top', - noscrollY + 'px');
    $('html').addClass('is-noscroll');
}

function noscrollFinish(){
    $('html').removeClass('is-noscroll');
    $(document).scrollTop(noscrollY);
    $('body').css('top', 'auto');
}

//---иконки категорий, конертируем в правильную кодировку
$( ".cat-icon" ).each(function() {
    $(this).parent().html( '<i data-icomoon="&#x' + parseInt($(this).val(), 10).toString(16) + ';"></i>');
});

$(document).ready(function() {
    $('input[name=payment_method_id]').change(function(){
        $('.change-payment-type-form').submit();
    });
});
