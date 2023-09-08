$(document).ready(function(){
    $('.confirm-buy').on('click', function(){
        if(confirm('Are you sure?')){
            let product_id = $(this).data('product_id');
            resolveAction('buy', product_id);
        }
    });

    $('.confirm-forget').on('click', function(){
        if(confirm('Are you sure?')){
            let product_id = $(this).data('product_id');
            resolveAction('reset', product_id);
        }
    });

    $('.confirm-delete').on('click', function(){
        if(confirm('Are you sure?')){
            let product_id = $(this).data('product_id');
            resolveAction('delete', product_id);
        }
    });
})

this.resolveAction = function(action, product_id){
    $.ajax({
        url     : "/shopping-carts/resolve-action",
        type    : "GET",
        headers : {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        },
        data    : {
            "cart_id"    : cart_id,
            "product_id" : product_id,
            "action"     : action,
        },
        success : function(response){
            if(typeof(response['success'] != 'undefined')){
                var element = $(`[data-row_product_id="${product_id}"]`);
                switch(action){
                    case 'buy':
                        if(!element.hasClass('item-complete')){
                            element.removeClass('item-pending').addClass('item-complete');
                        }
                        break;
                    case 'reset':
                        if(!element.hasClass('item-pending')){
                            element.removeClass('item-complete').addClass('item-pending');
                        }
                        break;
                    case 'delete':
                        element.remove();
                        break;
                }
            }
        }
    });
}
