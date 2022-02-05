<?php
    include_once'db/connect_db.php';
    session_start();
    if($_SESSION['role']=="Admin"){
      include_once'inc/header_all.php';
    }else{
        include_once'inc/header_all_operator.php';
    }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content container-fluid">
      <div class="row">
        <!-- get alert stock -->
        <?php
        $select = $pdo->prepare("SELECT count(product_code) as total FROM tbl_product WHERE stock <= min_stock");
        $select->execute();
        $row=$select->fetch(PDO::FETCH_OBJ);
        $total1 = $row->total;
        ?>
        <!-- get alert notification -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="product.php"><span class="info-box-icon bg-aqua"><i class="fa fa-archive"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Productos por agotarse</span>
              <?php if($total1==true){ ?>
              <span class="info-box-number"><small><?php echo $row->total;?></small></span>
              <?php }else{?>
              <span class="info-box-text"><strong>No hay existencias</strong></span>
              <?php }?>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>


        <!-- get total products-->
        <?php
        $select = $pdo->prepare("SELECT count(product_code) as t FROM tbl_product");
        $select->execute();
        $row=$select->fetch(PDO::FETCH_OBJ);
        $total = $row->t;
        ?>

        <!-- get total products notification -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="product.php"><span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Total de Productos</span>
              <span class="info-box-number"><small><?php echo $row->t ?></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

        <!-- get today transactions -->
        <?php
        $select = $pdo->prepare("SELECT count(invoice_id) as i, sum(total) as total FROM tbl_invoice");
        $select->execute();
        $row=$select->fetch(PDO::FETCH_OBJ);
        $invoice = $row->i ;
		$total = $row->total ;
        ?>
         <!-- get today transactions notification -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="create_order.php?or=1"><span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Ventas totales</span>
              <span class="info-box-number"><small>S/. <?php echo number_format($total,2); ?></small></span>
			  <span class="info-box-number"><small><?php echo $row->i ?> ventas</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>


        <!-- get today income -->
        <?php
        $select = $pdo->prepare("SELECT count(invoice_id) as i, sum(total) as total FROM tbl_invoice WHERE order_date = CURDATE()");
        $select->execute();
        $row=$select->fetch(PDO::FETCH_OBJ);
        $total = $row->total ;
        ?>
         <!-- get today income -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <a href="order.php"><span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span></a>

            <div class="info-box-content">
              <span class="info-box-text">Ventas de hoy</span>
              <span class="info-box-number"><small>S/. <?php echo number_format($total,2); ?></small></span>
			  <span class="info-box-number"><small><?php echo $row->i ?> ventas</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

      </div>

      <div class="col-md-offset-1 col-md-14">
        <div class="box box-success">
          <div class="box-header with-border">
              <h3 class="box-title">Lista de productos aplicados</h3>
          </div>
          <div class="box-body">
            <div class="col-md-offset-1 col-md-10">
              <div style="overflow-x:auto;">
                  <table class="table table-striped" id="myBestProduct">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Producto</th>
                              <th>Código</th>
                              <th>Vendido</th>
                              <th>Precio</th>
                              <th>Ingresos</th>
                          </tr>

                      </thead>
                      <tbody>
                          <?php
                          $no = 1;
                          $select = $pdo->prepare("SELECT product_code,product_name,price,product_satuan,sum(qty) as q, sum(qty*price) as total FROM
                          tbl_invoice_detail GROUP BY product_id ORDER BY sum(qty) DESC LIMIT 30");
                          $select->execute();
                          while($row=$select->fetch(PDO::FETCH_OBJ)){
                          ?>
                              <tr>
                              <td><?php echo $no++ ;?></td>
                              <td><?php echo $row->product_name; ?></td>
                              <td><?php echo $row->product_code; ?></td>
                              <td><span class="label label-primary"><?php echo $row->q; ?></span>
                              <span class="label label-default"><?php echo $row->product_satuan; ?></span>
                              </td>
                              <td><span class="label label-warning">S/. <?php echo number_format($row->price,2);?></span></td>
                              <td><span class="label label-success">S/. <?php echo number_format($row->total,2); ?></span></td>
                              </tr>

                        <?php
                          }
                        ?>
                      </tbody>
                  </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script>
  $(document).ready( function () {
      $('#myBestProduct').DataTable( {
	  
	  "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
	  dom: 'Bfrtip',
        buttons: [
           'excel', 'pdf', 'print'
        ]
	  } );
  } );
  </script>


 <?php
    include_once'inc/footer_all.php';
 ?>