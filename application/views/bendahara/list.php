

          <div class="col-sm-12">
            <div class="box">
              <div class="box-header">
                <h3 class="box-title"><?php echo $title; ?></h3>
              </div>

            <div class="box-body">
                  <!-- small box -->
                <div class="col-sm-12">
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3 style="display:inline"><?php echo $count_program ?> </h3><h4 style="display:inline">Program Anda :</h4>
                      <ul style="margin:15px">
                        <?php foreach ($program as $program) : ?>
                           <li>
                             <?php echo $program['nama_program']; ?>
                           </li>
                      <?php endforeach; ?>
                       </ul>
                    </div>
                    <div class="icon">
                      <i class="fa fa-pie-chart"></i>
                    </div>
                  </div>
                </div>
                <!-- ./col -->

                <div class="col-sm-12">
                <!-- small box -->
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3 style="display:inline"><?php echo $count_kegiatan ?> </h3><h4 style="display:inline">Kegiatan yang Anda Kelola :</h4>
                    <ul style="margin:15px">
                      <?php foreach ($kegiatan as $kegiatan) : ?>
                         <li>
                           <?php echo $kegiatan['nama_kegiatan']; ?>
                         </li>
                    <?php endforeach; ?>
                     </ul>
                  </div>
                  <div class="icon">
                    <i class="fa fa-file-text"></i>
                  </div>
                  <a href="<?php echo base_url('bendahara/kegiatan')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
                </div>
                <!-- ./col -->

                <?php if ($this->session->userdata('akses_level') == 'bendahara_gaji') { ?>
                    <div class="col-sm-12">
                    <!-- small box -->
                    <div class="small-box bg-red">
                      <div class="inner">
                        <h3><?php echo $count_ls ?> </h3>

                        <p>SPJ-LS</p>
                      </div>
                      <div class="icon">
                        <i class="fa fa-vine"></i>
                      </div>
                      <a href="<?php echo base_url('bendahara/ls')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
              <? } else { ?>
                <div class="col-sm-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3><?php echo $count_panjar ?> </h3>
                    <p> Panjar / SPJ-GU<p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-gg"></i>
                  </div>
                  <a href="<?php echo base_url('bendahara/panjar')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
                </div>
                <!-- ./col -->

                <div class="col-sm-6">
                <!-- small box -->
                <div class="small-box bg-red">
                  <div class="inner">
                    <h3><?php echo $count_ls ?> </h3>

                    <p>SPJ-LS</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-vine"></i>
                  </div>
                  <a href="<?php echo base_url('bendahara/ls')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <? } //endif ?>

          </div>
        </div>
      </div>
