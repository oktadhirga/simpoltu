
        <div class="col-sm-12">
          <p><a href="<?php echo base_url('bendahara/panjar') ?>" class="btn btn-sm btn-success"><i class="fa fa-level-up"> Kembali</i></a></p>
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            <div class="row">
              <div class="form-group">
                <label class="col-sm-2 control-label">Nama Kegiatan</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                        echo $nama_kegiatan;
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">No. Panjar</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                        echo $no_bukti;
                  ?>
                </label>
              </div>
            </div>
            <!--notifikasi -->
            <div class="box-body" id="notifikasi">

            </div>

        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No. SPJ</th>
                    <th>Tgl SPJ</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>



              <!--Modal Bootstrap -->
                <div class="modal" id="modal-spj">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Default Modal</h4>
                      </div>
                      <div class="modal-body">

                          <form class="form-horizontal" id="form-spj">
                          <div class="box-body">
                            <input type="hidden" value="" name="id_spj"/>
                            <input type="hidden" value="<?php echo $id_panjar ?>" name="id_panjar"/>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">No. SPJ</label>

                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_spj" value="<?php echo set_value('no_spj'); ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Tanggal SPJ</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="tgl_spj" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Keterangan</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="ket_spj" value="<?php echo set_value('ket_spj'); ?>"></textarea>
                                <span class="help-block"></span>
                              </div>
                            </div>
                          </div>

                        </form>


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="simpan-spj"><i class="fa fa-floppy-o"></i> Save</button>
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
