<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<META HTTP-EQUIV="Expires" CONTENT="-1">
	<title>{$meta_title}</title>
	<link rel="icon" href="design/images/favicon.ico" type="image/x-icon">
	<link href="design/css/style.css" rel="stylesheet" type="text/css"/>

	<script src="design/js/jquery/jquery.js"></script>
	<script src="design/js/jquery/jquery.form.js"></script>
	<script src="design/js/jquery/jquery-ui.min.js"></script>
	<script src="design/js/functions.js"></script>
	<link rel="stylesheet" type="text/css" href="design/js/jquery/jquery-ui.css" media="screen"/>

	{*========== icons ====================	*}

	<link href="design/lib/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
{*	<link href="design/lib/bootstrap-4.2.1/bootstrap.min.css" rel="stylesheet">*}
	<link href="design/lib/fontawesome-pro-5.7.2/css/all.min.css" rel="stylesheet">

{*	<link href="design/fontawesome-iconpicker/dist/css/font-awesome.min.css" rel="stylesheet">*}
{*	<link href="design/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css" rel="stylesheet">*}
{*	<script src="design/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.js"></script>*}

{*	<script type="text/javascript" src="design/fontIconPicker-2.0.0/jquery.fonticonpicker.min.js"></script>*}

{*	<!-- fontIconPicker core CSS -->*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/css/jquery.fonticonpicker.min.css" />*}

{*	<!-- required default theme -->*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/themes/grey-theme/jquery.fonticonpicker.grey.min.css" />*}

{*	<!-- optional themes -->*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/themes/dark-grey-theme/jquery.fonticonpicker.darkgrey.min.css" />*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css" />*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/themes/inverted-theme/jquery.fonticonpicker.inverted.min.css" />*}

{*	<!-- Font -->*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/demo/fontello-7275ca86/css/fontello.css" />*}
{*	<link rel="stylesheet" type="text/css" href="design/fontIconPicker-2.0.0/demo/icomoon/style.css" />*}
	{* ================== /icons ========================	*}

	<meta name="viewport" content="width=1024">

</head>
<body>

<a href='{$config->root_url}' class='admin_bookmark'></a>

<!-- ?????? ???????????????? -->
<div id="main">
	<!-- ?????????????? ???????? -->
	<ul id="main_menu">

		{if in_array('products', $manager->permissions)}
			<li>
				<a href="index.php?module=ProductsAdmin">
					<img src="design/images/menu/catalog.png">
					<b>??????????????</b>
				</a>
			</li>
		{elseif in_array('categories', $manager->permissions)}
			<li>
				<a href="index.php?module=CategoriesAdmin">
					<img src="design/images/menu/catalog.png">
					<b>??????????????</b>
				</a>
			</li>
		{elseif in_array('brands', $manager->permissions)}
			<li>
				<a href="index.php?module=BrandsAdmin">
					<img src="design/images/menu/catalog.png">
					<b>??????????????</b>
				</a>
			</li>
		{elseif in_array('features', $manager->permissions)}
			<li>
				<a href="index.php?module=FeaturesAdmin">
					<img src="design/images/menu/catalog.png">
					<b>??????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('orders', $manager->permissions)}
			<li>
				<a href="index.php?module=OrdersAdmin">
					<img src="design/images/menu/orders.png">
					<b>????????????</b>
				</a>
				{if $new_orders_counter}
					<div class="counter">
						<span>{$new_orders_counter}</span>
					</div>
				{/if}
			</li>
		{elseif in_array('labels', $manager->permissions)}
			<li>
				<a href="index.php?module=OrdersLabelsAdmin">
					<img src="design/images/menu/orders.png">
					<b>????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('users', $manager->permissions)}
			<li>
				<a href="index.php?module=UsersAdmin">
					<img src="design/images/menu/users.png">
					<b>????????????????????</b>
				</a>
			</li>
		{elseif in_array('groups', $manager->permissions)}
			<li>
				<a href="index.php?module=GroupsAdmin">
					<img src="design/images/menu/users.png">
					<b>????????????????????</b>
				</a>
			</li>
		{elseif in_array('coupons', $manager->permissions)}
			<li>
				<a href="index.php?module=CouponsAdmin">
					<img src="design/images/menu/users.png">
					<b>????????????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('pages', $manager->permissions)}
			<li>
				<a href="index.php?module=PagesAdmin">
					<img src="design/images/menu/pages.png">
					<b>????????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('blog', $manager->permissions)}
			<li>
				<a href="index.php?module=BlogAdmin">
					<img src="design/images/menu/blog.png">
					<b>????????</b>
				</a>
			</li>
		{/if}

		{if in_array('comments', $manager->permissions)}
			<li>


				<a href="index.php?module=CommentsAdmin">
					<img src="design/images/menu/comments.png">
					<b>??????????????????????</b>
				</a>

					{if $new_comments_counter || $new_feedback_counter }
						<div class="counter" >
							{if $new_comments_counter}
								<span>{$new_comments_counter}</span>
							{/if}

							{if $new_feedback_counter}
								<span style=" padding: 3px;background-color: orangered">{$new_feedback_counter}</span>
							{/if}
						</div>
					{/if}
			</li>
		{elseif in_array('feedbacks', $manager->permissions)}
			<li>
				<a href="index.php?module=FeedbacksAdmin">
					<img src="design/images/menu/comments.png">
					<b>??????????????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('import', $manager->permissions)}
			<li>
				<a href="index.php?module=ImportAdmin">
					<img src="design/images/menu/wizards.png">
					<b>??????????????????????????</b>
				</a>
			</li>
		{elseif in_array('export', $manager->permissions)}
			<li>
				<a href="index.php?module=ExportAdmin">
					<img src="design/images/menu/wizards.png">
					<b>??????????????????????????</b>
				</a>
			</li>
		{elseif in_array('backup', $manager->permissions)}
			<li>
				<a href="index.php?module=BackupAdmin">
					<img src="design/images/menu/wizards.png">
					<b>??????????????????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('stats', $manager->permissions)}
			<li>
				<a href="index.php?module=StatsAdmin">
					<img src="design/images/menu/statistics.png">
					<b>????????????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('design', $manager->permissions)}
			<li>
				<a href="index.php?module=ThemeAdmin">
					<img src="design/images/menu/design.png">
					<b>????????????</b>
				</a>
			</li>
		{/if}

		{if in_array('settings', $manager->permissions)}
			<li>
				<a href="index.php?module=SettingsAdmin">
					<img src="design/images/menu/settings.png">
					<b>??????????????????</b>
				</a>
			</li>
		{elseif in_array('delivery', $manager->permissions)}
			<li>
				<a href="index.php?module=DeliveriesAdmin">
					<img src="design/images/menu/settings.png">
					<b>??????????????????</b>
				</a>
			</li>
		{elseif in_array('payment', $manager->permissions)}
			<li>
				<a href="index.php?module=PaymentMethodsAdmin">
					<img src="design/images/menu/settings.png">
					<b>??????????????????</b>
				</a>
			</li>
		{elseif in_array('managers', $manager->permissions)}
			<li>
				<a href="index.php?module=ManagersAdmin">
					<img src="design/images/menu/settings.png">
					<b>??????????????????</b>
				</a>
			</li>
		{/if}
	</ul>
	<!-- ?????????????? ???????? (The End)-->


	<!-- ?????? ???????? -->
	<ul id="tab_menu">
		{$smarty.capture.tabs}
	</ul>
	<!-- ?????? ???????? (The End)-->


	<!-- ???????????????? ?????????? ???????????????? -->
	<div id="middle">
		{$content}
	</div>
	<!-- ???????????????? ?????????? ???????????????? (The End) -->

	<!-- ???????????? ?????????? -->
	<div id="footer">
		&copy; {date('Y')} ?????????????????????? <a href="https://axora.by">Axora</a>.
		???? ?????????? ?????? {$manager->login}.
		<a href='{$config->root_url}?logout' id="logout">??????????</a>
	</div>
	<!-- ???????????? ?????????? (The End)-->

