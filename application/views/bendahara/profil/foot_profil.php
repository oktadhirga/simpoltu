
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
              "url": "<?php echo base_url('admin/user/ajax_list')?>",
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

$('#tambah-user').click(function(){
      save_method = 'add';
      $('#form-user')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-user').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah User'); // Set Title to Bootstrap modal title

});//end-tambah-user

$('#simpan-user').click(function(){
  $('#simpan-user').text('saving...'); //change button text
  $('#simpan-user').attr('disabled',true); //set button disable
  var url;
  var notif;

  if(save_method == 'add') {
      url = "<?php echo site_url('admin/user/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>User " + $('[name="nama_user"]').val()  + " berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('admin/user/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>User " + $('[name="nama_user"]').val()  + " berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-user').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-user').modal('hide');
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
          $('#simpan-user').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-user').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-user').text('save'); //change button text
          $('#simpan-user').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-user


function edit_user(id_user)
{
    save_method = 'update';
    $('#form-user')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('admin/user/ajax_edit')?>/" + id_user,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_user"]').val(data.id_user);
            $('[name="nama_user"]').val(data.nama_user);
            $('[name="nip_user"]').val(data.nip_user);
            $('[name="username"]').val(data.username);
            $('[name="password"]').val(data.password);
            $('[name="akses_level"]').val(data.akses_level);
            $('#modal-user').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit User'); // Set title to Bootstrap modal title


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_user(id_user)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('admin/user/ajax_edit')?>/" + id_user,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus User ' + data.nama_user + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('admin/user/ajax_delete/')?>/"+id_user,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-user').modal('hide');
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
