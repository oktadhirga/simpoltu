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
          "rowId": 'id_spj_detail',
          "select": true,
          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/spj/ajax_list')?>/" + id_panjar,
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
                        "zeroRecords": "Anda belum memasukkan SPJ, silahkan <button class='btn btn-sm btn-flat' onclick='tambah_spj()'>Input SPJ</button>"
                      },

      }); //end-datatable

}); //end-document-ready

//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

// $('#tambah-spj').click(function(){
function tambah_spj(){
      save_method = 'add';
      $('#form-spj')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-spj').modal('show'); // show bootstrap modal
      $('.modal-title').text('Input SPJ'); // Set Title to Bootstrap modal title
};//end-tambah-spj

$('#simpan-spj').click(function(){
  $('#simpan-spj').text('saving...'); //change button text
  $('#simpan-spj').attr('disabled',true); //set button disable
  var url;
  var notif;

  if(save_method == 'add') {
      url = "<?php echo site_url('bendahara/spj/ajax_add')?>";
      notif = "<strong><h4>Info!</h4></strong>SPJ kegiatan berhasil ditambahkan";
  } else {
      url = "<?php echo site_url('bendahara/spj/ajax_update')?>";
      notif = "<strong><h4>Info!</h4></strong>SPJ kegiatan berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-spj').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-spj').modal('hide');
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
          $('#simpan-spj').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-spj').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-spj').text('save'); //change button text
          $('#simpan-spj').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-spj


function edit_spj(id_spj)
{
    save_method = 'update';
    $('#form-spj')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/spj/ajax_edit')?>/" + id_spj,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_spj"]').val(data.id_spj);
            $('[name="no_spj"]').val(data.no_spj);
            $('[name="tgl_spj"]').val(data.tglspj);
            $('[name="nilai_spj"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_spj));
            $('[name="ket_spj"]').val(data.ket_spj);
            $('#modal-spj').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit SPJ'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_spj(id_spj)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('bendahara/spj/ajax_edit')?>/" + id_spj,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus ' + data.ket_spj + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/spj/ajax_delete/')?>/"+id_spj,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-spj').modal('hide');
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
$('[name="nilai_spj"]').on( "keyup", function( event ) {


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
