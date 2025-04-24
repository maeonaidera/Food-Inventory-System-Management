<?php
$page_title = 'Add Sale';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(3);
?>

<?php
if (isset($_POST['add_sale'])) {
    $req_fields = array('s_id', 'quantity', 'price', 'total', 'date');
    validate_fields($req_fields);
    if (empty($errors)) {
        $p_id = $db->escape((int)$_POST['s_id']);
        $s_qty = $db->escape((int)$_POST['quantity']);
        $s_total = $db->escape($_POST['total']);
        $date = $db->escape($_POST['date']);
        $s_date = make_date();

        $sql  = "INSERT INTO sales (";
        $sql .= " product_id, qty, price, date";
        $sql .= ") VALUES (";
        $sql .= "'{$p_id}', '{$s_qty}', '{$s_total}', '{$s_date}'";
        $sql .= ")";

        if ($db->query($sql)) {
            update_product_qty($s_qty, $p_id);
            $session->msg('s', "Sale added.");
            redirect('add_sale.php', false);
        } else {
            $session->msg('d', ' Sorry failed to add!');
            redirect('add_sale.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_sale.php', false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
        <form method="post" action="ajax.php" autocomplete="off" id="sug-form">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Find It</button>
                    </span>
                    <input type="text" id="sug_input" class="form-control" name="title" placeholder="Search for product name">
                </div>
                <div id="result" class="list-group"></div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Sale Edit</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_sale.php">
                    <table class="table table-bordered">
                        <thead>
                            <th> Item </th>
                            <th> Price </th>
                            <th> Qty </th>
                            <th> Total </th>
                            <th> Date </th>
                            <th> Action </th>
                        </thead>
                        <tbody id="product_info">
                            <tr>
                                <td>
                                    <input type="text" class="form-control" name="item" placeholder="Item Name">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="price" placeholder="₱0.00">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="quantity" placeholder="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="total" id="total" placeholder="₱0.00" readonly>
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="date">
                                </td>
                                <td>
                                    <button type="submit" name="add_sale" class="btn btn-primary">Add Sale</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.querySelector('input[name="price"]');
    const quantityInput = document.querySelector('input[name="quantity"]');
    const totalInput = document.getElementById('total');

    function calculateTotal() {
        const price = parseFloat(priceInput.value.replace('₱', '').replace(',', '')) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = price * quantity;
        totalInput.value = '₱' + total.toFixed(2);
    }

    priceInput.addEventListener('input', calculateTotal);
    quantityInput.addEventListener('input', calculateTotal);
});
</script>

<?php include_once('layouts/footer.php'); ?>
