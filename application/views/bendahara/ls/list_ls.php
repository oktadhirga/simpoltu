
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            <p><button class="btn btn-success" id="tambah-ls"><i class="fa fa-plus"> Tambah</i></button></p>

            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>No. LS</th>
                    <th>Tgl LS</th>
                    <th>Nilai LS</th>
                    <th>Keterangan</th>
                    <th style="width:125px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-ls">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-ls">
                          <div class="box-body">
                            <input type="hidden" value="" name="id_ls"/>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama Kegiatan</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="option_kegiatan">
                                </select>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">No. LS</label>

                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_ls" value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Tanggal LS</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="tgl_ls" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nilai LS</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nilai_ls" value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Keterangan</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="ket_ls"></textarea>
                                <span class="help-block"></span>
                              </div>
                            </div>
                          </div>

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-ls"><i class="fa fa-floppy-o"></i> Save</button>
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