</div>
<!-- ?????? ???????????????? (The End)-->

{* ???????????????????? ?? ???????????????????????????????? *}
{if $settings->pz_server && $settings->pz_phones[$manager->login]}
	<script src="design/js/prostiezvonki/prostiezvonki.min.js"></script>
	<script>
		var pz_type = 'simpla';
		var pz_password = '{$settings->pz_password}';
		var pz_server = '{$settings->pz_server}';
		var pz_phone = '{$settings->pz_phones[$manager->login]}';
		{literal}
		function NotificationBar(message) {
			ttop = $('body').height() - 110;
			var HTMLmessage = "<div class='notification-message' style='  text-align:center; line-height: 40px;'> " + message + " </div>";
			if ($('#notification-bar').size() == 0) {
				$('body').prepend("<div id='notification-bar' style='-moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; display:none;  height: 40px; padding: 20px; background-color: #fff; position: fixed; top:" + ttop + "px; right:30px; z-index: 100; color: #000;border: 1px solid #cccccc;'>" + HTMLmessage + "</div>");
			}
			else {
				$('#notification-bar').html(HTMLmessage);
			}
			$('#notification-bar').slideDown();
		}

		$(window).on("blur focus", function (e) {
			if ($(this).data('prevType') !== e.type) {
				$(this).data('prevType', e.type);

				switch (e.type) {
					case 'focus':
						if (!pz.isConnected()) {
							pz.connect({
								client_id: pz_password,
								client_type: pz_type,
								host: pz_server
							});
						}
						break;
				}
			}
		});

		$(function () {
			// ?????????????? ????????????
			pz.setUserPhone(pz_phone);
			pz.connect({
				client_id: pz_password,
				client_type: pz_type,
				host: pz_server
			});
			pz.onConnect(function () {
				$(".ip_call").addClass('phone');
			});
			pz.onDisconnect(function () {
				$(".ip_call").removeClass('phone');
			});

			$(".ip_call").click(function () {
				var phone = $(this).attr('data-phone').trim();
				pz.call(phone);
				return false;
			});

			pz.onEvent(function (event) {
				if (event.isIncoming()) {
					$.ajax({
						type: "GET",
						url: "ajax/search_orders.php",
						data: {keyword: event.from, limit: "1"},
						dataType: 'json'
					}).success(function (data) {
						if (event.to == pz_phone)
							if (data.length > 0) {
								NotificationBar('<img src="design/images/phone_sound.png" align=absmiddle> ???????????? <a href="index.php?module=OrderAdmin&id=' + data[0].id + '">' + data[0].name + '</a>');
							}
							else {
								NotificationBar('<img src="design/images/phone_sound.png" align=absmiddle> ???????????? ?? ' + event.from + '. <a href="index.php?module=OrderAdmin&phone=' + event.from + '">?????????????? ??????????</a>');
							}
					});
				}
			});
			{/literal}
		});
	</script>
{/if}

{literal}
<script>
	$(function () {

	$("#logout").click(function (event) {
			event.preventDefault();


			let out = window.location.href.replace(/:\/\//, '://log:out@');
			jQuery.get(out).error(function() {
				window.location = '/';
			});

		});
		
	});
</script>
{/literal}




</body>
</html>
