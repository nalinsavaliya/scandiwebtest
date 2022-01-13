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
        $('.loader').show();
        $.ajax({
            url: window.baseUrl + 'src/Products/GetAttributes.php',
            type: "POST",
            data: {type_id: $(this).val()},
            dataType: "json",
            success: function(result){
                let attrHtml = '';
                let attributeLength = result.length;
                $.each(result, function( index, value ) {
                    debugger;
                    attrHtml += "<label for='"+ value.code +"'>"+ value.label +":</label><br>";
                    attrHtml += "<input class='require number' type='"+ value.type +"' id='"+ value.code +"' name='attribute["+ value.id +"]' value='' ><br>";
                    if(attributeLength == index + 1) {
                        attrHtml += "<p>" + value.message + "</p><br>";
                    }
                });

                $('.product-type').html(attrHtml);
                $('.product-type').removeClass("no-display")
                setTimeout(function () {
                    $('.loader').hide();
                },1000)
            }
        });
    });

    //validate product add page
    $.validator.addClassRules('require', {
        required: true
    });
    $.validator.addClassRules('number', {
        number : true,
        min: 0
    });

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
            }
        },

        messages :{
            price: {
                number: 'Please enter a valid price.'
            }
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