<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title ?> | Simpoltu</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/select2/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/custom.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/skins/_all-skins.min.css">


  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css">

  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datepicker/datepicker3.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- jQuery 2.2.3 -->
  <script src="<?php echo base_url();?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>

  <link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jquery-ui/jquery-ui.css">
  <script src="<?php echo base_url();?>assets/plugins/jquery-ui/jquery-ui.js"></script>
  <style>
      .capitalize {
        text-transform: capitalize;
      }
      .sortable {
          cursor: pointer;
      }
      .select2 {
          width: 100% !important;
      }
      #profil-alert{
        position: absolute;
        right: 0px;
        top: 50px;
        opacity: 0.8;
        z-index: 10000;
      }
      .profil-wrap {
          position: relative;
          display: inline-block;
          border: 1px grey solid;
          font-size: 0;
          margin-top: 5px;
      }
      .profil-wrap .close {
          position: absolute;
          top: 2px;
          right: 2px;
          z-index: 100;
          background-color: #FFF;
          padding: 5px 2px 2px;
          color: #000;
          font-weight: bold;
          cursor: pointer;
          opacity: .2;
          text-align: center;
          font-size: 22px;
          line-height: 10px;
          border-radius: 50%;
      }
      .profil-wrap:hover .close {
          opacity: 1;
      }
      option.parent{
        font-weight: bold;
      }
      .row-no-gap{

        padding-left: 0;
        padding-right: 0;
      }
      .modal-header { cursor: move }

  </style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">
