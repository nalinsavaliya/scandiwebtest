<?php
$productList = new \App\Products\ProductList();
$products = $productList->getProducts();
$productTypes = $productList->getProductTypes();

?>
<body>
<div class="loader no-display">
    <img src="./assets/images/loader.gif">
</div>

<div class="container">
    <!--Product Header-->
    <div class="product-list-header">
        <div class="left-side header">Product List</div>
        <div class="middle header">
            <button id="addproduct" class="product-list">ADD</button>
        </div>
        <div class="right-side header">
            <div class="mass-action">
                <select name="mass_action" id="mass_action" class="product-list">
                    <option value="delete">Mass delete action</option>
                </select>
                <div class="action">
                    <button id="apply" class="product-list">MASS DELETE</button>
                    <button id="save_product" class="no-display product-add">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div id="message">
        <span></span>
    </div>
    <!--End Product Header-->

    <!--Product List Section-->
    <div class="product-list-container product-list">
        <ul>
            <?php foreach ($products as $product) : ?>
                <li class="product-container" id="product_container_<?php echo $product['product_id']; ?>">
                    <input type="checkbox" name="product[]" class="delete-checkbox" id="product_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" value="<?php echo $product['product_id']; ?>">

                    <div class="product-attr"><?php echo $product['sku']; ?></div>
                    <div class="product-attr"><?php echo $product['name']; ?></div>
                    <div class="product-attr"><?php echo $product['price']; ?> $</div>

                    <?php $attributes =  $productList->getAttributeByProductId($product['product_id']); ?>
                    <div class="product-attr"><?php echo $productList->attributeDisplay($attributes); ?></div>

                </li>
            <?php endforeach; ?>
        </ul>

        <div class="no_item no-display <?php echo (!count($products)) ? 'info' : '' ?>">
            <span>We can't find products matching the selection.</span>
        </div>
    </div>
    <!--End Product List Section-->

    <!--Product Add Section-->
    <div class="product-add-container no-display product-add">
        <form action="#" id="product_form">

            <label for="sku">Sku:</label><br>
            <input type="text" id="sku" name="sku" value=""><br>

            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" value=""><br>

            <label for="price">Price:</label><br>
            <input type="text" id="price" name="price" value=""><br>

            <label for="price">Type Switcher:</label><br>
            <select name="type" id="productType">
                <option value="">Type Switcher</option>
                <?php foreach($productTypes as $pType){ ?>
                    <option value="<?php echo $pType[$productList::PRODUCT_TYPE_ID]; ?>"><?php echo $pType[$productList::PRODUCT_TYPE_TYPE]; ?></option>
                <?php } ?>
            </select>
            <br>

            <div class="product-type">
            </div>

        </form>
    </div>
    <!--End Product Add Section-->
</div>
</body>
<script>
    window.baseUrl = '<?php echo $productList->getBaseUrl() ?>'
</script>