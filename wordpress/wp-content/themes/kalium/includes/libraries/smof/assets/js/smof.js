/**
 * SMOF js
 *
 * contains the core functionalities to be used
 * inside SMOF
 */

/** Fire up jQuery - let's dance!
 */
jQuery( document ).ready( function ( $ ) {

	//(un)fold options in a checkbox-group
	$( '.fld' ).click( function () {
		var $fold = '.f_' + this.id;
		$( $fold ).stop().slideToggle( 'normal', "swing" );
	} );


	// Advanced Folding
	var aFoldToggleCheck = function ( $field, $toggles, currentValue ) {
		$toggles.each( function ( i, el ) {
			var $toggle = $( el ),
				acceptableValues = $toggle.data( 'advanced-folding-match' ).replace( $field.data( 'advanced-folding' ) + ':', '' ),
				acceptableValuesArr = acceptableValues.split( ',' );

			// True/False Field
			if ( acceptableValues == 'checked' || acceptableValues == 'notChecked' ) {

				if ( acceptableValues == 'checked' ) {
					$toggle[ currentValue == 'checked' ? 'removeClass' : 'addClass' ]( 'afold-collapsed' );
				} else if ( acceptableValues == 'notChecked' ) {
					$toggle[ currentValue == 'notChecked' ? 'removeClass' : 'addClass' ]( 'afold-collapsed' );
				}

			} else
				// Media Field
			if ( acceptableValues == 'hasMedia' || acceptableValues == 'hasNotMedia' ) {

				if ( acceptableValues == 'hasMedia' ) {
					$toggle[ currentValue == 'hasMedia' ? 'removeClass' : 'addClass' ]( 'afold-collapsed' );
				} else if ( acceptableValues == 'hasNotMedia' ) {
					$toggle[ currentValue == 'hasNotMedia' ? 'removeClass' : 'addClass' ]( 'afold-collapsed' );
				}

			} else
				// Match in Array
			if ( acceptableValuesArr.length > 0 ) {
				var isInArray = false;

				for ( var j in acceptableValuesArr ) {
					var val = acceptableValuesArr[ j ];
					if ( val == currentValue ) {
						isInArray = true;
						break;
					}
				}

				$toggle[ isInArray ? 'removeClass' : 'addClass' ]( 'afold-collapsed' );
			}
		} );
	};

	$( '[data-advanced-folding]' ).each( function ( i, el ) {
		var $el = $( el ),
			toggle_el = $el.data( 'advanced-folding' ),
			$toggles = $( '[data-advanced-folding-match^="' + toggle_el + ':"]' );

		// Image Radio List
		if ( $el.hasClass( 'section-images' ) ) {
			$el.find( '.of-radio-img-img' ).on( 'click', function ( ev ) {
				var currentVal = $el.find( '.of-radio-img-radio:checked' ).val();

				aFoldToggleCheck( $el, $toggles, currentVal );
			} );

			// Set Loaded Value
			aFoldToggleCheck( $el, $toggles, $el.find( '.of-radio-img-radio:checked' ).val() );
		}

		// Switch Button
		if ( $el.hasClass( 'section-switch' ) ) {
			$el.find( '.switch-options' ).on( 'click', function ( ev ) {
				var checked = $el.find( ':checkbox' ).is( ':checked' );

				aFoldToggleCheck( $el, $toggles, ( checked ? 'checked' : 'notChecked' ) );
			} );

			// Set Loaded Value
			aFoldToggleCheck( $el, $toggles, ( $el.find( ':checkbox' ).is( ':checked' ) ? 'checked' : 'notChecked' ) );
		}

		// Checkbox
		if ( $el.hasClass( 'section-checkbox' ) ) {
			$el.find( ':checkbox' ).on( 'click', function ( ev ) {
				var checked = $( this ).is( ':checked' );

				aFoldToggleCheck( $el, $toggles, ( checked ? 'checked' : 'notChecked' ) );
			} );

			// Set Loaded Value
			aFoldToggleCheck( $el, $toggles, ( $el.find( ':checkbox' ).is( ':checked' ) ? 'checked' : 'notChecked' ) );
		}

		// Selectbox
		if ( $el.hasClass( 'section-select' ) ) {
			$el.find( 'select.select' ).on( 'change', function () {
				var currentVal = $( this ).val();

				aFoldToggleCheck( $el, $toggles, currentVal );
			} );

			// Set Loaded Value
			aFoldToggleCheck( $el, $toggles, $el.find( 'select.select' ).val() );
		}

		// Media File
		if ( $el.hasClass( 'section-media' ) ) {
			var $uploadBtn = $el.find( '.upload.of-input' );

			$uploadBtn.on( 'change', function ( ev ) {
				var currentVal = $( this ).val() ? 'hasMedia' : 'hasNotMedia';

				aFoldToggleCheck( $el, $toggles, currentVal );
			} );

			// Set Loaded Value
			aFoldToggleCheck( $el, $toggles, ( $uploadBtn.val() ? 'hasMedia' : 'hasNotMedia' ) );
		}
	} );

	// Loaders
	$( '#section-image_loading_placeholder_preselected' ).each( function ( i, el ) {
		var $el = $( el ),
			$loaders = $el.find( '.loaders .loader' ),
			$select = $( '#image_loading_placeholder_preselected_loader' );

		$loaders.each( function ( j, el2 ) {
			var $el2 = $( el2 ),
				id = $el2.data( 'id' );


			$el2.on( 'click', function ( ev ) {
				ev.preventDefault();

				$loaders.removeClass( 'current' );
				$el2.addClass( 'current' );

				// Select Dropdown
				$select.find( 'option[value="' + id + '"]' ).attr( 'selected', true );
				$select.change();
			} );

			if ( id == $select.val() ) {
				$el2.addClass( 'current' );
			}
		} );

		$select.on( 'change', function ( ev ) {
			$loaders.removeClass( 'current' ).filter( '[data-id="' + $select.val() + '"]' ).addClass( 'current' );
		} );
	} );


	// Tabs
	$( '.tab-pane[data-tab-id]' ).each( function ( i, el ) {
		var $tabContainer = $( el ),
			$tabChildren = $( '[data-tab-item-of="' + $tabContainer.data( 'tabId' ) + '"]' );

		$tabContainer.append( $tabChildren );
	} );

	$( '.smof-tabs li a' ).each( function ( i, el ) {
		var $tab = $( el ),
			$tabLinks = $tab.closest( '.smof-tabs' ).find( 'li' );

		$tab.on( 'click', function ( ev ) {
			ev.preventDefault();
			smofSetCurrentTab( $tab.attr( 'href' ).replace( '#tab:', '' ), $tab, $tabLinks );
		} );
	} );

	var smofSetCurrentTab = function ( tabId, $tab, $tabLinks ) {

		var $tabPaneContainer = $tabLinks.closest( 'ul' ).next();

		$tabLinks.removeClass( 'active' );
		$tab.parent().addClass( 'active' );

		var $tabPanes = $tabPaneContainer.find( '.tab-pane' ),
			$activePane = $tabPanes.filter( '[data-tab-id="' + tabId + '"]' );

		$tabPanes.removeClass( 'active' );

		$activePane.stop().fadeIn( 'normal', function () {
			$activePane.removeAttr( 'style' ).addClass( 'active' );
		} );

		var top = $( window ).scrollTop();

		window.location.hash = tabId;
		$( window ).scrollTop( top );
	}

	// Current Tab
	var loc = window.location.hash.toString();

	if ( loc.length ) {
		loc = loc.replace( '#', '' );
		$( '.smof-tabs li a[href="#tab:' + loc + '"]' ).click();
	}


	// start: modified by Arlind
	$( "#of_container .section" ).each( function ( i, el ) {
		$( el ).addClass( 'has-wrapper' ).wrapInner( "<div class=\"option-wrapper\"></div>" );
	} );
	// end: modified by Arlind

	//Color picker
	$( '.of-color' ).wpColorPicker();

	//hides warning if js is enabled
	$( '#js-warning' ).hide();

	//Tabify Options
	$( '.group' ).hide();

	// Get the URL parameter for tab
	function getURLParameter( name ) {
		return decodeURI(
			( RegExp( name + '=' + '(.+?)(&|$)' ).exec( location.search ) || [, ''] )[ 1 ]
		);
	}

	// If the $_GET param of tab is set, use that for the tab that should be open
	if ( getURLParameter( 'tab' ) != "" ) {
		$.cookie( 'of_current_opt', '#' + getURLParameter( 'tab' ), { expires: 7, path: '/' } );
	}

	// Display last current tab
	if ( $.cookie( "of_current_opt" ) === null ) {
		$( '.group:first' ).fadeIn( 'fast' );
		$( '#of-nav li:first' ).addClass( 'current' );
	} else {

		var hooks = $( '#hooks' ).html();
		hooks = $.parseJSON( hooks );

		$.each( hooks, function ( key, value ) {

			if ( $.cookie( "of_current_opt" ) == '#of-option-' + value ) {
				$( '.group#of-option-' + value ).fadeIn();
				$( '#of-nav li.' + value ).addClass( 'current' );
			}

		} );

	}

	//Current Menu Class
	$( '#of-nav li a' ).click( function ( evt ) {

		if ( $( this ).parent().hasClass( 'changelog' ) ) {
			window.open( 'admin.php?page=kalium&tab=whats-new#changelog' );
			return;
		}

		if ( $( this ).attr( 'href' ).substring( 0, 1 ) != '#' ) {
			window.location.href = $( this ).attr( 'href' );
			return false;
		}

		$( '#of-nav li' ).removeClass( 'current' );
		$( this ).parent().addClass( 'current' );

		var clicked_group = $( this ).attr( 'href' );

		$.cookie( 'of_current_opt', clicked_group, { expires: 7, path: '/' } );

		$( '.group' ).hide();

		$( clicked_group ).fadeIn( 'fast' );
		return false;

	} );

	//Expand Options
	var flip = 0;

	$( '#expand_options' ).click( function () {
		if ( flip == 0 ) {
			flip = 1;
			$( '#of_container #of-nav' ).hide();
			$( '#of_container #content' ).width( 755 );
			$( '#of_container .group' ).add( '#of_container .group h2' ).show();

			$( this ).removeClass( 'expand' );
			$( this ).addClass( 'close' );
			//$( this ).text( 'Close' );

			$( '#of_container' ).addClass( 'is-expanded' );

		} else {
			flip = 0;
			$( '#of_container #of-nav' ).show();
			$( '#of_container #content' ).width( 595 );
			$( '#of_container .group' ).add( '#of_container .group h2' ).hide();
			$( '#of_container .group:first' ).show();
			$( '#of_container #of-nav li' ).removeClass( 'current' );
			$( '#of_container #of-nav li:first' ).addClass( 'current' );

			$( this ).removeClass( 'close' );
			$( this ).addClass( 'expand' );
			//$( this ).text( 'Expand' );

			$( '#of_container' ).removeClass( 'is-expanded' );
		}

	} );

	//Update Message popup
	$.fn.center = function () {
		this.animate( { "top": ( $( window ).height() - this.height() - 200 ) / 2 + $( window ).scrollTop() + "px" }, 100 );
		this.css( "left", 250 );
		return this;
	}

	//Masked Inputs (images as radio buttons)
	$( '.of-radio-img-img' ).click( function () {
		$( this ).parent().siblings().removeClass( 'of-radio-img-selected' );
		$( this ).parent().addClass( 'of-radio-img-selected' );
	} );
	$( '.of-radio-img-label' ).hide();
	$( '.of-radio-img-img' ).show();
	$( '.of-radio-img-radio' ).hide();

	//Masked Inputs (background images as radio buttons)
	$( '.of-radio-tile-img' ).click( function () {
		$( this ).parent().parent().find( '.of-radio-tile-img' ).removeClass( 'of-radio-tile-selected' );
		$( this ).addClass( 'of-radio-tile-selected' );
	} );
	$( '.of-radio-tile-label' ).hide();
	$( '.of-radio-tile-img' ).show();
	$( '.of-radio-tile-radio' ).hide();

	// Style Select
	( function () {
		styleSelect = {
			init: function () {
				$( '.select_wrapper' ).each( function () {
					if ( this.isStyled ) {
						return;
					}
					$( this ).prepend( '<span>' + $( this ).find( '.select option:selected' ).text() + '</span>' );
				} );
				$( '.select' ).on( 'change', function () {
					if ( this.isStyled ) {
						return;
					}
					$( this ).prev( 'span' ).replaceWith( '<span>' + $( this ).find( 'option:selected' ).text() + '</span>' );
				} );
				$( '.select' ).bind( 'change', function ( event ) {
					if ( this.isStyled ) {
						return;
					}
					$( this ).prev( 'span' ).replaceWith( '<span>' + $( this ).find( 'option:selected' ).text() + '</span>' );
				} );
			}
		};
		$( document ).ready( function () {
			styleSelect.init();
		} )
	} )();


	/** Aquagraphite Slider MOD */

	//Hide (Collapse) the toggle containers on load
	$( ".slide_body" ).hide();

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	$( ".slide_edit_button" ).on( 'click', function () {
		/*
		//display as an accordion
		$(".slide_header").removeClass("active");
		$(".slide_body").slideUp("fast");
		*/
		//toggle for each
		$( this ).parent().toggleClass( "active" ).next().stop().slideToggle( "fast" );
		return false; //Prevent the browser jump to the link anchor
	} );

	// Update slide title upon typing
	function update_slider_title( e ) {
		var element = e;
		if ( this.timer ) {
			clearTimeout( element.timer );
		}
		this.timer = setTimeout( function () {
			$( element ).parent().prev().find( 'strong' ).text( element.value );
		}, 100 );
		return true;
	}

	$( '.of-slider-title' ).on( 'keyup', function () {
		update_slider_title( this );
	} );


	//Remove individual slide
	$( '.slide_delete_button' ).on( 'click', function () {
		// event.preventDefault();
		var agree = confirm( "Are you sure you wish to delete this slide?" );
		if ( agree ) {
			var $trash = $( this ).parents( 'li' );
			//$trash.slideUp('slow', function(){ $trash.remove(); }); //chrome + confirm bug made slideUp not working...
			$trash.animate( {
				opacity: 0.25,
				height: 0,
			}, 500, function () {
				$( this ).remove();
			} );
			return false; //Prevent the browser jump to the link anchor
		} else {
			return false;
		}
	} );

	//Add new slide
	$( ".slide_add_button" ).on( 'click', function () {
		var slidesContainer = $( this ).prev();
		var sliderId = slidesContainer.attr( 'id' );

		var numArr = $( '#' + sliderId + ' li' ).find( '.order' ).map( function () {
			var str = this.id;
			str = str.replace( /\D/g, '' );
			str = parseFloat( str );
			return str;
		} ).get();

		var maxNum = Math.max.apply( Math, numArr );
		if ( maxNum < 1 ) {
			maxNum = 0
		}
		;
		var newNum = maxNum + 1;

		var newSlide = '<li class="temphide"><div class="slide_header"><strong>Slide ' + newNum + '</strong><input type="hidden" class="slide of-input order" name="' + sliderId + '[' + newNum + '][order]" id="' + sliderId + '_slide_order-' + newNum + '" value="' + newNum + '"><a class="slide_edit_button" href="#">Edit</a></div><div class="slide_body" style="display: none; "><label>Title</label><input class="slide of-input of-slider-title" name="' + sliderId + '[' + newNum + '][title]" id="' + sliderId + '_' + newNum + '_slide_title" value=""><label>Image URL</label><input class="upload slide of-input" name="' + sliderId + '[' + newNum + '][url]" id="' + sliderId + '_' + newNum + '_slide_url" value=""><div class="upload_button_div"><span class="button media_upload_button" id="' + sliderId + '_' + newNum + '">Upload</span><span class="button remove-image hide" id="reset_' + sliderId + '_' + newNum + '" title="' + sliderId + '_' + newNum + '">Remove</span></div><div class="screenshot"></div><label>Link URL (optional)</label><input class="slide of-input" name="' + sliderId + '[' + newNum + '][link]" id="' + sliderId + '_' + newNum + '_slide_link" value=""><label>Description (optional)</label><textarea class="slide of-input" name="' + sliderId + '[' + newNum + '][description]" id="' + sliderId + '_' + newNum + '_slide_description" cols="8" rows="8"></textarea><a class="slide_delete_button" href="#">Delete</a><div class="clear"></div></div></li>';

		slidesContainer.append( newSlide );
		var nSlide = slidesContainer.find( '.temphide' );
		nSlide.fadeIn( 'fast', function () {
			$( this ).removeClass( 'temphide' );
		} );

		optionsframework_file_bindings(); // re-initialise upload image..

		return false; //prevent jumps, as always..
	} );

	//Sort slides
	$( '.slider' ).find( 'ul' ).each( function () {
		var id = $( this ).attr( 'id' );
		$( '#' + id ).sortable( {
			placeholder: "placeholder",
			opacity: 0.6,
			handle: ".slide_header",
			cancel: "a"
		} );
	} );


	/**	Sorter (Layout Manager) */
	$( '.sorter' ).each( function () {
		var id = $( this ).attr( 'id' );
		$( '#' + id ).find( 'ul' ).sortable( {
			items: 'li',
			placeholder: "placeholder",
			connectWith: '.sortlist_' + id,
			opacity: 0.6,
			update: function () {
				$( this ).find( '.position' ).each( function () {

					var listID = $( this ).parent().attr( 'id' );
					var parentID = $( this ).parent().parent().attr( 'id' );
					parentID = parentID.replace( id + '_', '' )
					var optionID = $( this ).parent().parent().parent().attr( 'id' );
					$( this ).prop( "name", optionID + '[' + parentID + '][' + listID + ']' );

				} );
			}
		} );
	} );


	/**	Ajax Backup & Restore MOD */
	//backup button
	$( '#of_backup_button' ).on( 'click', function () {

		var answer = confirm( "Click OK to backup your current saved options." )

		if ( answer ) {

			var clickedObject = $( this );
			var clickedID = $( this ).attr( 'id' );

			var nonce = $( '#security' ).val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'backup_options',
				security: nonce
			};

			$.post( ajaxurl, data, function ( response ) {

				//check nonce
				if ( response == - 1 ) { //failed

					var fail_popup = $( '#of-popup-fail' );
					fail_popup.fadeIn();
					window.setTimeout( function () {
						fail_popup.fadeOut();
					}, 2000 );
				} else {

					var success_popup = $( '#of-popup-save' );
					success_popup.fadeIn();
					window.setTimeout( function () {
						location.reload();
					}, 1000 );
				}
			} );

		}

		return false;

	} );

	//restore button
	$( '#of_restore_button' ).on( 'click', function () {

		var answer = confirm( "'Warning: All of your current options will be replaced with the data from your last backup! Proceed?" )

		if ( answer ) {

			var clickedObject = $( this );
			var clickedID = $( this ).attr( 'id' );

			var nonce = $( '#security' ).val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'restore_options',
				security: nonce
			};

			$.post( ajaxurl, data, function ( response ) {

				//check nonce
				if ( response == - 1 ) { //failed

					var fail_popup = $( '#of-popup-fail' );
					fail_popup.fadeIn();
					window.setTimeout( function () {
						fail_popup.fadeOut();
					}, 2000 );
				} else {

					var success_popup = $( '#of-popup-save' );
					success_popup.fadeIn();
					window.setTimeout( function () {
						location.reload();
					}, 1000 );
				}

			} );

		}

		return false;

	} );

	/**	Ajax Transfer (Import/Export) Option */
	$( '#of_import_button' ).on( 'click', function () {

		var answer = confirm( "Click OK to import options." )

		if ( answer ) {

			var clickedObject = $( this );
			var clickedID = $( this ).attr( 'id' );

			var nonce = $( '#security' ).val();

			var import_data = $( '#export_data' ).val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'import_options',
				security: nonce,
				data: import_data
			};

			$.post( ajaxurl, data, function ( response ) {
				var fail_popup = $( '#of-popup-fail' );
				var success_popup = $( '#of-popup-save' );

				//check nonce
				if ( response == - 1 ) { //failed
					fail_popup.fadeIn();
					window.setTimeout( function () {
						fail_popup.fadeOut();
					}, 2000 );
				} else {
					success_popup.fadeIn();
					window.setTimeout( function () {
						location.reload();
					}, 1000 );
				}

			} );

		}

		return false;

	} );

	/** AJAX Save Options */
	$( '.of-save-button' ).on( 'click', function () {

		var nonce = $( '#security' ).val(),
			$this = $( this ),
			$em = $this.find( 'em' ),
			hasEm = $em.length == 1;

		if ( $this.data( 'busy' ) ) {
			return false;
		}

		$this.add( $( '.of-save-sticky' ) ).addClass( 'is-loading' );
		$this.data( 'busy', true );

		if ( hasEm && !$em.data( 'default' ) ) {
			$em.data( 'default', $em.html() );
		}

		//get serialized data from all our option fields
		var serializedReturn = $( '#of_form :input[name][name!="security"][name!="of_reset"]' ).serialize();

		$( '#of_form :input[type=checkbox]' ).each( function () {
			if ( !this.checked ) {
				serializedReturn += '&' + this.name + '=0';
			}
		} );

		var data = {
			type: 'save',
			action: 'of_ajax_post_action',
			security: nonce,
			data: serializedReturn
		};

		$.post( ajaxurl, data, function ( response ) {
			var success = $( '#of-popup-save' );
			var fail = $( '#of-popup-fail' );
			var loading = $( '.ajax-loading-img' );

			$this.add( $( '.of-save-sticky' ) ).removeClass( 'is-loading' );
			$this.data( 'busy', false );

			if ( hasEm && $em.data( 'success' ) ) {
				$em.fadeTo( 150, 0, function () {
					$em.html( '<i class="kalium-admin-icon-check"></i> ' + $em.data( 'success' ) ).fadeTo( 300, 1 );
				} );

				setTimeout( function () {
					$em.fadeTo( 150, 0, function () {
						$em.html( $em.data( 'default' ) ).fadeTo( 300, 1 );
					} );
				}, 3000 );
			}

			if ( response == 1 ) {
				success.fadeIn();
			} else {
				fail.fadeIn();
			}

			setTimeout( function () {
				success.fadeOut();
				fail.fadeOut();
			}, 2000 );
		} );

		return false;

	} );


	/* AJAX Options Reset */
	$( '#of_reset' ).click( function () {

		//confirm reset
		var answer = confirm( "Click OK to reset. All settings will be lost and replaced with default settings!" );

		//ajax reset
		if ( answer ) {

			var nonce = $( '#security' ).val();

			$( '.ajax-reset-loading-img' ).fadeIn();

			var data = {

				type: 'reset',
				action: 'of_ajax_post_action',
				security: nonce,
			};

			$.post( ajaxurl, data, function ( response ) {
				var success = $( '#of-popup-reset' );
				var fail = $( '#of-popup-fail' );
				var loading = $( '.ajax-reset-loading-img' );
				loading.fadeOut();

				if ( response == 1 ) {
					success.fadeIn();
					window.setTimeout( function () {
						location.reload();
					}, 1000 );
				} else {
					fail.fadeIn();
					window.setTimeout( function () {
						fail.fadeOut();
					}, 2000 );
				}


			} );

		}

		return false;

	} );


	/**	Tipsy @since v1.3 */
	if ( $().tipsy ) {
		$( '.tooltip, .typography-size, .typography-height, .typography-face, .typography-style, .of-typography-color' ).tipsy( {
			fade: true,
			gravity: 's',
			opacity: 0.7,
		} );
	}


	/**
	 * $ UI Slider function
	 * Dependencies 	 : $, $-ui-slider
	 * Feature added by : Smartik - http://smartik.ws/
	 * Date 			 : 03.17.2013
	 */
	$( '.smof_sliderui' ).each( function () {

		var obj = $( this );
		var sId = "#" + obj.data( 'id' );
		var val = parseInt( obj.data( 'val' ) );
		var min = parseInt( obj.data( 'min' ) );
		var max = parseInt( obj.data( 'max' ) );
		var step = parseInt( obj.data( 'step' ) );

		//slider init
		obj.slider( {
			value: val,
			min: min,
			max: max,
			step: step,
			range: "min",
			slide: function ( event, ui ) {
				$( sId ).val( ui.value );
			}
		} );

	} );


	/**
	 * Switch
	 * Dependencies 	 : jquery
	 * Feature added by : Smartik - http://smartik.ws/
	 * Date 			 : 03.17.2013
	 */
	$( ".cb-enable" ).click( function () {
		var parent = $( this ).parents( '.switch-options' );
		$( '.cb-disable', parent ).removeClass( 'selected' );
		$( this ).addClass( 'selected' );
		$( '.main_checkbox', parent ).attr( 'checked', true );

		var fold_reverse = $( this ).parents( '.section-switch' ).hasClass( 'fold_reverse' );

		//fold/unfold related options
		var obj = $( this );
		var $fold = '.f_' + obj.data( 'id' );
		$( $fold ).stop()[ fold_reverse ? 'slideUp' : 'slideDown' ]( 'normal', "swing" );
	} );
	$( ".cb-disable" ).click( function () {
		var parent = $( this ).parents( '.switch-options' );
		$( '.cb-enable', parent ).removeClass( 'selected' );
		$( this ).addClass( 'selected' );
		$( '.main_checkbox', parent ).attr( 'checked', false );

		var fold_reverse = $( this ).parents( '.section-switch' ).hasClass( 'fold_reverse' );

		//fold/unfold related options
		var obj = $( this );
		var $fold = '.f_' + obj.data( 'id' );
		$( $fold ).stop()[ fold_reverse ? 'slideDown' : 'slideUp' ]( 'normal', "swing" );
	} );
	//disable text select(for modern chrome, safari and firefox is done via CSS)
	// if ( ( $.browser.msie && $.browser.version < 10 ) || $.browser.opera ) {
	// 	$( '.cb-enable span, .cb-disable span' ).find().attr( 'unselectable', 'on' );
	// }


	/**
	 * Google Fonts
	 * Dependencies 	 : google.com, jquery
	 * Feature added by : Smartik - http://smartik.ws/
	 * Date 			 : 03.17.2013
	 */
	function GoogleFontSelect( slctr, mainID ) {

		var _selected = $( slctr ).val(); 						//get current value - selected and saved
		var _linkclass = 'style_link_' + mainID;
		var _previewer = mainID + '_ggf_previewer';

		if ( _selected ) { //if var exists and isset

			$( '.' + _previewer ).fadeIn();

			//Check if selected is not equal with "Select a font" and execute the script.
			if ( _selected !== 'none' && _selected !== 'Select a font' ) {

				//remove other elements crested in <head>
				$( '.' + _linkclass ).remove();

				//replace spaces with "+" sign
				var the_font = _selected.replace( /\s+/g, '+' );

				//add reference to google font family
				$( 'head' ).append( '<link href="https://fonts.googleapis.com/css?family=' + the_font + '" rel="stylesheet" type="text/css" class="' + _linkclass + '">' );

				//show in the preview box the font
				$( '.' + _previewer ).css( 'font-family', _selected + ', sans-serif' );

			} else {

				//if selected is not a font remove style "font-family" at preview box
				$( '.' + _previewer ).css( 'font-family', '' );
				$( '.' + _previewer ).fadeOut();

			}

		}

	}

	//init for each element
	$( '.google_font_select' ).each( function () {
		var mainID = $( this ).attr( 'id' );
		GoogleFontSelect( this, mainID );
	} );

	//init when value is changed
	$( '.google_font_select' ).change( function () {
		var mainID = $( this ).attr( 'id' );
		GoogleFontSelect( this, mainID );
	} );


	/**
	 * Media Uploader
	 * Dependencies 	 : jquery, wp media uploader
	 * Feature added by : Smartik - http://smartik.ws/
	 * Date 			 : 05.28.2013
	 */
	function optionsframework_add_file( event, selector ) {

		var upload = $( ".uploaded-file" ), frame;
		var $el = $( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media( {
			// Set the title of the modal.
			title: $el.data( 'choose' ),

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data( 'update' ),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		} );

		// When an image is selected, run a callback.
		frame.on( 'select', function () {
			// Grab the selected attachment.
			var attachment = frame.state().get( 'selection' ).first();
			frame.close();
			selector.find( '.upload' ).val( attachment.attributes.id ).trigger( 'change' );

			if ( attachment.attributes.type == 'image' ) {
				selector.find( '.screenshot' ).empty().hide().append( '<img class="of-option-image" src="' + attachment.attributes.url + '">' ).stop().slideDown( 'fast' );
			}
			selector.find( '.media_upload_button' ).unbind();
			selector.find( '.remove-image' ).show().removeClass( 'hide' );//show "Remove" button
			selector.find( '.of-background-properties' ).stop().slideDown();
			optionsframework_file_bindings();
		} );

		// Finally, open the modal.
		frame.open();
	}

	function optionsframework_remove_file( selector ) {
		selector.find( '.remove-image' ).hide().addClass( 'hide' );//hide "Remove" button
		selector.find( '.upload' ).val( '' ).trigger( 'change' );
		selector.find( '.of-background-properties' ).hide();
		selector.find( '.screenshot' ).stop().slideUp();
		selector.find( '.remove-file' ).unbind();
		// We don't display the upload button if .upload-notice is present
		// This means the user doesn't have the WordPress 3.5 Media Library Support
		if ( $( '.section-upload .upload-notice' ).length > 0 ) {
			$( '.media_upload_button' ).remove();
		}
		optionsframework_file_bindings();
	}

	function optionsframework_file_bindings() {
		$( '.remove-image, .remove-file' ).on( 'click', function () {
			optionsframework_remove_file( $( this ).parents( '.section-upload, .section-media, .slide_body' ) );
		} );

		$( '.media_upload_button' ).unbind( 'click' ).click( function ( event ) {
			optionsframework_add_file( event, $( this ).parents( '.section-upload, .section-media, .slide_body' ) );
		} );
	}

	optionsframework_file_bindings();


} ); //end doc ready