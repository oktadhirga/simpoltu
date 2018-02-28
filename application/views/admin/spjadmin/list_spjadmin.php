
        <div class="col-sm-12">
        <!--box judul dan rincian program -->
        <p>
          <a class="btn btn-sm btn-success" href="<?php echo base_url('admin/validasi') ?>" title="Back"><i class="fa fa-level-up"></i> Kembali</a>
        </p>
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body">

              <div class="box-body" id="notifikasi">

              </div>

              <table id="table-spj" class="table table-striped table-bordered sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                      <th>Nama Kegiatan</th>
                      <th>No. Panjar</th>
                      <th>Nilai Panjar</th>
                      <th>No. SPJ</th>
                      <th>Tgl SPJ</th>
                      <th>Keterangan</th>
                      <th>Status</th>
                    </tr>
                </thead>
            </table>

            </div>

          </div> <!--end box judul -->

          <div class="box"> <!--box tabel -->
            <div class="box-header">
              <h3 class="box-title">Rincian</h3>
            </div>

            <div class="box-body">


          <table id="table" class="table table-striped table-bordered sortable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>id_spj_detail</th>
                    <th>#</th>
                    <th>Kode Rekening</th>
                    <th>Uraian</th>
                    <th>Nilai</th>
                    <th>No. Bukti</th>
                    <th>Tgl. Bukti</th>
                </tr>
            </thead>

        </table>
      </div>
      <!-- /.box-body -->
    </div>

    <div class="box">
      <div class="box-body">
        <!--notifikasi -->

        <div class="row">
          <div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6">
            <table width="100%">
              <tr>
                <td style="width:50%;"><label class="control-label"> Jumlah SPJ Panjar </label></td>
                <td style="width:10%;"><label class="control-label"> : </label></td>
                <td style="width:10%;"><label class="control-label"> Rp </label></td>
                <td style="width:30%;" class="text-right"><label name="jum_spj_panjar" class="control-label">-</label></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6">
            <table width="100%">
              <tr>
                <td style="width:50%;"><label class="control-label"> Sisa Panjar </label></td>
                <td style="width:10%;"><label class="control-label"> : </label></td>
                <td style="width:10%;"><label class="control-label"> Rp </label></td>
                <td style="width:30%;" class="text-right"><label name="sisa_panjar" class="control-label">-</label></td>
              </tr>
            </table>
          </div>
        </div>


        <!--Modal Bootstrap -->
          <div class="modal" id="modal-pengesahan">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Default Modal</h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" id="form-pengesahan">
                    <div class="box-body">

                      <input type="hidden" value="" name="id_spj"/>
                      <div class="form-group">
                        <label class="col-sm-3 control-label">No SPJ</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" name="no_spj" value="" readonly="readonly">
                          <span class="help-block"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 control-label">No Pengesahan</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" name="no_pengesahan" value="">
                          <span class="help-block"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Tanggal Pengesahan</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" name="tgl_pengesahan" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                          <span class="help-block"></span>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="isVerified" value="">
                              <strong>SPJ disahkan</strong>
                            </label>
                          </div>
                        </div>
                      </div>

                    </div> <!--end box-body-->

                  </form>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="simpan-pengesahan"><i class="fa fa-floppy-o"></i> Save</button>
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
