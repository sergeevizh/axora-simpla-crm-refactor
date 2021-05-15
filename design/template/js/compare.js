$(document).ready(function(){
    /**
    * Compare
    */
    if($('.compare').length){
        compareRows();
    }

    $('.compare__list').on('setPosition', function(){
        compareRows();
    }).slick({
        infinite: false,
        slidesToShow: 3,
        swipeToSlide: true,
        edgeFriction: 0,
        responsive: [
        {
            breakpoint: 992,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
            }
        },
        ]
    });

    // удяляем товар из сравнения
    $(document).on('click', '.js-remove-compare', function(e){
        e.preventDefault();
        var $compare_item_wrapper = $(this).closest('.slick-slide');
        var $compare_item = $(this).closest('.compare__item');

        addOrRemove($(this));

        $compare_item.width($compare_item.width());

        $compare_item_wrapper.animate({width: 0}, 300, function(){
            $('.compare__list').slick('slickRemove', $compare_item_wrapper.index());

            if(!$('.compare__item').length){
                $('.compare').remove();
            }

            if($('.compare__item').length == 0){
                $('.empty-products-msg').show();
            }
        });
    });

    $('.compare__different-input').on('change', function(e){
        if($(this).is(':checked')){
            $('.compare__data-row').hide();

            for(var i = 0; i < $('.compare__sidebar .compare__data-row').length; i++){
                var t = '';
                var d = 0;
                $('.compare__item .compare__data-row:nth-child('+ i +')').each(function(){
                    if($(this).text() != t){
                        t = $(this).text();
                        d++;
                    }
                });
                if(d > 1){
                    $('.compare__data-row:nth-child('+ i +')').show();
                }
            }
        } else {
            $('.compare__data-row').show();
        }
    });

    $('.compare__data-row').hover(function(){
        $('.compare__data-row:nth-child('+ ($(this).index() + 1) +')').addClass('is-active');
    },function(){
        $('.compare__data-row').removeClass('is-active');
    });

    /**
     * Add compare
     */
    $(document).on('click', '.js-add-compare', function(e){
        e.preventDefault();

        addOrRemove($(this))
    });
});

function addOrRemove(calledElement) {

    var product_id = calledElement.data('product-id'),
        $this = calledElement;

    $.ajax({
        url: 'ajax/product/compare',
        data: { product_id: product_id },
        dataType: 'json',
        method: 'POST',
        cache: false,
        success: function(data) {
            $('.js-compares-informer').html(data);

            if($this.hasClass('is-active')) {
                $('.is-active.js-add-compare-'+product_id).removeClass('is-active');
            } else {
                $('.js-add-compare-'+product_id).addClass('is-active');
            }
        }
    });
}

function compareRows(){
    var headerH = 0;
    $('.compare__item-header').each(function(){
        $(this).height('auto');
        headerH = Math.max($(this).outerHeight(), headerH);
    });
    $('.compare__sidebar-header, .compare__item-header').outerHeight(headerH);

    $('.compare').addClass('is-visible');

    for(var i = 0; i < $('.compare__sidebar .compare__data-row').length; i++){
        var $row = $('.compare__data-row:nth-child('+ (i + 1) +')');
        var H = 0;
        $row.height('auto').each(function(){
            H = Math.max($(this).outerHeight(), H);
        });
        $row.outerHeight(H);
    }
}
