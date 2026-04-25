( function ( $ ) {
	'use strict';

	$( function () {
		var frame;
		var $attachmentId = $( '#rpl_pdf_attachment_id' );
		var $status = $( '#rpl_pdf_status' );
		var $remove = $( '#rpl_remove_pdf' );
		var escapeHtml = function ( value ) {
			return $( '<div>' ).text( value ).html();
		};

		$( '#rpl_select_pdf' ).on( 'click', function ( event ) {
			event.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: rplAdminMedia.title,
				button: {
					text: rplAdminMedia.button
				},
				library: {
					type: 'application/pdf'
				},
				multiple: false
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var title = attachment.title || attachment.filename || attachment.url;

				$attachmentId.val( attachment.id );
				$status.html(
					'<strong>' + rplAdminMedia.selectedText + '</strong> ' +
					'<a href="' + encodeURI( attachment.url ) + '" target="_blank" rel="noopener">' + escapeHtml( title ) + '</a>'
				);
				$remove.prop( 'disabled', false );
			} );

			frame.open();
		} );

		$remove.on( 'click', function ( event ) {
			event.preventDefault();
			$attachmentId.val( '' );
			$status.text( rplAdminMedia.emptyText );
			$remove.prop( 'disabled', true );
		} );
	} );
}( jQuery ) );
