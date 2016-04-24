/*global _:false, wp:false */

(function( $, _, wp, undefined ) {
	'use strict';

	var api = wp.customize;

	$.each({
		'show_on_front': {
			controls: [ 'page_for_posts', 'archive_on_front' ],
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

})( jQuery, _, wp );
