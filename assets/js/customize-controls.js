/*global _:false, wp:false */

(function( $, _, wp, undefined ) {
	'use strict';

	var api = wp.customize;

	$.each({
		'show_on_front': {
			controls: [ 'archive_on_front', 'page_for_posts' ],
			callback: function( to ) { return 'archive' === to; }
		}
	}, function( settingId, o ) {
		api( settingId, function( setting ) {
			$.each( o.controls, function( i, controlId ) {
				api.control( controlId, function( control ) {
					var visibility = function( to ) {
						control.container.toggle( o.callback( to ) );
					};

					visibility( setting.get() );
					setting.bind( visibility );
				});
			});
		});
	});

	// Change previewed URL to the homepage when changing the archive_on_front.
	api( 'show_on_front', 'archive_on_front', function( showOnFront, archiveOnFront ) {
		var updatePreviewUrl = function() {
			if ( showOnFront() === 'archive' && showOnFront() !== '' ) {
				api.previewer.previewUrl.set( api.settings.url.home );
			}
		};
		showOnFront.bind( updatePreviewUrl );
		archiveOnFront.bind( updatePreviewUrl );
	});

})( jQuery, _, wp );
