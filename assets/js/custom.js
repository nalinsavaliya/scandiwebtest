$(document).ready(function(){
    // for apply massaction
    $(document).on('click','#apply',function(){
        //Delete items
        $('#message').removeClass();
        $('#message span').html('');

        if($('#mass_action').val() == 'delete'){
           // if not selected product
            if(!$('.delete-checkbox:checked').length){
                $('#message').addClass('error');
                $('#message span').html('Please select product.');
                return;
            }
            let productIds = [];
            $('.delete-checkbox:checked').each(function(i){
                productIds.push($(this).val());
            });
            $('.loader').show();
            $.ajax({
                url: window.baseUrl + 'src/Products/DeleteProduct.php',
                type: "POST",
                data: {'product_ids': productIds.join(',')},
                dataType: "json",
                success: function(result){
                    if(result.success){
                        $(productIds).each(function(i){
                            $('#product_container_' + productIds[i]).remove();
                        });
                        $('#message').addClass('success');
                        if(!$('.product-container').length){
                            $(".no_item").removeClass('no-display');
                            $(".no_item").addClass('info');
                        }
                    }else{
                        $('#message').addClass('error');
                    }

                    $('#message span').html(result.message);
                    setTimeout(function () {
                        $('.loader').hide();
                    },1000)

                }
            });
        }
    });

    //******For add to cart product**/////

    $(document).on('click','#addproduct',function(){
        $('.product-type').addClass("no-display");
        $('#message').removeClass();
        $('#message span').html('');
        $('.product-list').hide();
        $('.product-add').show();

        $('.left-side').html('Product Add');
    });

    // Change attribute on product page
    $(document).on('change','#productType', function() {
        $('.product-type').addClass("no-display");
        $('.' + this.value).removeClass("no-display");
    });

    //validate product add page
    $('#product_form').validate({ // initialize the plugin
        rules: {
            sku: {
                required: true
            },
            name: {
                required: true
            },
            price: {
                required: true,
                number : true,
                min: 0
            },
            type: {
                required: true
            },
            size: {
                required: true,
                number : true,
                min: 0
            },
            weight: {
                required: true,
                number : true,
                min: 0
            },
            height: {
                required: true,
                number : true,
                min: 0
            },
            width: {
                required: true,
                number : true,
                min: 0
            },
            length: {
                required: true,
                number : true,
                min: 0
            }
        },
        messages :{
            price: {
                number: 'Please enter a valid price.'
            },
            size: {
                number: 'Please enter a valid size.'
            },
            weight: {
                number: 'Please enter a valid weight.'
            },
            height: {
                number: 'Please enter a valid height.'
            },
            width: {
                number: 'Please enter a valid width.'
            },
            length: {
                number: 'Please enter a valid length.'
            },
        },
        submitHandler: function (form) {
            $('#message').removeClass();
            $('#message span').html('');
            $('.loader').show();
            $.ajax({
                url: window.baseUrl + 'src/Products/AddProduct.php',
                type: "POST",
                data: $('#product_form').serialize(),
                dataType: "json",
                success: function(result){
                    if(result.success){
                        $("#product_form")[0].reset();
                        $('#message').addClass('success');
                        location.reload();
                    }else{
                        $('#message').addClass('error');
                    }

                    $('#message span').html(result.message);
                    setTimeout(function () {
                        $('.loader').hide();
                    },1000)

                }
            });
        }
    });

    $(document).on('click','#save_product',function(){
        $('#product_form').submit();
    });

});