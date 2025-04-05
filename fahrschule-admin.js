jQuery( document ).ready( function( $ ) {
    $( '.fahrschule-antwort-link' ).on( 'click', function( e ) {
    e.preventDefault();
    var eintrag_id = $( this ).data( 'eintrag-id' );
    var antwort = prompt( 'Antwort auf Eintrag ' + eintrag_id + ':', '' );
    if ( antwort != null ) {
    $.ajax( {
    url: fahrschule_admin_params.ajax_url,
    type: 'POST',
    data: {
    action: 'fahrschule_antwort',
    eintrag_id: eintrag_id,
    antwort: antwort,
    fahrschule_admin_key: fahrschule_admin_params.fahrschule_admin_nonce
    },
    success: function( response ) {
    alert( 'Die Antwort wurde erfolgreich gespeichert.' );
    }
    } );
    }
    } );
    } );
