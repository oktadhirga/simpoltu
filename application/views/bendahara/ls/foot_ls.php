
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
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

$(document).ready(function() {
      //datatables
      $('#notifikasi').hide();
      table = $('#table').DataTable({
          "paging"  : false,
          "info" : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/ls/ajax_list')?>",
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

$('#tambah-ls').click(function(){
      save_method = 'add';
      $('#form-ls')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-ls').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah LS'); // Set Title to Bootstrap modal title
      $.ajax({
          url : "<?php echo base_url('bendahara/ls/option_kegiatan')?>",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_kegiatan"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data kegiatan from ajax');
          }
      });
});//end-tambah-ls

$('#simpan-ls').click(function(){
  $('#simpan-ls').text('saving...'); //change button text
  $('#simpan-ls').attr('disabled',true); //set button disable
  var url;
  var number = $('[name="nilai_ls"]').val().replace(/[^0-9]/g, '');
  $('[name="nilai_ls"]').val(number);
  if(save_method == 'add') {
      url = "<?php echo site_url('bendahara/ls/ajax_add')?>";
  } else {
      url = "<?php echo site_url('bendahara/ls/ajax_update')?>";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-ls').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-ls').modal('hide');
              $('#notifikasi').html(data.notif).addClass('alert alert-info');
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
          $('#simpan-ls').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-ls').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-ls').text('save'); //change button text
          $('#simpan-ls').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-ls


function edit_ls(id_ls)
{
    save_method = 'update';
    $('#form-ls')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/ls/ajax_edit')?>/" + id_ls,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_ls"]').val(data.id_ls);
            $('[name="no_ls"]').val(data.no_ls);
            $('[name="tgl_ls"]').val(data.tgl_ls);
            $('[name="nilai_ls"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_ls));
            $('[name="ket_ls"]').val(data.ket_ls);
            $('#modal-ls').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit LS'); // Set title to Bootstrap modal title
            $.ajax({
                url : "<?php echo base_url('bendahara/ls/option_kegiatan_edit')?>/"+ data.id_kegiatan,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="option_kegiatan"]').html(data);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data kegiatan from ajax');
                }
            });

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_ls(id_ls)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('bendahara/ls/ajax_edit')?>/" + id_ls,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus ' + data.ket_ls + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/ls/ajax_delete/')?>/"+id_ls,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-ls').modal('hide');
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

//Amout format
$('[name="nilai_ls"]').on( "keyup", function( event ) {


    // When user select text in the document, also abort.
    var selection = window.getSelection().toString();
    if ( selection !== '' ) {
        return;
    }

    // When the arrow keys are pressed, abort.
    if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
        return;
    }


    var $this = $( this );

    // Get the value.
    var input = $this.val();

    var input = input.replace(/[\D\s\._\-]+/g, "");
            input = input ? parseInt( input, 10 ) : 0;

            $this.val( function() {
                return ( input === 0 ) ? "" : input.toLocaleString( "id-ID" );
            } );
} );


function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}


</script>
</body>
</html>
