
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



<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/extensions/Buttons/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/extensions/Buttons/js/buttons.print.min.js"></script>
<!-- bootstrap datepicker -->
<script src="<?php echo base_url();?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- bootstrap color picker -->
<script src="<?php echo base_url();?>assets/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="<?php echo base_url();?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>


<script>
  $(function () {
    //Date picker
    $('#dateFrom').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      // minDate:  0,
      // onSelect: function(){
      //       var date2 = $('#dateFrom').val();
      //       //date2.setDate(date2.getDate() + 1);
      //       $('#dateTo').datepicker('update', '2011-03-05');
      //       //sets minDate to dt1 date + 1
      //       $('#dateTo').datepicker('option', 'minDate', date2);
      //   }
    })
    .on('changeDate', function(e){
       var date2 = $('#dateFrom').datepicker('getDate');
       $('#dateTo').datepicker('setStartDate', date2);
       $('#dateTo').datepicker('setDate', date2);
    });
    $('#dateTo').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy'

    });
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


}); //end-document-ready

$('[name="optLp"]').change(function(){
  if ($(this).val() == 5){
    $('.to-date').hide();
  } else {
    $('.to-date').show();
  }
})

$('#tampil').on('click', function(){
  $.ajax({
   type: "GET",
   dataType: "html",
   url: "<?php echo base_url('bendahara/cetak/bku2')?>",
    }).success(function (data) {
      $('#hasil').html(data);
      $('#tabel_bku').DataTable({
             bSort: false,
             responsive: true,
             dom: 'Bfrtip',
             "columnDefs": [
                   {"className": "dt-center", "targets": "_all"}
                 ],
             buttons:[
                 {
                   extend : 'excelHtml5',
                   text : '<i class="fa fa-file-excel-o"></i>',
                   titleAttr : "ExportToExcel",
                   title : $("#judul").text(),
                 },
                 {
                    extend: 'pdfHtml5',
                    pageSize: 'folio',
                   text : '<i class="fa fa-file-pdf-o"><i>',
                   titleAttr : "ExportToPDF",
                   title : $("#judul").text(),
                   download: 'open',


                 },
                 {
                   extend: 'print',
                   text : '<i class="fa fa-print"><i>',
                   titleAttr : "Print",
                   title : "Buku Khas Umum Bendahara",
                   footer: false,
                   customize: function (win) {
                     $(win.document.body)
                           .append(
                             '<div style="margin : 0 0 50px 0"></div><div class="col-sm-6 text-center">Mengetahui, <br></div> <div class="col-sm-6 text-center">Trenggalek, <br></div>'
                               //'<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; bottom:0; left:0;" />'
                           );
                    $(win.document.body).find('table, td').css({'border': '1px solid grey'});
                    $(win.document.body).find('table th').css({'border': '1.5px solid grey', 'text-align' : 'center', 'margin-left' : '10px'});

                        //$(win.document.body).find('table').addClass('display').css({'font-size': '14px'});
                        // $(win.document.body).find('tr:nth-child(odd) td').each(function(index){
                        //     $(this).css('background-color','#D0D0D0');
                        // });
                        $(win.document.body).find('h1').css({'text-align': 'center', 'margin' : '0 0 20px 0'});
                  }
                 },
               ],
           });
        });

});

$('#cetak').click(function(){
   $('#form-laporan').attr('action', '<?php echo base_url('bendahara/laporan/cetak') ?>');
});


$('#xls').click(function(){
   $('#form-laporan').attr('action', '<?php echo base_url('bendahara/laporan/xls') ?>');
});

function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
}


</script>
</body>
</html>
