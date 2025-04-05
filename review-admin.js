jQuery( document ).ready( function( $ ) {
    $( '.review-respond-link' ).on( 'click', function( e ) {
    e.preventDefault();
    var entry_id = $( this ).data( 'entry-id' );
    var response = prompt( 'Response to entry ' + entry_id + ':', '' );
    if ( response != null ) {
    $.ajax( {
    url: review_admin_params.ajax_url,
    type: 'POST',
    data: {
    action: 'review_respond',
    entry_id: entry_id,
    response: response,
    review_admin_key: review_admin_params.review_admin_nonce
    },
    success: function( response ) {
    alert( 'The response has been successfully saved.' );
    }
    } );
    }
    } );
    } ); 