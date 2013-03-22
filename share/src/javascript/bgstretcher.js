/*
	Background Stretcher jQuery Plugin
	� 2011 ajaxBlender.com
	For any questions please visit www.ajaxblender.com 
	or email us at support@ajaxblender.com
	
	Version: 2.0.1
*/

;(function(jQuery){
	/*  Variables  */
	var container = null;
	var allLIs = '', containerStr = '';
	
	var element = this;
	var _bgStretcherPause = false;
	var _bgStretcherAction = false;
	var _bgStretcherTm = null;
	var random_line = new Array();
	var random_temp = new Array();
	var r_image = 0;
	var swf_mode = false;
	var img_options = new Array();
	
	jQuery.fn.bgStretcher = function(settings){
		
		if (jQuery('.bgstretcher-page').length || jQuery('.bgstretcher-area').length) {
			if(typeof(console) !== 'undefined' && console != null) console.log('More than one bgStretcher'); 
			return false;
		}
		settings = jQuery.extend({}, jQuery.fn.bgStretcher.defaults, settings);
		jQuery.fn.bgStretcher.settings = settings;
		
		function _build(body_content){
			if(!settings.images.length){ return; }
			
			_genHtml(body_content);

			containerStr = '#' + settings.imageContainer;
			container = jQuery(containerStr);
			allLIs = '#' + settings.imageContainer + ' LI';
			jQuery(allLIs).hide().css({'z-index': 1, overflow: 'hidden'});
			
			if(!container.length){ return; }
			jQuery(window).resize(function(){
				_resize(body_content)
			});
			
			_resize(body_content);
			
			var stratElement = 0;
			/*  Rebuild images for simpleSlide  */
			if (settings.transitionEffect == 'simpleSlide') {
				if (settings.sequenceMode == 'random') {
					if(typeof(console) !== 'undefined' && console != null) {
						console.log('Effect \'simpleSlide\' don\'t be to use with mode random.');
						console.log('Mode was automaticly set in normal.');
					}
				}
				jQuery(allLIs).css({'float': 'left', position: 'static'});
				jQuery(allLIs).show();
				if (jQuery.fn.bgStretcher.settings.slideDirection == 'NW' || jQuery.fn.bgStretcher.settings.slideDirection == 'NE') {
					jQuery.fn.bgStretcher.settings.slideDirection = 'N';
				}
				if (jQuery.fn.bgStretcher.settings.slideDirection == 'SW' || jQuery.fn.bgStretcher.settings.slideDirection == 'SE') {
					jQuery.fn.bgStretcher.settings.slideDirection = 'S';
				}
				if (jQuery.fn.bgStretcher.settings.slideDirection == 'S' || jQuery.fn.bgStretcher.settings.slideDirection == 'E') {
					settings.sequenceMode = 'back';
					jQuery(allLIs).removeClass('bgs-current');
					jQuery(allLIs).eq(jQuery(allLIs).length - jQuery.fn.bgStretcher.settings.startElementIndex - 1).addClass('bgs-current');
					if (jQuery.fn.bgStretcher.settings.slideDirection == 'E') {
						l = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).width()*(-1);
						t = 0;
					} else { // S
						t = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).height()*(-1);
						l = 0;
					}
					jQuery(containerStr+' UL').css({left: l+'px', top: t+'px'});
				} else {
					settings.sequenceMode = 'normal';
					if (jQuery.fn.bgStretcher.settings.startElementIndex != 0) {
						if (jQuery.fn.bgStretcher.settings.slideDirection == 'N') {
							t = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).height()*(-1);
							l = 0;
						} else { // W
							l = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).width()*(-1);
							t = 0;
							console.log(l);
						}
						jQuery(containerStr+' UL').css({left: l+'px', top: t+'px'});
					}
				}
			}
			
			if (jQuery(settings.buttonNext).length || jQuery(settings.buttonPrev).length || jQuery(settings.pagination).length){
				if (settings.sequenceMode == 'random') {
					if(typeof(console) !== 'undefined' && console != null) {
						console.log('Don\'t use random mode width prev-button, next-button and pagination.');
					}
				} else {
					/*  Prev and Next Buttons init  */
					if (jQuery(settings.buttonPrev).length){
						jQuery(settings.buttonPrev).addClass('bgStretcherNav bgStretcherNavPrev');
						jQuery(settings.buttonPrev).click(function(){
							jQuery.fn.bgStretcher.buttonSlide('prev');
						});
					}
					if (jQuery(settings.buttonNext).length){
						jQuery(settings.buttonNext).addClass('bgStretcherNav bgStretcherNavNext');
						jQuery(settings.buttonNext).click(function(){
							jQuery.fn.bgStretcher.buttonSlide('next');
						});
					}
					/*  Pagination  */
					if (jQuery(settings.pagination).length) {
						jQuery.fn.bgStretcher.pagination();
					}
				}
			}
			
			/*  Random mode init  */
			if (settings.sequenceMode == 'random') {
				var i = Math.floor(Math.random()*jQuery(allLIs).length);
				jQuery.fn.bgStretcher.buildRandom(i);
				if (settings.transitionEffect != 'simpleSlide') {
					jQuery.fn.bgStretcher.settings.startElementIndex = i;
				}
				stratElement = i;
			} else {
				if (jQuery.fn.bgStretcher.settings.startElementIndex > (jQuery(allLIs).length - 1)) jQuery.fn.bgStretcher.settings.startElementIndex = 0;
				stratElement = jQuery.fn.bgStretcher.settings.startElementIndex;
				if (settings.transitionEffect == 'simpleSlide') {
					if (jQuery.fn.bgStretcher.settings.slideDirection == 'S' || jQuery.fn.bgStretcher.settings.slideDirection == 'E') {
						stratElement = jQuery(allLIs).length - 1 - jQuery.fn.bgStretcher.settings.startElementIndex;
					}
				}
			}
			
			jQuery(allLIs).eq(stratElement).show().addClass('bgs-current');
			jQuery.fn.bgStretcher.loadImg(jQuery(allLIs).eq(stratElement));
			
			/*  Go slideshow  */
			if(settings.slideShow && jQuery(allLIs).length > 1){
				_bgStretcherTm = setTimeout('jQuery.fn.bgStretcher.slideShow(\''+jQuery.fn.bgStretcher.settings.sequenceMode+'\', -1)', settings.nextSlideDelay);
			}
			
		};
		
		function _resize(body_content){
			var winW = 0;
			var winH = 0;
			var contH = 0;
			var contW = 0;
			
			if (jQuery('BODY').hasClass('bgStretcher-container')) {
				winW = jQuery(window).width();
				winH = jQuery(window).height(); 
				if ((jQuery.browser.msie) && (parseInt(jQuery.browser.version) == 6)) {
					jQuery(window).scroll(function(){
						jQuery('#'+settings.imageContainer).css('top', jQuery(window).scrollTop());
					});					
				}
			} else {
				jQuery('.bgstretcher').css('position', 'absolute').css('top', '0px');
				winW = body_content.width();
				winH = body_content.height(); 
			}
			
			var imgW = 0, imgH = 0;
			var leftSpace = 0;
			
			//	Max image size
			if(settings.maxWidth != 'auto'){
				if (winW > settings.maxWidth){
					leftSpace = (winW - settings.maxWidth)/2;
					contW = settings.maxWidth;
				} else contW = winW;
			} else contW = winW;
			if(settings.maxHeight != 'auto'){
				if (winH > settings.maxHeight){
					contH = settings.maxHeight;
				} else contH = winH;
			} else contH = winH;
			
			//	Update container's size
			container.width(contW);
			container.height(contH);
			
			//	Non-proportional resize
			if(!settings.resizeProportionally){
				imgW = contH;
				imgH = contH;
			} else {
				var initW = settings.imageWidth, initH = settings.imageHeight;
				var ratio = initH / initW;
				
				imgW = contW;
				imgH = Math.round(contW * ratio);
				
				if(imgH < contH){
					imgH = contH;
					imgW = Math.round(imgH / ratio);
				}
			}
			
			// Anchoring
			var mar_left = 0;
			var mar_top = 0;
			var anchor_arr;
			if (jQuery.fn.bgStretcher.settings.anchoring != 'left top') {
				anchor_arr = (jQuery.fn.bgStretcher.settings.anchoring).split(' ');
				if (anchor_arr[0] == 'right') {
					mar_left = (winW - contW);
				} else {
					if (anchor_arr[0] == 'center') mar_left = Math.round((winW - contW)/2);
				}
				if (anchor_arr[1] == 'bottom') {
					mar_top = (winH - contH);
				} else {
					if (anchor_arr[1] == 'center') {
						mar_top = Math.round((winH - contH)/2);
					}
				}
				container.css('marginLeft', mar_left+'px').css('marginTop', mar_top+'px');
			}
			mar_left = 0;
			mar_top = 0;
			if (jQuery.fn.bgStretcher.settings.anchoringImg != 'left top') {
				anchor_arr = (jQuery.fn.bgStretcher.settings.anchoringImg).split(' ');
				if (anchor_arr[0] == 'right') {
					mar_left = (contW - imgW);
				} else {
					if (anchor_arr[0] == 'center') mar_left = Math.round((contW - imgW)/2);
				}
				if (anchor_arr[1] == 'bottom') {
					mar_top = (contH - imgH);
				} else {
					if (anchor_arr[1] == 'center') {
						mar_top = Math.round((contH - imgH)/2);
					}
				}
			}
			img_options['mar_left'] = mar_left;
			img_options['mar_top'] = mar_top;
			
			//	Apply new size for images
			if (container.find('LI:first').hasClass('swf-mode')) {
				
				var path_swf = container.find('LI:first').html();
				container.find('LI:first').html('<div id="bgstretcher-flash">&nbsp;</div>');
				
				var header = new SWFObject('flash/stars.swf', 'flash-obj', contW, contH, '9');
				header.addParam('wmode', 'transparent');
				header.write('bgstretcher-flash');
				
			}; 
			img_options['imgW'] = imgW;
			img_options['imgH'] = imgH;
			
			if(!settings.resizeAnimate){
				container.children('UL').children('LI.img-loaded').find('IMG').css({'marginLeft': img_options["mar_left"]+'px', 'marginTop': img_options["mar_top"]+'px'});
				container.children('UL').children('LI.img-loaded').find('IMG').css({'width': img_options["imgW"]+'px', 'height': img_options["imgH"]+'px'});
			} else {
				container.children('UL').children('LI.img-loaded').find('IMG').animate({'marginLeft': img_options["mar_left"]+'px', 'marginTop': img_options["mar_top"]+'px'}, 'normal');
				container.children('UL').children('LI.img-loaded').find('IMG').animate({'width': img_options["imgW"]+'px', 'height': img_options["imgH"]+'px'}, 'normal');
			}
			
			jQuery(allLIs).width(container.width()).height(container.height());
			
			if (jQuery.fn.bgStretcher.settings.transitionEffect == 'simpleSlide') {
				if (jQuery.fn.bgStretcher.settings.slideDirection == 'W' || jQuery.fn.bgStretcher.settings.slideDirection == 'E') {
					container.children('UL').width(container.width() * jQuery(allLIs).length).height(container.height());
					if ( jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) != -1 ){
						l = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * container.width()*(-1);
						container.children('UL').css({left: l+'px'});
					}
				} else {
					container.children('UL').height(container.height() * jQuery(allLIs).length).width(container.width());
					if ( jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) != -1 ){
						t = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).height()*(-1);
						container.children('UL').css({top: t+'px'});
					}
				}
			}
			
		};
		
		function _genHtml(body_content){
			var code = '';
			var cur_bgstretcher;

			body_content.each(function(){
				jQuery(this).wrapInner('<div class="bgstretcher-page" />').wrapInner('<div class="bgstretcher-area" />');
				code = '<div id="' + settings.imageContainer + '" class="bgstretcher"><ul>';
				// if swf
				if (settings.images.length) {
					var ext = settings.images[0].split('.');
					ext = ext[ext.length-1];
					
					if (ext != 'swf') {
						var ind = 0;
						for(i = 0; i < settings.images.length; i++){
							if (settings.transitionEffect == 'simpleSlide' && settings.sequenceMode == 'back') 
								ind = settings.images.length-1-i;
									else ind = i;
							if (jQuery.fn.bgStretcher.settings.preloadImg) {
									code += '<li><span class="image-path">' + settings.images[ind] + '</span></li>';
								} else {
									code += '<li class="img-loaded"><img src="' + settings.images[ind] + '" alt="" /></li>';
								}		
						}
					} else {
						code += '<li class="swf-mode">' + settings.images[0] + '</li>';	
					}
				}
				
				code += '</ul></div>';
				cur_bgstretcher = jQuery(this).children('.bgstretcher-area');
				jQuery(code).prependTo(cur_bgstretcher);
				cur_bgstretcher.css({position: 'relative'});
				cur_bgstretcher.children('.bgstretcher-page').css({'position': 'relative', 'z-index': 3});
			});

		};
		
		/*  Start bgStretcher  */
		this.addClass('bgStretcher-container');
		_build(this);
	};
	
	jQuery.fn.bgStretcher.loadImg = function(obj){
		if (obj.hasClass('img-loaded')) return true;
		obj.find('SPAN.image-path').each(function(){
			var imgsrc = jQuery(this).html();
			var imgalt = '';
			var parent = jQuery(this).parent();
			var img = new Image();
			
			jQuery(img).load(function () {
				jQuery(this).hide();
				parent.prepend(this);
				jQuery(this).fadeIn('100');
			}).error(function () {
			}).attr('src', imgsrc).attr('alt', imgalt);
			
			jQuery(img).css({'marginLeft': img_options["mar_left"]+'px', 'marginTop': img_options["mar_top"]+'px'});
			jQuery(img).css({'width': img_options["imgW"]+'px', 'height': img_options["imgH"]+'px'});
		});
		obj.addClass('img-loaded');
		return true;
	}
	
	jQuery.fn.bgStretcher.play = function(){
       _bgStretcherPause = false;
       jQuery.fn.bgStretcher._clearTimeout();
       jQuery.fn.bgStretcher.slideShow(jQuery.fn.bgStretcher.settings.sequenceMode, -1);
       
	};
	
	jQuery.fn.bgStretcher._clearTimeout = function(){
       if(_bgStretcherTm != null){
           clearTimeout(_bgStretcherTm);
           _bgStretcherTm = null;
       }
	}
	
	jQuery.fn.bgStretcher.pause = function(){
	   _bgStretcherPause = true;
	   jQuery.fn.bgStretcher._clearTimeout();
	};
	
	jQuery.fn.bgStretcher.sliderDestroy = function(){
		var cont = jQuery('.bgstretcher-page').html();
		jQuery('.bgStretcher-container').html('').html(cont).removeClass('bgStretcher-container');
		jQuery.fn.bgStretcher._clearTimeout();
		_bgStretcherPause = false;
	}
	
	/*  Slideshow  */
	jQuery.fn.bgStretcher.slideShow = function(sequence_mode, index_next){	
		_bgStretcherAction = true;
		if (jQuery(allLIs).length < 2) return true;
		var current = jQuery(containerStr + ' LI.bgs-current');
		var next;
        
        jQuery(current).stop(true, true);
		
		if (index_next == -1) {
			switch (sequence_mode){
				case 'back':
					next = current.prev();
					if(!next.length){ next = jQuery(containerStr + ' LI:last'); 	}
					break;
				case 'random':
					if (r_image == jQuery(containerStr + ' LI').length) {
						jQuery.fn.bgStretcher.buildRandom(random_line[jQuery(containerStr + ' LI').length-1]);
						r_image = 0;
					}
					next = jQuery(containerStr + ' LI').eq(random_line[r_image]);
					r_image++;
					break;
				default:
					next = current.next();
					if(!next.length){ next = jQuery(containerStr + ' LI:first'); }	
			}
		} else {
			next = jQuery(containerStr + ' LI').eq(index_next);
		}
		
		jQuery(containerStr + ' LI').removeClass('bgs-current');
		jQuery.fn.bgStretcher.loadImg(next);
		next.addClass('bgs-current');
		
		switch (jQuery.fn.bgStretcher.settings.transitionEffect){
			case 'fade':
				jQuery.fn.bgStretcher.effectFade(current, next);
				break;
			case 'simpleSlide':
				jQuery.fn.bgStretcher.simpleSlide();
				break;
			case 'superSlide':
				jQuery.fn.bgStretcher.superSlide(current, next, sequence_mode);
				break;
			default : 
				jQuery.fn.bgStretcher.effectNone(current, next);
				
			}
		if (jQuery(jQuery.fn.bgStretcher.settings.pagination).find('LI').length) {
			jQuery(jQuery.fn.bgStretcher.settings.pagination).find('LI.showPage').removeClass('showPage');
			jQuery(jQuery.fn.bgStretcher.settings.pagination).find('LI').eq(jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current'))).addClass('showPage');
		}
			
		// callback
		if (jQuery.fn.bgStretcher.settings.callbackfunction) {
			if(typeof jQuery.fn.bgStretcher.settings.callbackfunction == 'function')
					jQuery.fn.bgStretcher.settings.callbackfunction.call();
		}	
		
		if(!_bgStretcherPause){
		  _bgStretcherTm = setTimeout('jQuery.fn.bgStretcher.slideShow(\''+jQuery.fn.bgStretcher.settings.sequenceMode+'\', -1)', jQuery.fn.bgStretcher.settings.nextSlideDelay);
		}
	};
	
	/*  Others effects  */
	jQuery.fn.bgStretcher.effectNone = function(current, next){
		next.show();
		current.hide();
		_bgStretcherAction = false;
	};	
	jQuery.fn.bgStretcher.effectFade = function(current, next){
		next.fadeIn( jQuery.fn.bgStretcher.settings.slideShowSpeed );
		current.fadeOut( jQuery.fn.bgStretcher.settings.slideShowSpeed, function(){
			_bgStretcherAction = false;
		} );
	};
	
	jQuery.fn.bgStretcher.simpleSlide = function(){
		var t, l;
		switch (jQuery.fn.bgStretcher.settings.slideDirection) {
			case 'N':
			case 'S':
				t = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).height()*(-1);
				l = 0;
				break;
			default:
				l = jQuery(containerStr + ' LI').index(jQuery(containerStr + ' LI.bgs-current')) * jQuery(containerStr).width()*(-1);
				t = 0;
		}
		jQuery(containerStr+' UL').animate({left: l+'px', top: t+'px'}, jQuery.fn.bgStretcher.settings.slideShowSpeed, function(){
			_bgStretcherAction = false;
		});
		
	};
	
	jQuery.fn.bgStretcher.superSlide = function(current, next, sequence_mode){
		var t, l;
		switch (jQuery.fn.bgStretcher.settings.slideDirection) {
			case 'S':
				t = jQuery(containerStr).height();
				l = 0;
				break;
			case 'E':
				t = 0;
				l = jQuery(containerStr).width();
				break;
			case 'W':
				t = 0;
				l = jQuery(containerStr).width()*(-1);
				break;
			case 'NW':
				t = jQuery(containerStr).height()*(-1);
				l = jQuery(containerStr).width()*(-1);
				break;
			case 'NE':
				t = jQuery(containerStr).height()*(-1);
				l = jQuery(containerStr).width();
				break;
			case 'SW':
				t = jQuery(containerStr).height();
				l = jQuery(containerStr).width()*(-1);
				break;
			case 'SE':
				t = jQuery(containerStr).height();
				l = jQuery(containerStr).width();
				break;	
			default:
				t = jQuery(containerStr).height()*(-1);
				l = 0;

		}
				
		if (sequence_mode == 'back') {
				next.css({'z-index': 2, top: t+'px', left: l+'px'});
				next.show();
				next.animate({left: '0px', top: '0px'}, jQuery.fn.bgStretcher.settings.slideShowSpeed, function(){
						current.hide();
						jQuery(this).css({'z-index': 1});
						_bgStretcherAction = false;
					});
			} else {
					current.css('z-index', 2);
					next.show();
					current.animate({left: l+'px', top: t+'px'}, jQuery.fn.bgStretcher.settings.slideShowSpeed, function(){
						jQuery(this).hide().css({'z-index': 1, top: '0px', left: '0px'});
						_bgStretcherAction = false;
					});
				}	
	};
	
	/*  Build line random images  */
	jQuery.fn.bgStretcher.buildRandom = function(el_not){
		var l = jQuery(allLIs).length;
		var i, j, rt;
		for (i = 0; i < l; i++ ) {
			random_line[i] = i;
			random_temp[i] = Math.random()*l;
		}
		for (i = 0; i < l; i++ ) {
			for (j = 0; j < (l-i-1); j++) {
				if (random_temp[j] > random_temp[j+1]) {
					rt = random_temp[j];
					random_temp[j] = random_temp[j+1];
					random_temp[j+1] = rt;
					rt = random_line[j];
					random_line[j] = random_line[j+1];
					random_line[j+1] = rt;
				}
			}
		}
		
		if (random_line[0] == el_not) {
			rt = random_line[0];
			random_line[0] = random_line[l-1];
			random_line[l-1] = rt;
		}
	};
	
	/*  Prev and Next buttons */
	jQuery.fn.bgStretcher.buttonSlide = function(button_point){
		if (_bgStretcherAction || (jQuery(allLIs).length < 2)) return false;
		var mode = '';
		if (button_point == 'prev') {
			mode = 'back';
			if (jQuery.fn.bgStretcher.settings.sequenceMode == 'back')  mode = 'normal';
		} else {
			mode = jQuery.fn.bgStretcher.settings.sequenceMode;
		}
		jQuery(allLIs).stop(true, true);
		jQuery.fn.bgStretcher._clearTimeout();
		jQuery.fn.bgStretcher.slideShow(mode, -1);
		return false;
	};
	
	/*  Pagination  */
	jQuery.fn.bgStretcher.pagination = function(){
		var l = jQuery(allLIs).length;
		var output = ''; var i = 0;
		if (l > 0) {
			output += '<ul>';
				for (i = 0; i < l; i++){
					output += '<li><a href="javascript:;">'+(i+1)+'</a></li>';
				}
			output += '</ul>';
			jQuery(jQuery.fn.bgStretcher.settings.pagination).html(output);
			jQuery(jQuery.fn.bgStretcher.settings.pagination).find('LI:first').addClass('showPage');
			
			jQuery(jQuery.fn.bgStretcher.settings.pagination).find('A').click(function(){
				if (jQuery(this).parent().hasClass('showPage')) return false;
				jQuery(allLIs).stop(true, true);
				jQuery.fn.bgStretcher._clearTimeout();
				jQuery.fn.bgStretcher.slideShow(jQuery.fn.bgStretcher.settings.sequenceMode, jQuery(jQuery.fn.bgStretcher.settings.pagination).find('A').index(jQuery(this)));
				return false;
			});
			
		}
		return false;
	}
	
	/*  Default Settings  */
	jQuery.fn.bgStretcher.defaults = {
		imageContainer:             'bgstretcher',
		resizeProportionally:       true,
		resizeAnimate:              false,
		images:                     [],
		imageWidth:                 1024,
		imageHeight:                768,
		maxWidth:					'auto',
		maxHeight:					'auto',
		nextSlideDelay:             3000,
		slideShowSpeed:             'normal',
		slideShow:                  true,
		transitionEffect:			'fade', // none, fade, simpleSlide, superSlide
		slideDirection:				'N', // N, S, W, E, (if superSlide - NW, NE, SW, SE)
		sequenceMode:				'normal', // back, random
		buttonPrev:					'',
		buttonNext:					'',
		pagination: 				'',
		anchoring: 					'left top', // right bottom center
		anchoringImg: 				'left top', // right bottom center
		preloadImg:					false,
		startElementIndex:			0,
		callbackfunction:			null
	};
	jQuery.fn.bgStretcher.settings = {};
})(jQuery);