jQuery(document).ready(function($) {
    var data = {
    	action: 'pito_svc_ajax',
    	current_page: pito_svs_ajax.current_page
    };

    $.post( pito_svs_ajax.ajax_url, data, function( response ) {
    	// Nothing
    });
});