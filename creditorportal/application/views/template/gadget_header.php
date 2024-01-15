<?php
//creditor_logo
$creditor_logo=$this->session->userdata('creditor_logo');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gadgets Insurance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/assets/gadget/css/style.css" >
    <link rel="stylesheet" href="/assets/gadget/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/gadget/css/responsive.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   <!-- <script src="/assets/gadget/js/jquery.slim.min.js"></script>-->
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="/assets/gadget/js/popper.min.js"></script>
    <script src="/assets/gadget/js/bootstrap.bundle.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">



    <script type="text/javascript" src="<?php echo base_url('assets/js/essential-plugins.js', PROTOCOL); ?>"></script>
   <!-- <script type="text/javascript" src="<?php /*echo base_url('assets/js/jquery-ui.js', PROTOCOL); */?>"></script>-->
    <script src="<?php echo base_url('assets/js/jquery.form.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery.validate.js', PROTOCOL); ?>"></script>
    <script src="<?php echo base_url('assets/js/additional-methods.js', PROTOCOL); ?>"></script>

    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.colorbox.js', PROTOCOL); ?>"></script>

    <!-- Datatable plugin-->
    <script type="text/javascript" src='<?PHP echo base_url('assets/js/jquery.dataTables.min.js', PROTOCOL); ?>'></script>
    <script type="text/javascript" src='<?PHP echo base_url('assets/js/datatable.js', PROTOCOL); ?>'></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/jquery.noty.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/select2.min.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/moment-with-locales.js', PROTOCOL); ?>"></script>
    <script type="text/javascript" src="<?PHP echo base_url('assets/js/bootstrap-material-datetimepicker.js', PROTOCOL); ?>"></script>

</head>
<style>
    .error{
        color:red;
    }
</style>
<body>
<div class="main">
    <div id="navbar">
        <div class="container">
            <div class="logo">
                <?php
                if($creditor_logo == "" || is_null($creditor_logo)){ ?>
                    <img src="/assets/gadget/img/logo.png" onclick="goIndex()" width="130" style="cursor: pointer; ">
          <?php      }else{
                    ?>
                    <img src="<?php echo $creditor_logo; ?>" onclick="goIndex()" style="cursor: pointer;height: 40px; ">
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
<script>
    //logo
    $(".logo").on('click', function(event){
        window.location.href = '/GadgetInsurance';
    });
    function goIndex() {
        window.location.href = '/GadgetInsurance';
    }
</script>