
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
      var selected = [];
      disable_btn_left();
      disable_btn_right();
      disable_input();
      $('#tambah-ls_detail').attr("disabled", false);
      var id_ls = $('[name="id_ls"]').val();
      sum_ls();
      $('#notifikasi').hide();


      table = $('#table').DataTable({
          "paging"    : false,
          "info"      : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/ls_detail/ajax_list')?>/" + id_ls,
              "type": "POST"
          },

          "rowCallback" : function( row, data ) {
                if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                $(row).addClass('selected');
              }
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

          "fnDrawCallback": function() {

                $("#table tbody").on( 'click', 'tr', function () {

                        var id_ls_detail = table.row( this ).data()[0];
                        edit_ls_detail(id_ls_detail);
                });

            }

      }); //end-datatable

      $("#table tbody").on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
                empty_input();
                disable_btn_left();
            } else {
              table.$('tr.selected').removeClass('selected');
              $(this).addClass('selected');

            }
      });

}); //end-document-ready


function sum_ls(){
  var id_ls = $('[name="id_ls"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/ls_detail/sum_ls')?>/" + id_ls,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="jum_ls_detail"]').text(new Intl.NumberFormat('id-ID').format(data));

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  })
};



//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});


//button delete
$('#hapus-ls_detail').on("click", (function(){
  if ($(this).is('[disabled=disabled]') == false) {
      var id_ls_detail = $('[name="id_ls_detail"]').val();
      delete_ls_detail(id_ls_detail);
    };
}));


//button batal
$('#batal-ls_detail').on("click", (function(){
  if ($(this).is('[disabled=disabled]') == false) {
      empty_input();
      disable_input();
      disable_btn_right();
      disable_btn_left();
      $('#tambah-ls_detail').attr("disabled", false);
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $("#table tbody tr").removeClass('selected');
  }
}));


//button tambah
$('#tambah-ls_detail').on("click", (function(){
    if ($(this).is('[disabled=disabled]') == false) {
      save_method = 'add';
      disable_btn_left();
      $("#table tbody tr").removeClass('selected');
      $('#tambah-ls_detail').attr("disabled", true);
      enable_input();
      enable_btn_right();
      $('#form-ls_detail')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $.ajax({
          url : "<?php echo base_url('bendahara/ls_detail/option_rekening')?>",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_rekening"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });
    }
}));//end-tambah-ls_detail

$('#edit-ls_detail').on("click", function(){
    if ($(this).is('[disabled=disabled]') == false) {
      enable_input();
      enable_btn_right();
      disable_btn_left();
    }
});

$('#simpan-ls_detail').click(function(){
  if ($(this).is('[disabled=disabled]') == false) {
      $('#simpan-ls_detail').text('saving...'); //change button text
      $('#simpan-ls_detail').attr('disabled',true); //set button disable
      var url;
      var notif;
      var nilai_ls_detail = $('[name="nilai_ls_detail"]').val().replace(/[^0-9]/g, '');
      $('[name="nilai_ls_detail"]').val(nilai_ls_detail);

      if(save_method == 'add') {
          url = "<?php echo site_url('bendahara/ls_detail/ajax_add')?>";
          notif = "<strong><h4>Info!</h4></strong>ls kegiatan berhasil ditambahkan";
      } else {
          url = "<?php echo site_url('bendahara/ls_detail/ajax_update')?>";
          notif = "<strong><h4>Info!</h4></strong>ls kegiatan berhasil diedit";
      }

      // ajax adding data to database
      $.ajax({
          url : url,
          type: "POST",
          data: $('#form-ls_detail').serialize(),
          dataType: "JSON",
          success: function(data)
          {

              if(data.status) //if success close modal and reload ajax table
              {
                  $('#notifikasi').html(notif).addClass('alert alert-info');
                  $('#notifikasi').fadeTo(4000, 500).slideUp(500, function(){
                      $('#notifikasi').slideUp(500);
                  });
                  disable_btn_left();
                  disable_btn_right();
                  disable_input();
                  empty_input();
                  $('#tambah-ls_detail').attr("disabled", false);
                  $('.form-group').removeClass('has-error'); // clear error class
                  $('.help-block').empty(); // clear error string
                  reload_table();
                  sum_ls();
              }
              else
              {

                  for (var i = 0; i < data.inputerror.length; i++)
                  {
                      $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                      $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                  }
                  $('#simpan-ls_detail').attr("disabled", false);
              }
              $('#simpan-ls_detail').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text


          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error add / update data');
              $('#simpan-ls_detail').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
              $('#simpan-ls_detail').attr('disabled',false); //set button enable

          }
        });
    }
  }); //end-simpan-ls_detail


function edit_ls_detail(id_ls_detail)
{
    save_method = 'update';
    disable_input();
    disable_btn_right();
    enable_btn_left();
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/ls_detail/ajax_edit')?>/" + id_ls_detail,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_ls_detail"]').val(data.id_ls_detail);
            $('[name="nilai_ls_detail"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_ls_detail));
            $('[name="ket_ls_detail"]').val(data.ket_ls_detail);

            //get rekening
            $.ajax({
                url : "<?php echo base_url('bendahara/ls_detail/option_rekening_edit')?>/"+ data.id_rekening,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="option_rekening"]').html(data);

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

function delete_ls_detail(id_ls_detail)
{

          if(confirm('Anda yakin akan menghapus ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/ls_detail/ajax_delete/')?>/"+id_ls_detail,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table

                      reload_table();
                      empty_input();
                      disable_input();
                      disable_btn_right();
                      disable_btn_left();
                      $('#tambah-ls_detail').attr("disabled", false);
                      $('.form-group').removeClass('has-error'); // clear error class
                      $('.help-block').empty(); // clear error string
                      sum_ls();

                  },
                  error: function (jqXHR, textStatus, errorThrown)
                  {
                      alert('Error deleting data');
                  }
              });

          }

} //end-delete

//Amout format
$('[name="nilai_ls_detail"]').on( "keyup", function( event ) {


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

function enable_input() {
  $('[name="option_rekening"]').prop("disabled", false);
  $('[name="nilai_ls_detail"]').prop("disabled", false);
  $('[name="ket_ls_detail"]').prop("disabled", false);
}

function empty_input() {
  $('[name="option_rekening"]').html('');
  $('[name="nilai_ls_detail"]').val('');
  $('[name="ket_ls_detail"]').val('');
}

function disable_input() {
  $('[name="option_rekening"]').prop("disabled", true);
  $('[name="nilai_ls_detail"]').prop("disabled", true);
  $('[name="ket_ls_detail"]').prop("disabled", true);
}

function disable_btn_left() {
  $('#tambah-ls_detail').attr("disabled", true);
  $('#edit-ls_detail').attr("disabled", true);
  $('#hapus-ls_detail').attr("disabled", true);
}

function enable_btn_left() {
  $('#tambah-ls_detail').removeAttr("disabled");
  $('#edit-ls_detail').removeAttr("disabled");
  $('#hapus-ls_detail').removeAttr("disabled");
}

function disable_btn_right() {
  $('#simpan-ls_detail').attr("disabled", true);
  $('#batal-ls_detail').attr("disabled", true);
}

function enable_btn_right() {
  $('#simpan-ls_detail').removeAttr("disabled");
  $('#batal-ls_detail').removeAttr("disabled");
}



</script>
</body>
</html>
