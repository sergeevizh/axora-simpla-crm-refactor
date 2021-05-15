$(document).ready(function() {

$(".js-autocomplete-search").devbridgeAutocomplete({
	serviceUrl:'ajax/product/search-products',
	minChars: 2,
	groupBy: 'type',
	width: 'auto',
	forceFixPosition: false,
	preserveInput: true,
	appendTo: '.js-autocomplete-search-results',
	noCache: false,
	onSelect: function(suggestion){
		//$(".input_search").closest('form').submit();
	},
	formatGroup: function (suggestion, category) {
		let typesSerch = {
			'product' : 'Товары',
			'category' : 'Каталог',
			'brand' : 'Бренды',
		};

		return '<div class="search__result-title">' + typesSerch[category] +'</div>';
	},
	formatResult: function(suggestion, currentValue){
		var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
		var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';

		if(suggestion.type === 'product'){
			return '<div class="item item_inline">' +
				'\t\t<div class="item__content">' +
				'\t\t\t<a href="'+suggestion.data.url+'" class="item__image-field">' +
				'\t\t\t\t '+ (suggestion.data.image ? '<img src="'+suggestion.data.image+'" alt="'+suggestion.value+'" class="item__image">' : '') +
				'\t\t\t</a>' +
				'\t\t\t<div class="item__meta">' +
				(suggestion.data.category ? '\t\t\t\t<div class="meta__item"><a href="'+ suggestion.data.category.url +'">'+ suggestion.data.category.name +'</a></div>' : '') +
				'\t\t\t</div>' +
				'\t\t\t<div class="item__title">' +
				'\t\t\t\t<a href="'+ suggestion.data.url +'">'+ suggestion.value +'</a>' +
				'\t\t\t</div>' +
				'\t\t</div>' +

				'\t\t<div class="item__footer">' +
				'\t\t\t<div class="item__price">' +
				'\t\t\t\t<div class="price">' +
				'\t\t\t\t\t<div class="price__value">'+suggestion.data.price+'</div>' +
				'\t\t\t\t</div>' +
				'\t\t\t</div>' +
				'\t\t</div>' +
				'</div>';
		} else {
			return '<div class="search__result-item">' +
				'<a href="'+suggestion.data.url+'" class="search__result-link">' + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') +'</a>' +
				'</div>';
		}

		//return (suggestion.data.image?"<img align=absmiddle src='"+suggestion.data.image+"'> ":'') ;
	}
});

});
