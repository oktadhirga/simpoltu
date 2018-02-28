
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>Program</th>
                    <th>Rekening Kegiatan</th>
                    <th>KPA</th>
                    <th>PPTK</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-kegiatan">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-kegiatan">
                          <div class="box-body">

                            <input type="hidden" value="" name="id_kegiatan"/>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama Kegiatan</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_kegiatan" value="" required>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Program</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_program" value="" disabled>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Rekening Kegiatan</label>
                              <div class="col-sm-9">
                                <div class="input-group">
                                  <span class="input-group-addon"><?php echo $pengaturan[2]->nilai_pengaturan ?><span id="rekening"></span></span>
                                  <input type="text" class="form-control" name="rekening_kegiatan" value="">
                                </div>
                                <div class="help-block"></div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama KPA</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_kpa" value="">
                                <div class="help-block"></div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">NIP KPA</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nip_kpa" value="">
                                <div class="help-block"></div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Nama PPTK</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_pptk" value="">
                                <div class="help-block"></div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">NIP PPTK</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nip_pptk" value="">
                                <div class="help-block"></div>
                              </div>
                            </div>

                          </div> <!--end box-body-->

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-kegiatan"><i class="fa fa-floppy-o"></i> Simpan</button>
                      </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->


                <!--Modal Bootstrap Rekening -->
                  <div class="modal" id="modal-rekening">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Default Modal</h4>
                        </div>
                        <div class="modal-body">
                            <div id="notifikasi2"></div>

                            <form class="form-horizontal" id="form-rekening">
                            <div class="box-body">

                              <input type="hidden" value="" name="id_kegiatan"/>
                              <input type="hidden" value="" name="id_max"/>
                              <div class="form-group">
                                <label class="col-sm-3 control-label">Kegiatan</label>
                                <div class="col-sm-9">
                                  <textarea type="text" class="form-control" name="nama_kegiatan" rows="2" style="resize: none" disabled></textarea>
                                  <span class="help-block"></span>
                                </div>
                              </div>
                              <div class="form-group">
                                <label class="col-sm-3 control-label">Rekening</label>
                                <div class="col-sm-9">
                                  <select class="form-control select2" name="option_rekening" required="required">
                                  <input name="rekening" type="hidden">
                                  <span class="help-block"></span>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label class="col-sm-3 control-label">Jumlah Anggaran</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control" name="jumlah_anggaran" value="">
                                  <div class="help-block"></div>
                                </div>
                              </div>

                            </div> <!--end box-body-->

                          </form>


                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                          <button type="button" class="btn btn-primary" id="simpan-rekening"><i class="fa fa-floppy-o"></i> Simpan</button>
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
