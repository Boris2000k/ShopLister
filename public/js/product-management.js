$(document).ready(function(){
    $('#discount, #price').on('input', function(){
        let price = Number($('#price').val() ?? 0);
        let discount = Number($('#discount').val());
        if(price > 0 && discount > 0){
            if(!isNaN(discount) && discount <= 100 && discount > 0 && price > 0){
                console.log('go')
                let new_price = price - ( (price / 100) * discount );
                $('.new-price').text("New Price: $" + new_price.toFixed(2));
            }
        } else {
            $('.new-price').text("");
        }
    });

    // if both price and discount is set, on page load calculate new price
    if(Number($('#price').val()) > 0 && Number($('#discount').val()) > 0){
        $('#discount').trigger('input');
    }
})
//Date range picker init
$('#date-range-picker').daterangepicker({
    locale: {
        format: 'YYYY-MM-DD'
    }
})
