<?php
    include_once'db/connect_db.php';
    session_start();
    if($_SESSION['username']==""){
        header('location:index.php');
    }else{
        if($_SESSION['role']=="Admin"){
          include_once'inc/header_all.php';
        }else{
            include_once'inc/header_all_operator.php';
        }
    }

    error_reporting(0);

	if($id=$_GET['id']){
    $id = $_GET['id'];

	$select = $pdo->prepare("SELECT product_id,qty FROM tbl_invoice_detail where invoice_id=$id");
      $select->execute();
      $result = $select->fetchAll();

      foreach($result as $row){
		 $id_prod = $row['product_id'];
        $select2 = $pdo->prepare("SELECT stock FROM tbl_product where product_id=$id_prod");
		$select2->execute();
		$result2 = $select2->fetch();
		
		$rem_qty = $result2['stock'] + $row['qty'];
		
		 $update = $pdo->prepare("UPDATE tbl_product SET stock = '$rem_qty' WHERE product_id=$id_prod");
         $update->execute();
		
      }
	  
	   $delete_query = "DELETE tbl_invoice , tbl_invoice_detail FROM tbl_invoice INNER JOIN tbl_invoice_detail ON tbl_invoice.invoice_id =
    tbl_invoice_detail.invoice_id WHERE tbl_invoice.invoice_id=$id";
    $delete = $pdo->prepare($delete_query);
    if($delete->execute()){
        echo'<script type="text/javascript">
            jQuery(function validation(){
            swal("Info", "Transacción eliminada", "info", {
            button: "Continuar",
                });
            });
            </script>';
    }

	}
	
?>

<html>
<head>
<meta http-equiv="refresh" content="60">
</head>
</html>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <!-- <section class="content-header">
      <h1>
      Transacción
      </h1>
      <hr>
    </section>-->

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Lista de ventas</h3>
                <a href="create_order.php" class="btn btn-success btn-sm pull-right">Agregar venta</a>
            </div>
            <div class="box-body">
                <div style="overflow-x:auto;">
                    <table class="table table-striped" id="myOrder">
                        <thead>
                            <tr>
                                <th style="width:20px;">No</th>
                                <th style="width:100px;">Usuario</th>
                                <th style="width:100px;">Fecha</th>
                                <th style="width:100px;">Dinero</th>
                                <th style="width:50px;">Opción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $select = $pdo->prepare("SELECT * FROM tbl_invoice ORDER BY invoice_id DESC");
                            $select->execute();
                            while($row=$select->fetch(PDO::FETCH_OBJ)){
                            ?>
                                <tr>
                                <td><?php echo $no++ ; ?></td>
                                <td class="text-uppercase"><?php echo $row->cashier_name; ?></td>
                                <td><?php echo $row->order_date; ?></td>
                                <td>S/. <?php echo number_format($row->total,2); ?></td>
                                <td>
                                    <?php if($_SESSION['role']=="Admin"){ ?>
                                    <a href="order.php?id=<?php echo $row->invoice_id; ?>" onclick="return confirm('¿Realmente desea eliminar la transacción?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                    <a href="misc/nota.php?id=<?php echo $row->invoice_id; ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i></a>
                                </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>


    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script>
  $(document).ready( function () {
      $('#myOrder').DataTable( {
	  
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