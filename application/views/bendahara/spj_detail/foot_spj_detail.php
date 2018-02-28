
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
      $('#tambah-spj_detail').attr("disabled", false);
      var id_spj = $('[name="id_spj"]').val();
      cek_status();
      sum_spj();
      sisa_spj();
      $('#notifikasi').hide();
      //cek_status

      table = $('#table').DataTable({
          "paging"    : false,
          "info"      : false,
          "processing": true, //Feature control the processing indicator.
          "serverSide": true, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.

          // Load data for the table's content from an Ajax source
          "ajax": {
              "url": "<?php echo base_url('bendahara/spj_detail/ajax_list')?>/" + id_spj,
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

                        var id_spj_detail = table.row( this ).data()[0];
                        edit_spj_detail(id_spj_detail);
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


function cek_status(){
  var id_spj = $('[name="id_spj"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/spj/cek_status')?>/" + id_spj,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
          if (data.isVerified == 1) {
            $('#form-spj_detail').hide();
            table_pajak.column( 4 ).visible( false );
          }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  })
}

function status(){
  var id_spj = $('[name="id_spj"]').val();
  var status = true;
  $.ajax({
      url : "<?php echo base_url('bendahara/spj/cek_status')?>/" + id_spj,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
        if (data.isVerified == 1) {
          status = false;
        }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  })

  return status;
}


function sum_spj(){
  var id_spj = $('[name="id_spj"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/spj_detail/sum_spj')?>/" + id_spj,
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
  var id_spj = $('[name="id_spj"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/spj_detail/sisa_spj')?>/" + id_spj,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

          $('[name="sisa_panjar"]').text(new Intl.NumberFormat('id-ID').format(data));

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
$('#hapus-spj_detail').on("click", (function(){
  if ($(this).is('[disabled=disabled]') == false) {
      var id_spj_detail = $('[name="id_spj_detail"]').val();
      delete_spj_detail(id_spj_detail);
    };
}));


//button batal
$('#batal-spj_detail').on("click", (function(){
  if ($(this).is('[disabled=disabled]') == false) {
      empty_input();
      disable_input();
      disable_btn_right();
      disable_btn_left();
      $('#tambah-spj_detail').attr("disabled", false);
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $("#table tbody tr").removeClass('selected');
  }
}));


//button tambah
$('#tambah-spj_detail').on("click", (function(){
    if ($(this).is('[disabled=disabled]') == false) {
      save_method = 'add';
      disable_btn_left();
      $("#table tbody tr").removeClass('selected');
      $('#tambah-spj_detail').attr("disabled", true);
      enable_input();
      enable_btn_right();
      $('#form-spj_detail')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $.ajax({
          url : "<?php echo base_url('bendahara/spj_detail/option_uraian_rekening')?>",
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
}));//end-tambah-spj_detail

$('#edit-spj_detail').on("click", function(){
    if ($(this).is('[disabled=disabled]') == false) {
      enable_input();
      enable_btn_right();
      disable_btn_left();
    }
});

$('#simpan-spj_detail').click(function(){
  if ($(this).is('[disabled=disabled]') == false) {
      $('#simpan-spj_detail').text('saving...'); //change button text
      $('#simpan-spj_detail').attr('disabled',true); //set button disable
      var url;
      var notif;
      var nilai_spj = $('[name="nilai_spj"]').val().replace(/[^0-9]/g, '');
      $('[name="nilai_spj"]').val(nilai_spj);

      if(save_method == 'add') {
          url = "<?php echo site_url('bendahara/spj_detail/ajax_add')?>";
          notif = "<strong><h4>Info!</h4></strong>SPJ kegiatan berhasil ditambahkan";
      } else {
          url = "<?php echo site_url('bendahara/spj_detail/ajax_update')?>";
          notif = "<strong><h4>Info!</h4></strong>SPJ kegiatan berhasil diedit";
      }

      // ajax adding data to database
      $.ajax({
          url : url,
          type: "POST",
          data: $('#form-spj_detail').serialize(),
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
                  $('#tambah-spj_detail').attr("disabled", false);
                  $('.form-group').removeClass('has-error'); // clear error class
                  $('.help-block').empty(); // clear error string
                  reload_table();
                  sum_spj();
                  sisa_spj();
              }
              else
              {

                  for (var i = 0; i < data.inputerror.length; i++)
                  {
                      $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                      $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                  }
                  $('#simpan-spj_detail').attr("disabled", false);
              }
              $('#simpan-spj_detail').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text


          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error add / update data');
              $('#simpan-spj_detail').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
              $('#simpan-spj_detail').attr('disabled',false); //set button enable

          }
        });
    }
  }); //end-simpan-spj_detail


function edit_spj_detail(id_spj_detail)
{
    save_method = 'update';
    disable_input();
    disable_btn_right();
    enable_btn_left();
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('bendahara/spj_detail/ajax_edit')?>/" + id_spj_detail,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_spj_detail"]').val(data.id_spj_detail);
            $('[name="nilai_spj"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_spj));
            $('[name="no_spj_detail"]').val(data.no_spj_detail);
            $('[name="tgl_spj_detail"]').val(data.tglspjdetail);
            $('[name="ket_spj_detail"]').val(data.ket_spj_detail);


            //get rekening
            $.ajax({
                url : "<?php echo base_url('bendahara/spj_detail/option_uraian_rekening_edit')?>/"+ data.id_rekening,
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

function delete_spj_detail(id_spj_detail)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : "<?php echo base_url('bendahara/spj_detail/ajax_edit')?>/" + id_spj_detail,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus no. bukti ' + data.no_spj_detail + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : "<?php echo base_url('bendahara/spj_detail/ajax_delete/')?>/"+id_spj_detail,
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
                      $('#tambah-spj_detail').attr("disabled", false);
                      $('.form-group').removeClass('has-error'); // clear error class
                      $('.help-block').empty(); // clear error string
                      sum_spj();
                      sisa_spj();

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

$('[name="nilai_pajak"]').on( "keyup", function( event ) {


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

function reload_table_pajak() {
  table_pajak.ajax.reload(null,false); //reload datatable ajax
}

function enable_input() {
  $('[name="option_rekening"]').prop("disabled", false);
  $('[name="nilai_spj"]').prop("disabled", false);
  $('[name="no_spj_detail"]').prop("disabled", false);
  $('[name="tgl_spj_detail"]').prop("disabled", false);
  $('[name="ket_spj_detail"]').prop("disabled", false);
}

function empty_input() {
  $('[name="option_rekening"]').html('');
  $('[name="nilai_spj"]').val('');
  $('[name="no_spj_detail"]').val('');
  $('[name="tgl_spj_detail"]').val('');
  $('[name="ket_spj_detail"]').val('');
}

function disable_input() {
  $('[name="option_rekening"]').prop("disabled", true);
  $('[name="nilai_spj"]').prop("disabled", true);
  $('[name="no_spj_detail"]').prop("disabled", true);
  $('[name="tgl_spj_detail"]').prop("disabled", true);
  $('[name="ket_spj_detail"]').prop("disabled", true);
}

function disable_btn_left() {
  $('#tambah-spj_detail').attr("disabled", true);
  $('#edit-spj_detail').attr("disabled", true);
  $('#hapus-spj_detail').attr("disabled", true);
}

function enable_btn_left() {
  $('#tambah-spj_detail').removeAttr("disabled");
  $('#edit-spj_detail').removeAttr("disabled");
  $('#hapus-spj_detail').removeAttr("disabled");
}

function disable_btn_right() {
  $('#simpan-spj_detail').attr("disabled", true);
  $('#batal-spj_detail').attr("disabled", true);
}

function enable_btn_right() {
  $('#simpan-spj_detail').removeAttr("disabled");
  $('#batal-spj_detail').removeAttr("disabled");
}

function input_pajak(id_spj_detail){
  $('[name="id_spj_detail"]').val(id_spj_detail);
  table_pajak = $('#table-pajak').DataTable({
      "bDestroy"  : true,
      "searching" : false,
      "paging" : false,
      "info": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "<?php echo base_url('bendahara/spj_detail/pajak_list') ?>/" + id_spj_detail,
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
                    "zeroRecords": "Pajak Masih Kosong"
                  },

  }); //end-datatable

  $('#form-pajak')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  $('#modal-pajak').modal('show'); // show bootstrap modal
  var id_spj = $('[name="id_spj"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/spj/cek_status')?>/" + id_spj,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {
          if (data.isVerified == 1) {
            table_pajak.column( 4 ).visible( false );
            $('#form-pajak').hide();
            $('#simpan-pajak').hide();
          }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
  })
}


$(document).on('change', '[name="option_pajak"]', function(){
  var pajak = $(this).val();
  var id_spj_detail = $('[name="id_spj_detail"]').val();
  $.ajax({
      url : "<?php echo base_url('bendahara/spj_detail/get_pajak') ?>",
      type: "POST",
      data : {
                'id_spj_detail' : id_spj_detail,
                'pajak'         : pajak
            },
      dataType: "JSON",
      success: function(data)
      {
        if (data.id_spj_detail == null) {
          metode = 'add';
          $('[name="nilai_pajak"]').val('');
          $('[name="tgl_setor_pajak"]').val('');
        } else {
          metode = 'update';
          $('[name="id_pajak"]').val(data.id);
          $('[name="nilai_pajak"]').val(new Intl.NumberFormat('id-ID').format(data.nilai_pajak));
          $('[name="tgl_setor_pajak"]').val(data.tgl_setor_pajak);
        }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data rekening from ajax');
      }
  });
})

$('#simpan-pajak').click(function(){
      $('#simpan-pajak').text('saving...'); //change button text
      $('#simpan-pajak').attr('disabled',true); //set button disable
      var url;
      var nilai_pajak = $('[name="nilai_pajak"]').val().replace(/[^0-9]/g, '');
      $('[name="nilai_pajak"]').val(nilai_pajak);

      if(metode == 'add') {
          url = "<?php echo site_url('bendahara/spj_detail/pajak_add')?>";
      } else {
          url = "<?php echo site_url('bendahara/spj_detail/pajak_update')?>";
      }

      // ajax adding data to database
      $.ajax({
          url : url,
          type: "POST",
          data: $('#form-pajak').serialize(),
          dataType: "JSON",
          success: function(data)
          {

              if(data.status) //if success close modal and reload ajax table
              {
                  $('#notifikasi-pajak').html(data.notif).addClass('alert alert-info');
                  $('#notifikasi-pajak').fadeTo(4000, 500).slideUp(500, function(){
                      $('#notifikasi-pajak').slideUp(500);
                  });
                  $('.form-group').removeClass('has-error'); // clear error class
                  $('.help-block').empty(); // clear error string
                  reload_table_pajak();
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
              $('#simpan-pajak').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
              $('#simpan-pajak').attr("disabled", false);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error add / update data');
              $('#simpan-pajak').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
              $('#simpan-pajak').attr('disabled',false); //set button enable

          }
        });
  }); //end-simpan-pajak

  function hapus_pajak(id)
  {


    if(confirm('Anda yakin akan menghapus ?'))
    {
        // ajax delete data to database
        $.ajax({
            url : "<?php echo base_url('bendahara/spj_detail/pajak_delete/')?>/" + id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                //if success reload ajax table

                reload_table_pajak();
                reload_table();
                $('[name="option_pajak"]').val('');
                $('[name="nilai_pajak"]').val('');
                $('[name="tgl_setor_pajak"]').val('');
                $('.form-group').removeClass('has-error'); // clear error class
                $('.help-block').empty(); // clear error string

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }


  } //end-delete





</script>
</body>
</html>
