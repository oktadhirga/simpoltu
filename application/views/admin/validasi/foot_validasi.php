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
<!-- BendaharaLTE App -->
<script src="<?php echo base_url();?>assets/dist/js/app.min.js"></script>
<!-- BendaharaLTE for demo purposes -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url();?>assets/plugins/select2/select2.full.min.js"></script>

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

$(document).ready(function() {
      //datatables
      $('#notifikasi').hide();
      table = $('#table').DataTable({
          "bInfo" : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('admin/validasi/ajax_list')?>",
              "type": "POST"
          },

          //Set column definition initialisation properties.
          "columnDefs": [
          {
              "targets": [ -1 ], //last column
              "orderable": false, //set not orderable
          },
              { "targets": [7], visible: false},
          ],


          "rowCallback": function( row, data, index ) {
            if ( data[7] == "1" ) {
              $(row).css('color', '#008D4C'); // You can use hex code as well
            }
          }



      }); //end-datatable

      //show program
      $.ajax({
          url : "<?php echo base_url('admin/validasi/option_program')?>",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_program"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });

}); //end-document-ready


$('[name="option_program"]').change(function(){
  var url;
  var id_program = $(this).val();
  if (id_program == -1) {
    var url = "<?php echo base_url('admin/validasi/ajax_list')?>";
  } else {
    var url = "<?php echo base_url('admin/validasi/ajax_list_filter')?>/" + id_program;
  }

  table.destroy();

  table = $('#table').DataTable({
      "columns": [
        null, null, null, null, null, null, null, null,
        { "width": "150px" }
      ],
      "bInfo" : false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": url,
          "type": "POST"
      },

      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [ -1 ], //last column
          "orderable": false, //set not orderable
      }, { "targets": [7], visible: false},
      ],
      "rowCallback": function( row, data, index ) {
        if ( data[7] == "1" ) {
          $(row).css('color', '#008D4C'); // You can use hex code as well
        }
      }
  }); //end-datatable

});


//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

$('#tambah-panjar').click(function(){
      save_method = 'add';
      $('#form-panjar')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-panjar').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Panjar'); // Set Title to Bootstrap modal title
      $.ajax({
          url : "<?php echo base_url('admin/validasi/option_kegiatan')?>",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_kegiatan"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });
});//end-tambah-panjar

$('#simpan-panjar').click(function(){
  $('#simpan-panjar').text('saving...'); //change button text
  $('#simpan-panjar').attr('disabled',true); //set button disable
  var url;
  var notif;
  var number = $('[name="nilai_panjar"]').val().replace(/[^0-9]/g, '');
  $('[name="nilai_panjar"]').val(number);

  if(save_method == 'add') {
      url = "<?php echo site_url('admin/validasi/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>Panjar kegiatan berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('admin/validasi/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>Panjar kegiatan berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-panjar').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-panjar').modal('hide');
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
          $('#simpan-panjar').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-panjar').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-panjar').text('save'); //change button text
          $('#simpan-panjar').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-panjar


function edit_panjar(id_panjar)
{
    save_method = 'update';
    $('#form-panjar')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('admin/validasi/ajax_edit')?>/" + id_panjar,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_panjar"]').val(data.id_panjar);
            $('[name="no_bukti"]').val(data.no_bukti);
            $('[name="tgl_bukti"]').val(data.tglbukti);
            $('[name="nilai_panjar"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_panjar));
            $('[name="ket_panjar"]').val(data.ket_panjar);
            $('#modal-panjar').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Panjar'); // Set title to Bootstrap modal title
            $.ajax({
                url : "<?php echo base_url('admin/validasi/option_kegiatan_edit')?>/"+ data.id_kegiatan,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="option_kegiatan"]').html(data);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_panjar(id_panjar)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('admin/validasi/ajax_edit')?>/" + id_panjar,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus panjar untuk ' + data.ket_panjar + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('admin/validasi/ajax_delete/')?>/"+id_panjar,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-panjar').modal('hide');
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

function validasi_panjar(id_panjar)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('admin/validasi/ajax_edit')?>/" + id_panjar,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan memverifikasi ' + data.ket_panjar + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('admin/validasi/ajax_update_verifikasi/')?>/"+id_panjar,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-panjar').modal('hide');
                      reload_table();
                  },
                  error: function (jqXHR, textStatus, errorThrown)
                  {
                      alert('Error Verification Data');
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
$('[name="nilai_panjar"]').on( "keyup", function( event ) {


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
