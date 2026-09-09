/**
 * Admin standard tab handler — vanilla JS (no jQuery dependency).
 *
 * Replicates the tab switching behaviour previously provided by jQuery UI Tabs.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	'use strict';

	var tabNav = document.querySelector( '.azrcrv-ui-tabs-nav' );

	if ( ! tabNav ) {
		return;
	}

	var tabItems   = tabNav.querySelectorAll( 'li' );
	var tabAnchors = tabNav.querySelectorAll( 'a.azrcrv-ui-tabs-anchor' );

	/**
	 * Activate a tab by its anchor element.
	 *
	 * @param {HTMLElement} anchor The anchor element of the tab to activate.
	 */
	function activateTab( anchor ) {
		var targetId = anchor.getAttribute( 'href' ).replace( '#', '' );

		// Update all tab list items.
		tabItems.forEach( function ( li ) {
			var liAnchor = li.querySelector( 'a.azrcrv-ui-tabs-anchor' );
			if ( liAnchor === anchor ) {
				li.classList.add( 'azrcrv-ui-state-active' );
				li.setAttribute( 'aria-selected', 'true' );
				li.setAttribute( 'aria-expanded', 'true' );
			} else {
				li.classList.remove( 'azrcrv-ui-state-active' );
				li.setAttribute( 'aria-selected', 'false' );
				li.setAttribute( 'aria-expanded', 'false' );
			}
		} );

		// Show/hide tab panels.
		var allPanels = document.querySelectorAll( '.azrcrv-ui-tabs-scroll' );
		allPanels.forEach( function ( panel ) {
			if ( panel.id === targetId ) {
				panel.classList.remove( 'azrcrv-ui-tabs-hidden' );
				panel.setAttribute( 'aria-hidden', 'false' );
			} else {
				panel.classList.add( 'azrcrv-ui-tabs-hidden' );
				panel.setAttribute( 'aria-hidden', 'true' );
			}
		} );
	}

	// Click handler.
	tabAnchors.forEach( function ( anchor ) {
		anchor.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			activateTab( anchor );
		} );

		// Keyboard: Enter key.
		anchor.addEventListener( 'keyup', function ( e ) {
			if ( e.key === 'Enter' || e.keyCode === 13 ) {
				e.preventDefault();
				activateTab( anchor );
			}
		} );
	} );

	// Hover class toggling for tab list items.
	tabItems.forEach( function ( li ) {
		li.addEventListener( 'mouseenter', function () {
			li.classList.add( 'azrcrv-ui-state-hover' );
		} );
		li.addEventListener( 'mouseleave', function () {
			li.classList.remove( 'azrcrv-ui-state-hover' );
		} );
	} );

} );
