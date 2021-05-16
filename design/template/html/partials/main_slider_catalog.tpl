{foreach $banners_slider as $key => $banner}
    {if $banner['image'] && $banner['type'] == 2}
        <div class="main-catalog__grid {if $key == 5 } main-catalog__grid_large{/if}">
            <a href="{$banner['link']}" style="background-color: #c5cdd5;"
               class="main-catalog__item {if $key == 0} main-catalog__item_vertical {/if}">
                <span class="main-catalog__item-hover-bg" style="background-color: #e6edf5;"></span>
                <span class="main-catalog__image"
                      style="background-image: url('{asset($banner['image'])}');"></span>
                <span class="main-catalog__title">{$banner['sub_text']}</span>
            </a>
        </div>
    {/if}
{/foreach}
