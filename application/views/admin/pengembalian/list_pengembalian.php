
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <p>
                <a class="btn btn-sm btn-success" href="<?php echo base_url('admin/validasi') ?>" title="Back"><i class="glyphicon glyphicon-triangle-left"></i> Kembali</a>
              </p>
            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>No. Panjar</th>
                    <th>No. Pengembalian</th>
                    <th>Tgl. Pengembalian</th>
                    <th>Nilai Pengembalian</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-pengembalian">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-pengembalian">
                          <div class="box-body">
                            <input type="hidden" value="" name="id_pengembalian"/>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama Kegiatan</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="option_kegiatan">
                                </select>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">No. Bukti</label>

                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_pengembalian" value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Tanggal Bukti</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="tgl_pengembalian" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nilai</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nilai_pengembalian" value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Keterangan</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="ket_pengembalian"></textarea>
                                <span class="help-block"></span>
                              </div>
                            </div>
                          </div>

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-pengembalian"><i class="fa fa-floppy-o"></i> Save</button>
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
