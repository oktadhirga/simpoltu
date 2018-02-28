<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo base_url('bendahara/dasbor')?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>BPP</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>Bendahara PP</b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>
    <div class="app-name">
      <span class="app-name-lg">Sistem Informasi Manajemen Pelaporan Bendahara Pengeluaran Pembantu</span>
      <span class="app-name-mini">SIMPOLTU</span>
    </div>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="" class="user-image myProfil" alt="User Image">
            <span class="hidden-xs myName capitalize"></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="" class="img-circle myProfil" alt="User Image">

              <p>
                <span class="myName capitalize"></span>
                <small><?php echo '-'.ucfirst($this->session->userdata('akses_level')).'-' ?></small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <button class="btn btn-default btn-flat" id="profil">Profil</button>
                <!-- <button class="btn btn-default btn-flat" id="password">Password</button> -->
              </div>
              <div class="pull-right">
                <a href="<?php echo base_url('login/signout') ?>" class="btn btn-default btn-flat" id="btn_signout">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
      </ul>
    </div>
  </nav>
</header>
