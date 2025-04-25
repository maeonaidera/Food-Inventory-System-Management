<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  if ($session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>
<?php
 $c_categorie     = count_by_id('categories');
 $c_product       = count_by_id('products');
 $c_sale          = count_by_id('sales');
 $c_user          = count_by_id('users');
 $products_sold   = find_higest_saleing_product('10');
 $recent_products = find_recent_product_added('5');
 $recent_sales    = find_recent_sale_added('5')
?>
<?php include_once('layouts/header.php'); ?>
<div class="row" style="margin-top: 30px;">
 <div class="col-md-12">
    <div class="panel" >
      <div class="jumbotron text-center">
         <h1>Welcome User <hr> Inventory Management System</h1>
         <p>Browes around to find out the pages that you can access!</p>
      </div>
    </div>
 </div>
	
	<a href="sales.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-green">
          <i class="glyphicon glyphicon-stats"></i>
        </div>
        <div class="panel-value pull-right">
    <h2 class="margin-top">
    <?php 
      if ($c_sale['total'] == 8) {
        echo '8';
      } else {
        echo '₱' . $c_sale['total'];
      }
    ?>
    </h2>
              <p class="text-muted">Sales</p>
            </div>
          </div>
        </div>
      </a>
</div>
<?php include_once('layouts/footer.php'); ?>
