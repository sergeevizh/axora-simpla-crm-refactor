{api module=banners method=gets var=banners_slider _=['visible' => 1, 'type'=>1]}
{if $banners_slider}
<div class="b-main-slider">
	{foreach $banners_slider as $banner}
		{if $banner->image}
		<div class="b-main-slider__item" data-slick-index="0"{if $banner->link} onclick="location = '{$banner->link}'"{/if}>
			<img src="{$config->root_url}/{$config->banners_dir}{$banner->image}" alt="">
			<div class="b-main-slider__item-content">
				<div class="l-wrapper">
					<div class="b-main-slider__item-title">
						{if $banner->link}
							<a href="{$banner->link}">{$banner->sub_text|escape}</a>
						{else}
							<span>{$banner->sub_text|escape}</span>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}
	{/foreach}
</div>
{/if}
