jQuery(document).ready(function($) {

    var data = {
    	action: 'pito_svc_ajax',
    	current_page: pito_svs_ajax_vars.current_page
    };

    $.post( pito_svs_ajax_vars.ajax_url, data, function( response ) {
    	$( '.pito_svc_widget span.svc_number' ).each( function() {
    		var $this = $( this );
            var number = parseInt( $this.html() ) || 0;

    		$this.html( parseInt( number + 1 ) );
    	});
    });
});