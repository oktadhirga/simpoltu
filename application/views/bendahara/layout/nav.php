<aside class="main-sidebar">
   <!-- sidebar: style can be found in sidebar.less -->
   <section class="sidebar">
     <!-- Sidebar user panel -->
     <div class="user-panel">
       <div class="pull-left image">
         <img src="" class="img-circle myProfil" alt="User Image">
       </div>
       <div class="pull-left info">
         <p class="myName capitalize"></p>
       </div>
     </div>

     <!-- sidebar menu: : style can be found in sidebar.less -->
     <ul class="sidebar-menu">
       <li class="header">MAIN NAVIGATION</li>
       <li><a href="<?php echo base_url('bendahara/dasbor'); ?>"><i class="fa fa-home"></i> <span>Home</span></a></li>
       <li class="treeview">
         <a href="#">
           <i class="fa fa-dashboard"></i> <span>Data Master</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
           <li><a href="<?php echo base_url('bendahara/kegiatan'); ?>"><i class="fa fa-circle-o"></i>Data Kegiatan</a></li>
           <li><a href="<?php echo base_url('bendahara/rekening'); ?>"><i class="fa fa-circle-o"></i>Data Rekening Belanja</a></li>
         </ul>
       </li>
       <li class="treeview">
         <a href="#">
           <i class="fa fa-money"></i> <span>SPJ</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
           <?php if ($this->session->userdata('akses_level') != 'bendahara_gaji'): ?>
             <li><a href="<?php echo base_url('bendahara/panjar'); ?>"><i class="fa fa-gg"></i> <span>SPJ-GU</span></a></li>
           <?php endif; ?>
           <li><a href="<?php echo base_url('bendahara/ls'); ?>"><i class="fa fa-vine"></i> <span>SPJ-LS</span></a></li>
         </ul>
       </li>
       <li><a href="<?php echo base_url('bendahara/laporan'); ?>"><i class="fa fa-newspaper-o"></i> <span>Laporan</span></a></li>

     </ul>
   </section>
   <!-- /.sidebar -->
 </aside>
