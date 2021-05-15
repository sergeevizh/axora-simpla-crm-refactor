{$meta_title = "Сравнение товаров" scope=root}

<div class="container">
	<ul class="breadcrumbs">
		<li class="breadcrumbs__item">
			<a href="home.php" class="breadcrumbs__link">Главная</a>
		</li>
		<li class="breadcrumbs__item">Сравнение</li>
	</ul>

	<h1>Сравнение</h1>

	{if $products}
		<div class="compare">
			<div class="compare__sidebar">
				<div class="compare__sidebar-header">
					<div class="form-check">
						<label class="form-check__label">
							<input type="checkbox" class="form-check__input compare__different-input">
							<span class="form-check__text">Показать только отличия </span>
						</label>
					</div>
				</div>

				<ul class="compare__data-list">
					{foreach $features as $feature}
						<li class="compare__data-row">
							<span class="compare__data-text">{$feature->name|escape}</span>
						</li>
					{/foreach}
				</ul>
			</div>

			<div class="compare__content">
				<div class="compare__list">
					{foreach $products as $product}
						<div class="compare__item">
							<div class="compare__item-header">
								{include file="partials/product.tpl" is_compares=true}
							</div>
							<ul class="compare__data-list">
								{foreach $features as $f}
								<li class="compare__data-row">
									<span class="compare__data-text">
										{if $product->features[$f->id]->values}
											{*implode(', ', $product->features[$f->id]->values)*}
											{$product->features[$f->id]->text}
										{else}
											&#8212;
										{/if}
									</span>
								</li>
								{/foreach}
							</ul>
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	{else}
		<span class="empty-products">Товаров нет</span>
	{/if}
	{*	для js	*}
	<span class="empty-products-msg" style="display: none;">Товаров нет</span>
</div>
