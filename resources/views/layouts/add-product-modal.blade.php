<!-- The Modal -->
<div class="modal" id="add-to-cart-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add Product To Shopping Cart</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="col-12">
                    <div class="add-product">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="text-dark modal-product-name">Product Name</h3>
                            </div>
                            <div class="input-group w-auto justify-content-end align-items-center">
                                <input type="button" value="-" class="button-minus border rounded-circle icon-shape icon-sm mx-1" data-field="quantity">
                                <input type="number" step="1" max="10" value="1" name="quantity" class="quantity-field border-0 text-center w-25">
                                <input type="button" value="+" class="button-plus border rounded-circle icon-shape icon-sm " data-field="quantity">
                            </div>
                        </div>
                    </div>
                    <div class="response">

                    </div>
                </div>

            </div>

            <!-- Modal footer -->
            <div class="add-product-buttons">
                <div class="modal-footer">
                    <h4 class="text-left text-success item-price">Price: 1234$</h4>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary add-to-cart-confirmed">Confirm</button>
                </div>
            </div>

        </div>
    </div>
</div>
