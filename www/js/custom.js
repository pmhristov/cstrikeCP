$(document).ready(function() {
	// ------ ONLY FOR DEMONSTRATION ------- //
	$(".expand_custom a").click(function(e){
		e.preventDefault();
		e.stopPropagation();
		var el = $(this).parents('li');
		el.find('.custom .expand_custom').html('<img src="img/preload.gif" alt="">');
		setTimeout(function() {
		  el.find('.custom .expand_custom').parent().hide();
		  el.find('.custom-hidden').removeClass('custom-hidden');
		}, 500);
	});
	$('.progress-preview').each(function(){
		var el = $(this);
		function up(){
			var width = el.find('.bar').width();
			var parentWidth = el.width();
			var percent = Math.round((100*width/parentWidth));
			var plus = Math.round(Math.random() * 10);
			var newPercent = percent + plus;
			if(newPercent > 100){
				newPercent = 0;
			}
		  	el.find('.bar').width(newPercent+"%").html(newPercent+"%");
		  	setTimeout(up, 1500);
		}
		up();
	});
	$(".style-toggler").click(function(e){
		if($('.style-switcher').position().left < -1){
			$(this).animate({
				left:'+=133'
			});
			$('.style-switcher').animate({
				left:'-1px'
			},400);
		} else {
			$(this).animate({
					left:'-=133'
				});
				$('.style-switcher').animate({
					left:'-140px'
				},400);
			}
	});
	var $style = $(".style-toggler");
	var $styleswitcher = $('.style-switcher');
	$(window).scroll(function(){
		$style.stop().animate({
			top:$(window).scrollTop()+100
		},200);
		$styleswitcher.stop().animate({
			top:$(window).scrollTop()+100
		},200);
	});
	$(".style-switcher a").click(function(e){
		$('link').last().attr('href', "css/"+this.className+".css");
	});
/* 	$("input[type=submit], button[type=submit]").click(function(e){
		if(!$(this).parents('form').hasClass("wizard")){
			$.jGrowl("Form was submitted and saved to database.");
		}
	}); */
	if($('.flot').length > 0){
	  	var sin = [], cos = [], tmp = [];
		for (var i = 0; i < 16; i += 0.5) {
			sin.push([i, Math.sin(i)]);
			cos.push([i, Math.cos(i)]);
		}

		var options = {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			yaxis: { min: -1.1, max: 1.1 },
			colors: [ '#2872bd', '#666666', '#feb900', '#128902', '#c6c12f']
		};
		var options2 = {
			series: {
				pie: { 
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 1,
						formatter: function(label, series){
							return '<div style="font-size:12px;text-align:center;padding:2px;color:white;font-weight:bold">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						background: { opacity: 0.8 }
					}
				}
			},
			legend:{
				show:false
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: [ '#2872bd', '#666666', '#feb900', '#128902', '#c6c12f']
		};

		if($('.flot.flot-line').length > 0){
			$.plot($(".flot.flot-line"), [ {label: "Active guests", data: sin}, {label: "Active members", data: cos} ] , options);
		}

		if($(".flot-pie").length > 0){
			$.plot($(".flot-pie"), [ {label: "Active guests", data: 5}, {label: "Active members", data: 10},{label: "Label #3", data: 3},{label: "Label #4", data: 7} ] , options2);
		}
		var d1 = [];
		for (var i = 0; i <= 24; i += 1)
			d1.push([i, parseInt(Math.random() * 28)]);

		var ds = new Array();

		ds.push({
			data:d1,
			bars: {
				show: true, 
				barWidth: 0.6, 
				order: 1,
				lineWidth : 2
			}
		});

		if($('.flot-bar').length > 0){
			$.plot($(".flot-bar"), ds, {grid: {
				hoverable: true,
				clickable: true
			},colors: [ '#2872bd', '#666666', '#feb900', '#128902', '#c6c12f']});
		}

		if($('.flot-live').length > 0){
			$(function () {
				var data = [], totalPoints = 300;
				function getRandomData() {
					if (data.length > 0)
						data = data.slice(1);

					while (data.length < totalPoints) {
						var prev = data.length > 0 ? data[data.length - 1] : 50;
						var y = prev + Math.random() * 10 - 5;
						if (y < 0)
							y = 0;
						if (y > 100)
							y = 100;
						data.push(y);
					}

					var res = [];
					for (var i = 0; i < data.length; ++i)
						res.push([i, data[i]])
					return res;
				}

				var updateInterval = 30;


				var options = {
				series: { shadowSize: 0 },
				yaxis: { min: 0, max: 100 },
				xaxis: { show: false }
				};
				var plot = $.plot($(".flot-live"), [ getRandomData() ], options);

				function update() {
					plot.setData([ getRandomData() ]);
					plot.draw();

					setTimeout(update, updateInterval);
				}

				update();
			});
		}

	$(".flot-bar,.flot-pie,.flot,.flot-multi").bind("plothover", function (event, pos, item) {
		if (item) {
			if(event.currentTarget.className == 'flot flot-bar'){
				var y = Math.round(item.datapoint[1]);
			} else if(event.currentTarget.className == 'flot flot-pie') {
				var y = Math.round(item.datapoint[0])+"%";
			} else if(event.currentTarget.className == 'flot flot-line'){
				var y = (Math.round(item.datapoint[1] * 1000)/1000);
			} else {
				var y = (Math.round(item.datapoint[1]*1000)/1000)+"€";
			}
			$("#tooltip").remove();
			showTooltip(pos.pageX, pos.pageY,"Value = "+y);
		}
		else {
			$("#tooltip").remove();
			previousPoint = null;            
		}
	});

	function showTooltip(x, y, contents) {
		$('<div id="tooltip">' + contents + '</div>').css( {
			top: y + 5,
			left: x + 10,
		}).appendTo("body").show();
	}
	}
	// ------ END ONLY FOR DEMONSTRATION ------- //

	// ------ DO NOT CHANGE ------- //
	$('.deleteRow').click(function(e){
		e.preventDefault();
		$(this).parents('tr').fadeOut();
	});

	$(".animateRow").click(function(e){
		e.preventDefault();
		var el = $(this).parents('tr');
		var target = $($(this).data('target'));
		var defaultColor = target.find('a.dropdown-toggle').css('color');
		var titleindex = parseInt($(this).data('title'))-1;
		var userindex = parseInt($(this).data('user'))-1;
		var dateindex = parseInt($(this).data('date'))-1;
		var title = el.find('td:eq('+titleindex+')').html();
		var user = el.find('td:eq('+userindex+') a').html();
		var userContent = el.find('td:eq('+userindex+') a').data('content');
		var date = el.find('td:eq('+dateindex+')').html();
		el.css({
			position:'absolute',
			left:el.position().left,
			top:el.position().top
		});
		el.animate({
			left:target.position().left,
			top:target.position().top,
			width:target.width(),
			height:target.height()
		}, 1000, function(){
			el.hide();
			var value = parseInt(target.find('a.dropdown-toggle .label').html());
			if(isNaN(value)){
				value = 0;
			}
			target.find('a.dropdown-toggle .label').html(value+1);
			if(target.find('.label').is(":hidden")){
				target.find('.label').show();
			}
			target.find('a.dropdown-toggle').stop().animate({
				backgroundColor:target.find('a.dropdown-toggle .label').css('backgroundColor'),
				color:'#fff'
			},300, function(){
				target.find('a.dropdown-toggle').animate({
				backgroundColor:target.css('background-color'),
				color:defaultColor
			}, 200, function(){
				target.find('a.dropdown-toggle').css('background-color', '').css('color', '');
			});
			});
		});
		target.find('.dropdown-menu').append('<li class="custom"><div class="title">'+title+'<span>'+date+' by <a href="#" class="pover" data-title="'+user+'" data-content="'+userContent+'">'+user+'</a></span></div><div class="action"><div class="btn-group"><a href="#" class="tip btn btn-mini" title="Show order"><img src="img/icons/fugue/magnifier.png" alt=""></a><a href="#" class="tip btn btn-mini" title="Delete order"><img src="img/icons/fugue/cross.png" alt=""></a></div></div></li>');
		$(".pover").popover();$(".tip").tooltip();
	});
	$('.main-nav > li.active > a').click(function(e){
		if($(window).width() <= 767){
			e.preventDefault();
			if($(this).hasClass('open') && (!$(this).hasClass('toggle-collapsed'))){
				$(this).removeClass('open');
				$(this).parents('.main-nav').find('li').each(function(e){
					$(this).find('.collapsed-nav').addClass('closed');
					$(this).hide();
				});
				$(this).parent().show();
			} else {
				if($(this).hasClass('toggle-collapsed')){
					$(this).parent().addClass('active open');
				}
				$(this).addClass('open');
				$(this).parents('.main-nav').find('li').show();
			}
		}
	});
	$(".mini > li > a").hover(function(e){
  	e.stopPropagation();
  	if(!$(this).parent().hasClass("open")){
		$(this).find(".label").stop().animate({
		  		top: '-10px'
		  	},200, function(){
		  		$(this).stop().animate({top: '-6px'},100);
		  	});
  	}
	}, function(){});
	$('.toggle-collapsed').click(function(e){
		e.preventDefault();
	if($(this).parent().find('.collapsed-nav').is(":visible")){
		$(this).parent().removeClass("open");
		$(this).parent().find('.collapsed-nav').slideUp();
		$(this).find('img').attr("src", 'img/toggle-subnav-down.png');
	} else {
		$(this).parent().addClass("active open");
		$(this).parent().find('.collapsed-nav').slideDown();
		$(this).find('img').attr("src", 'img/toggle-subnav-up-white.png');
	}
	});

	$('.collapsed-nav li a').hover(function(e){
		if(!$(this).parent().hasClass('active')){
			$(this).stop().animate({
	  		marginLeft: '5px'
	  	}, 300);
		}
	}, function(){
	$(this).stop().animate({
			marginLeft: '0'
		}, 100);
	});
	$('a.preview').live('mouseover mouseout mousemove click',function(e){
			if(e.type == 'mouseover'){
				$('body').append('<div id="image_preview"><img src="'+$(this).attr('href')+'" width="150"></div>');
				$("#image_preview").fadeIn();
			} else if(e.type == 'mouseout') {
				$("#image_preview").remove();
			} else if(e.type == 'mousemove'){
				$("#image_preview").css({
					top:e.pageY+10+"px",
					left:e.pageX+10+"px"
				});
			} else if(e.type == 'click'){
				$("#image_preview").remove();
			}
		});

	$('.sel_all').click(function(){
		$(this).parents('table').find('.selectable-checkbox').attr('checked', this.checked);
	});
	// ------ END DO NOT CHANGE ------- //

	// ------ PLUGINS ------- //
	// - dataTables
	if($('.dataTable').length > 0){
		$('.dataTable').each(function(e){
			var opt = {
				"sPaginationType": "bootstrap",
					"oLanguage":{
						"sSearch": "",
						"sLengthMenu": "Limit: _MENU_"
					}
			};
			if($(this).hasClass("dataTable-noheader")){
				opt.bFilter = false;
				opt.bLengthChange = false;
			}
			if($(this).hasClass("dataTable-nofooter")){
				opt.bInfo = false;
				opt.bPaginate = false;
			}
			if($(this).hasClass("dataTable-nosort")){
				var column = $(this).data('nosort');
				opt.aoColumnDefs =  [
	          		{ 'bSortable': false, 'aTargets': [ column ] }
	      		];
			}
			$(this).dataTable(opt);
			$('.dataTables_filter input').attr("placeholder", "Search here...");
			$('.dataTables_length select').attr("class", "uniform");
		});
	}
	// - validation
	if($('.validate').length > 0){
		$('.validate').validate({
			highlight: function(label) {
			    	$(label).closest('.control-group').addClass('error');
			    },
			     success: function(label) {
			    	label.addClass('valid').closest('.control-group').addClass('success');
			    }
		});
	}
	// - wizard
	if($(".wizard").length > 0){
		$(".wizard").formwizard({ 
		 	formPluginEnabled: true,
		 	validationEnabled: true,
		 	focusFirstInput : false,
		 	validationOptions: {
		 		highlight: function(label) {
			    	$(label).closest('.control-group').addClass('error');
			    },
			     success: function(label) {
			    	label.addClass('valid').closest('.control-group').addClass('success');
			    }
		 	},
		 	formOptions :{
				success: function(data){
				},
				beforeSubmit: function(data){
					$('#myModal').modal('show');
				},
				dataType: 'json',
				resetForm: true
		 	}	
		 }
		);
	}
	// - tooltips
	$(".tip").tooltip();
	// - popover
	$(".pover").popover();
	$(".pover").click(function(e){
		e.preventDefault();
		if($(this).data('trigger') == "manual"){
			$(this).popover('toggle');
		}
	});
	$(".table-has-pover").live('mouseenter', function(){
		$('.pover').popover();
	});
	// - growl-like notification
	if($('.opengrowl').length > 0){
		$(".opengrowl").click(function(e){
			e.preventDefault();
			var content = $(this).data('content');
			if($(this).hasClass("hasheader")){
				var head = $(this).data('header');
				$.jGrowl(content, { header: head });
			} else {
				$.jGrowl(content);
			}
		});
	}
	// - fancybox
	if($('.fancy').length > 0){
		$('.fancy').live('mouseenter',function(){
			$('.fancy').fancybox();
		});
	}
	// - quickstats
	if($('.small-chart').length > 0){
		 $('.small-chart').each(function(e){
	  	var color = "#" + $(this).data('color');
	  	var stroke = "#" + $(this).data('stroke');
	  	var type = $(this).data('type');
	  	$(this).peity(type, {
	  		colour: color,
	  		colours: ['#dddddd', color],
	  		diameter: 32,
	  		strokeColour: stroke,
	  		width: 60,
	  		height:32
	  	});
	  });
	}
	// - counter
	if($('.counter').length > 0){
		$('.counter').each(function(e){
			var max = $(this).data('max');
			if(!max) max = 100;
			$(this).textareaCount({
				'maxCharacterSize': max,
				'displayFormat' : 'Characters left: #left'
			});
		});
	}
	// - uniform
	if($('.uniform').length > 0){
		$('.uniform').uniform({
			radioClass: 'uniRadio'
		});
	}
	// - chosen
	if($('.cho').length > 0){
		$(".cho").chosen({no_results_text: "Няма намерени резултати за:"});
	}
	// - cleditor
	if($('.cleditor').length > 0){
		$(".cleditor").cleditor({width:'auto'});
	}
	// - spinner
	if($('.spinner').length > 0){
		$('.spinner').spinner();
	}
	// - timepicker
	if($('.timepicker').length > 0){
		$('.timepicker').timepicker({
			defaultTime: 'current',
			minuteStep: 1,
			disableFocus: true,
			template: 'dropdown'
		});
	}
	// - tagsinput
	if($(".tagsinput").length > 0){
		$('.tagsinput').tagsInput({width:'auto', height:'auto'});
	}
	// - plupload
	if($('.plupload').length > 0){
		$('.plupload').pluploadQueue({
			runtimes : 'html5,gears,flash,silverlight,browserplus',
			url : 'js/plupload/upload.php',
			max_file_size : '10mb',
			chunk_size : '1mb',
			unique_names : true,
			resize : {width : 320, height : 240, quality : 90},
			filters : [
				{title : "Image files", extensions : "jpg,gif,png"},
				{title : "Zip files", extensions : "zip"}
			],
			flash_swf_url : 'js/plupload/plupload.flash.swf',
			silverlight_xap_url : 'js/plupload/plupload.silverlight.xap'
		});
		$(".plupload_header").remove();
		$(".plupload_progress_container").addClass("progress").addClass('progress-striped');
		$(".plupload_progress_bar").addClass("bar");
		$(".plupload_button").each(function(e){
			if($(this).hasClass("plupload_add")){
				$(this).attr("class", 'btn btn-primary pl_add btn-small');
			} else {
				$(this).attr("class", 'btn btn-success pl_start btn-small');
			}
		});
	}
	// - datepicker
	if($('.datepick').length > 0){
		$('.datepick').datepicker();	
	}
	// - masked inputs
	if($('.mask_date').length > 0){
		$(".mask_date").inputmask("9999/99/99");	
	}
	if($('.mask_phone').length > 0){
		$(".mask_phone").inputmask("(999) 999-9999");
	}
	if($('.mask_serialNumber').length > 0){
		$(".mask_serialNumber").inputmask("9999-9999-99");	
	}
	if($('.mask_productNumber').length > 0){
		$(".mask_productNumber").inputmask("AAA-9999-A");	
	}
	// - slider
	if($('.slider').length > 0){
		$(".slider").each(function(e){
			var orient = $(this).data('orientation');
			var min = $(this).data('min');
			var max = $(this).data('max');
			var step = $(this).data('step');
			var range = $(this).data('range');
			var rangestart = $(this).data('rangestart');
			var rangestop = $(this).data('rangestop');


			if(orient == ""){
				orient = "horizontal";
			}

			var el = $(this);

			if(range != undefined){
				$(this).find('.slide').slider({
					range:true,
					values:[rangestart, rangestop],
					orientation: orient,
					min: min,
					max: max,
					step: step,
					slide: function( event, ui ) {
						el.parent().find('.amount').html( ui.values[0]+" - "+ui.values[1] );
					}
				});
				$( this ).find('.amount').html( $(this).parent().find('.slide').slider( "values",0 )+" - "+$(this).parent().find('.slide').slider( "values",1 ) );
			} else {
				$(this).find('.slide').slider({
					orientation: orient,
					min: min,
					max: max,
					step: step,
					slide: function( event, ui ) {
						el.parent().find('.amount').html( ui.value );
					}
				});
				$( this ).find('.amount').html( $(this).parent().find('.slide').slider( "value" ) );
			}

		});
	}

});