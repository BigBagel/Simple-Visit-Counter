jQuery(document).ready(function($) {

    var data = {
    	action: 'pito_svc_ajax',
    	current_page: pito_svs_ajax_vars.current_page,
        admin: pito_svs_ajax_vars.admin,
        logged_in: pito_svs_ajax_vars.logged_in
    };

    $.post( pito_svs_ajax_vars.ajax_url, data, function( response ) {

        if ( response.success ) {
            $( '.pito_svc_widget span.svc_number.count' ).html( response.stats.count );
            $( '.pito_svc_widget span.svc_number.count_unique' ).html( response.stats.count_unique );
            $( '.pito_svc_widget span.svc_number.count_today' ).html( response.stats.count_today );
            $( '.pito_svc_widget span.svc_number.count_today_unique' ).html( response.stats.count_today_unique );

            if ( typeof response.single_stats != "undefined" ) {
                $( '.pito_svc_widget span.svc_number.single_count' ).html( response.single_stats.count );
                $( '.pito_svc_widget span.svc_number.single_count_today' ).html( response.single_stats.count_today );
            }
        }

    });
});