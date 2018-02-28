<script type="text/javascript">
var id_user = '<?php echo $this->session->userdata('id_user') ?>';
$(document).ready(function() {

    $(".modal-dialog").draggable({handle: ".modal-header"});
    load_profil();

}); //end-document-ready


$('#simpan-profil-backup').click(function(){
	$('#simpan-profil').text('saving...'); //change button text
	$('#simpan-profil').attr('disabled',true); //set button disable
	var url = "<?php echo site_url('bendahara/profil/ajax_update')?>";

	// ajax adding data to database
	$.ajax({
			url : url,
			type: "POST",
			data: $('#form-profil').serialize(),
			dataType: "JSON",
			success: function(data)
			{

					if(data.status) //if success close modal and reload ajax table
					{
							$('#modal-profil').modal('hide');
							$('#profil-alert').html(data.notif).addClass('alert alert-info');
							$('#profil-alert').fadeTo(4000, 500).slideUp(500, function(){
								$('#profil-alert').slideUp(500);
							});
              load_profil();

					}
					else
					{
							for (var i = 0; i < data.inputerror.length; i++)
							{
									$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
									$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
							}
					}
					$('#simpan-profil').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
					$('#simpan-profil').attr('disabled',false); //set button enable


			},
			error: function (jqXHR, textStatus, errorThrown)
			{
					alert('Error add / update data');
					$('#simpan-profil').text('save'); //change button text
					$('#simpan-profil').attr('disabled',false); //set button enable

			}
		});
	}); //end-simpan-profil


$('#profil').click(function()
{
		var id_user = "<?php echo $this->session->userdata('id_user');?>";
		save_method = 'update';
		$('#form-profil')[0].reset(); // reset form on modals
		$('.form-group').removeClass('has-error'); // clear error class
		$('.help-block').empty(); // clear error string
    $('.result').empty();

		//Ajax Load data from ajax
		$.ajax({
				url : "<?php echo base_url('bendahara/profil/ajax_edit')?>/" + id_user,
				type: "POST",
				dataType: "JSON",
				success: function(data)
				{
            if (data.pic_user == '' || data.pic_user == 'NULL') {
              $('.profil-wrap').hide();
            } else {
              srcImage = '<?php echo base_url().'assets/profile/' ?>' + data.pic_user;
              $('#myProfil').attr('src', srcImage);
              $('.profil-wrap').show();
              $('.close').show();
            }

						$('[name="id_user"]').val(data.id_user);
						$('[name="nama_user"]').val(data.nama_user);
						$('[name="nip_user"]').val(data.nip_user);
						$('[name="username"]').val(data.username);
						$('[name="password"]').val(data.password);
						$('#modal-profil').modal('show'); // show bootstrap modal when complete loaded
						$('.modal-title').text('Edit Profil'); // Set title to Bootstrap modal title


				},
				error: function (jqXHR, textStatus, errorThrown)
				{
						alert('Error get data profil from ajax');
				}
		});
});

function delete_profil(id_user)
{
  if(confirm('Anda yakin akan menghapus foto profil ?'))
  {
      // ajax delete data to database
      $.ajax({
          url : "<?php echo base_url('bendahara/profil/delete_profil/')?>" + id_user,
          type: "POST",
          dataType: "JSON",
          success: function(data)
          {
            $('#modal-profil').modal('hide');
             load_profil();
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error deleting data');
          }
      });

  }
}

$('[name="pic_user"]').change(function(e){
  $('#myProfil').attr('src', '');
  $('.result').empty();
  var form = $('form')[0];
  var formData = new FormData(form);
  $.ajax({
    url : "<?php echo site_url('bendahara/profil/cek_image') ?>",
    type: "POST",
    dataType: "JSON",
    cache: false,
    contentType: false,
    processData: false,
    data : formData,
    success: function(data){
      if (data.status) {
        readURL($('[name="pic_user"]')[0]);
      } else {
        $('.result').html(data.html);
        $('[name="pic_user"]').val('');
        $('.profil-wrap').hide();
      }
    }
  });

});

$('#simpan-profil').click(function(){
	$('#simpan-profil').text('saving...'); //change button text
	$('#simpan-profil').attr('disabled',true); //set button disable
	var url = "<?php echo site_url('bendahara/profil/ajax_update')?>";
  var form = $('form')[0];
  var formData = new FormData(form);
	// ajax adding data to database
	$.ajax({
			url : url,
			type: "POST",
			data: formData,
			dataType: "JSON",
      contentType : false,
      processData : false,
      cache: false,
			success: function(data)
			{
					if(data.status) //if success close modal and reload ajax table
					{
							$('#modal-profil').modal('hide');
							$('#profil-alert').html(data.notif).addClass('alert alert-info');
							$('#profil-alert').fadeTo(4000, 500).slideUp(500, function(){
								$('#profil-alert').slideUp(500);
							});
              load_profil();

					}
					else
					{
							for (var i = 0; i < data.inputerror.length; i++)
							{
									$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
									$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
							}
					}
					$('#simpan-profil').html('<i class="fa fa-floppy-o"></i> Simpan'); //change button text
					$('#simpan-profil').attr('disabled',false); //set button enable


			},
			error: function (jqXHR, textStatus, errorThrown)
			{
					alert('Error add / update data profil');
					$('#simpan-profil').text('save'); //change button text
					$('#simpan-profil').attr('disabled',false); //set button enable

			}
		});
	}); //end-simpan-profil


//Show before upload
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.profil-wrap').show();
            $('.close').hide();
            $('#myProfil').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

//reload name and picture
function load_profil(){
  //Ajax Load data from ajax
  $.ajax({
      url : "<?php echo base_url('bendahara/profil/ajax_dashboard')?>/" + id_user,
      type: "POST",
      dataType: "JSON",
      success: function(data)
      {
          if (data.pic_user == '' || data.pic_user == 'NULL') {
            srcImage = '<?php echo base_url().'assets/profile/default.png' ?>';
          } else {
            srcImage = '<?php echo base_url().'assets/profile/' ?>' + data.pic_user;
          }

          $('.myProfil').attr('src', srcImage);
          $('.myName').text(data.nama_user);

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error get data from ajax');
      }
});
}

</script>

<?php
	if($foot) {
		$this->load->view($foot);
	}
?>
