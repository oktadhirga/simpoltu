
<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url();?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url();?>assets/plugins/select2/select2.full.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url();?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url();?>assets/plugins/fastclick/fastclick.js"></script>
<!-- BendaharaLTE App -->
<script src="<?php echo base_url();?>assets/dist/js/app.min.js"></script>
<!-- BendaharaLTE for demo purposes -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>

<!-- InputMask -->
<script src="<?php echo base_url();?>assets/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url();?>assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url();?>assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>

<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    //Datemask2 mm/dd/yyyy
    $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
    //Money Euro
    $("[data-mask]").inputmask();

  });

</script>

<!-- page script -->
<script>
var id_panjar = '<?php echo $id_panjar ?>';
$(document).ready(function() {
      //datatables
      var id_spj = $('[name="id_spj"]').val();
      sum_spj();
      sisa_spj();
      $('#notifikasi').hide();
      table1 = $('#table-spj').DataTable({
          "paging":   false,
          "ordering": false,
          "info":     false,
          "filter"  : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('admin/spj/ajax_list')?>/" + id_panjar,
              "type": "POST"
          },


          //Set column definition initialisation properties.
          "columnDefs": [
          {
              "targets": [ -1 ], //last column
              "orderable": false, //set not orderable
          },
          ],


      }); //end-datatable

      table2 = $('#table').DataTable({
         "paging" : false,
          "info":     false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('admin/spj_detail/ajax_list')?>/" + id_panjar,
              "type": "POST"
          },


          //Set column definition initialisation properties.
          "columnDefs": [
          {
              "targets": [ -1 ], //last column
              "orderable": false, //set not orderable
          },
          ],

          "columnDefs": [
                { "targets": [0], visible: false},
          ],


      }); //end-datatable


}); //end-document-ready


function sum_spj(){
  $.ajax({
      url : "<?php echo base_url('admin/spj_detail/sum_spj')?>/" + id_panjar,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="jum_spj_panjar"]').text(new Intl.NumberFormat('id-ID').format(data));

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  })
};

function sisa_spj(){
  $.ajax({
      url : "<?php echo base_url('admin/spj_detail/sisa_spj')?>/" + id_panjar,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="sisa_panjar"]').text(new Intl.NumberFormat('id-ID').format(data));

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get Sisa from ajax');
      }
  })
};





function validasi_spj(id_spj){
  $('#form-pengesahan')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  $('#modal-pengesahan').modal('show'); // show bootstrap modal
  $('.modal-title').text('Pengesahan SPJ'); // Set Title to Bootstrap modal title
  //Ajax Load data from ajax
  $.ajax({
      url : "<?php echo base_url('admin/spj/ajax_edit')?>/" + id_spj,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
          $('[name="id_spj"]').val(data.id_spj);
          $('[name="no_spj"]').val(data.no_spj);
          $('[name="no_pengesahan"]').val(data.no_pengesahan);
          $('[name="tgl_pengesahan"]').val(data.tgl_pengesahan);
          if (data.isVerified == null || data.isVerified == 0) {
            $('[name="isVerified"]').val(0);
            $('[name="isVerified"]').prop('checked', false);
          } else {
            $('[name="isVerified"]').val(1);
            $('[name="isVerified"]').prop('checked', true);
          }


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  });
}


$('#simpan-pengesahan').click(function(){
      $('#simpan-pengesahan').text('saving...'); //change button text
      $('#simpan-pengesahan').attr('disabled',true); //set button disable
      // $('[name="isVerified"]').change(function(){
      //     this.value = (Number(this.checked));
      // });
      $('[name="isVerified"]').on('change', function(){
          this.value = this.checked ? 1 : 0;
      }).change();

      // ajax adding data to database
      $.ajax({
          url : "<?php echo site_url('admin/spj/ajax_update_pengesahan')?>",
          type: "POST",
          data: $('#form-pengesahan').serialize(),
          dataType: "JSON",
          success: function(data)
          {

              if(data.status) //if success close modal and reload ajax table
              {
                  $('#notifikasi').html(data.notif).addClass('alert alert-info');
                  $('#notifikasi').fadeTo(4000, 500).slideUp(500, function(){
                      $('#notifikasi').slideUp(500);
                  });
                  $('.form-group').removeClass('has-error'); // clear error class
                  $('.help-block').empty(); // clear error string
                  reload_table();
                  sum_spj();
                  sisa_spj();
                  $('#modal-pengesahan').modal('hide'); // hide bootstrap modal
              }
              else
              {
                  for (var i = 0; i < data.inputerror.length; i++)
                  {
                      $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                      $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                  }
              }
              $('#simpan-pengesahan').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
              $('#simpan-pengesahan').attr('disabled',false); //set button enable

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error add / update data');
              $('#simpan-pengesahan').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
              $('#simpan-pengesahan').attr('disabled',false); //set button enable

          }
        });
    }); //end-simpan-pengesahan




function reload_table() {
  table1.ajax.reload(null,false); //reload datatable ajax
}


</script>
</body>
</html>
