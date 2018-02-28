
/////////////////////////////////////////////////////////
//////////////////////Kegiatan///////////////////////////
/////////////////////////////////////////////////////////

//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

$('#tambah-kegiatan').click(function(){
      save_method = 'add';
      $('#form-kegiatan')[0].reset(); // reset form on modals
      $('.form-group').removeClass('has-error'); // clear error class
      $('.help-block').empty(); // clear error string
      $('#modal-kegiatan').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tambah Kegiatan'); // Set Title to Bootstrap modal title
      //Ajax Load program from ajax
      $.ajax({
          url : base_url + "admin/kegiatan/option_program",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_program"]').html(data);
              change_rekening();

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error program data from ajax');
          }
      });
      //Ajax Load user from ajax
      $.ajax({
          url : base_url + "admin/kegiatan/option_user",
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {

              $('[name="option_user"]').html(data);

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });
});//end-tambah-kegiatan

$('#simpan-kegiatan').click(function(){
  $('#simpan-kegiatan').text('saving...'); //change button text
  $('#simpan-kegiatan').attr('disabled',true); //set button disable
  var url;
  var notif;

  if(save_method == 'add') {
      url = base_url + "admin/kegiatan/ajax_add";
      notif = "<strong><h4>Info!</h4></strong>Kegiatan " + $('[name="nama_kegiatan"]').val()  + " berhasil ditambahkan";
  } else {
      url = base_url + "admin/kegiatan/ajax_update";
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
        url : base_url + "admin/kegiatan/ajax_edit/" + id_kegiatan,
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
                url : base_url + "admin/kegiatan/option_program_edit/" + data.id_program,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {
                    $('[name="option_program"]').html(data);
                    change_rekening();

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
            $.ajax({
                url : base_url + "admin/kegiatan/option_user_edit/" + data.id_user,
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {

                    $('[name="option_user"]').html(data);

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

function delete_kegiatan(id_kegiatan)
{
    //cek terlebih dahulu idnya..

    $.ajax({
        url : base_url + "admin/kegiatan/ajax_edit/" + id_kegiatan,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

          if(confirm('Anda yakin akan menghapus Kegiatan ' + data.nama_kegiatan + ' ?'))
          {
              // ajax delete data to database
              $.ajax({
                  url : base_url + "admin/kegiatan/ajax_delete/" + id_kegiatan,
                  type: "POST",
                  dataType: "JSON",
                  success: function(data)
                  {
                      //if success reload ajax table
                      $('#modal-kegiatan').modal('hide');
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

function change_rekening(){
  var id_program = $('[name="option_program"]').val();
  if (id_program == '') {
    $('#rekening').text('');
  } else {

      $.ajax({
          url : base_url + "admin/program/ajax_edit/" + id_program,
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {
              $('#rekening').text(data.rekening_program +' .');
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });
    }
};

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}
