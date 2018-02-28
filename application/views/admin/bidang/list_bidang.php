
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            <p><button class="btn btn-success" id="tambah-bidang"><i class="fa fa-plus"> Tambah</i></button></p>

            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bidang</th>
                    <th>Akronim</th>
                    <th>Kepala</th>

                    <th style="width:125px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-bidang">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-bidang">
                          <div class="box-body">
                            <input type="hidden" value="" name="id_bidang"/>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama Bidang</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_bidang" value="" required>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Akronim</label>

                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="akronim" value="">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Kepala Bidang</label>

                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="kepala_bidang" value="">
                              </div>
                            </div>
                          </div>

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-bidang"><i class="fa fa-floppy-o"></i> Save</button>
                      </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->





            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
