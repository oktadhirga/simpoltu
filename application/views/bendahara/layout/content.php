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
        <?php  if ($breadcrum1) : ?>
          <li><a href="#"><?php echo $breadcrum1 ?></a></li>
        <?php endif ?>
        <?php  if ($breadcrum2) : ?>
          <li class="active"><?php echo $breadcrum2 ?></li>
        <?php endif ?>
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
