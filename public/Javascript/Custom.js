$(document).ready(function() {
    var table = $('body').find('#example').DataTable( {
        lengthChange: true
    } );

    $("#input-6").rating().on("rating:change", function(event, value, caption) {
        $('#rating_form_rating').val(value);
    });
    $('.btn, .link_btn').click(function (){
        $('.loading').show();
    });
} );