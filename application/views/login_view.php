<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"><title><?php echo $title ?> | Simpoltu</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!--STYLESHEETS-->
  <link href="<?php echo base_url();?>assets/dist/login/css/style.css" rel="stylesheet" />

</head>
<body>

<!--WRAPPER-->
<div id="wrapper">
      <div class="information"><h1>SIMPOLTU</h1>
        <strong>S</strong>istem <strong>I</strong>nformasi <strong>M</strong>anajemen Pela<strong>po</strong>ran Bendahara Penge<strong>l</strong>uaran Pemban<strong>tu</strong>, membantu anda mengelola laporan keuangan...
      </div>

    <div class="user-icon"></div>
    <div class="pass-icon"></div>

    <form name="login-form" class="login-form" action="<?php echo base_url('login'); ?>" method="post">


        <div class="header">
        <h1>Login Form</h1>
        </div>

        <div class="content">
        	<input name="username" type="text" class="input username" value="<?php echo set_value('username') ?>" placeholder="Username" />
          <input name="password" type="password" class="input password" value="<?php echo set_value('password') ?>" placeholder="Password" />

          <label class="select">Tahun Anggaran</label>
          <select id="option_year" class="option" name="tahun_anggaran" >

          </select>
        </div>

        <div class="footer">
          <?php
              //validasi form
              echo validation_errors('<div class="warning">','</div>');
              //echo '<div class="warning">Mohon maaf, sedang update aplikasi</div>';
              //cetak notifikasi
              if($this->session->flashdata('sukses')) {
              echo '<div class="warning">';
              echo $this->session->flashdata('sukses');
              echo '</div>';
              }
          ?>
        <input type="submit" name="submit" value="Login" class="button" />
        </div>

    </form>



</div>
<!--END WRAPPER-->

<div class="gradient"></div>

<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url();?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    	$(".username").focus(function() {
    		$(".user-icon").css("left","-48px");
    	});
    	$(".username").blur(function() {
    		$(".user-icon").css("left","0px");
    	});

    	$(".password").focus(function() {
    		$(".pass-icon").css("left","-48px");
    	});
    	$(".password").blur(function() {
    		$(".pass-icon").css("left","0px");
    	});
    });
    $('.alert-success, .alert-warning').delay(3000).fadeOut('slow');
    //get year
    $.getJSON("<?php echo base_url('login/year/') ?>", function(data) {
    $("#option_year option").remove();
    $.each(data, function(){
        $("#option_year").append('<option value="'+ this.tahun +'">'+ this.tahun +'</option>');
    })
})
</script>
</body>
</html>
