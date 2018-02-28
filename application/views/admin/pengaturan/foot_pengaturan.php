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
<!-- AdminLTE App -->
<script src="<?php echo base_url();?>assets/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>
<!-- page script -->
<script>

$(document).ready(function() {
      //datatables
      $('#notifikasi').hide();
}); //end-document-ready


//Remove Error
$("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
});

$('#simpan-pengaturan').click(function(){
  $('#simpan-pengaturan').text('saving...'); //change button text
  $('#simpan-pengaturan').attr('disabled',true); //set button disable
  var url = "<?php echo site_url('admin/pengaturan/ajax_update')?>";

  // ajax adding data to database
  $.ajax({
      url : url,
      type: "POST",
      data: $('#form-pengaturan').serialize(),
      dataType: "JSON",
      success: function(data)
      {

          if(data.status) //if success close modal and reload ajax table
          {
              $('#notifikasi').html(data.notif).addClass('alert alert-info');
              $('#notifikasi').fadeTo(4000, 500).slideUp(500, function(){
                $('#notifikasi').slideUp(500);
              });
          }
          else
          {
              for (var i = 0; i < data.inputerror.length; i++)
              {
                  $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                  $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
          }

          $('#simpan-pengaturan').html('<i class="fa fa-floppy-o"></i> Save'); //change button text
          $('#simpan-pengaturan').attr('disabled',false); //set button enable


      },
      error: function (jqXHR, textStatus, errorThrown)
      {
          alert('Error add / update data');
          $('#simpan-pengaturan').text('save'); //change button text
          $('#simpan-pengaturan').attr('disabled',false); //set button enable

      }
    });
  }); //end-simpan-pengaturan


</script>
</body>
</html>
