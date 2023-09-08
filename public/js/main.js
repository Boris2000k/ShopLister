$(document).ready(function() {
    $('.delete').on('click', function(e){
        return confirm('Are you sure?');
    });
    $('.copy-cart').on('click', function(e){
        return confirm('Are you sure you want to repeat this cart?');
    });

    // store active cart in cookie
    $('.active-cart-dd').on('change', function(){
        document.cookie = `selected_cart_id=${$(this).val()};path=/`
    })
    init();
});

this.init = function(){
    var price = null;
    var product_id = null;
    var cart_id = null;
    var product_name = null;
    var store_id = null;

    $('.quantity-field').on('input', function(){
        // calculate price in modal
        let amount = $(this).val();
        $('.item-price').text(`Price: ${Math.round((amount * price + Number.EPSILON) * 100) / 100} $`);
    });
    // init amount product input
    function incrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
        if (!isNaN(currentVal)) {
            parent.find('input[name=' + fieldName + ']').val(currentVal + 1);
        } else {
            parent.find('input[name=' + fieldName + ']').val(0);
        }
    }
    function decrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
        if (!isNaN(currentVal) && currentVal > 0) {
            parent.find('input[name=' + fieldName + ']').val(currentVal - 1);
        } else {
            parent.find('input[name=' + fieldName + ']').val(0);
        }
    }
    $('.input-group').on('click', '.button-plus', function(e) {
        incrementValue(e);
        $('.quantity-field').trigger('input');
    });
    $('.input-group').on('click', '.button-minus', function(e) {
        decrementValue(e);
        $('.quantity-field').trigger('input');
    });

    $('.add-to-cart').on('click', function(e){
        $('.response').text("");
        $('.add-product').removeClass('hidden');
        $('.add-product-buttons').removeClass('hidden');
        product_id = $(this).data('product_id')
        cart_id = $('.active-cart-dd').find(":selected").val();
        product_name = $(this).data('product_name');
        price = $(this).data('product_price');
        store_id = $(this).data('store_id');
        $('.quantity-field').val(1);
        $('.modal-product-name').text(product_name);
        $('.item-price').text(`Price: ${price} $`);

        $("#add-to-cart-modal").modal().open();
    });

    $('.add-to-cart-confirmed').on('click', function(){
        $.ajax({
            url: "/add-to-cart",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "store_id" : store_id,
                "cart_id" : cart_id,
                "product_id" : product_id,
                "amount" : $('.quantity-field').val(),
            },
            success:function(data){
                console.log(data['error'])
                var response = "";
                $('.add-product').addClass('hidden');
                $('.add-product-buttons').addClass('hidden');
                if(typeof(data['success']) != 'undefined'){
                    response = `<h3 class="text-success">${data['success']}</h3>`
                } else {
                    response = `<h3 class="text-danger">${data['error']}</h3>`
                }
                $('.response').append(response)
            },
        })
    })
}
