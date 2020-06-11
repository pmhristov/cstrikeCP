/* <![CDATA[ */
jQuery(function($) {
	$
		.extend(
			true,
			{
				getXML: function(url, data, callback) {
					$.get(url, data, callback, 'xml');
				},
				getHTML: function(url, data, callback) {
					$.get(url, data, callback, 'html');
				},
				getJSONP: function(url, data, callback) {
					$.get(url, data, callback, 'jsonp');
				},
				getText: function(url, data, callback) {
					$.get(url, data, callback, 'text');
				},
				postXML: function(url, data, callback) {
					$.post(url, data, callback, 'xml');
				},
				postHTML: function(url, data, callback) {
					$.post(url, data, callback, 'html');
				},
				postScript: function(url, data, callback) {
					$.post(url, data, callback, 'script');
				},
				postJSON: function(url, data, callback) {
					$.post(url, data, callback, 'json');
				},
				postJSONP: function(url, data, callback) {
					$.post(url, data, callback, 'jsonp');
				},
				postText: function(url, data, callback) {
					$.post(url, data, callback, 'text');
				}
			}
		)
		.ajaxSetup({
			cache: false
		});
		
	var url_split = window.location.href.slice(window.location.href.indexOf('/') + 1).split('/');
	var form_submit = $('.content form');

	
	if (form_submit.length > 0) {
	
		form_submit
			.submit(function (e) {
				e
					.preventDefault();
					
				var submit_error = submit_error_first = false;
				var postdata_form = {};

				$('.error-box:visible')
					.hide();

					$('#img_loading')
						.css('display', 'block');
					
 					$(':submit, :reset', form_submit)
						.attr('disabled', 'disabled')
						.fadeTo(1000, .4);
					$
						.post(
							$(this).attr('action'),
							form_submit.serialize(),
							function (receivedData) {
								$('#img_loading')
									.css('display', 'none');

								$(':submit, :reset', form_submit)
									.removeAttr('disabled')
									.fadeTo(1000, 1);
									
								switch (receivedData.status) {
									case 1:
										$.colorbox({
											width: '55%',
											minHeight: '100px',
											maxHeight: '95%',
											html: receivedData.msg
										}); 

										form_submit
											.get(0)
											.reset();
									break;
									case 2:
										$('body')
											.append(
												$('<div />', {
													'id': 'payment_container'
												})
												.append(receivedData.msg)
											)
											.children('#payment_container')
											.find('form')
											.first()
											.submit();
									break;
									default:
										$('.error-box')
											.css('display', 'block')
											.html(receivedData.msg);
									break;
								}
							},
							'json'
						);
			});
	}	

	

	$('[name=payment_method]')
		.change(function() {
			$('#payment_submit').attr("disabled", true);
			$('#img_loading')
									.css('display', 'block');
			var payment_method = $('[name=payment_method]:checked').val();
			$.post('/ajax/payment.php', {
				serverid: $('[name="serverid"]').val(),
				payment_method: payment_method,
				value: $('[name="value"]').val()
			}, 		
				function(data) {
					$('#img_loading')
									.css('display', 'none'),
					$('#payment_method_value').html(data);
				}
			);
		});
	
	if (url_split[2] == 'balance-add') {
		var payment_method = $('[name=payment_method]:checked').val();
		$('#payment_submit').attr("disabled", true);
		$('#img_loading')
									.css('display', 'block');
		$.post('/ajax/payment.php', {
			serverid: $('[name="serverid"]').val(),
			payment_method: payment_method,
			value: $('[name="value"]').val()
		}, 		
			function(data) {
					$('#img_loading')
									.css('display', 'none'),				
				$('#payment_method_value').html(data);
			}
		);	
	
	$('#balance_value')
		.change(function() {
			$('#payment_submit').attr("disabled", true);
			$('#img_loading')
									.css('display', 'block');
			$.post('/ajax/payment.php', {
			serverid: $('[name="serverid"]').val(),
			payment_method: payment_method,
			value: $('[name="value"]').val()
			}, 		
				function(data) {
					$('#img_loading')
									.css('display', 'none'),					
					$('#payment_method_value').html(data);
				}
			);
		});
		
		$('#payment_submit').attr("disabled", true);
		$.post('/ajax/payment.php', {
			serverid: $('[name="serverid"]').val(),
			payment_method: payment_method,
			value: $('[name="value"]').val()
		}, 		
			function(data) {
				$('#payment_method_value').html(data);
			}
		);
}
	var map_select = $('#map');

	map_select
		.change(function(e) {
			e
				.preventDefault();
			
			var objs = $('#newmap, #newmaptext');
			
			objs
				.fadeOut(500, function() {
					$('#newmap')
						.unbind('load')
						.load(function() {
							objs
								.fadeIn(500);
						})
						.attr('src', '/images/maps/' + map_select.val() + '.jpg');
						
					$('#newmaptext')
						.text(map_select.val());
				});
		});
		
	var refresh_button = $('#refresh_button');

	refresh_button
	.click(function (e) {
 			var obj = $('#img_field');
			
			$.post(
				'/submit/gametracker_preview.php',
				{
				img: $('#img').val(),
				
				font_hostname: $('#font_hostname').val(),
				font_addr: $('#font_addr').val(),
				font_players: $('#font_players').val(),
				font_map: $('#font_map').val(),
				font_status: $('#font_status').val(),
				
				color_hostname: $('#color_hostname').val(),
				color_addr: $('#color_addr').val(),
				color_players: $('#color_players').val(),
				color_map: $('#color_map').val(),
				color_status: $('#color_status').val(),

				size_hostname: $('#size_hostname').val(),
				size_addr: $('#size_addr').val(),
				size_players: $('#size_players').val(),
				size_map: $('#size_map').val(),
				size_status: $('#size_status').val()
				
				},				
				
				function(returnedData) {
					obj
						.slideUp(500, function() {
							obj
							.html(returnedData)
							.slideDown(500);
						});
                },
				'html'
			);

	});	
		

//Plugins
	var hostname = $('#plugin');

	hostname
		.change(function() {
			var obj = $('#plugin_info');
			
			obj
				.slideUp(500, function() {
					$
						.get(
							'/ajax/html.php',
							{
								type: 'plugin',
								content: hostname.val()
							},
							function(receivedData) {
								obj
									.html(receivedData)
									.slideDown(500);
							}
						);
				});
		});
		
	$('#link_more')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#content_more')
				.slideToggle(500);
		});
		
	$('#admins_more_link')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#admins_more')
				.slideToggle(500);
		});	

	$('#plugins_more_link')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#plugins_more')
				.slideToggle(500);
		});	
		
	$('#flags_help_link')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#flags_help')
				.slideToggle(500);
		});
	$('#plugins_order_link')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#plugins_order_form')
				.slideToggle(500);
		});

	$('#amx_admin_more')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#amx_admin_more_content')
				.slideToggle(500);
		});	

	$('#map_info_button')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#map_info')
				.slideToggle(500);
		});

	$('#server_status_button')
		.click(function(e) {
			e
				.preventDefault();
				
			$('#server_status_info')
				.slideToggle(500);
		});
		
			
	$('select[name="mode"]')
	.change(function (e) {
		if ($(this).val() == "1" || $(this).val() == "") {
			$('#mode_info').slideUp(500);
		}
		else {
 			var obj = $('#mode_info');
			
			obj
				.slideUp(500, function() {
					$
						.get(
							'/ajax/html.php',
							{
								type: 'mode',
								content: $('#mode').val()
							},
							function(receivedData) {
								obj
									.html(receivedData)
									.slideDown(500);
							}
						);
				});
		}
	});
	
	$('select[id="anticheat"]')
	.change(function (e) {
 			var obj = $('#anticheat_info');
			
			obj
				.slideUp(500, function() {
					$
						.get(
							'/ajax/html.php',
							{
								type: 'anticheat',
								content: $('#anticheat').val()
							},
							function(receivedData) {
								obj
									.html(receivedData)
									.slideDown(500);
							}
						);
				});
	});	
	
	$('[name=amx_admin_delete_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-admin-delete.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});
		
	$('#restart_round')
		.click(function(e) {
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/restart.php',
					{
						'restart': 'round'
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});

	$('#restart_map')
		.click(function(e) {
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/restart.php',
					{
						'restart': 'map'
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});	
		
	$('#restart_system')
		.click(function(e) {
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/restart.php',
					{
						'restart': 'system'
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});	
		
	$('#server_start')
		.click(function(e) {
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/control.php',
					{
						'action': 'start'
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});		

	$('#server_stop')
		.click(function(e) {
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/control.php',
					{
						'action': 'stop'
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});			
		
	$('[name=amx_plugin_activate_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-plugin-activate.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});	
		
	$('[name=anticheat_activate_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/anticheat_activate.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});	

	$('[name=anticheat_deactivate_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/anticheat_deactivate.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});			

		
	$('#dashboardPlayersInfo')
		.ready(function() {
			
			var interval = 60000;
			var refresh = function() {
				$('#dashboardPlayersInfo').html('<div style="text-align: center;"><img src="/img/loading.gif" /></div>');
				$.get(
					'/ajax/getPlayers.php',
					function(responseText) {
						$('#dashboardPlayersInfo').html(responseText);
						setTimeout(function() {
							refresh();
						}, interval);						
					}
				);
			};
			refresh();
		});				
		
		
	$('[name=plugin_description]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-plugin-description.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
					}
				);
		});			
		
	$('[name=amx_plugin_deactivate_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-plugin-deactivate.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});	

	$('[name=amx_plugin_delete_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-plugin-delete.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});			

	$('[name=amx_redirect_delete_submit]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/amx-redirect-delete.php',
					{
						'id': id
					},
					function(responseText) {
						alert(responseText);
						window.location.reload();
					}
				);
		});			
		
	$('[name=maplist_remove]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/maplist-delete.php',
					{
						'id': id
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});	

	$('[name=radio_remove]')
		.click(function(e) {
		var id = $(this).attr('rel');
			e
				.preventDefault();
				
			$
				.get(
					'/ajax/radio-delete.php',
					{
						'id': id
					},
					function(responseText) {
						$.colorbox({
							width: '55%',
							height: '550px',
							html: responseText
						}); 
					}
				);
		});		
		
		$('[name=ban_check]').click(function () {
			var thisCheck = $(this);
			if (thisCheck.is(':checked')) {
				$('#ban_details').slideDown(500);
			}
			else {
				$('#ban_details').slideUp(500);
			}
		});

	$('#select_all').change(function() {
		var checkboxes = $(this).closest('form').find(':checkbox');
		if($(this).is(':checked')) {
			checkboxes.attr('checked', 'checked');
		} else {
			checkboxes.removeAttr('checked');
		}
	});		
});
/* ]]> */
