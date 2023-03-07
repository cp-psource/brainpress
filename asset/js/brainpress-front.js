/*! BrainPress - v2.2.2
 * https://n3rds.work/piestingtal_source/ps-brainpress-classicpress-lms-online-akademie-plugin/
 * Copyright (c) 2019; * Licensed GPLv2+ */
/*global _brainpress*/
/*global pwsL10n*/

var BrainPress = BrainPress || {};

(function( $ ) {

	BrainPress.Models = BrainPress.Models || {};
	BrainPress.UI = BrainPress.UI || {};
	BrainPress.utility = BrainPress.utility || {};
	BrainPress.Events = BrainPress.Events || {};

	BrainPress.utility.timer_validate = function( s, ref ) {
		var time = 'ref_' + (BrainPress.utility.hashcode( '' + s ) + '').split('').reverse().join('');
		return time === ref;
	};

	BrainPress.utility.checkPasswordStrength = function(
		$pass1, $pass2, $strengthResult, $submitButton, blacklistArray
	) {
		var pass1 = $pass1.val();
		var pass2 = $pass2.val();

		// Reset the form & meter
		if ( $submitButton ) {
			$submitButton.attr( 'disabled', 'disabled' );
		}
		$strengthResult.removeClass( 'short bad good strong' );

		// Extend our blacklist array with those from the inputs & site data
		blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() );

		// Get the password strength
		var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );

		// Add the strength meter results
		switch ( strength ) {
			case 2:
				$strengthResult.addClass( 'bad' ).html( pwsL10n.bad );
				break;

			case 3:
				$strengthResult.addClass( 'good' ).html( pwsL10n.good );
				break;

			case 4:
				$strengthResult.addClass( 'strong' ).html( pwsL10n.strong );
				break;

			case 5:
				$strengthResult.addClass( 'short' ).html( pwsL10n.mismatch );
				break;

			default:
				$strengthResult.addClass( 'short' ).html( pwsL10n.short );

		}

		// The meter function returns a result even if pass2 is empty,
		// enable only the submit button if the password is strong and
		// both passwords are filled up
		if ( $submitButton ) {
			if ( 2 < strength && strength !== 5 && '' !== pass2.trim() ) {
				$submitButton.removeAttr( 'disabled' );
			}
		}

		return strength;
	};
	// Actions and Filters
	BrainPress.actions = BrainPress.actions || {}; // Registered actions
	BrainPress.filters = BrainPress.filters || {}; // Registered filters

	/**
	 * Add a new Action callback to BrainPress.actions
	 *
	 * @param tag The tag specified by do_action()
	 * @param callback The callback function to call when do_action() is called
	 * @param priority The order in which to call the callbacks. Default: 10 (like ClassicPress)
	 */
	BrainPress.add_action = function( tag, callback, priority ) {
		if ( undefined === priority ) {
			priority = 10;
		}

		// If the tag doesn't exist, create it.
		BrainPress.actions[ tag ] = BrainPress.actions[ tag ] || [];
		BrainPress.actions[ tag ].push( { priority: priority, callback: callback } );
	};

	/**
	 * Add a new Filter callback to BrainPress.filters
	 *
	 * @param tag The tag specified by apply_filters()
	 * @param callback The callback function to call when apply_filters() is called
	 * @param priority Priority of filter to apply. Default: 10 (like ClassicPress)
	 */
	BrainPress.add_filter = function( tag, callback, priority ) {
		if ( undefined === priority ) {
			priority = 10;
		}

		// If the tag doesn't exist, create it.
		BrainPress.filters[ tag ] = BrainPress.filters[ tag ] || [];
		BrainPress.filters[ tag ].push( { priority: priority, callback: callback } );
	};

	/**
	 * Remove an Anction callback from BrainPress.actions
	 *
	 * Must be the exact same callback signature.
	 * Warning: Anonymous functions can not be removed.

	 * @param tag The tag specified by do_action()
	 * @param callback The callback function to remove
	 */
	BrainPress.remove_action = function( tag, callback ) {
		BrainPress.filters[ tag ] = BrainPress.filters[ tag ] || [];

		BrainPress.filters[ tag ].forEach( function( filter, i ) {
			if ( filter.callback === callback ) {
				BrainPress.filters[ tag ].splice(i, 1);
			}
		} );
	};

	/**
	 * Remove a Filter callback from BrainPress.filters
	 *
	 * Must be the exact same callback signature.
	 * Warning: Anonymous functions can not be removed.

	 * @param tag The tag specified by apply_filters()
	 * @param callback The callback function to remove
	 */
	BrainPress.remove_filter = function( tag, callback ) {
		BrainPress.filters[ tag ] = BrainPress.filters[ tag ] || [];

		BrainPress.filters[ tag ].forEach( function( filter, i ) {
			if ( filter.callback === callback ) {
				BrainPress.filters[ tag ].splice(i, 1);
			}
		} );
	};

	/**
	 * Calls actions that are stored in BrainPress.actions for a specific tag or nothing
	 * if there are no actions to call.
	 *
	 * @param tag A registered tag in Hook.actions
	 * @options Optional JavaScript object to pass to the callbacks
	 */
	BrainPress.do_action = function( tag, options ) {
		var actions = [];

		if ( undefined !== BrainPress.actions[ tag ] && BrainPress.actions[ tag ].length > 0 ) {
			BrainPress.actions[ tag ].forEach( function( hook ) {
				actions[ hook.priority ] = actions[ hook.priority ] || [];
				actions[ hook.priority ].push( hook.callback );
			} );

			actions.forEach( function( hooks ) {
				hooks.forEach( function( callback ) {
					callback( options );
				} );
			} );
		}
	};

	/**
	 * Calls filters that are stored in BrainPress.filters for a specific tag or return
	 * original value if no filters exist.
	 *
	 * @param tag A registered tag in Hook.filters
	 * @options Optional JavaScript object to pass to the callbacks
	 */
	BrainPress.apply_filters = function( tag, value, options ) {

		var filters = [];

		if ( undefined !== BrainPress.filters[ tag ] && BrainPress.filters[ tag ].length > 0 ) {

			BrainPress.filters[ tag ].forEach( function( hook ) {
				filters[ hook.priority ] = filters[ hook.priority ] || [];
				filters[ hook.priority ].push( hook.callback );
			} );

			filters.forEach( function( hooks ) {
				hooks.forEach( function( callback ) {
					value = callback( value, options );
				} );
			} );
		}

		return value;
	};

	BrainPress.Models.CourseFront = Backbone.Model.extend( {
		url: _brainpress._ajax_url + '?action=course_front',
		parse: function( response ) {
			// Trigger course update events
			if ( true === response.success ) {
				this.set( 'response_data', response.data );
				this.trigger( 'brainpress:' + response.data.action + '_success', response.data );
			} else {
				this.set( 'response_data', {} );
				if ( response.data ) {
					this.trigger( 'brainpress:' + response.data.action + '_error', response.data );
				}
			}
		},
		defaults: {}
	} );

	// AJAX Posts
	BrainPress.Models.Post = BrainPress.Models.Post || Backbone.Model.extend( {
		url: _brainpress._ajax_url + '?action=',
		parse: function( response ) {
			var context = this.get( 'context' );

			// Trigger course update events
			if ( true === response.success ) {
				if ( undefined === response.data ) {
					response.data = {};
				}

				this.set( 'response_data', response.data );
				var method = 'brainpress:' + context + response.data.action + '_success';
				this.trigger( method, response.data );
			} else {
				if ( 0 !== response ) {
					this.set( 'response_data', {} );
					this.trigger( 'brainpress:' + context + response.data.action + '_error', response.data );
				}
			}
			BrainPress.Post.set( 'action', '' );
		},
		prepare: function( action, context ) {
			this.url = this.get( 'base_url' ) + action;

			if ( undefined !== context ) {
				this.set( 'context', context );
			}
		},
		defaults: {
			base_url: _brainpress._ajax_url + '?action=',
			context: 'response:'
		}
	} );

	BrainPress.Post = new BrainPress.Models.Post();
	BrainPress.Enrollment = BrainPress.Enrollment || {};
	BrainPress.Page = BrainPress.Page || {};
	BrainPress.Enrollment.dialog = BrainPress.Enrollment.dialog || {};

	// Prepare the enrollment modal
	function create_modal_model() {
		var steps = $( '[data-type="modal-step"]' );

		if ( undefined === steps || ! steps.length ) {
			return;
		}

		BrainPress.Enrollment.Modal = Backbone.Modal.extend( {
			template: _.template( $( '#modal-template' ).html() ),
			viewContainer: '.enrollment-modal-container',
			submitEl: '.done',
			cancelEl: '.cancel',
			options: 'meh',
			// Dynamically create the views from the templates.
			// This allows for WP filtering to add/remove steps
			views: (function() {
				var object = {};

				$.each( steps, function( index, item ) {
					var step = index + 1;
					var id = $( item ).attr( 'id' );
					if ( undefined !== id ) {
						object['click #step' + step] = {
							view: _.template( $( '#' + id ).html() ),
							onActive: 'setActive'
						};
					}
				} );

				return object;
			})(),
			events: {
				'click .previous': 'previousStep',
				'click .next': 'nextStep',
				'click .cancel-link': 'closeDialog'
			},
			previousStep: function( e ) {
				e.preventDefault();
				this.previous();
				if ( typeof this.onPrevious === 'function' ) {
					this.onPrevious();
				}
			},
			nextStep: function( e ) {
				e.preventDefault();
				this.next();
				if ( typeof this.onNext === 'function' ) {
					this.onNext();
				}
			},
			closeDialog: function() {
				$('.enrolment-container-div' ).detach();
			},
			setActive: function( options ) {
				this.trigger( 'modal:updated', { view: this, options: options } );
			},
			cancel: function() {
				$('.enrolment-container-div' ).detach();
			}
		} );

		// Create a modal view class
		BrainPress.Enrollment.dialog = new BrainPress.Enrollment.Modal();

		BrainPress.Enrollment.dialog.beforeSubmit = function() {
			var step = this.currentIndex;
			process_popup_enrollment( step );

			if ( step === ( BrainPress.Enrollment.dialog.views.length - 1 ) ) {
				$('.enrolment-container-div' ).addClass('hidden');
			}

			return false;
		};

		BrainPress.Enrollment.dialog.openAtAction = function( action ) {
			var steps = $( '[data-type="modal-step"]' );
			$.each( steps, function( i, step ) {
				var step_action = $( step ).attr('data-modal-action');
				if ( undefined !== step_action && action === step_action ) {
					BrainPress.Enrollment.dialog.openAt( i );
				}
			});
		};

		BrainPress.Enrollment.dialog.on( 'modal:updated', function() {
			//console.log( 'Activated...');
			//console.log( this.currentIndex );
		});

		// Dialog return actions
		BrainPress.Enrollment.dialog.handle_signup_return = function( data ) {
			var signup_errors = data['signup_errors'];
			var steps = $( '[data-type="modal-step"]' );
			if ( 0 === signup_errors.length && data['user_data']['logged_in'] === true ) {
				// Check if the page is redirected from an invitation link
				if ( _brainpress.invitation_data ) {
					// Add user as instructor
					BrainPress.Enrollment.dialog.add_instructor( data );
				} else {
					$.each( steps, function( i, step ) {
						var action = $( step ).attr( 'data-modal-action' );
						if ( 'yes' === _brainpress.current_course_is_paid && 'paid_enrollment' === action ) {
							BrainPress.Enrollment.dialog.openAt( i );
						} else if ( 'enrolled' === action ) {
							if ( ! data['already_enrolled'] ) {
								// We're in! Now lets enroll
								BrainPress.Enrollment.dialog.attempt_enroll( data );
							} else {
								location.href = _brainpress.course_url;
							}
						}
					});
				}
			} else {
				if ( signup_errors.length > 0 ) {
					$( '.bbm-wrapper #error-messages' ).html('');

					// Display signup errors
					var err_msg = '<ul>';
					signup_errors.forEach( function( item ) {
						err_msg += '<li>' + item + '</li>';
					} );
					err_msg += '</ul>';

					$( '.bbm-wrapper #error-messages' ).html( err_msg );
					$( 'input[name=password]' ).val('');
					$( 'input[name=password_confirmation]' ).val('');
				} else {
					// Redirect to login
					$.each( steps, function( i, step ) {
						var action = step.attr('data-modal-action');
						if ( 'login' === action ) {
							BrainPress.Enrollment.dialog.openAt( i );
						}
					});
				}
			}
		};

		BrainPress.Enrollment.dialog.handle_login_return = function( data ) {
			var signup_errors = data['signup_errors'];
			var steps = $( '[data-type="modal-step"]' );
			if ( 0 === signup_errors.length && data['logged_in'] === true ) {
				// Check if the page is redirected from an invitation link
				if ( _brainpress.invitation_data ) {
					// Add user as instructor
					BrainPress.Enrollment.dialog.add_instructor( data );
				} else {
					$.each( steps, function( i, step ) {
						var action = $( step ).attr( 'data-modal-action' );
						if ( 'yes' === _brainpress.current_course_is_paid && 'paid_enrollment' === action ) {
							BrainPress.Enrollment.dialog.openAt( i );
						} else if ( 'enrolled' === action ) {
							if ( ! data['already_enrolled'] ) {
								// We're in! Now lets enroll
								BrainPress.Enrollment.dialog.attempt_enroll( data );
							} else {
								location.href = _brainpress.course_url;
							}
						}
					});
				}
			} else {
				if ( signup_errors.length > 0 ) {
					$( '.bbm-wrapper #error-messages' ).html('');
					// Display signup errors
					var err_msg = '<ul>';
					signup_errors.forEach( function( item ) {
						err_msg += '<li>' + item + '</li>';
					} );
					err_msg += '</ul>';
					$( '.bbm-wrapper #error-messages' ).html( err_msg );
					$( 'input[name=password]' ).val('');
				}
			}
		};

		// Student successfully enrolled
		BrainPress.Enrollment.dialog.handle_enroll_student_return = function( data ) {
			var steps = $( '[data-type="modal-step"]' );

			if ( true === data['success'] ) {
				$.each( steps, function( i, step ) {
					var action = $( step ).attr( 'data-modal-action' );
					if ( 'yes' === _brainpress.current_course_is_paid && 'paid_enrollment' === action ) {
						BrainPress.Enrollment.dialog.openAt( i );
					} else if ( 'enrolled' === action ) {
						BrainPress.Enrollment.dialog.openAt( i );
					}
				});
			}

			$('.enrolment-container-div' ).removeClass('hidden');
		};

		BrainPress.Enrollment.dialog.signup_validation = function() {
			var valid = true; // we're optimists
			$('.bbm-wrapper #error-messages' ).html('');

			var errors = [];
			// All fields required
			if (
				'' === $( 'input[name=first_name]' ).val().trim() ||
				'' === $( 'input[name=last_name]' ).val().trim() ||
				'' === $( 'input[name=username]' ).val().trim() ||
				'' === $( 'input[name=email]' ).val().trim() ||
				'' === $( 'input[name=password]' ).val().trim() ||
				'' === $( 'input[name=password_confirmation]' ).val().trim()
			) {
				valid = false;
				errors.push( _brainpress.signup_errors['all_fields'] );
			}

			var strength = BrainPress.utility.checkPasswordStrength(
				$('input[name=password]'),         // First password field
				$('input[name=password_confirmation]'), // Second password field
				$('#password-strength'),           // Strength meter
				false,
				[]        // Blacklisted words
			);

			// Can't have a weak password
			if ( strength <= 2 ) {
				valid = false;
				errors.push( _brainpress.signup_errors['weak_password'] );
			}

			// Passwords must match
			if ( strength === 5 ) {
				valid = false;
				errors.push( _brainpress.signup_errors['mismatch_password'] );
			}

			if ( errors.length > 0 ) {
				var err_msg = '<ul>';
				errors.forEach( function( item ) {
					err_msg += '<li>' + item + '</li>';
				} );
				err_msg += '</ul>';

				$( '.bbm-wrapper #error-messages' ).html( err_msg );
			}

			return valid;
		};

		BrainPress.Enrollment.dialog.login_validation = function() {
			var valid = true;
			$('.bbm-wrapper #error-messages' ).html('');

			// All fields required
			if (
				'' === $( 'input[name=log]' ).val().trim() ||
				'' === $( 'input[name=pwd]' ).val().trim()
			) {
				valid = false;
			}

			return valid;
		};

		BrainPress.Enrollment.dialog.signup_data = function( data ) {
			data.first_name = $( 'input[name=first_name]' ).val();
			data.last_name = $( 'input[name=last_name]' ).val();
			data.username = $( 'input[name=username]' ).val();
			data.email = $( 'input[name=email]' ).val();
			data.password = $( 'input[name=password]' ).val();
			data.nonce = $( '.bbm-modal-nonce.signup' ).attr('data-nonce');

			return data;
		};

		BrainPress.Enrollment.dialog.login_data = function( data ) {
			var course_id = $( '.enrollment-modal-container.bbm-modal__views' ).attr('data-course');
			data.username = $( 'input[name=log]' ).val();
			data.password = $( 'input[name=pwd]' ).val();
			data.course_id = course_id;
			data.nonce = $( '.bbm-modal-nonce.login' ).attr('data-nonce');
			return data;
		};

		BrainPress.Enrollment.dialog.attempt_enroll = function( enroll_data ) {
			var nonce = $( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce');
			var course_id = $( '.enrollment-modal-container.bbm-modal__views' ).attr('data-course');

			if ( undefined === nonce || undefined === course_id ) {
				var temp = $(document.createElement('div'));
				temp.html( _.template( $( '#modal-template' ).html() )() );
				temp = $( temp ).find('.enrollment-modal-container')[0];
				nonce = $(temp).attr('data-nonce');
				course_id = $(temp).attr('data-course');
			}

			BrainPress.Post.prepare( 'course_enrollment', 'enrollment:' );
			BrainPress.Post.set( 'action', 'enroll_student' );

			var data = {
				nonce: nonce,
				student_id: enroll_data['user_data']['ID'],
				course_id: course_id,
				step: ''
			};
			BrainPress.Post.set( 'data', data );
			BrainPress.Post.save();

			// Manual hook here as this is not a step in the modal templates
			BrainPress.Post.off( 'brainpress:enrollment:enroll_student_success' );
			BrainPress.Post.on( 'brainpress:enrollment:enroll_student_success', function( data ) {
				// Update nonce
				$( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce', data['nonce'] );

				if ( undefined !== data['callback'] ) {
					var fn = BrainPress.Enrollment.dialog[ data['callback'] ];
					if ( typeof fn === 'function' ) {
						fn( data );
						return;
					}
				}
			} );
		};

		// Get new nonce instance
		BrainPress.Enrollment.dialog.new_nonce = function( nonce_name, callback ) {
			BrainPress.Post.prepare( 'course_enrollment', 'enrollment:' );
			BrainPress.Post.set( 'action', 'get_nonce' );

			var data = {
				action: 'get_nonce',
				nonce: nonce_name,
				step: ''
			};

			BrainPress.Post.set( 'data', data );
			BrainPress.Post.save();

			BrainPress.Post.off( 'brainpress:enrollment:get_nonce_success' );
			BrainPress.Post.on( 'brainpress:enrollment:get_nonce_success', callback );
		};

		// Add instructor
		BrainPress.Enrollment.dialog.add_instructor = function( return_data ) {

			BrainPress.Enrollment.dialog.new_nonce( 'brainpress_add_instructor', function( nonce ) {
				var course_id = _brainpress.invitation_data.course_id;

				BrainPress.Post.prepare( 'course_enrollment', 'enrollment:' );
				BrainPress.Post.set( 'action', 'add_instructor' );

				var data = {
					action: 'add_instructor',
					nonce: nonce.nonce,
					course_id: course_id,
					invite_code: _brainpress.invitation_data.code,
					instructor_id: return_data.user_data.ID,
					step: ''
				};

				BrainPress.Post.set( 'data', data );
				BrainPress.Post.save();

				BrainPress.Post.off( 'brainpress:enrollment:add_instructor_success' );
				BrainPress.Post.on( 'brainpress:enrollment:add_instructor_success', function() {
					BrainPress.Enrollment.dialog.openAtAction( 'instructor-verified' );
				} );

				BrainPress.Post.off( 'brainpress:enrollment:add_instructor_error' );
				BrainPress.Post.on( 'brainpress:enrollment:add_instructor_error', function() {
					BrainPress.Enrollment.dialog.openAtAction( 'verification-failed' );
				});

			});
		};

		// Password Indicator
		$( 'body' ).on( 'keyup', 'input[name=password], input[name=password_confirmation]',
			function() {
				BrainPress.utility.checkPasswordStrength(
					$('input[name=password]'),         // First password field
					$('input[name=password_confirmation]'), // Second password field
					$('#password-strength'),           // Strength meter
					false, //$('.bbm-button.done.signup'),           // Submit button
					[]        // Blacklisted words
				);
			}
		);
	}

	// Init YouTube
	//var tag = document.createElement( 'script' );
	//tag.src = "https://www.youtube.com/iframe_api";
	//var firstScriptTag = document.getElementsByTagName( 'script' )[ 0 ];
	//firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );

	function render_popup_enrollment() {
		var newDiv = $(document.createElement('div'));
		$( 'body' ).append( newDiv );
		$( newDiv ).addClass('enrolment-container-div');

		if ( _brainpress.current_student > 0 ) {

			// Is paid course?
			if ( 'yes' === _brainpress.current_course_is_paid ) {
				$(newDiv).html(BrainPress.Enrollment.dialog.render().el);
				BrainPress.Enrollment.dialog.openAtAction('paid_enrollment');
			} else {
				$(newDiv ).addClass('hidden');
				var enroll_data = {
					user_data: {
						ID: parseInt( _brainpress.current_student )
					}
				};
				// We're logged in, so lets try to enroll
				BrainPress.Enrollment.dialog.attempt_enroll( enroll_data );
				$(newDiv).html(BrainPress.Enrollment.dialog.render().el);
			}

		} else {
			$(newDiv).html(BrainPress.Enrollment.dialog.render().el);
		}
	}

	function process_popup_enrollment( step ) {
		if ( undefined === step ) {
			return false;
		}

		var action = $( $( '[data-type="modal-step"]' )[ step ] ).attr('data-modal-action');
		var nonce = $( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce');
		var fn;

		BrainPress.Post.prepare( 'course_enrollment', 'enrollment:' );
		BrainPress.Post.set( 'action', action );

		if ( action === 'signup' ) {
			fn = BrainPress.Enrollment.dialog[ 'signup_validation' ];
			if ( typeof fn === 'function' && true !== fn() ) {
				return;
			}
		}

		if ( action === 'login' ) {
			fn = BrainPress.Enrollment.dialog[ 'login_validation' ];
			if ( typeof fn === 'function' && true !== fn() ) {
				return;
			}
		}

		var data = {
			nonce: nonce,
			step: step
		};

		fn = BrainPress.Enrollment.dialog[ action + '_data' ];
		if ( typeof fn === 'function' ) {
			data = fn( data );
		}

		BrainPress.Post.set( 'data', data );
		BrainPress.Post.save();

		BrainPress.Post.on( 'brainpress:enrollment:' + action + '_success', function( data ) {

			// Update nonce
			$( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce', data['nonce'] );

			if ( undefined !== data['callback'] ) {
				fn = BrainPress.Enrollment.dialog[ data['callback'] ];
				if ( typeof fn === 'function' ) {
					fn( data );
					return;
				}
			}
			if ( undefined !== data.last_step && parseInt( data.last_step ) < ( BrainPress.Enrollment.dialog.views.length -1 ) ) {
				BrainPress.Enrollment.dialog.openAt( parseInt( data.last_step ) + 1 );
				$('.enrolment-container-div' ).removeClass('hidden');
			}

		} );

		BrainPress.Post.on( 'brainpress:enrollment:' + action + '_error', function( data ) {
			if ( undefined !== data['callback'] ) {
				fn = BrainPress.Enrollment.dialog[ data['callback'] ];
				if ( typeof fn === 'function' ) {
					fn( data );
					return;
				}

			}
		} );

	}

	function bind_enrollment_actions() {
		BrainPress.Post.on( 'brainpress:notification:delete_success', function() {
			window.location.reload();
		} );
	}

	function bind_buttons() {
		// Section Title Click
		$( '.unit-archive-list-wrapper .section-title' ).on( 'click', function() {
			var link = $( $( $( this ).parents( '.unit-archive-single' )[0] ).find('a.unit-archive-single-title')[0] ).attr('href');
			var section_hash = 'section-' + $(this).attr('data-id');
			var has_link = $( this ).find( 'a.unit-archive-single-title').length > 0;

			if ( has_link ) { window.location.href = link + '#' + section_hash; }
		} );

		// Module Title Click
		$( '.unit-archive-list-wrapper .module-title' ).on( 'click', function() {
			var link = $( $( $( this ).parents( '.unit-archive-single' )[0] ).find('a.unit-archive-single-title')[0] ).attr('href');
			var mod_hash = 'module-' + $(this).attr('data-id');
			var has_link = $( this ).find( 'a.unit-archive-single-title').length > 0;
			if ( has_link ) {
				window.location.href = link + '#' + mod_hash;
			}
			/**
			 * "focus" view mode
			 */
			if ( 'focus' == $( '.unit-archive-list-wrapper' ).data( 'view-mode' ) ) {
				var module = $('a', $(this)).attr("href").split("#");
				var link = $( $( $( this ).parents( '.unit-archive-single' )[0] ).find('a.unit-archive-single-title')[0] ).attr('href') + '#' + module[1];
				window.location.href = link;
			}
		} );

		$( '.apply-button.signup, .apply-button.enroll' ).on( 'click', function( ev ) {
			ev = ev || window.event;

			ev.preventDefault();
			ev.stopPropagation();
			ev.stopImmediatePropagation();
			render_popup_enrollment();
		});

		$( '.apply-button' ).on( 'click', function( e ) {
			var target = e.currentTarget;

			if ( $( target ).attr( 'data-link' ) ) {
				window.location.href = $( target ).attr( 'data-link' );
			}
		} );

		$( 'button' ).on( 'click', function( e ) {
			var target = e.currentTarget;

			if ( $( target ).attr( 'data-link' ) ) {
				window.location.href = $( target ).attr( 'data-link' );
			}
		} );

		// Make course boxes clickable
		$( '.course_list_box_item.clickable' ).on( 'click', function( e ) {
			var target = e.currentTarget;

			if ( $( target ).attr( 'data-link' ) ) {
				window.location.href = $( target ).attr( 'data-link' );
			}
		} );


		$( '.li-locked-unit a' ).on('click', function( e ) {
			e.stopImmediatePropagation();
			e.preventDefault();
		} );

		//$( '.view-response' ).link_popup( { link_text:  _brainpress.workbook_view_answer });
		//$( '.view-response' ).link_popup( { link_text:  '<span class="dashicons dashicons-visibility"></span>' });
		$( '.workbook-table .view-response' ).link_popup( { link_text:  '<span class="dashicons dashicons-visibility"></span>', offset_x: -160 });
		$( '.workbook-table .feedback' ).link_popup( { link_text:  '<span class="dashicons dashicons-admin-comments"></span>' });
		bind_psecommerce_add_to_cart_button();

		/**
		 * close message
		 */
		$('.course-message-container .course-message-close').on( 'click', function() {
			$(this).parent().slideUp();
		});

		/**
		 * fold/unfold list
		 */
		$('.unit-archive-single .fold').on('click', function() {
			var target = $('>ul', $(this).parent() );
			var unit = $('.unit-archive-single-title', $(this).parent());
			var modules_container = $('.unit-archive-module-wrapper', $(this).parent());
			var container = $(this);
			if ( container.hasClass('folded') ) {
				target.slideDown( function() {
					container.removeClass('folded');
					container.closest('li').removeClass('folded').addClass('unfolded');
					unit.attr('href', unit.data('original-href'));
					unit.off('click');
				});
			} else {
				target.slideUp(function() {
					container.addClass('folded');
					container.closest('li').removeClass('unfolded').addClass('folded');
					if ( "undefined" == typeof( unit.data('href') ) ) {
						/**
						 * find last seen module
						 */
						var module = $('.module-seen', modules_container).last();
						if ( module.length ) {
							module = $('.module-title', module );
							if ( module.length ) {
								unit.attr('href', unit.attr('href') + '#module-'+ module.data('id') );
								return false;
							}
						}
						/**
						 * find last seen section
						 */
						var section = $('.section-seen', modules_container).last();
						if ( section.length ) {
							section = $('.section-title', section );
							if ( section.length ) {
								unit.attr('href', unit.attr('href') + '#section-'+ section.data('id') );
								return false;
							}
						}
					}
				});
			}
			return false;
		});

		// Course structure folded
		$( '.course-structure .unit .fold' ).on( 'click', function() {
			var span = $(this),
				container = span.parents( 'li' ).first(),
				module_wrapper = container.find( '.unit-structure-modules' ),
				is_open = container.is( '.folded' )
			;

			if ( is_open ) {
				container.removeClass( 'folded' ).addClass( 'unfolded' );
				span.removeClass( 'folded' );
				module_wrapper.slideDown();
			} else {
				container.removeClass( 'unfolded' ).addClass( 'folded' );
				span.addClass( 'folded' );
				module_wrapper.slideUp();
			}
		});

		/*
		 * Comments
		 */
		$( '.brainpress-focus-view #commentform #submit' ).unbind( 'click' ).on( 'click', function() {
			var form = $(this).closest('form'), mask;
			if ( '' == $( '#comment', form ).val() ) {
				alert(_brainpress.comments.require_valid_comment);
				return false;
			}
			$('#respond #cancel-comment-reply-link').hide();
			form.append('<div class="mask"><span><i class="fa fa-spinner fa-pulse"></i></span></div>');
			mask = $('.mask', form );
			mask.css({
				width: form.width()+"px",
				height: form.height()+"px",
			});
			$('span', mask).css({
				marginTop: ( form.height() / 2 - 20 ) +"px"
			});
			var model = new BrainPress.Models.CourseFront();
			model.set( 'action', 'comment_add_new' );
			model.set( 'comment_parent', $( '#comment_parent', form ).val() );
			model.set( 'comment_post_ID', $( '#comment_post_ID', form ).val() );
			model.set( 'comment_content', $( '#comment', form ).val() );
			model.set( 'brainpress_subscribe', $( '[name=brainpress_subscribe]', form ).val() );
			model.set( 'nonce', $( '#brainpress-add-commment-nonce', form ).val() );
			model.save();
			model.off( 'brainpress:comment_add_new_success' );
			model.on( 'brainpress:comment_add_new_success', function( data ) {
				var focus_nav_next = $( '.focus-nav-next.module-is-not-done' );

					/**
					 * Single-comment answer.
					 */
					if ( "single-comment" == data.answer_mode ) {
						/**
						 * parent comment
						 */

						if ( 0 == data.data.comment_parent ) {
							var comment_list = $( '#comments .comments-list' );
							if ( 0 < comment_list.length ) {
								$('#comments .comments-list').prepend( data.data.html );
							} else {
								// Reload the module
								BrainPress.FocusMode.init_focus_mode();
							}
						} else {
							/**
							 * nested comment
							 */
							comment_parent = $('#comment-'+data.data.comment_parent);
							if ( 0 == $('.children', comment_parent ).length ) {
								comment_parent.append('<ul class="children"></ul>');
							}
							$('.children', comment_parent).first().append(data.data.html);
							$('.comments-list').before($('#respond'));
						}

						/**
						 * reset form
						 */
						$('#comment').val('');
						$('#comment_parent').val(0);

						// Focus to the last inserted comment
						var last_inserted_comment = $( '#comment-' + data.data.comment_id ), top;
						if ( 0 < last_inserted_comment.length ) {
							top = last_inserted_comment.offset().top;
							$(window).scrollTop( top );
						}
					} else {
						$('#comments .comments-list-container').html( data.data.html );
					}
					bind_buttons();

				$('.mask', form ).detach();

				// Update the navigation links.
				if ( 0 < focus_nav_next.length ) {
					$('.brainpress-focus-view .focus-nav-next').replaceWith( data.data.next_nav );
					BrainPress.FocusMode.bind_focus_nav();
				}

				/**
				 * Notify others that the module is change.
				 **/
				BrainPress.Events.trigger( 'brainpress:module_change', data );
			});
			model.on( 'brainpress:comment_add_new_error', function() {
				/**
				 * detach last mask
				 */
				mask.detach();
				$('#respond #cancel-comment-reply-link').show();
			});
			return false;
		});

		/**
		 * bind file uploads
		 */
		$('label.file').on( 'change', 'input[type=file]', function() {
			var target = $('span', $(this).parent() );
			if ( $(this).val().length) {
				target.html( target.data('change') );
			} else {
				target.html( target.data('upload') );
			}
			return true;
		});
	}

	/**
	 * MP add to cart
	 */
	function bind_psecommerce_add_to_cart_button() {
		if ( undefined === _brainpress.psecommerce_is_used || 'no' === _brainpress.psecommerce_is_used ) {
			return;
		}
		$('body.single-course button.mp_button-addcart').on( 'click', function() {
			var form = $(this).closest('form');
			$.ajax({
				type: 'POST',
				url: form.data('ajax-url'),
				data: {
					product: $('[name=product_id]', form).val(),
					cart_action: 'add_item'
				}
			}).done( function(data) {
				if ( data.success && undefined !== _brainpress.psecommerce_cart_url ) {
					window.location.assign( _brainpress.psecommerce_cart_url );
				}
			});
			return false;
		});
	}

	function bind_module_actions() {
		// Resubmit
		$( '.module-container .module-result .resubmit a' ).on( 'click', function() {
			var parent = $( this ).parents( '.module-container' );
			var elements = $( parent ).find( '.module-elements' );
			var response = $( parent ).find( '.module-response' );
			var result = $( parent ).find( '.module-result' );

			$( elements ).removeClass( 'hide' );
			$( response ).addClass( 'hide' );
			$( result ).addClass( 'hide' );
		} );

		// Validate File Selected
		$( '.module-container input[type=file]' ).on( 'change', function() {
			var parent = $( this ).parents( '.module-container' );
			var filename = $( this ).val();
			var extension = filename.split( '.' ).pop();
			var allowed_extensions = _.keys( _brainpress.allowed_student_extensions );
			var allowed_string = allowed_extensions.join( ', ' );
			var progress = $( parent ).find( '.upload-progress' );
			var allowed = _.contains( allowed_extensions, extension );
			var submit_button_container = $( '.module-submit-action', $(this).closest('.module-elements')).parent();

			$( progress ).find( '.invalid-extension' ).detach();

			if ( allowed ) {
				submit_button_container.closest('form').addClass('is-valid-file');
			} else {
				$( progress ).append( '<span class="invalid-extension">' + _brainpress.invalid_upload_message + allowed_string + '</span>' );
				submit_button_container.closest('form').removeClass('is-valid-file');
			}
		} );

		// Submit Result
		// Depracated!!!
		$( '.module-submit-action' ).on( 'click', function() { return;
			var el = this;
			var parent = $( el ).parents( '.module-container' );
			var elements = $( parent ).find( '.module-elements' );
			var response = $( parent ).find( '.module-response' );
			var result = $( parent ).find( '.module-result' );

			var module_id = $( parent ).attr( 'data-module' );
			var module_type = $( parent ).attr( 'data-type' );
			var course_id = $( parent ).find( '[name=course_id]' ).val();
			var unit_id = $( parent ).find( '[name=unit_id]' ).val();
			var student_id = $( parent ).find( '[name=student_id]' ).val();
			var value = '';

			var not_valid = false;

			switch ( module_type ) {

				case 'input-checkbox':
					value = [];
					$.each( $( parent ).find( '[name="module-' + module_id + '"]:checked' ), function( i, item ) {
						value.push( $( item ).val() );
					} );
					not_valid = value.length === 0;
					break;

				case 'input-radio':
					el = $( parent ).find( '[name="module-' + module_id + '"]:checked' );
					if ( el ) {
						value = $( el ).val();
					} else {
						not_valid = true;
					}
					break;

				case 'input-select':
					value = $( parent ).find( '[name=module-' + module_id + ']' ).val();
					break;

				case 'input-text':
					value = $( parent ).find( '[name=module-' + module_id + ']' ).val();
					not_valid = value.trim().length === 0;
					break;

				case 'input-textarea':
					value = $( parent ).find( '[name=module-' + module_id + ']' ).val();
					not_valid = value.trim().length === 0;
					break;

				case 'input-quiz':
					value = [];
					var questions = $( parent ).find( '.module-quiz-question' );

					$.each( questions, function( qi, question) {
						var answers = $( question).find('[type="checkbox"],[type="radio"]');
						value[qi] = [];
						$.each( answers, function( ai, answer ) {
							value[qi][ai]= $(answer).is( ':checked' );
						});
					});
					break;

				case 'input-form':
					value = [];
					var questions = $( parent ).find( '.module-form-question' );

					$.each( questions, function( qi, question) {
						var answers = $( question).find('textarea,select,[type="text"],[type="checkbox"],[type="radio"]');
						value[qi] = [];
						$.each( answers, function( ai, answer ) {
							if( $( answer ).is( '[type="text"]' ) || $( answer ).is( 'textarea' ) || $( answer ).is( 'select' ) ){
								value[qi][ai] = $( answer ).val();
							} else {
								value[qi][ai]= $( answer ).is( ':checked' );
							}
						});
					});
					break;					

				case 'input-upload':
					if ( supportAjaxUploadWithProgress() ) {
						var formData = new FormData();

						var file = $( parent ).find( '[name=module-' + module_id + ']' )[ 0 ].files[ 0 ];

						// Exit if no file is selected
						if ( ! file || ! file.name ) {
							return;
						}

						// Exit if extension not supported
						var extension = file.name.split( '.' ).pop();
						var allowed_extensions = _.keys( _brainpress.allowed_student_extensions );
						var allowed = _.contains( allowed_extensions, extension );
						var response_div;

						if ( !allowed ) {
							return;
						}

						var uri = '';
						formData.append( 'course_action', 'upload-file' );
						formData.append( 'course_id', course_id );
						formData.append( 'unit_id', unit_id );
						formData.append( 'module_id', module_id );
						formData.append( 'student_id', student_id );
						formData.append( 'src', 'ajax' );
						formData.append( 'file', file );

						var xhr = new XMLHttpRequest();

						// Started
						xhr.upload.addEventListener( 'loadstart', function() {
							var progress = $( parent ).find( '.upload-progress' );
							$( progress ).find( '.spinner' ).detach();
							$( progress ).append( '<span class="image spinner">&#xf111</span>' );
						}, false );
						// Progress
						xhr.upload.addEventListener( 'progress', function( e ) {
							var percent = e.loaded / e.total * 100;
							var percent_el = $( parent ).find( '.upload-percent' );
							percent = parseInt( percent );

							if ( percent_el.length > 0 ) {
								$( percent_el ).replaceWith( '<span class="upload-percent">' + percent + '%</span>' );
							} else {
								$( parent ).find( '.upload-progress' ).append( '<span class="upload-percent">' + percent + '%</span>' );
							}
						}, false );

						xhr.upload.addEventListener( 'load', function() {
							// Keep this here for future
						}, false );

						xhr.addEventListener( 'readystatechange', function( e ) {
							var status, readyState;
							try {
								readyState = e.target.readyState;
								status = e.target.status;
							}
							catch ( err ) {
								return;
							}

							// Set a default as ready state might trigger xhr requests
							var data = { success: false };
							try {
								data = JSON.parse( e.target.responseText );
							} catch( err ) {}

							// Upload completed!
							if ( readyState === 4 && parseInt( status ) === 200 && data.success ) {
								// Completed with success :)

								$( parent ).find( '.upload-percent' ).detach();
								$( parent ).find( '.upload-progress .spinner' ).detach();
								$( result ).detach();
								$( elements ).addClass( 'hide' );
								response_div = response.length > 0 ? $( response ) : $( '<div class="module-response">' ).insertAfter( elements );
								response_div.replaceWith( '<div class="module-response">' +
									'<p class="file_holder">' + _brainpress.file_uploaded_message + '</p>' +
									'</div>'
								);

								// Update the navigation links.
								if ( data.html && data.html.length ) {
									var module = jQuery( data.html );
									var new_nav = module.find( '.focus-nav' );
									$('.brainpress-focus-view .focus-nav').replaceWith( new_nav );
									BrainPress.FocusMode.bind_focus_nav();
								}

							} else if ( readyState === 4 ) {
								// Completed with error...

								$( parent ).find( '.upload-percent' ).detach();
								$( parent ).find( '.upload-progress .spinner' ).detach();
								$( result ).detach();
								$( elements ).addClass( 'hide' );
								response_div = response.length > 0 ? $( response ) : $( '<div class="module-response">' ).insertAfter( elements );

								response_div.replaceWith( '<div class="module-response">' +
									'<p class="file_holder">' + _brainpress.file_upload_fail_message + '</p>' +
									'</div>'
								);
							}

						}, false );

						// Set up request
						xhr.open( 'POST', uri, true );

						// Fire!
						xhr.send( formData );

					} else {
						$( parent ).find( 'form' ).submit();
					}

					// No processing past this point
					return;
					// break; - no `break` because of `return` above.
			} // End: switch()

			if ( not_valid ) {
				return;
			}

			// Add Spinner.
			$( elements ).find( '.response-processing' ).detach();
			$( elements ).find( '.module-submit-action' ).append( '<span class="response-processing image spinner">&#xf111</span>' );

			// Record Response
			var model = new BrainPress.Models.CourseFront();

			model.set( 'action', 'record_module_response' );
			model.set( 'course_id', course_id );
			model.set( 'unit_id', unit_id );
			model.set( 'module_id', module_id );
			model.set( 'student_id', student_id );
			model.set( 'response', value );
			model.set( 'module_type', module_type );

			model.save();

			model.on( 'brainpress:record_module_response_success', function( data ) {

				// Redirect back to units details when completed.
				if ( data.completed ) {
					var course_link = $( '.course-units-link' );

					if ( course_link.length > 0 ) {
						window.location = course_link.attr( 'href' );
					}
				}
				$( elements ).find( '.response-processing' ).detach();

				$( result ).detach();
				$( elements ).addClass( 'hide' );

				var html = '';
				if ( data.quiz_result_screen != null && data.quiz_result_screen.length > 0 ) {
					// Enable navigation after submit
					$('.brainpress-focus-view .not-active').removeClass('not-active');

					html = data.quiz_result_screen;

					if (
							( 'boolean' === typeof( data.results ) && false === data.results ) ||
							( data.results.attributes.mandatory && ! data.results.passed )
					   ){
						$('.brainpress-focus-view .focus-nav-next').addClass('not-active');
					}

					// Hide not passed message
					var not_passed_div = $( '.not-passed-message' );

					if ( not_passed_div.length > 0 ) { 
						not_passed_div.hide();
					}
				} else {
					html = '<div class="module-response">' +
						'<p class="file_holder">' + _brainpress.response_saved_message + '</p>' +
						'</div>';
				}

				/**
				 * message for quiz
				 */
				if ( 'input-quiz' === data.module_type ) {
					var not_passed_message = $('.not-passed-message');
					if ( 'undefined' !== typeof( data.results.message ) ) {
						if ( 'boolean' === typeof( data.results.message.hide ) && data.results.message.hide ) {
							not_passed_message.detach();
						} else {
							if ( ! not_passed_message.length ) {
								not_passed_message = '<div class="not-passed-message"></div>';
								$('.module-content').after(not_passed_message);
								not_passed_message = $('.not-passed-message');
							}
							not_passed_message.html(data.results.message.text);
						}
					}
				}

				if ( 0 === response.length ) {
					$( html ).insertAfter( elements );
				} else {
					$( response ).replaceWith( html );
				}

				// Update the navigation links.
				if ( data.html && data.html.length ) {
					var module = jQuery( data.html );
					var new_nav = module.find( '.focus-nav' );
					$('.brainpress-focus-view .focus-nav').replaceWith( new_nav );
				}
				BrainPress.FocusMode.bind_focus_nav();

				/**
				 * Notify others that the module is change.
				 **/
				BrainPress.Events.trigger( 'brainpress:module_change', data );
			} );

			model.on( 'brainpress:record_module_response_error', function() {
				$( elements ).find( '.response-processing' ).detach();

				$( result ).detach();
				$( elements ).addClass( 'hide' );

				var html = '<div class="module-response">' +
					'<p class="file_holder">' + _brainpress.response_fail_message + '</p>' +
					'</div>';

				if ( 0 === response.length ) {
					$( parent ).append( html );
				} else {
					$( response ).replaceWith( html );
				}
			} );
		} );
	}

	function supportAjaxUploadWithProgress() {
		// Is the File API supported?
		function supportFileAPI() {
			var fi = document.createElement( 'INPUT' );
			fi.type = 'file';
			return 'files' in fi;
		}

		// Are progress events supported?
		function supportAjaxUploadProgressEvents() {
			var xhr = new XMLHttpRequest();
			return !!(xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
		}

		// Is FormData supported?
		function supportFormData() {
			return !!window.FormData;
		}

		return supportFileAPI() && supportAjaxUploadProgressEvents() && supportFormData();
	}


	function course_completion() {
		var model = new BrainPress.Models.CourseFront();

		model.set( 'action', 'calculate_completion' );
		model.set( 'course_id', _brainpress.current_course );
		model.set( 'student_id', _brainpress.current_student );
		model.save();
	}

	function external() {
		var a_col = $( 'ul.units-archive-list a' ).css('color');
		var p_col = $( 'body' ).css('color').replace('rgb(', '' ).replace(')', '' ).split( ',');
		var emptyFill = 'rgba(' + p_col[0] + ', ' + p_col[1] + ', ' + p_col[2] + ', 1)';

		$( '.course-progress-disc' ).each( function () {
			var item_data = $( this ).data();
			var text_color = a_col;
			var text_align = 'center';
			var text_denominator = 4.5;
			var text_show = true;
			var animation = { duration: 1200, easing: "circleProgressEase" };
			if ( item_data.knobFgColor ) {
				a_col = item_data.knobFgColor;
			}
			if ( item_data.knobEmptyColor) {
				emptyFill = item_data.knobEmptyColor;
			}
			if ( item_data.knobTextColor ) {
				text_color = item_data.knobTextColor;
			}
			if ( item_data.knobTextAlign ) {
				text_align = item_data.knobTextAlign;
			}
			if ( item_data.knobTextDenominator ) {
				text_denominator = item_data.knobTextDenominator;
			}
			if ( 'undefined' !== typeof( item_data.knobTextShow ) ) {
				text_show = item_data.knobTextShow;
			}
			if ( 'undefined' !== typeof( item_data.knobAnimation ) ) {
				animation = item_data.knobAnimation;
			}

			var init = { color: a_col };
			$( this ).circleProgress( { fill: init, emptyFill: emptyFill, animation: animation } );

			var parent = $( this ).parents('ul')[0];

			$( this ).on( 'circle-animation-progress', function( e, v ) {
				var obj = $( this ).data( 'circle-progress' ),
					ctx = obj.ctx,
					s = obj.size,
					sv = (100 * v).toFixed(),
					ov = (100 * obj.value ).toFixed();
				sv = 100 - sv;
				if ( sv < ov ) {
					sv = ov;
				}
				ctx.save();
				if ( text_show ) {
					ctx.font = s / text_denominator + 'px sans-serif';
					ctx.textAlign = text_align;
					ctx.textBaseline = 'middle';
					ctx.fillStyle = text_color;
					ctx.fillText( sv + '%', s / 2 + s / 80, s / 2 );
				}
				ctx.restore();
			} );

			$( this ).on( 'circle-animation-end', function() {
				var obj = $( this ).data( 'circle-progress' ),
					ctx = obj.ctx,
					s = obj.size,
					sv = (100 * obj.value ).toFixed();
				obj.drawFrame( obj.value );
				if ( text_show ) {
					ctx.font = s / text_denominator + 'px sans-serif';
					ctx.textAlign = text_align;
					ctx.textBaseline = 'middle';
					ctx.fillStyle = text_color;
					ctx.fillText( sv + '%', s / 2, s / 2 );
				}
			} );

			// In case animation doesn't run
			var obj = $( this ).data( 'circle-progress' ),
				ctx = obj.ctx,
				s = obj.size,
				sv = (100 * obj.value ).toFixed();
			if ( text_show ) {
				ctx.font = s / text_denominator + 'px sans-serif';
				ctx.textAlign = text_align;
				ctx.textBaseline = 'middle';
				ctx.fillStyle = text_color;
				ctx.fillText( sv + '%', s / 2, s / 2 + s / 80 );
			}
			if (  'undefined' !== typeof( item_data.knobTextPrepend ) && item_data.knobTextPrepend ) {
				$( this ).parent().prepend(  '<span class="progress">'+sv + '%</span>' );
			}
		} );
	}

	function bind_course_discussions() {
		$( '.course-discussion-content.new .button-links .submit-discussion' ).on( 'click', function() {
			$( this ).parents( 'form' ).submit();
		} );
	}

	BrainPress.FocusMode = BrainPress.FocusMode || {};

	BrainPress.FocusMode.bind_focus_nav = function() {

		$( '.brainpress-focus-view .focus-nav-prev' ).off('click');
		$( '.brainpress-focus-view .focus-nav-next' ).off('click');
		$( '.brainpress-focus-view .focus-nav-reload' ).off('click');
		$( '.brainpress-focus-view a.breadcrumb-course-unit-section.crumb' ).off('click');
		$( '.brainpress-focus-view a.nav-go-back-link' ).off('click');

		$( '.brainpress-focus-view a.nav-go-back-link' ).on('click', function( ev ) {
			ev.preventDefault();

			var url = $( this ).attr( 'href' );
			window.location.href = url;
			window.location.reload();

			return false;
		});

		$( '.brainpress-focus-view .focus-nav-prev, .brainpress-focus-view .focus-nav-next, .brainpress-focus-view .focus-nav-reload' ).on('click', function() {

			if ( $(this).hasClass( 'module-is-not-done' ) ) {
				create_modal_model();
				var newDiv = $(document.createElement('div'));
				$( 'body' ).append( newDiv );
				$( newDiv ).addClass('enrolment-container-div');
				$( newDiv ).html(BrainPress.Enrollment.dialog.render().el);
				BrainPress.Enrollment.dialog.openAtAction( 'mandatory' );
				return false;
			}

			var element = $('.brainpress-focus-view' );
			var type = $( this ).attr('data-type');
			var item_id = $( this ).attr('data-id');
			var new_unit_id = $( this ).attr('data-unit');
			var cur_unit_id = element.attr('data-unit');
			var url = $( this ).attr('data-url');
			var offset = 0;

			// Prevent from reloading if there's nothing to load!
			if ( ! type && ! item_id ) {
				return;
			}

			$( '.brainpress-focus-view .loader' ).removeClass('hidden');
			$( '.brainpress-focus-view .focus-main' ).hide( 'fast');

			if ( new_unit_id && new_unit_id.length && url ) {
				if ( cur_unit_id && new_unit_id !== cur_unit_id ) {
					$( 'body,html' ).animate( { scrollTop: offset, duration: 100 } );

					// Navigation changed to a different Unit:
					// Need to reload the page to refresh all page elements.
					window.location.href = url;
					return false;
				}
			}
			if ( url && url.length ) {
				element.attr('data-url', url);
			}

			if ( $('.entry-title' ).length > 0 ) {
				offset = $('.entry-title' ).offset().top - 32;
			} else {
				offset = 32;
			}
			offset = BrainPress.apply_filters( 'brainpress_focus_top_offset', offset );

			$( 'body,html' ).animate( { scrollTop: offset, duration: 100 } );

			BrainPress.FocusMode.load_focus_item( type, item_id );
		});

		$( 'a.breadcrumb-course-unit-section.crumb, a.breadcrumb-course-unit.crumb' ).on('click', function() {
			var type = 'section';
			var item_id = $( this ).attr('data-id');

			$( '.brainpress-focus-view .loader' ).removeClass('hidden');
			$( '.brainpress-focus-view .focus-main' ).hide( 'fast');

			var offset = 0;
			if ( $('.entry-title' ).length > 0 ) {
				offset = $('.entry-title' ).offset().top - 32;
			} else {
				offset = 32;
			}
			offset = BrainPress.apply_filters( 'brainpress_focus_top_offset', offset );

			$( 'body,html' ).animate( { scrollTop: offset, duration: 100 } );

			BrainPress.FocusMode.load_focus_item( type, item_id );
		});

	};

	BrainPress.FocusMode.load_focus_item = function( type, item_id ) {

		BrainPress.Post.prepare( 'course_front', 'focus:' );
		BrainPress.Post.set( 'action', 'get_unit_' + type );

		if ( undefined === item_id || item_id.length === 0 ) {
			item_id = 1;
		}

		var element = $('.brainpress-focus-view' );
		var data = {
			course_id: element.attr('data-course'),
			unit_id: element.attr('data-unit'),
			type: type,
			item_id: item_id
		};
		var new_url = element.attr('data-url');

		if ( new_url && new_url.length ) {
			// Update the browsers Address-Bar to reflect the current module.
			// This is helpful to make F5/Refresh stay on current module.
			new_url = new_url.replace( 'http://', '//' ).replace( 'https://', '//' );
			window.history.replaceState('', 'BrainPress', new_url);
		}

		$('.brainpress-focus-view' ).load(
			_brainpress.home_url + '/brainpress_focus/' + data.course_id + '/' + data.unit_id + '/' + data.type + '/' + data.item_id,
			initialize_module
		);

		function initialize_module() {
			BrainPress.FocusMode.bind_focus_nav();
			BrainPress.Page.init();

			// Audio Player Fix
			$( 'audio' ).css( 'visibility', 'visible' );
			$( 'audio' ).css( 'outline', 'none' );

			$( '.quiz_timer').brainpress_timer( {
				toggle_element: $('.quiz_timer').siblings('.module-quiz-questions'),
				seconds: parseInt( $('.quiz_timer').attr('data-time') ),
				action: 'none',
				running: false
			} );

			$( '.quiz_timer').on('timer_started', function() {
			});

			$( '.quiz_timer').on('timer_ended', function() {
			});

			/**
			 * scroll to comment if it is needed
			 */
			if ( location.hash.match(/^#comment-/) !== null ) {
				$('html, body').animate({
					scrollTop: $(location.hash).offset().top
				}, 0 );
			}

			/**
			 * add class to tree
			 */
			var tree = $('.course-structure-block ul.tree');
			if ( tree.length ) {
				$('.module', tree ).removeClass( 'current-module' );
				$('.module-' + data.item_id, tree ).addClass( 'current-module' );
			}

			/**
			 * check redirect
			 */
			var redirect = $('.course-redirect-data');
			if ( redirect.length ) {
				if ( 'unit-not-available' == redirect.data('redirect') ) {
					var url = _brainpress.course_url_unit_nor_available;
					url += '&type=' + redirect.data('type');
					url += '&id=' + redirect.data('id');
					//window.location.href = url;
				}
			}

		}

		//
		//BrainPress.Post.set( 'data', data );
		//BrainPress.Post.save();
		//
		//// Manual hook here as this is not a step in the modal templates
		//BrainPress.Post.off( 'brainpress:focus:get_unit_' + type + '_success' );
		//BrainPress.Post.on( 'brainpress:focus:get_unit_' + type + '_success', function( data ) {
		//    // Update nonce
		//    //$( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce', data['nonce'] );
		//    console.log(data.section_info.content);
		//    //if ( undefined !== data['callback'] ) {
		//    //    var fn = BrainPress.Enrollment.dialog[ data['callback'] ];
		//    //    if ( typeof fn === 'function' ) {
		//    //        console.log('callback is next....' + data['callback'] );
		//    //        fn( data );
		//    //        return;
		//    //    }
		//    //}
		//} );
		//
		//BrainPress.Post.off( 'brainpress:focus:get_unit_' + type + '_error' );
		//BrainPress.Post.on( 'brainpress:focus:get_unit_' + type + '_error', function( data ) {
		//    // Update nonce
		//    //$( '.enrollment-modal-container.bbm-modal__views' ).attr('data-nonce', data['nonce'] );
		//    console.log(data);
		//    //if ( undefined !== data['callback'] ) {
		//    //    var fn = BrainPress.Enrollment.dialog[ data['callback'] ];
		//    //    if ( typeof fn === 'function' ) {
		//    //        console.log('callback is next....' + data['callback'] );
		//    //        fn( data );
		//    //        return;
		//    //    }
		//    //}
		//} );

	};

	/**
	 * Reload course structure whenever a new module is loaded or updated.
	 **/
	var refresh_course_structure = function() {
		var course_structure_block = $( '.course-structure-block' );

		if ( course_structure_block.length > 0 ) {
			var course_nonce = course_structure_block.data( 'nonce' ),
				course_data = course_structure_block.data(),
				course_id = course_data.course,
				url = window.location
			;

			var data = {
				data: course_data,
				course_id: course_id,
				nonce: course_nonce
			};

			$.get( url, data, function( response ) {
				// Reload course structure
				course_structure_block.replaceWith( response );
			} );
		}
	};
	BrainPress.Events.on( 'brainpress:module_change', refresh_course_structure );

	BrainPress.FocusMode.init_focus_mode = function() {

		var is_module = location.hash.match(/^#module-/ ) !== null;
		var is_section = location.hash.match(/^#section-/) !== null;
		var is_comment = location.hash.match(/^#comment-/) !== null;
		var item_id;

		if ( ! is_module && ! is_section && ! is_comment ) {
			is_section = true;
		}

		if ( is_module ) {
			item_id =  location.hash.replace('#module-', '');
			BrainPress.FocusMode.load_focus_item( 'module', item_id );
		}

		if ( is_section ) {
			item_id = location.hash;

			if ( undefined === item_id || item_id.length === 0 ) {
				var element = $('.brainpress-focus-view');
				item_id = $( element ).attr('data-page');
			}

			item_id = item_id.replace( '#section-', '' );

			BrainPress.FocusMode.load_focus_item( 'section', item_id );
		}

		if ( is_comment ) {
			item_id =  location.hash.replace('#comment-', '');
			BrainPress.FocusMode.load_focus_item( 'comment', item_id );
		}

	};

	function bind_focus_mode() {
		var focus_active = $('.brainpress-focus-view');

		if ( undefined !== focus_active && focus_active.length > 0 ) {
			BrainPress.FocusMode.init_focus_mode();
		}
	}

	BrainPress.Page.init = function() {
		var is_focus_mode = $('.brainpress-focus-view').length > 0;

		bind_buttons();
		bind_module_actions();
		bind_course_discussions();
		external();

		// TIMER
		if ( ! is_focus_mode ) {
			$( '.quiz_timer').brainpress_timer();
			$( '.quiz_timer').on('timer_started', function() {
			});

			$( '.quiz_timer').on('timer_ended', function() {
			});
		}

	};

	// Toggle module
	BrainPress.toggleModule = function() {
		var button = $(this),
			module_id = button.data( 'module' ),
			module_elements = $( '#cp-element-' + module_id ),
			module_response = $( '#cp-response-' + module_id )
		;
		module_response.addClass( 'hide' );
		module_elements.removeClass( 'hide' );

		return false;
	};

	$( document ).ready( function() {
		BrainPress.Page.init();
		create_modal_model();
		bind_focus_mode();
		bind_enrollment_actions();

		var unsubscribe = $( '#cp-unsubscribe-message' );

		if ( unsubscribe.length > 0 ) {
			$( '<div class="enrolment-container-div">' ).html( BrainPress.Enrollment.dialog.render().el ).appendTo( 'body' );
			BrainPress.Enrollment.dialog.openAtAction( 'unsubscribe' );
		}
	} )
	.on( 'click', '.cp .button-reload-module', BrainPress.toggleModule );

	/**
	 * bind arrows on course module page
	 */
	$(document).keydown( function( e ) {
		/**
		 * Avoid to change module if CTRL, ALT, META or Shift is pressed.
		 */
		if( e.ctrlKey || e.altKey || e.shiftKey || e.metaKey ) {
			return;
		}
		/**
		 * avoid when we edit something
		 */
		var focus = $(':focus');
		if( 0 < focus.length ) {
			if ( focus.is("textarea") ) {
				return;
			}
			if ( focus.is("input") ) {
				return;
			}
		}
		switch( e.which ) {
			case 37: // left
				if ( $('.focus-nav .focus-nav-prev a').length > 0 ) {
					$('.focus-nav .focus-nav-prev a').trigger('click');
				}
				break;
			case 39: // right
				if ( $('.focus-nav .focus-nav-next a').length > 0 ) {
					$('.focus-nav .focus-nav-next a').trigger('click');
				}
				break;
		}
	});

})( jQuery );

BrainPress.current = BrainPress.current || {};

