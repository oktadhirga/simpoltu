
        <div class="col-sm-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <?php //echo form_open('bendahara/laporan/cetak') ?>
              <form id="form-laporan" class="" action="" method="get">

                <div class="row">
                  <div class="form-group col-sm-4">
                    <label>Pilih Kegiatan :</label>
                    <select class="form-control" name="optKg">
                      <?php foreach ($kegiatan as $kegiatan): ?>
                        <option value="<?php echo $kegiatan['id_kegiatan'] ?>"><?php echo $kegiatan['nama_kegiatan'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-sm-4">
                    <label>Jenis Laporan :</label>
                    <select class="form-control" name="optLp">
                      <option value="1">Buku Kas Umum</option>
                      <option value="2">Buku Pembantu Kas Tunai</option>
                      <option value="3">Buku Pembantu Pajak</option>
                      <option value="4">Laporan Pertanggungjawaban</option>
                      <option value="5">Realisasi Per Kegiatan</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="">Tanggal :</label>
                    <div class="form-group form-inline">
                      <div class="input-group">
                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                         <input type="text" class="form-control" id="dateFrom" name="tgA" required>
                       </div>

                      <label for="to" class="to-date"> s/d </label>
                      <div class="input-group to-date">
                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                         <input type="text" class="form-control" id="dateTo" name="tgB" required>
                       </div>
                    </div>
                    <?php
                    if($this->session->flashdata('error_tgl')): ?>
                      <span class="text text-danger"><?=$this->session->flashdata('error_tgl') ?></span>
                    <? endif; ?>
                </div>

                <!-- <p><button class="btn btn-success btn-sm" id="tampil" href=""><i class="fa fa-search"> Tampilkan</i></button> -->
                <button formtarget="_blank" type="submit" class="btn btn-primary btn-sm btn-flat" id="cetak"><i class="fa fa-print"> Cetak</i></button>
                <button formtarget="_blank" type="submit" class="btn btn-success btn-sm btn-flat" id="xls"><i class="fa fa-file-excel-o"> Excel</i></button>
              </form>
                <?php //echo form_close() ?>

            <div id="hasil" style="margin-top : 50px"></div>



            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
