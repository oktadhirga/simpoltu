<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"><?php echo $breadcrum1 ?></a></li>
        <li class="active"><?php echo $breadcrum2 ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">

<?php
	if($isi) {
		$this->load->view($isi);
	}
?>

</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
