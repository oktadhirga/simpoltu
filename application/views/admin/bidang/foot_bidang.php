<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url();?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
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
              "url": "<?php echo base_url('admin/bidang/ajax_list')?>",
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


//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

$('#tambah-bidang').click(function(){
      save_method = 'add';
      $('#form-bidang')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-bidang').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Bidang'); // Set Title to Bootstrap modal title
});//end-tambah-bidang

$('#simpan-bidang').click(function(){
  $('#simpan-bidang').text('saving...'); //change button text
  $('#simpan-bidang').attr('disabled',true); //set button disable
  var url;
  var notif;

  if(save_method == 'add') {
      url = "<?php echo site_url('admin/bidang/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>Data " + $('[name="nama_bidang"]').val()  + " berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('admin/bidang/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>Data " + $('[name="nama_bidang"]').val()  + " berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-bidang').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-bidang').modal('hide');
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
          $('#simpan-bidang').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-bidang').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-bidang').text('save'); //change button text
          $('#simpan-bidang').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-bidang


function edit_bidang(id_bidang)
{
    save_method = 'update';
    $('#form-bidang')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('admin/bidang/ajax_edit')?>/" + id_bidang,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_bidang"]').val(data.id_bidang);
            $('[name="nama_bidang"]').val(data.nama_bidang);
            $('[name="akronim"]').val(data.akronim);
            $('[name="kepala_bidang"]').val(data.kepala_bidang);
            $('#modal-bidang').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Bidang'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_bidang(id_bidang)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('admin/bidang/ajax_edit')?>/" + id_bidang,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus ' + data.nama_bidang + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('admin/bidang/ajax_delete/')?>/"+id_bidang,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-bidang').modal('hide');
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
