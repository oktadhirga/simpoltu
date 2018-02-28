
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
      var id_panjar = '<?php echo $id_panjar ?>';
      $('#notifikasi').hide();
      table = $('#table').DataTable({
          "paging":   false,
          "ordering": false,
          "info":     false,
          "filter"  : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/pengembalian/ajax_list')?>/" + id_panjar,
              "type": "POST"
          },

          //Set column definition initialisation properties.
          "columnDefs": [
          {
              "targets": [ -1 ], //last column
              "orderable": false, //set not orderable
          },
          ],
          "language": {
                        "zeroRecords": "Anda belum memasukkan Pengembalian, silahkan <button class='btn btn-sm btn-flat' onclick='tambah_pengembalian()'>Input Pengembalian</button>"
                      },

      }); //end-datatable

}); //end-document-ready


//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

function tambah_pengembalian(){
      var id_panjar = '<?php echo $id_panjar ?>';
      save_method = 'add';
      $('#form-pengembalian')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-pengembalian').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Pengembalian Panjar'); // Set Title to Bootstrap modal title
      //hitung sisa
      hitung_sisa(id_panjar);
};//end-tambah-pengembalian

$('#simpan-pengembalian').click(function(){
  $('#simpan-pengembalian').text('saving...'); //change button text
  $('#simpan-pengembalian').attr('disabled',true); //set button disable
  var url;
  var notif;
  var number = $('[name="nilai_pengembalian"]').val().replace(/[^0-9]/g, '');
  $('[name="nilai_pengembalian"]').val(number);

  if(save_method == 'add') {
      url = "<?php echo site_url('bendahara/pengembalian/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>Panjar kegiatan berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('bendahara/pengembalian/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>Panjar kegiatan berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-pengembalian').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-pengembalian').modal('hide');
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
          $('#simpan-pengembalian').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-pengembalian').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data pengembalian');
          $('#simpan-pengembalian').text('save'); //change button text
          $('#simpan-pengembalian').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-pengembalian


function edit_pengembalian(id_pengembalian)
{
    var id_panjar = '<?php echo $id_panjar ?>';
    save_method = 'update';
    $('#form-pengembalian')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/pengembalian/ajax_edit')?>/" + id_pengembalian,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_pengembalian"]').val(data.id_pengembalian);
            $('[name="no_pengembalian"]').val(data.no_pengembalian);
            $('[name="tgl_pengembalian"]').val(data.tglpengembalian);
            $('[name="nilai_pengembalian"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_pengembalian));
            hitung_sisa(id_panjar);
            $('[name="ket_pengembalian"]').val(data.ket_pengembalian);
            $('#modal-pengembalian').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Panjar'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_pengembalian(id_pengembalian)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('bendahara/pengembalian/ajax_edit')?>/" + id_pengembalian,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus ' + data.ket_pengembalian + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/pengembalian/ajax_delete/')?>/"+id_pengembalian,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-pengembalian').modal('hide');
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
$('[name="nilai_pengembalian"]').on( "keyup", function( event ) {


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

function hitung_sisa(id_panjar){
  $.ajax({
      url : "<?php echo base_url('bendahara/pengembalian/hitung_sisa')?>/" + id_panjar,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
        if(confirm('Sisa panjar pada SPJ Kegiatan ini adalah ' + new Intl.NumberFormat('id-ID').format(data)  + '.\n Anda ingin menggunakan jumlah tersebut?'))
        {

            $('[name="nilai_pengembalian"]').val(new Intl.NumberFormat('id-ID').format(data));

        }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  });
}

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}


</script>
</body>
</html>
