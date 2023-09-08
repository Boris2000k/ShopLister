$(document).ready(function(){
    $('.product-filter').on('change', function(){
        var product_type = $(this).data('filter_type');
        if($(this).is(':checked')){
            // hide these products
            $(`[data-product_type="${product_type}"]`).removeClass('hidden')
        } else {
            // show these products
            $(`[data-product_type="${product_type}"]`).addClass('hidden')
        }
    });
});
