
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            <p><button class="btn btn-success" id="tambah-panjar"><i class="fa fa-plus"> Tambah</i></button></p>

            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>No. Bukti</th>
                    <th>Tgl Bukti</th>
                    <th>Nilai Panjar</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th style="width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-panjar">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-panjar">
                          <div class="box-body">
                            <input type="hidden" value="" name="id_panjar"/>
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
                                <input type="text" class="form-control" name="no_bukti" value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Tanggal Bukti</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="tgl_bukti" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nilai Panjar</label>
                              <div class="col-sm-9">
                                <input type="text" name="nilai_panjar" value="" readonly><a href="javascript:void(0)" title="Sinkronkan" class="sinkron_button btn btn-flat"><i class="glyphicon glyphicon-refresh"></i></a>
                                <span class="help-block"></span>
                                <div class="field-wrapper">

                                    <!-- DYNAMIC FROM JQUERY TO CREATE FORM -->

                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Keterangan</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="ket_panjar"></textarea>
                                <span class="help-block"></span>
                              </div>
                            </div>
                          </div>

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-panjar"><i class="fa fa-floppy-o"></i> Save</button>
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
