/**
 * @package PublishPress
 * @author PressShack
 *
 * Copyright (c) 2017 PressShack
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */

( function ( $, window, document, counter ) {
	"use strict";

	/*----------  Handler  ----------*/

	/**
	 * Object for handling the requirements in the post form.
	 * @type {Object}
	 */
	var PP_Content_Checklist = {
		/**
		 * Constant for the event validate_requirements
		 * @type {String}
		 */
		EVENT_VALIDATE_REQUIREMENTS: 'pp-content-checklist:validate_requirements',

		/**
		 * Constant for the event tick. Trigered by a setInterval
		 * @type {String}
		 */
		EVENT_TIC: 'pp-content-checklist:tic',

		/**
		 * Constant for the event update_requirement_state
		 * @type {String}
		 */
		EVENT_UPDATE_REQUIREMENT_STATE: 'pp-content-checklist:update_requirement_state',

		/**
		 * Constat for the event toggle_custom_item
		 * @type {String}
		 */
		EVENT_TOGGLE_CUSTOM_ITEM: 'pp-content-checklist:toggle_custom_item',

		/**
		 * Constant for the interval of the tic event
		 * @type {Number}
		 */
		TIC_INTERVAL: 500,

		/**
		 * List of interface elements
		 * @type {Object}
		 */
		elems: {
			'original_post_status': $( '#original_post_status' ),
			'publish_button'      : $( '#publish' ),
			'document'            : $( document ),
		},

		/**
		 * Stores the states for the object.
		 * @type {Object}
		 */
		state: {
			/**
			 * Flag for the publishing state
			 * @type {Boolean}
			 */
			is_publishing: false,

			/**
			 * Flag for the confirmed state
			 * @type {Boolean}
			 */
			is_confirmed: false,

			/**
			 * Flag for the should_block state
			 * @type {Boolean}
			 */
			should_block: false,
		},

		/**
		 * Initialize the object and events
		 * @return {void}
		 */
		init: function() {
			// Create a custom event
			this.elems.document.on( this.EVENT_VALIDATE_REQUIREMENTS, function( event ) {
				this.validate_requirements( event );
			}.bind( this ) );

			// On clicking the submit button
			this.elems.publish_button.click( function() {
				this.state.is_publishing = true;
			}.bind( this ) );

			// On clicking the confimation button in the modal window
			this.elems.document.on( 'confirmation', '.remodal', function() {
				this.state.is_confirmed = true;

				// Trigger the publish button
				this.elems.publish_button.trigger( 'click' );
			}.bind( this ) );

			// Hook to the submit button
			$( 'form#post' ).submit( function( event ) {
				// Reset the should_block state
				this.state.should_block = false;

				this.elems.document.trigger( this.EVENT_VALIDATE_REQUIREMENTS );

				return ! this.state.should_block;
			}.bind( this ) );

			// Hook to the requirement items
			$( '[id^=pp-checklist-req]' ).on( this.EVENT_UPDATE_REQUIREMENT_STATE, function( event, state ) {
				this.update_requirement_icon( state, $( event.target ) );
			}.bind( this ) );

			// Add event to the custom items
			$( '.pp-checklist-custom-item' ).click( function( event ) {
				var target = event.target;

				if ( 'LI' !== target.nodeNAME ) {
					target = $( target ).parent( 'li' )[0];
				}

				if ( typeof target !== 'undefined' ) {
					this.elems.document.trigger( this.EVENT_TOGGLE_CUSTOM_ITEM, $( target ) );
				}
			}.bind( this ) );

			// On clicking the confimation button in the modal window
			this.elems.document.on( this.EVENT_TOGGLE_CUSTOM_ITEM, function( event, item ) {
				var $item   = $( item ),
					$icon   = $item.children( '.dashicons' ),
					checked = $icon.hasClass( 'dashicons-yes' );

				if ( checked ) {
					$icon
						.removeClass( 'dashicons-yes' )
						.addClass( 'dashicons-no' );

					$item
						.removeClass( 'status-yes' )
						.addClass( 'status-no' );
				} else {
					$icon
						.removeClass( 'dashicons-no' )
						.addClass( 'dashicons-yes' );

					$item
						.removeClass( 'status-no' )
						.addClass( 'status-yes' );
				}

				$item.children( 'input[type="hidden"]' ).val( $item.hasClass( 'status-yes' ) ? 'yes' : 'no' );
			}.bind( this ) );

			// Start the tic event
			setInterval( function() {
				$( document ).trigger( this.EVENT_TIC );
			}.bind( this ), this.TIC_INTERVAL );
		},

		/**
		 * Check if the current post is already published
		 * @return {Boolean} True if published.
		 */
		is_published: function() {
			return 'publish' === this.elems.original_post_status.val();
		},

		/**
		 * Validates the requirements and show the warning, blocking or not the
		 * submission, according to the config. Returns false if the submission
		 * should be blocked.
		 *
		 * @param  {[type]} event
		 * @return {Boolean}
		 */
		validate_requirements: function( event ) {
			this.state.should_block = false;

			// Bypass all checks because the confirmation button was clicked.
			if ( this.state.is_confirmed ) {
				this.state.is_confirmed = false;

				return;
			}

			// Check if the post is already published, to bypass the check
			if ( this.is_published() ) {
				return;
			}

			var list_unchecked = {
					'block'   : [],
					'warning' : [],
				};

			/**
			 * Check the element of the requirement, to see if it is marked
			 * as incomplete.
			 *
			 * @param  {Object} $req The DOM element
			 * @param  {Array}  list The list to inject the requirement, if incomplete
			 * @return {void}
			 */
			var check_requirement = function( $req, list ) {
				if ( this.state.is_publishing && $req.hasClass( 'status-no' ) ) {
					// Check if the requirement is not ok
					var $unchecked_req = $req.find( '.status-label' );

					if ( $unchecked_req.length > 0 ) {
						list.push( $unchecked_req.html().trim() );
					}
				}
			}.bind( this );

			var check_requirement_action = function( action_type ) {
				var $elems = $( '.pp-checklist-req.' + action_type ),
					$unchecked_label,
					$req;

				for ( var i = 0; i < $elems.length; i++ ) {
					check_requirement( $( $elems[ i ] ), list_unchecked[ action_type ] );
				}
			}.bind( this );

			// Check if any of the requirements is set to trigger warnings
			check_requirement_action( 'warning' );
			check_requirement_action( 'block' );

			// Check if we have warnings to display
			if ( list_unchecked.warning.length > 0 || list_unchecked.block.length > 0 ) {
				var message = '';

				// Check if we don't have any unchecked block req
				if ( 0 === list_unchecked.block.length ) {
					// Only display a warning
					message = objectL10n_checklist_requirements.msg_missed_optional + '<div class="pp-checklist-modal-list"><ul><li>' + list_unchecked.warning.join( '</li><li>' ) + '</li></ul></div>';

					// Display the confirm
					$( '#pp-checklist-modal-confirm-content' ).html( message );
					$( '[data-remodal-id=pp-checklist-modal-confirm]' ).remodal().open();
				} else {
					message = objectL10n_checklist_requirements.msg_missed_required + '<div class="pp-checklist-modal-list"><ul><li>' + list_unchecked.block.join( '</li><li>' ) + '</li></ul></div>';

					if ( list_unchecked.warning.length > 0 ) {
						message += '' + objectL10n_checklist_requirements.msg_missed_important + '<div class="pp-checklist-modal-list"><ul><li>' + list_unchecked.warning.join( '</li><li>' ) + '</li></ul></div>';
					}

					// Display the alert
					$( '#pp-checklist-modal-alert-content' ).html( message );
					$( '[data-remodal-id=pp-checklist-modal-alert]' ).remodal().open();
				}

				this.state.should_block = true;
			}

			this.state.is_publishing = false;
		},

		/**
		 * Updates the icon in the requirement checklist according to the
		 * current state.
		 *
		 * @param  {Boolean} is_completed
		 * @param  {Object}  $element
		 * @return {void}
		 */
		update_requirement_icon: function( is_completed, $element ) {
			var $icon_element = $element.find( '.dashicons' )

			if ( is_completed ) {
				// Ok
				$icon_element.removeClass( 'dashicons-no' );
				$icon_element.addClass( 'dashicons-yes' );
				$icon_element.parent().removeClass( 'status-no' );
				$icon_element.parent().addClass( 'status-yes' );
			} else {
				// Not ok
				$icon_element.removeClass( 'dashicons-yes' );
				$icon_element.addClass( 'dashicons-no' );
				$icon_element.parent().removeClass( 'status-yes' );
				$icon_element.parent().addClass( 'status-no' );
			}
		},

		/**
		 * Check if the value is valid, based on the min and max values.
		 * It makes a smarty check based on the following:
		 *
		 *  - Both same value = exact
		 *  - Min not empty, max empty or < min = only min
		 *  - Min not empty, max not empty and > min = both min and max
		 *  - Min empty, max not empty and > min = only max
		 *
		 * @param  {Float} count
		 * @param  {Float} min_value
		 * @param  {Float} max_value
		 *
		 * @return {Bool}
		 */
		check_valid_quantity: function( count, min_value, max_value ) {
			var is_valid = false;

			// Both same value = exact
			if ( min_value === max_value ) {
				is_valid = count === min_value;
			}

			// Min not empty, max empty or < min = only min
			if ( min_value > 0 && max_value < min_value ) {
				is_valid = count >= min_value;
			}

			// Min not empty, max not empty and > min = both min and max
			if ( min_value > 0 && max_value > min_value ) {
				is_valid = count >= min_value && count <= max_value;
			}

			// Min empty, max not empty and > min = only max
			if ( min_value === 0 && max_value > min_value ) {
				is_valid = count <= max_value;
			}

			return is_valid;
		},
	};

	// Exposes and initialize the object
	window.PP_Content_Checklist = PP_Content_Checklist;
	PP_Content_Checklist.init();


	/*----------  Warning icon in submit button  ----------*/

	// Show warning icon close to the submit button
	if ( objectL10n_checklist_requirements.show_warning_icon_submit ) {
		var $icon = $( '<span>' )
			.addClass( 'dashicons dashicons-warning pp-checklist-warning-icon' )
			.hide()
			.prependTo( $( '#publishing-action' ) )
			.attr( 'title', objectL10n_checklist_requirements.title_warning_icon );

		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var has_uncheked = $( '#pp-checklist-req-box' ).children( '.status-no' );

			if ( has_uncheked.length > 0 ) {
				// Not ok
				$icon.show();
			} else {
				// Ok
				$icon.hide();
			}
		} );
	}

	/*----------  Hide submit button  ----------*/

	// Hide the submit button
	if ( objectL10n_checklist_requirements.hide_publish_button ) {
		var $button = $('#publish');

		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var has_uncheked = $( '#pp-checklist-req-box' ).children( '.status-no' );

			if ( has_uncheked.length > 0 ) {
				// Not ok
				$button.hide();
			} else {
				// Ok
				$button.show();
			}
		} );
	}

	/*----------  Featured Image  ----------*/

	if ( $( '#pp-checklist-req-featured_image' ).length > 0 ) {
		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var has_image = $( '#postimagediv' ).find( '#set-post-thumbnail' ).find( 'img' ).length > 0;

			$( '#pp-checklist-req-featured_image' ).trigger(
				PP_Content_Checklist.EVENT_UPDATE_REQUIREMENT_STATE,
				has_image
			);
		} );
	}

	/*---------- Tags Number  ----------*/

	if ( $( '#pp-checklist-req-tags_count' ).length > 0 ) {
		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var count = $( '.tagchecklist' ).children( 'span' ).length,
				min_value = parseInt( objectL10n_checklist_requirements.requirements.tags_count.value[0] ),
				max_value = parseInt( objectL10n_checklist_requirements.requirements.tags_count.value[1] );

			$( '#pp-checklist-req-tags_count' ).trigger(
				PP_Content_Checklist.EVENT_UPDATE_REQUIREMENT_STATE,
				PP_Content_Checklist.check_valid_quantity( count, min_value, max_value )
			);
		} );
	}

	/*----------  Categories Number  ----------*/

	if ( $( '#pp-checklist-req-categories_count' ).length > 0 ) {
		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var count = $( '.categorychecklist input:checked' ).length,
				min_value = parseInt( objectL10n_checklist_requirements.requirements.categories_count.value[0] ),
				max_value = parseInt( objectL10n_checklist_requirements.requirements.categories_count.value[1] );

			$( '#pp-checklist-req-categories_count' ).trigger(
				PP_Content_Checklist.EVENT_UPDATE_REQUIREMENT_STATE,
				PP_Content_Checklist.check_valid_quantity( count, min_value, max_value )
			);
		} );
	}

	/*----------  Filled in Excerpt  ----------*/

	if ( $( '#pp-checklist-req-filled_excerpt' ).length > 0 ) {
		$( document ).on( PP_Content_Checklist.EVENT_TIC, function( event ) {
			var has_excerpt = false;

			if ( $( '#excerpt' ).val().trim().length > 0 ) {
				has_excerpt = true;
			}

			$( '#pp-checklist-req-filled_excerpt' ).trigger(
				PP_Content_Checklist.EVENT_UPDATE_REQUIREMENT_STATE,
				has_excerpt
			);
		} );
	}

} )( jQuery, window, document, new wp.utils.WordCounter() );
