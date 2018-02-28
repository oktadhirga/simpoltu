<aside class="main-sidebar">
   <!-- sidebar: style can be found in sidebar.less -->
   <section class="sidebar">
     <!-- Sidebar user panel -->
     <div class="user-panel">
       <div class="pull-left image">
         <img src="<?php echo base_url('assets/profile/default.png') ?>" class="img-circle" alt="User Image">
       </div>
       <div class="pull-left info">
         <p><?php echo ucfirst($this->session->userdata('nama_user')) ?></p>
       </div>
     </div>
     <!-- sidebar menu: : style can be found in sidebar.less -->
     <ul class="sidebar-menu">
       <li class="header">MAIN NAVIGATION</li>
       <li><a href="<?php echo base_url('admin/dasbor'); ?>"><i class="fa fa-home"></i> <span>Home</span></a></li>
       <li class="treeview">
         <a href="#">
           <i class="fa fa-dashboard"></i> <span>Data Master</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
           <li><a href="<?php echo base_url('admin/user'); ?>"><i class="fa fa-circle-o"></i>Data User</a></li>
           <li><a href="<?php echo base_url('admin/program'); ?>"><i class="fa fa-circle-o"></i>Data Program-Kegiatan</a></li>
           <li><a href="<?php echo base_url('admin/rekening'); ?>"><i class="fa fa-circle-o"></i>Data Rekening Belanja</a></li>
         </ul>
       </li>
       <li><a href="<?php echo base_url('admin/validasi'); ?>"><i class="fa fa-pinterest-p"></i> <span>Pengesahan SPJ-GU</span></a></li>
       <li><a href="<?php echo base_url('admin/pengaturan'); ?>"><i class="fa fa-cogs"></i> <span>Pengaturan Umum</span></a></li>
     </ul>
   </section>
   <!-- /.sidebar -->
 </aside>
