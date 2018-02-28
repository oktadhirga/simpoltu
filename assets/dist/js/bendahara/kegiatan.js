
/////////////////////////////////////////////////////////
//////////////////////Kegiatan///////////////////////////
/////////////////////////////////////////////////////////

//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});


$('#simpan-kegiatan').click(function(){
  $('#simpan-kegiatan').text('saving...'); //change button text
  $('#simpan-kegiatan').attr('disabled',true); //set button disable
  var url;
  var notif;

  if(save_method == 'add') {
      url = base_url + "bendahara/kegiatan/ajax_add";
      notif = "<strong><h4>Info!</h4></strong>Kegiatan " + $('[name="nama_kegiatan"]').val()  + " berhasil ditambahkan";
  } else {
      url = base_url + "bendahara/kegiatan/ajax_update";
      notif = "<strong><h4>Info!</h4></strong>Kegiatan " + $('[name="nama_kegiatan"]').val()  + " berhasil diedit";
  }

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-kegiatan').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#modal-kegiatan').modal('hide');
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
          $('#simpan-kegiatan').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-kegiatan').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-kegiatan').text('save'); //change button text
          $('#simpan-kegiatan').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-kegiatan


function edit_kegiatan(id_kegiatan)
{
    save_method = 'update';
    $('#form-kegiatan')[0].reset(); // reset form on modals
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
            $('[name="rekening_kegiatan"]').val(data.rekening_kegiatan);
            $('[name="nama_kpa"]').val(data.nama_kpa);
            $('[name="nip_kpa"]').val(data.nip_kpa);
            $('[name="nama_pptk"]').val(data.nama_pptk);
            $('[name="nip_pptk"]').val(data.nip_pptk);
            $('#modal-kegiatan').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Kegiatan'); // Set title to Bootstrap modal title

            //Ajax Load option from ajax
            $.ajax({
                url : base_url + "bendahara/program/ajax_edit/" + data.id_program,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {
                    $('[name="nama_program"]').val(data.nama_program);
                    $('#rekening').text(data.rekening_program +' .');

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data program from ajax');
                }
            });

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function edit_rekening(id_kegiatan){
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

}

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
