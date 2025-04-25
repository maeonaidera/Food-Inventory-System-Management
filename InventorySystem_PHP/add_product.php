<?php
$page_title = 'Add Product';
require_once('includes/load.php');
page_require_level(2);
$all_categories = find_all('categories');

if (isset($_POST['add_product'])) {
    require_once('includes/upload.php');
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'saleing-price');
    validate_fields($req_fields);

    if (empty($errors)) {
        $p_name  = remove_junk($db->escape($_POST['product-title']));
        $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
        $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
        $p_buy   = remove_junk($db->escape($_POST['buying-price']));
        $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
        $date    = make_date();
        $media = new Media();
        $media_id = 0; // Default media_id for no image

        // Handle image upload
        if (!empty($_FILES["product-photo"]["name"])) {
            if ($media->upload($_FILES["product-photo"])) {
                // Generate a unique id for filename uniqueness
                $unique_id = time() . rand(1000, 9999);
                if ($media->process_media($unique_id)) {
                    // Get last inserted media id
                    $media_id = $db->insert_id();
                } else {
                    $session->msg("d", join(' ', $media->errors));
                    redirect('add_product.php', false);
                }
            } else {
                $session->msg("d", join(' ', $media->errors));
                redirect('add_product.php', false);
            }
        }

        // Insert product
        $query = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, date, media_id) ";
        $query .= "VALUES ('{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$date}', '{$media_id}')";

        if ($db->query($query)) {
            $session->msg('s', "Product added successfully!");
            redirect('product.php', false);
        } else {
            $session->msg('d', 'Failed to add product!');
            redirect('add_product.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_product.php', false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Product</span>
                </strong>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form method="post" action="add_product.php" enctype="multipart/form-data" class="clearfix">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th-large"></i>
                                </span>
                                <input type="text" class="form-control" name="product-title" placeholder="Product Title" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" name="product-categorie" required>
                                        <option value="">Select Product Category</option>
                                        <?php foreach ($all_categories as $cat): ?>
                                            <option value="<?php echo (int)$cat['id'] ?>">
                                                <?php echo $cat['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Upload Product Photo</label>
                                    <input type="file" name="product-photo" class="form-control" id="product-photo-input" accept="image/*">
                                    <img id="product-photo-preview" src="uploads/products/no_image.png" alt="Product Photo Preview" style="margin-top:10px; max-width: 200px; max-height: 200px; display: block;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-shopping-cart"></i>
                                        </span>
                                        <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label >₱</label>
                                        </span>
                                        <input type="number" class="form-control" name="buying-price" placeholder="₱0.00" required>
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                        <label >₱</label>
                                        </span>
                                        <input type="number" class="form-control" name="saleing-price" placeholder="₱0.00" required>
                                        <span class="input-group-addon">.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="add_product" class="btn btn-danger">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script>
document.getElementById('product-photo-input').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('product-photo-preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
    } else {
        preview.src = 'uploads/products/no_image.png';
    }
});
</script>