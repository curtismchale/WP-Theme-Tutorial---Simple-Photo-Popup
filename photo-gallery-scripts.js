jQuery( document ).ready( function( $ ){

    $( '.thwp_photo_gallery a' ).click(function (e){

        // stop the link from doing anything
        e.preventDefault();

        // get the ID of the post we want to get the video for
        var id = $( this ).attr('id');

        // Ajax goodness
        $.post( THWPPhotoAjax.ajaxurl, { action: 'get_photo', ID: id }, function ( response ){

            // call fancybox with the ajax content
            $.fancybox({
                content: response
            });

        } );

    });

});
