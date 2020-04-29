/* global kco_params */
jQuery( function( $ ) {

	// Check if we have params.
	if ( 'undefined' === typeof kco_params ) {
		return false;
	}
	var kco_wc = {
		bodyEl: $( 'body' ),
		checkoutFormSelector: $( 'form.checkout' ),

		// Order notes.
		orderNotesValue: '',
		orderNotesSelector: 'textarea#order_comments',
		orderNotesEl: $( 'textarea#order_comments' ),

		// Payment method.
		paymentMethodEl: $( 'input[name="payment_method"]' ),
		paymentMethod: '',
		selectAnotherSelector: '#klarna-checkout-select-other',

		// Form fields.
		shippingUpdated: false,
		blocked: false,

		// Email exist.
		emailExists: kco_params.email_exists,

		preventPaymentMethodChange: false,

		timeout: null,
		interval: null,

		documentReady: function() {
			kco_wc.log( kco_params );
			if ( 0 < kco_wc.paymentMethodEl.length ) {
				kco_wc.paymentMethod = kco_wc.paymentMethodEl.filter( ':checked' ).val();
			} else {
				kco_wc.paymentMethod = 'kco';
			}
			kco_wc.moveExtraCheckoutFields();
			kco_wc.confirmLoading();
			kco_wc.kcoResume();
		},

		kcoSuspend: function( autoResumeBool ) {
			if ( window._klarnaCheckout ) {
				window._klarnaCheckout( function( api ) {
					api.suspend({
						autoResume: {
						  enabled: autoResumeBool
						}
					  });
				});
			}
		},

		kcoResume: function() {
			if ( window._klarnaCheckout ) {
				window._klarnaCheckout( function( api ) {
					if ( false === kco_wc.blocked ) {
						api.resume();
					}
				});
			}
		},

		confirmLoading: function() {
			$( '#kco-confirm-loading' )
				.css( 'minHeight', '300px' )
				.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
		},

		updateCart: function() {
			kco_wc.kcoSuspend( true );
			$.ajax({
				type: 'POST',
				url: kco_params.update_cart_url,
				data: {
					checkout: $( 'form.checkout' ).serialize(),
					nonce: kco_params.update_cart_nonce
				},
				dataType: 'json',
				success: function( data ) {
				},
				error: function( data ) {
				},
				complete: function( data ) {
					$( 'body' ).trigger( 'update_checkout' );
					kco_wc.kcoResume();
				}
			});
		},

		updateKlarnaOrder: function() {
			if ( 'kco' === kco_wc.paymentMethod && 'no' === kco_params.is_confirmation_page ) {
				kco_wc.kcoSuspend( true );
				$( '.woocommerce-checkout-review-order-table' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				$.ajax({
					type: 'POST',
					url: kco_params.update_klarna_order_url,
					data: {
						nonce: kco_params.update_klarna_order_nonce
					},
					dataType: 'json',
					success: function( data ) {
					},
					error: function( data ) {
					},
					complete: function( data ) {
						if ( true === data.responseJSON.success ) {
							kco_wc.kcoResume();
							$( '.woocommerce-checkout-review-order-table' ).unblock();
						} else {
							if ( '' !== data.responseJSON.data.redirect_url ) {
								console.log( 'Cart do not need payment. Reloading checkout.' );
								window.location.href = data.responseJSON.data.redirect_url;
							}
						}
					}
				});
			}
		},

		// Display Shipping Price in order review if Display shipping methods in iframe settings is active.
		maybeDisplayShippingPrice: function() {
			if ( 'kco' === kco_wc.paymentMethod && 'yes' === kco_params.shipping_methods_in_iframe && 'no' === kco_params.is_confirmation_page ) {
				if ( jQuery( '#shipping_method input[type=\'radio\']' ).length ) {

					// Multiple shipping options available.
					$( '#shipping_method input[type=\'radio\']:checked' ).each( function() {
						var idVal = $( this ).attr( 'id' );
						var shippingPrice = $( 'label[for=\'' + idVal + '\']' ).text();
						$( '.woocommerce-shipping-totals td' ).html( shippingPrice );
					});
				} else {

					// Only one shipping option available.
					var idVal = $( '#shipping_method input[name=\'shipping_method[0]\']' ).attr( 'id' );
					var shippingPrice = $( 'label[for=\'' + idVal + '\']' ).text();
					$( '.woocommerce-shipping-totals td' ).html( shippingPrice );
				}
			}
		},

		// When "Change to another payment method" is clicked.
		changeFromKco: function( e ) {
			e.preventDefault();

			$( kco_wc.checkoutFormSelector ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {
					kco: false,
					nonce: kco_params.change_payment_method_nonce
				},
				url: kco_params.change_payment_method_url,
				success: function( data ) {},
				error: function( data ) {},
				complete: function( data ) {
					kco_wc.log( data.responseJSON );
					window.location.href = data.responseJSON.data.redirect;
				}
			});
		},

		// When payment method is changed to KCO in regular WC Checkout page.
		maybeChangeToKco: function() {
			if ( ! kco_wc.preventPaymentMethodChange ) {
			kco_wc.log( $( this ).val() );

			if ( 'kco' === $( this ).val() ) {
				$( '.woocommerce-info' ).remove();

				$( kco_wc.checkoutFormSelector ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$.ajax({
					type: 'POST',
					data: {
						kco: true,
						nonce: kco_params.change_payment_method_nonce
					},
					dataType: 'json',
					url: kco_params.change_payment_method_url,
					success: function( data ) {},
					error: function( data ) {},
					complete: function( data ) {
						kco_wc.log( data.responseJSON );
						window.location.href = data.responseJSON.data.redirect;
					}
				});
			}
		}
		},

		log: function( message ) {
			if ( kco_params.logging ) {
				console.log( message );
			}
		},

		maybeSuspendIframe: function( allValid ) {
			if ( true === allValid ) {
				kco_wc.blocked = false;
				$( '#kco-required-fields-notice' ).remove();
				kco_wc.kcoResume();
			} else 	if ( ! $( '#kco-required-fields-notice' ).length ) { // Only if we dont have an error message already.
				kco_wc.blocked = true;
				$( 'form.checkout' ).prepend( '<div id="kco-required-fields-notice" class="woocommerce-NoticeGroup woocommerce-NoticeGroup-updateOrderReview"><ul class="woocommerce-error" role="alert"><li>' +  kco_params.required_fields_text + '</li></ul></div>' );
				var etop = $( 'form.checkout' ).offset().top;
				$( 'html, body' ).animate({
					scrollTop: etop
					}, 1000 );
				kco_wc.kcoSuspend( false );
			}
		},

		/**
		 * Moves all non standard fields to the extra checkout fields.
		 */
		moveExtraCheckoutFields: function() {

			// Move order comments.
			$( '.woocommerce-additional-fields' ).appendTo( '#kco-extra-checkout-fields' );

			var form = $( 'form[name="checkout"] input, form[name="checkout"] select, textarea' );
			for ( i = 0; i < form.length; i++ ) {
				var name = form[i].name;

				// Check if this is a standard field.
				if ( -1 === $.inArray( name, kco_params.standard_woo_checkout_fields ) ) {

					// This is not a standard Woo field, move to our div.
					if ( 0 < $( 'p#' + name + '_field' ).length ) {
						$( 'p#' + name + '_field' ).appendTo( '#kco-extra-checkout-fields' );
					} else {
						$( 'input[name="' + name + '"]' ).closest( 'p' ).appendTo( '#kco-extra-checkout-fields' );
					}
				}
			}
		},

		getKlarnaOrder: function() {
			kco_wc.preventPaymentMethodChange = true;
			$( '.woocommerce-checkout-review-order-table' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			$.ajax({
				type: 'POST',
				url: kco_params.get_klarna_order_url,
				data: {
					nonce: kco_params.get_klarna_order_nonce
				},
				dataType: 'json',
				success: function( data ) {
				},
				error: function( data ) {
					return false;
				},
				complete: function( data ) {
					kco_wc.setCustomerData( data.responseJSON.data );

					// Check Terms checkbox, if it exists.
					if ( 0 < $( 'form.checkout #terms' ).length ) {
						$( 'form.checkout #terms' ).prop( 'checked', true );
					}
					$( 'form.checkout' ).submit();
					return true;
				}
			});
		},

		setCustomerData: function( data ) {
			console.log( data );

			// Billing fields.
			$( '#billing_first_name' ).val( data.billing_address.given_name );
			$( '#billing_last_name' ).val( data.billing_address.family_name );
			$( '#billing_company' ).val( ( data.billing_address.organization_name ? data.billing_address.organization_name : '' ) );
			$( '#billing_address_1' ).val( data.billing_address.street_address );
			$( '#billing_address_2' ).val( ( data.billing_address.street_address2 ? data.billing_address.street_address2 : '' ) );
			$( '#billing_city' ).val( data.billing_address.city );
			$( '#billing_postcode' ).val( data.billing_address.postal_code );
			$( '#billing_phone' ).val( data.billing_address.phone );
			$( '#billing_email' ).val( data.billing_address.email );
			$( '#billing_country' ).val( data.billing_address.country.toUpperCase() );
			$( '#billing_state' ).val( ( data.billing_address.region ? data.billing_address.region : '' ) );

			$( '#ship-to-different-address-checkbox' ).prop( 'checked', true);

			// Shipping fields.
			$( '#shipping_first_name' ).val( data.shipping_address.given_name );
			$( '#shipping_last_name' ).val( data.shipping_address.family_name );
			$( '#shipping_company' ).val( ( data.billing_address.organization_name ? data.billing_address.organization_name : '' ) );
			$( '#shipping_address_1' ).val( data.shipping_address.street_address );
			$( '#shipping_address_2' ).val( ( data.shipping_address.street_address2 ? data.shipping_address.street_address2 : '' ) );
			$( '#shipping_city' ).val( data.shipping_address.city );
			$( '#shipping_postcode' ).val( data.shipping_address.postal_code );
			$( '#shipping_country' ).val( data.shipping_address.country.toUpperCase() );
			$( '#shipping_state' ).val( ( data.shipping_address.region ? data.shipping_address.region : '' ) );
		},

		init: function() {
			$( document ).ready( kco_wc.documentReady );
			kco_wc.bodyEl.on( 'update_checkout', kco_wc.kcoSuspend( true ) );
			kco_wc.bodyEl.on( 'updated_checkout', kco_wc.updateKlarnaOrder );
			kco_wc.bodyEl.on( 'updated_checkout', kco_wc.maybeDisplayShippingPrice );
			kco_wc.bodyEl.on( 'updated_checkout', kco_wc.maybePrintValidationMessage );
			kco_wc.bodyEl.on( 'change', 'input.qty', kco_wc.updateCart );
			kco_wc.bodyEl.on( 'change', 'input[name="payment_method"]', kco_wc.maybeChangeToKco );
			kco_wc.bodyEl.on( 'click', kco_wc.selectAnotherSelector, kco_wc.changeFromKco );

			if ( 'function' === typeof window._klarnaCheckout ) {
				window._klarnaCheckout( function( api ) {
					api.on({
						'shipping_address_change': function( data ) {
							kco_wc.log( 'shipping_address_change' );
							kco_wc.log( data );
							$( '.woocommerce-checkout-review-order-table' ).block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							kco_wc.kcoSuspend( true );
							$.ajax(
								{
									url: kco_params.iframe_shipping_address_change_url,
									type: 'POST',
									dataType: 'json',
									data: {
										data: data,
										nonce: kco_params.iframe_shipping_address_change_nonce
									},
									success: function( response ) {
										kco_wc.log( response );

										// All good release checkout and trigger update_checkout event
										kco_wc.setCustomerData( response.data );
										kco_wc.kcoResume();

										$( 'body' ).trigger( 'update_checkout' );
									},
									error: function( response ) {
										kco_wc.log( response );
									},
									complete: function( response ) {
										$( '.woocommerce-checkout-review-order-table' ).unblock();
										kco_wc.shippingUpdated = true;
										kco_wc.bodyEl.trigger( 'kco_shipping_address_changed', response );
									}
								}
							);
						},
						'change': function( data ) {
							kco_wc.log( 'change', data );
						},
						'order_total_change': function( data ) {
							kco_wc.log( 'order_total_change', data );
						},
						'shipping_option_change': function( data ) {
							kco_wc.log( 'shipping_option_change', data );
							kco_wc.log( data );
							$( '.woocommerce-checkout-review-order-table' ).block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							kco_wc.kcoSuspend( true );
							$.ajax(
								{
									url: kco_params.update_shipping_url,
									type: 'POST',
									dataType: 'json',
									data: {
										data: data,
										nonce: kco_params.update_shipping_nonce
									},
									success: function( response ) {
										kco_wc.log( response );
										$( 'body' ).trigger( 'update_checkout' );
									},
									error: function( response ) {
										kco_wc.log( response );
									},
									complete: function( response ) {
										$( '#shipping_method #' + response.responseJSON.data.shipping_option_name ).prop( 'checked', true );
										$( 'body' ).trigger( 'kco_shipping_option_changed', [ data ]);
										$( '.woocommerce-checkout-review-order-table' ).unblock();
										kco_wc.kcoResume();
									}
								}
							);
						},
						'can_not_complete_order': function( data ) {
							kco_wc.log( 'can_not_complete_order', data );
						},
						'validation_callback': function( data, callback ) {
							kco_wc.logToFile( 'validation_callback from Klarna triggered' );
							// Empty current hash.
							window.location.hash = '';
							// Check for any errors.
							kco_wc.timeout = setTimeout( function() { kco_wc.failOrder( callback, 'timeout' ); }, kco_params.timeout_time * 1000 );
							$( document.body ).on( 'checkout_error', function() { kco_wc.failOrder( callback, 'checkout_error' ); } );
							// Run interval untill we find a hashtag or timer runs out.
							kco_wc.interval = setInterval( function() { kco_wc.checkUrl( callback ); }, 500 );
							// Start processing the order.
							kco_wc.getKlarnaOrder();
						}
					});
				});
			}
		},

		checkUrl: function( callback ) {
			if ( window.location.hash ) {
				var currentHash = window.location.hash;
				if ( -1 < currentHash.indexOf( '#klarna-success' ) ) {
					kco_wc.logToFile( 'klarna-success hashtag detected in URL.' );
					callback({ should_proceed: true });
					// Clear the interval.
					clearInterval(kco_wc.interval);
					// Remove the timeout.
					clearTimeout( kco_wc.timeout );
					// Remove the processing class from the form.
					kco_wc.checkoutFormSelector.removeClass( 'processing' );
					$( '.woocommerce-checkout-review-order-table' ).unblock();
					$( kco_wc.checkoutFormSelector ).unblock();
				}
			} 
		},

		failOrder: function( callback, event ) {
			kco_wc.logToFile( 'Timeout for validation_callback triggered.' );
			// Send false and cancel 
			callback({ should_proceed: false });
			// Clear the interval.
			clearInterval(kco_wc.interval);
			// Remove the timeout.
			clearTimeout( kco_wc.timeout );
			// Renable the form.
			$( 'body' ).trigger( 'updated_checkout' );
			kco_wc.checkoutFormSelector.removeClass( 'processing' );
			$( kco_wc.checkoutFormSelector ).unblock();
			if ( 'timeout' === event ) {
				$('#kco-timeout').remove();
				$('form.checkout').prepend(
					'<div id="kco-timeout" class="woocommerce-NoticeGroup woocommerce-NoticeGroup-updateOrderReview"><ul class="woocommerce-error" role="alert"><li>'
					+  kco_params.timeout_message
					+ '</li></ul></div>'
				);
			}
		},

		logToFile: function( message ) {
			$.ajax(
				{
					url: kco_params.log_to_file_url,
					type: 'POST',
					dataType: 'json',
					data: {
						message: message,
						nonce: kco_params.log_to_file_nonce
					}
				}
			);
		}
	};

	kco_wc.init();
	$( document ).on( 'keypress', '#kco-order-review .qty', function( event ) {
		if ( 13 == event.keyCode ) {
			event.preventDefault();
		}
	});
});
