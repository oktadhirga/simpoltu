
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

                          <form class="" id="form-pengaturan">
                          <div class="box-body">

                            <div class="form-group">
                              <label class="col-sm-3 control-label">Instansi</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="instansi" value="<?php echo $pengaturan[0]->nilai_pengaturan ?>" required>
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Unit Kerja</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="unit_kerja" value="<?php echo $pengaturan[1]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Alamat Unit Kerja</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="alamat_unit_kerja" value="<?php echo $pengaturan[5]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Kota - Kode Pos</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="kota_kdpos" value="<?php echo $pengaturan[6]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label">Rekening Unit Kerja</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="rekening_uk" value="<?php echo $pengaturan[2]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label">Pengguna Anggaran</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="pengguna_anggaran" value="<?php echo $pengaturan[7]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label">NIP Pengguna Anggaran</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nip_pa" value="<?php echo $pengaturan[8]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label">Bendahara Pengeluaran</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="bendahara_pengeluaran" value="<?php echo $pengaturan[3]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label">NIP Bendahara Pengeluaran</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control" name="nip_bp" value="<?php echo $pengaturan[4]->nilai_pengaturan ?>">
                                <span class="help-block"></span>
                              </div>
                            </div>

                          </div>

                          <button type="button" class="btn btn-primary pull-right" id="simpan-pengaturan"><i class="fa fa-floppy-o"></i>  Simpan</button>

                        </form>






            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
