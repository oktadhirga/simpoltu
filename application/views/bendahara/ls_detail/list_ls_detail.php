
        <div class="col-sm-12">
        <p><a href="<?php echo base_url('bendahara/ls') ?>" class="btn btn-sm btn-success"><i class="fa fa-level-up"> Kembali</i></a></p>
        <!--box judul dan rincian program -->
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
                    if (!empty($kegiatan)) {
                        echo $kegiatan->nama_kegiatan;
                    }
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">No. LS</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($ls)) {
                        echo $ls->no_ls;
                    }
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Tanggal LS</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($ls)) {
                        echo $ls->tgl_ls;
                    }
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Keterangan LS</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($ls)) {
                        echo $ls->ket_ls;
                    }
                  ?>
                </label>
              </div>
              <div class="row">
                <div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6">
                  <table width="100%">
                    <tr>
                      <td style="width:50%;"><label class="control-label"> Nilai LS </label></td>
                      <td style="width:10%;"><label class="control-label"> : </label></td>
                      <td style="width:10%;"><label class="control-label"> Rp </label></td>
                      <td style="width:30%;" class="text-right"><label class="control-label">
                        <?php
                        $jumlah_desimal ="0";
                        $pemisah_desimal =",";
                        $pemisah_ribuan =".";
                          if (!empty($ls)) {
                              echo number_format($ls->nilai_ls, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
                          }
                        ?>
                      </label></td>
                    </tr>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6">
                  <table width="100%">
                    <tr>
                      <td style="width:50%;"><label class="control-label"> Jumlah SPJ </label></td>
                      <td style="width:10%;"><label class="control-label"> : </label></td>
                      <td style="width:10%;"><label class="control-label"> Rp </label></td>
                      <td style="width:30%;" class="text-right"><label name="jum_ls_detail" class="control-label">-</label></td>
                    </tr>
                  </table>
                </div>
              </div>

            </div>
          </div> <!--end box judul -->

          <div class="box"> <!--box tabel -->

            <div class="box-body">


        <table id="table" class="table table-striped table-bordered sortable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>id_ls</th>
                    <th>#</th>
                    <th>Kode Rekening</th>
                    <th>Uraian</th>
                    <th>Nilai</th>
                </tr>
              </thead>

        </table>
      </div>
      <!-- /.box-body -->
    </div>

    <div class="box">
      <div class="box-body">
        <!--notifikasi -->
        <div class="box-body" id="notifikasi">

        </div>

        <div class="col-md-7">
            <form class="form-horizontal" id="form-ls_detail">
                <div class="box">
                  <div class="box-header with-border">
                    <h3 class="box-title">Input Belanja</h3>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                    </div>
                  </div>
                  <!-- /.box-header -->

                <div class="box-body">
                  <input type="hidden" value="<?php echo $ls->id_ls; ?>" name="id_ls"/>
                  <input type="hidden" value="" name="id_ls_detail"/>
                  <input type="hidden" value="<?php echo $ls->tgl_ls; ?>" name="tgl_ls_detail"/>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Rekening</label>
                    <div class="col-sm-9">
                      <select class="form-control select2" name="option_rekening" required="required">
                      <input name="rekening" type="hidden" />
                      <span class="help-block"></span>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Nilai</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="nilai_ls_detail" value="" >
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Keterangan</label>
                    <div class="col-sm-9">
                      <textarea type="text" class="form-control" name="ket_ls_detail" ></textarea>
                      <span class="help-block"></span>
                    </div>
                  </div>

                </div>
              </div>



            <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Tambah" id="tambah-ls_detail"><i class="glyphicon glyphicon-plus"></i> Tambah</a>
            <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" id="edit-ls_detail"><i class="glyphicon glyphicon-pencil"></i> Ubah</a>
            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" id="hapus-ls_detail"><i class="glyphicon glyphicon-trash"></i> Hapus</a>
            <a class="btn btn-sm btn-default pull-right" href="javascript:void(0)" title="Batal" id="batal-ls_detail"><i class=""></i> Batal</a>
            <a class="btn btn-sm btn-default pull-right" href="javascript:void(0)" title="Simpan" id="simpan-ls_detail"><i class="fa fa-floppy-o"></i> Simpan</a>

            </form>
        </div>





            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
