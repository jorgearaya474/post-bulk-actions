/**
 * Bulk Tab Action Handler for PBA Plugin.
 *
 * @package PBA
 */

jQuery( document ).ready( function ( $ ) {
	// Get URL params.
	const urlParams = new URLSearchParams( window.location.search );
	const action = urlParams.get( 'pba_action' );

	if ( ! action ) {
		return;
	}

	let blockedCounter = 0;

	try {
		if ( action === 'bulk-edit-tabs' ) {
			handleBulkEditTabs( urlParams );
		}

		if ( action === 'bulk-view-tabs' ) {
			handleBulkViewTabs( urlParams );
		}
	} catch ( err ) {
		console.error( 'Error processing the bulk action: ', err );
	} finally {
		// Show blocked alert after a delay to allow popups to load.
		setTimeout(
			function () {
				showBlockedAlert( blockedCounter );
			},
			1000
		);

		cleanUrlParams();
	}

	/**
	 * Handle bulk edit tabs action.
	 */
	function handleBulkEditTabs( urlParams ) {
		const postIds = urlParams.get( 'pba_post_ids' );

		if ( ! postIds ) {
			throw new Error( 'No IDs found' );
		}

		const ids = postIds.split( ',' ).filter(
			function ( id ) {
				return id.trim();
			}
		);

		if ( ids.length === 0 ) {
			throw new Error( 'No valid IDs to process' );
		}

		if ( ids.length > 3 ) {
			const confirmation = confirm(
				'Are you sure you want to open ' + ids.length + ' edit tabs?'
			);

			if ( ! confirmation ) {
				return;
			}
		}

		ids.forEach(
			function ( postId, index ) {
				if ( ! postId.trim() || isNaN( postId ) ) {
					console.warn( 'Invalid post ID: ', postId );
					return;
				}

				const url = pba_ajax.edit_url.replace( '%d', postId.trim() );

				setTimeout(
					function () {
						const newWindow = window.open( url, '_blank' );
						checkPopupBlocked( newWindow, url );
					},
					index * 150
				);
			}
		);
	}

	/**
	 * Handle bulk view tabs action.
	 */
	function handleBulkViewTabs( urlParams ) {
		const postUrls = urlParams.get( 'pba_post_urls' );

		if ( ! postUrls ) {
			throw new Error( 'No URLs found' );
		}

		const urls = postUrls.split( ',' ).filter(
			function ( url ) {
				return url.trim();
			}
		);

		if ( urls.length === 0 ) {
			throw new Error( 'No valid URLs to be processed' );
		}

		if ( urls.length > 3 ) {
			const confirmation = confirm(
				'Are you sure you want to open ' + urls.length + ' view tabs?'
			);

			if ( ! confirmation ) {
				return;
			}
		}

		urls.forEach(
			function ( url, index ) {
				const cleanUrl = url.trim();

				if ( ! isValidUrl( cleanUrl ) ) {
					console.warn( 'Invalid URL: ', cleanUrl );
					return;
				}

				setTimeout(
					function () {
						const newWindow = window.open( cleanUrl, '_blank' );
						checkPopupBlocked( newWindow, cleanUrl );
					},
					index * 150
				);
			}
		);
	}

	/**
	 * Validate URL format.
	 */
	function isValidUrl( url ) {
		try {
			const urlObj = new URL( url );

			return (
				urlObj.protocol === 'http:' ||
				urlObj.protocol === 'https:'
			);
		} catch ( e ) {
			return false;
		}
	}

	/**
	 * Check if popup was blocked.
	 */
	function checkPopupBlocked( windowObject, url ) {
		if ( windowObject === null ) {
			blockedCounter++;
			console.warn( 'Blocked tab for: ', url );
			return;
		}

		setTimeout(
			function () {
				try {
					if ( windowObject.closed || windowObject.outerHeight === 0 ) {
						blockedCounter++;
						console.warn( 'Blocked tab for: ', url );
					}
				} catch ( e ) {
					blockedCounter++;
					console.warn( 'Blocked tab for: ', url );
				}
			},
			250
		);
	}

	/**
	 * Show alert if popups were blocked.
	 */
	function showBlockedAlert( blockedCount ) {
		if ( blockedCount > 0 ) {
			const message = blockedCount === 1
				? 'One tab was blocked by your browser. Please allow popups for this site.'
				: blockedCount + ' tabs were blocked by your browser. Please allow popups for this site.';

			alert( message );
		}
	}

	/**
	 * Clean URL parameters.
	 */
	function cleanUrlParams() {
		const cleanSearchParams = new URLSearchParams( window.location.search );
		const keysToDelete = Array.from( cleanSearchParams.keys() ).filter(
			function ( key ) {
				return key.startsWith( 'pba_' );
			}
		);

		keysToDelete.forEach(
			function ( key ) {
				cleanSearchParams.delete( key );
			}
		);

		const newSearch = cleanSearchParams.toString();
		const newUrl = window.location.pathname + ( newSearch ? '?' + newSearch : '' );

		if ( window.history && window.history.replaceState ) {
			window.history.replaceState( {}, document.title, newUrl );
		}
	}
});
