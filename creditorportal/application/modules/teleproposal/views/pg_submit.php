<?php //echo '<pre>';print_r($data);exit; ?>
<form action="<?php echo base_url().'teleproposal/payment_return_view/'.$emp_id; ?>" id="PGForm" method="POST">
    <script
        src="https://checkout.razorpay.com/v1/checkout.js"
        data-key="<?php echo $key?>"
        data-amount="<?php echo $amount?>"
        data-currency="INR"
        data-name="<?php echo $name?>"
        data-image="<?php echo $image?>"
        data-description="<?php echo $description?>"
        data-prefill.name="<?php echo $prefill['name']?>"
        data-prefill.email="<?php echo $prefill['email']?>"
        data-prefill.contact="<?php echo $prefill['contact']?>"
        data-notes.shopping_order_id="3456"
        data-order_id="<?php echo $order_id?>"
        <?php if ($display_currency !== 'INR') { ?> data-display_amount="<?php echo $display_amount?>" <?php } ?>
        <?php if ($display_currency !== 'INR') { ?> data-display_currency="<?php echo $display_currency?>" <?php } ?>
    >
    </script>
    <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
    <input type="hidden" name="amount" value="<?php echo $amount?>">

    <input type="hidden" name="shopping_order_id" value="3456">
</form>
<script src="/assets/js/customer-portal/jquery.2.2.3.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.razorpay-payment-button').hide();
        $('#PGForm').submit();
    })
</script>