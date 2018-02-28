
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url();?>assets/plugins/select2/select2.full.min.js"></script>

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
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>

<script>
var base_url = '<?php echo base_url() ?>';
$(document).ready(function() {
      //datatables
      $('#notifikasi').hide();
      var id_kegiatan = $('[name="id_kegiatan"]').val();
      table = $('#table').DataTable({
          "ordering" : false,
          "paging" : false,
          "info" : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/anggaran/ajax_list')?>/" + id_kegiatan,
              "type": "POST"
          },

          //Set column definition initialisation properties.
          "columnDefs": [
          {
              "targets": [ -1 ], //last column
              "orderable": false, //set not orderable
          },
          { "targets": [4], visible: false},
          { className: "text-right", "targets": [3] }
          ],


          "language": {
                        "zeroRecords": "Data Anggaran Kosong"
                      },

          "rowCallback": function( row, data, index ) {
            if ( data[4] == "0" ) {
              $(row).css({'color': 'white', 'background-color': '#9E9E9E', 'cursor' : 'not-allowed'}); // You can use hex code as well
            }
          },

      }); //end-datatable

}); //end-document-ready

$('#edit-rekening').click(function(){
  var id_kegiatan = $('[name="id_kegiatan"]').val();
  save_method = 'update';
  $('#form-rekening')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  //Ajax Load data from ajax
  $.ajax({
      url : base_url + "bendahara/kegiatan/ajax_edit/" + id_kegiatan,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="id_kegiatan"]').val(data.id_kegiatan);
          $('[name="nama_kegiatan"]').val(data.nama_kegiatan);
          $('#modal-rekening').modal('show'); // show bootstrap modal when complete loaded
          $('.modal-title').text('Edit Anggaran Belanja'); // Set title to Bootstrap modal title

          //Ajax Load option from ajax
          $.ajax({
              url : base_url + "bendahara/spj_detail/option_rekening",
              type: "GET",
              dataType: "JSON",
              success: function(data)
              {

                  $('[name="option_rekening"]').html(data);

              },
              error: function (jqXHR, textStatus, errorThrown)
              {
                  alert('Error get data rekening from ajax');
              }
          });

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  });

});

$(document).on('change', '[name="option_rekening"]', function(){
// $('[name="option_rekening"]').change(function(){
  if($("option:selected", this).hasClass('parent')){
      $('[name="jumlah_anggaran"]').prop("readonly", true);
      $('#simpan-rekening').hide();
  } else {
      $('[name="jumlah_anggaran"]').prop("readonly", false);
      $('#simpan-rekening').show();
  }

  var id_rekening = $(this).val();
  var id_kegiatan = $('[name="id_kegiatan"]').val();
  $.ajax({
      url : base_url + "bendahara/kegiatan/get_rekening_max/" + id_kegiatan + "/" + id_rekening,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
        if (data.id_max == null) {
          metode = 'add';
        } else {
          metode = 'update';
          $('[name="id_max"]').val(data.id_max);
        }

          $('[name="jumlah_anggaran"]').val(new Intl.NumberFormat('id-ID').format(data.jumlah));


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data rekening from ajax');
      }
  });
})

$('#simpan-rekening').click(function(){
  $('#simpan-rekening').text('saving...'); //change button text
  $('#simpan-rekening').attr('disabled',true); //set button disable
  var url;
  var jumlah_anggaran = $('[name="jumlah_anggaran"]').val().replace(/[^0-9]/g, '');
  $('[name="jumlah_anggaran"]').val(jumlah_anggaran);

  if(metode == 'add') {
      url = base_url + "bendahara/kegiatan/ajax_max_add";
  } else {
      url = base_url + "bendahara/kegiatan/ajax_max_update";
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
              $('#notifikasi2').html(data.notif).addClass('alert alert-info');
              $('#notifikasi2').fadeTo(4000, 500).slideUp(500, function(){
                $('#notifikasi2').slideUp(500);
              });
              jumlah_anggaran =  $('[name="jumlah_anggaran"]').val();
            $('[name="jumlah_anggaran"]').val(new Intl.NumberFormat('id-ID').format(jumlah_anggaran));;
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
          $('#simpan-rekening').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
          $('#simpan-rekening').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-rekening').text('Simpan'); //change button text
          $('#simpan-rekening').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-kegiatan


$('[name="jumlah_anggaran"]').on( "keyup", function( event ) {
    // When user select text in the document, also abort.
    if ( window.getSelection().toString() !== '' ) {
        return;
    }

    // When the arrow keys are pressed, abort.
    if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
        return;
    }


    var $this = $( this );

    // Get the value.
    var masukan = $this.val();

    var masukan = masukan.replace(/[\D\s\._\-]+/g, "");
            masukan = masukan ? parseInt( masukan, 10 ) : 0;

            $this.val( function() {
                return ( masukan === 0 ) ? "" : masukan.toLocaleString( "id-ID" );
            } );
} );

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}

function delete_anggaran(id_max){
  if(confirm('Anda yakin akan menghapus data ini ?'))
  {
      // ajax delete data to database
      $.ajax({
          url : "<?php echo base_url('bendahara/anggaran/ajax_delete/')?>"+id_max,
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
}

</script>
</body>
</html>
