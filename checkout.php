<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<?php
$query = "
    SELECT * from orders as o
        inner join order_list as ol on o.id = ol.order_id 
            where o.order_type = '1' AND o.client_id = ".$_settings->userdata('id');
$all_rent_orders = $conn->query($query);
$all_rent_orders = mysqli_num_rows($all_rent_orders);
if(isset($_GET['goto'])){
    $query = "
        SELECT p.title,i.price,p.id as pid 
        from `inventory` i 
            inner join products p on p.id = i.product_id 
                where i.id = '{$_GET['product']}'  ";
}
else{
    $query = "
    SELECT c.*,p.title,i.price,p.id as pid 
    from `cart` c 
        inner join `inventory` i on i.id=c.inventory_id 
        inner join products p on p.id = i.product_id 
        where c.client_id = ".$_settings->userdata('id');
        if(isset($_GET['product']))
            $query .= " and i.id = '{$_GET['product']}' ";
}


$total = 0;
    $qry = $conn->query($query);

    while($row= $qry->fetch_assoc()):
        // $total += $row['price'] * $row['quantity'];
        $total += $row['price'] ;
    endwhile;
?>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body"></div>
            <h3 class="text-center"><b>Checkout</b></h3>
            <hr class="border-dark">
            <form action="" id="place_order">
                <input type="hidden" name="amount" value="<?php echo $total ?>">
                <input type="hidden" name="payment_method" value="cod">
                <input type="hidden" name="paid" value="0">
                <?php if(isset($_GET['product'])){ ?>
                    <input type="hidden" name="product_id" value="<?= $_GET['product'] ?>">
                <?php } ?>
                <div class="row row-col-1 justify-content-center">
                    <div class="col-6">
                    <div class="form-group col mb-0">
                    <label for="" class="control-label">Order Type</label>
                    </div>
                    <div class="form-group d-flex pl-2">
                        <div class="custom-control custom-radio">
                          <input class="custom-control-input custom-control-input-primary" type="radio" id="customRadio4" name="order_type" value="2" checked="">
                          <label for="customRadio4" class="custom-control-label">For Buy</label>
                        </div>
                        <?php if($all_rent_orders > 2) {?>
                            <div class="custom-control custom-radio ml-3">
                                <b> <p class="text-danger"> You all ready have <?= $all_rent_orders?>  for rent </p></b>
                            </div>
                        <?php }else{ ?>
                            <?php if(!isset($_GET['goto'])) {?>
                                <div class="custom-control custom-radio ml-3">
                                    <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="1">
                                    <label for="customRadio5" class="custom-control-label">For Rent</label>
                                </div>
                            <?php } 
                        }?>
                        <!-- <div class="custom-control custom-radio ml-3">
                          <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="1">
                          <label for="customRadio5" class="custom-control-label">For Pick up</label>
                        </div> -->
                      </div>
                        <div class="form-group col address-holder">
                            <label for="" class="control-label">Delivery Address</label>
                            <textarea id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                        </div>
                        <div class="col">
                            <span><h4><b>Total:</b> <?php echo number_format($total) ?></h4></span>
                        </div>
                        <hr>
                        <div class="col my-3">
                        <h4 class="text-muted">Payment Method</h4>
                            <div class="d-flex w-100 justify-content-between">
                                <button class="btn btn-flat btn-primary">Cash on Delivery</button>
                                <span id="paypal-button"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
paypal.Button.render({
    env: 'sandbox', // change for production if app is live,
 
        //app's client id's
	client: {
        sandbox:    'AdDNu0ZwC3bqzdjiiQlmQ4BRJsOarwyMVD_L4YQPrQm4ASuBg4bV5ZoH-uveg8K_l9JLCmipuiKt4fxn',
        //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
    },
 
    commit: true, // Show a 'Pay Now' button
 
    style: {
    	color: 'blue',
    	size: 'small'
    },
 
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                    	//total purchase
                        amount: { 
                        	total: '<?php echo $total; ?>', 
                        	currency: 'PHP' 
                        }
                    }
                ]
            }
        });
    },
 
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
    		// //sweetalert for successful transaction
    		// swal('Thank you!', 'Paypal purchase successful.', 'success');
            payment_online()
        });
    },
 
}, '#paypal-button');

function payment_online(){
    $('[name="payment_method"]').val("Online Payment")
    $('[name="paid"]').val(1)
    $('#place_order').submit()
}
$(function(){
    // $('[name="order_type"]').change(function(){
    //     if($(this).val() ==2){
    //         $('.address-holder').show('slow')
    //     }else{
    //         $('.address-holder').hide('slow')
    //     }
    // })
    $('#place_order').submit(function(e){
        e.preventDefault()
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=place_order',
            method:'POST',
            data:$(this).serialize(),
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("an error occured","error")
                end_loader();
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    alert_toast("Order Successfully placed.","success")
                    setTimeout(function(){
                        location.replace('./')
                    },2000)
                }else{
                    console.log(resp)
                    alert_toast("an error occured","error")
                    end_loader();
                }
            }
        })
    })
})
</script>