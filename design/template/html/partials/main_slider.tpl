<div class="main-slider">
    {foreach $banners_slider as $banner}
        {if $banner['image'] && $banner['type'] == 1}
            <a href="{$banner['link']}" class="main-slider__item">
    		<span class="main-slider__image"
                  style="background-image: url('{asset($banner['image'])}');">
    			{if $banner['sub_text']}
                    <span class="main-slider__image-text">
                        <span class="main-slider__image-text-content">{$banner['sub_text']}</span>
                    </span>
                {/if}
    		</span>
            </a>
        {/if}
    {/foreach}
</div>