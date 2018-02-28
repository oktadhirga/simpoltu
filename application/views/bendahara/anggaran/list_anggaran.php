
        <div class="col-sm-12">
          <p><a href="<?php echo base_url('bendahara/kegiatan') ?>" class="btn btn-sm btn-success"><i class="fa fa-level-up"> Kembali</i></a></p>
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-2 control-label">Kegiatan</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($list_kegiatan)) {
                        echo $list_kegiatan->nama_kegiatan;
                    }
                  ?>
                </label>
              </div>
            </div>

          <div class="box-body">
            <p><button class="btn btn-success btn-sm" id="edit-rekening"><i class="fa fa-pencil"> Tambah/Edit</i></button></p>

            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th style="width:5px">#</th>
                    <th>Kode Rekening</th>
                    <th>Uraian Rekening</th>
                    <th>Anggaran</th>
                    <th>Parent</th>
                    <th style="width:50px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>


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

                              <input type="hidden" value="<?php echo $list_kegiatan->id_kegiatan ?>" name="id_kegiatan"/>
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
