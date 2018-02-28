<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url();?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url();?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url();?>assets/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>
<!-- page script -->
<script>

$(document).ready(function() {
      //datatables
      $('#notifikasi').hide();
      table = $('#table').DataTable({

          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/rekening/ajax_list')?>",
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

}); //end-document-ready

//CHECK RADIO
$("#optionRincian").click(function(){
  //Radio Check for Belanja
  if ($("#optionRincian").is(":checked")) {
    $("#option_jenis_belanja").show();
  }
});
$("#optionJenis").click(function(){
  //Radio Check for Belanja
  if ($("#optionJenis").is(":checked")) {
    $("#option_jenis_belanja").hide();
  }
});

//Change Option Jenis Belanja
$('[name="option_jenis_belanja"]').change(function(){
  var id_rekening = $(this).val();
  $.ajax({
      url : "<?php echo base_url('bendahara/rekening/ajax_edit') ?>/" + id_rekening,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="kode_rekening"]').val(data.kode_rekening + ".");

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data jenis belanja from ajax');
      }
  });

})

//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

$('#tambah-rekening').click(function(){
      save_method = 'add';
      $("#optionRincian").prop("checked", true);
      $("#option_jenis_belanja").show();

      $('#form-rekening')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-rekening').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Rekening'); // Set Title to Bootstrap modal title
      $.ajax({
          url : "<?php echo base_url('bendahara/rekening/option_jenis_belanja') ?>",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_jenis_belanja"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data jenis belanja from ajax');
          }
      });
});//end-tambah-rekening

$('#simpan-rekening').click(function(){
  $('#simpan-rekening').text('saving...'); //change button text
  $('#simpan-rekening').attr('disabled',true); //set button disable
  var url;
  var notif;
  if ($("#optionJenis").is(":checked")) {
    $('[name="option_jenis_belanja"]').val('0');
  }

  if(save_method == 'add') {
      url = "<?php echo site_url('bendahara/rekening/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>Kode rekening " + $('[name="kode_rekening"]').val()  + " berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('bendahara/rekening/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>Kode rekening " + $('[name="kode_rekening"]').val()  + " berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-rekening').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-rekening').modal('hide');
              $('#notifikasi').html(notif).addClass('alert alert-info');
              $('#notifikasi').fadeTo(4000, 500).slideUp(500, function(){
                $('#notifikasi').slideUp(500);
              });
              reload_table();
          }
          else
          {
              for (var i = 0; i < data.inputerror.length; i++)
              {
                  $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                  $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
          }
          $('#simpan-rekening').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-rekening').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-rekening').text('save'); //change button text
          $('#simpan-rekening').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-rekening


function edit_rekening(id_rekening)
{
    save_method = 'update';
    $('#form-rekening')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/rekening/ajax_edit')?>/" + id_rekening,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if (data.parent == '0') {
              $("#optionJenis").prop("checked", true);
              $("#option_jenis_belanja").hide();
            } else {
              $("#optionRincian").prop("checked", true);
              $("#option_jenis_belanja").show();
            }

            $('[name="id_rekening"]').val(data.id_rekening);
            $('[name="kode_rekening"]').val(data.kode_rekening);
            $('[name="uraian_rekening"]').val(data.uraian_rekening);
            $('#modal-rekening').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Rekening'); // Set title to Bootstrap modal title
            $.ajax({
                url : "<?php echo base_url('bendahara/rekening/option_jenis_belanja_edit') ?>/" + data.id_rekening,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="option_jenis_belanja"]').html(data);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data jenis belanja from ajax');
                }
            });

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_rekening(id_rekening)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('bendahara/rekening/ajax_edit')?>/" + id_rekening,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus kode rekening' + data.kode_rekening + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/rekening/ajax_delete/')?>/"+id_rekening,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-rekening').modal('hide');
                      reload_table();
                  },
                  error: function (jqXHR, textStatus, errorThrown)
                  {
                      alert('Error deleting data');
                  }
              });

          }

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });

} //end-delete

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}


</script>
</body>
</html>
