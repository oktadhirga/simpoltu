
        <div class="col-sm-12">
        <p><a href="<?php echo base_url('bendahara/panjar/spj/'.$list_program->id_panjar) ?>" class="btn btn-sm btn-success"><i class="fa fa-level-up"> Kembali</i></a></p>
        <!--box judul dan rincian program -->
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-2 control-label">Program</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($list_program)) {
                        echo $list_program->nama_program;
                    }
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Kegiatan</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($list_program)) {
                        echo $list_program->nama_kegiatan;
                    }
                  ?>
                </label>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">No. SPJ</label>
                <label class="col-sm-1 control-label">:</label>
                <label class="col-sm-9 control-label">
                  <?php
                    if (!empty($list_program)) {
                        echo $list_program->no_spj;
                    }
                  ?>
                </label>
              </div>
              <div class="row">
                <div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6">
                  <table width="100%">
                    <tr>
                      <td style="width:50%;"><label class="control-label"> Jumlah Panjar </label></td>
                      <td style="width:10%;"><label class="control-label"> : </label></td>
                      <td style="width:10%;"><label class="control-label"> Rp </label></td>
                      <td style="width:30%;" class="text-right"><label class="control-label">
                        <?php
                        $jumlah_desimal ="0";
                        $pemisah_desimal =",";
                        $pemisah_ribuan =".";
                          if (!empty($list_program)) {
                              echo number_format($list_program->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
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
                      <td style="width:30%;" class="text-right"><label name="jum_spj_panjar" class="control-label">-</label></td>
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
                    <th>id_spj_detail</th>
                    <th>#</th>
                    <th>Kode Rekening</th>
                    <th>Uraian</th>
                    <th>Nilai</th>
                    <th>No. Bukti</th>
                    <th>Tgl. Bukti</th>
                    <th>Pajak</th>
                    <th></th>
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

        <div class="col-md-7">
            <form class="form-horizontal" id="form-spj_detail">
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
                  <input type="hidden" value="<?php echo $list_program->id_spj; ?>" name="id_spj"/>
                  <input type="hidden" value="" name="id_spj_detail"/>
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
                      <input type="text" class="form-control" name="nilai_spj" value="" >
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">No. Bukti</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="no_spj_detail" value="">
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Tanggal Bukti</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="tgl_spj_detail" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="" disabled>
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Keterangan</label>
                    <div class="col-sm-9">
                      <textarea class="form-control" rows="3" name="ket_spj_detail"></textarea>
                      <!-- <input type="text" class="form-control" name="ket_spj_detail" value=""> -->
                      <span class="help-block"></span>
                    </div>
                  </div>
                </div>
              </div>



            <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Tambah" id="tambah-spj_detail"><i class="glyphicon glyphicon-plus"></i> Tambah</a>
            <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" id="edit-spj_detail"><i class="glyphicon glyphicon-pencil"></i> Ubah</a>
            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" id="hapus-spj_detail"><i class="glyphicon glyphicon-trash"></i> Hapus</a>
            <a class="btn btn-sm btn-default pull-right" href="javascript:void(0)" title="Batal" id="batal-spj_detail"><i class=""></i> Batal</a>
            <a class="btn btn-sm btn-default pull-right" href="javascript:void(0)" title="Simpan" id="simpan-spj_detail"><i class="fa fa-floppy-o"></i> Simpan</a>

            </form>
        </div>



        <!--Modal Bootstrap -->
          <div class="modal" id="modal-pajak">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Input Pajak</h4>
                </div>
                <div class="modal-body">

                <div class="box">
                  <div class="box-body" id="notifikasi-pajak">

                  </div>

                  <div class="box-body">
                    <table id="table-pajak" class="table table-striped table-bordered sortable" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Jenis Pajak</th>
                                <th>Nilai Pajak</th>
                                <th>Tanggal Setor</th>
                                <th>Aksi</th>
                            </tr>
                          </thead>

                    </table>
                  </div>
                </div>

                <form class="form-horizontal" id="form-pajak">
                <div class="box-body">
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="id_spj_detail" value="" >
                    <input type="hidden" class="form-control" name="id_pajak" value="" >
                    <label class="col-sm-3 control-label">Jenis Pajak</label>
                    <div class="col-sm-9">
                      <select class="form-control" name="option_pajak" required="required">
                        <option value="">-- Pilih Pajak --</option>
                        <option value="PPN">PPN</option>
                        <option value="PPh 21">PPh 21</option>
                        <option value="PPh 22">PPh 22</option>
                        <option value="PPh 23">PPh 23</option>
                        <option value="PPh Pasal 4 (2)">PPh Pasal 4 (2)</option>
                        <option value="Pajak Daerah">Pajak Daerah</option>
                      </select>
                      <!-- <input type="text" class="form-control hidden" name="jenis_pajak" value="" > -->
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Nilai Pajak</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="nilai_pajak" value="" >
                      <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Tanggal Penyetoran</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="tgl_setor_pajak" data-inputmask="'alias': 'dd-mm-yyyy'" data-mask value="">
                      <span class="help-block"></span>
                    </div>
                  </div>

                </div>
                </form>
                </div>
                <!-- /.box-body -->

                <div class="modal-footer" id="foot-pajak">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="simpan-pajak"><i class="fa fa-floppy-o"></i> Simpan</button>
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
