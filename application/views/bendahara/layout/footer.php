<!--Modal Profil -->
  <div class="modal" id="modal-profil">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Default Modal</h4>
        </div>
        <div class="modal-body">

            <form class="form-horizontal" id="form-profil" enctype="multipart/form-data" >
            <div class="box-body">

              <input type="hidden" value="" name="id_user"/>
              <div class="form-group">
                <label class="col-sm-3 control-label">Nama User</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="nama_user" value="" required>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">NIP User</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="nip_user" value="" >
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Username</label>
                <div class="col-sm-9">

                    <input type="text" class="form-control" name="username" value="" required>

                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Password</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="password" value="" required>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Profil</label>
                <div class="col-sm-9">
                  <input type="file" name="pic_user" value="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3"></label>
                <div class="col-sm-9">
                  <?php $id_user = $this->session->userdata('id_user'); ?>
                    <div class="profil-wrap">
                        <a class="close" onclick="delete_profil('<?php echo $id_user ?>')">&times;</a>
                        <img id="myProfil" width="100px">
                    </div>
                    <div class="result text-danger"></div>
                </div>
              </div>


            </div> <!--end box-body-->

          </form>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="simpan-profil"><i class="fa fa-floppy-o"></i> Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

<!-- alert success profile -->
  <div class="alert alert-success collapse" id="profil-alert">
  </div>
<!-- end alert -->

<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 0.1.4.5
  </div>
  <strong>Copyright &copy; 2017 <a href="http://bkd.trenggalekkab.go.id" target="_blank">BKD Trenggalek</a>.</strong> All rights
  reserved.
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">

  <!-- Tab panes -->
  <div class="tab-content">
    <!-- Home tab content -->
    <div class="tab-pane" id="control-sidebar-home-tab">

    </div>

  </div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
