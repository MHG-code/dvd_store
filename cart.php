<style>
    .cart-prod-img {
    height: 7vh;
    width: 18vw;
    object-fit: cover;
}
</style>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-end mb-2">
                <button class="btn btn-outline-dark btn-flat btn-sm" type="button" id="empty_cart">Empty Cart</button>
            </div>
        </div>
        <div class="card rounded-0">
            <div class="card-body">
                <h3><b>Cart List</b></h3>
                <hr class="border-dark">
                <?php 
                    $qry = $conn->query("SELECT c.*,p.title,i.price,p.id as pid from `cart` c 
                        inner join `inventory` i on i.id=c.inventory_id 
                        inner join products p on p.id = i.product_id 
                            where c.client_id = ".$_settings->userdata('id')
                            );
                    while($row= $qry->fetch_assoc()):
                        $upload_path = base_app.'/uploads/product_'.$row['pid'];
                        $img = "";
                        foreach($row as $k=> $v){
                            $row[$k] = trim(stripslashes($v));
                        }
                        if (is_dir($upload_path)) {
                            $fileO = scandir($upload_path);
                            if (isset($fileO[2])){
          
                               $cover_upload_path = base_app . '/uploads/product_' . $row['pid'].'/'.$fileO[2];
                               if(is_dir($cover_upload_path)){
                                  $fileO = scandir($cover_upload_path);
                                  $img = "uploads/product_" . $row['pid'] . "/cover/" . $fileO[2];
                                  
                               }else{
                                  $img = "/uploads/product_" . $row['pid']."/".$fileO[2];
                               }
                              
          
                            }
                            
                         }
                ?>
                    <div class="d-flex w-100 justify-content-between  mb-2 py-2 border-bottom cart-item">
                        <div class="d-flex justify-content-between align-items-center col-8">
                            <img src="<?php echo validate_image($img) ?>" loading="lazy" class="cart-prod-img mr-2 mr-sm-2" alt="">
                            <div>
                                <span class="mr-2 pr-2"><a class="font-weight-bold" href="./?p=checkout&product=<?php echo ($row['inventory_id']) ?>" data-id="<?php echo $row['id'] ?>" style="font-size:larger; color:black; text-decoration:none">Buy</a></span>
                                <span class="mr-2"><a href="javascript:void(0)" class="btn btn-sm btn-outline-danger rem_item" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></a></span>
                            </div>
                            <!-- <div>
                                <p class="mb-1 mb-sm-1"><?php echo $row['title'] ?></p>
                                
                                <p class="mb-1 mb-sm-1"><small><b>Price:</b> <span class="price"><?php echo number_format($row['price']) ?></span></small></p>
                                <div>
                                <div class="input-group" style="width:130px !important">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-sm btn-outline-secondary min-qty" type="button" id="button-addon1"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="number" class="form-control form-control-sm qty text-center cart-qty" placeholder="" aria-label="Example text with button addon" value="<?php echo $row['quantity'] ?>" aria-describedby="button-addon1" data-id="<?php echo $row['id'] ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-outline-secondary plus-qty" type="button" id="button-addon1"><i class="fa fa-plus"></i></button>
                                    </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <div class="col text-right align-items-center d-flex justify-content-end">
                            <!-- <h4><b class="total-amount"><?php //echo number_format($row['price'] * $row['quantity']) ?></b></h4> -->
                            <h4><b class="total-amount"><?php echo number_format($row['price']) ?></b></h4>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="d-flex w-100 justify-content-between mb-2 py-2 border-bottom">
                    <div class="col-8 d-flex justify-content-end"><h4>Grand Total:</h4></div>
                    <div class="col d-flex justify-content-end"><h4 id="grand-total">-</h4></div>
                </div>
            </div>
        </div>
        <div class="d-flex w-100 justify-content-center">
            <a href="./?p=checkout" class="btn btn-sm btn-flat btn-light font-weight-bold rounded p-2 w-25" style="font-size: 1.5rem;">SEND</a>
        </div>


        <!-- For rent -->
        <div class="card rounded-0 mt-3">
            <div class="card-body">
                <h3><b>Rent List</b></h3>
                <hr class="border-dark">
                <?php 
    
                    $qry = $conn->query("SELECT *, i.id as inventory_id from `orders` o 
                        inner join order_list ol on ol.order_id = o.id
                        inner join `inventory` i on i.product_id=ol.product_id 
                             where o.client_id = ".$_settings->userdata('id')." AND o.order_type = '1'
                            order by unix_timestamp(o.date_created)
                                desc ");
                    while($row= $qry->fetch_assoc()):
                        $upload_path = base_app.'/uploads/product_'.$row['product_id'];
                        $img = "";
                        foreach($row as $k=> $v){
                            $row[$k] = trim(stripslashes($v));
                        }
                        if (is_dir($upload_path)) {
                            $fileO = scandir($upload_path);
                            if (isset($fileO[2])){
          
                               $cover_upload_path = base_app . '/uploads/product_' . $row['product_id'].'/'.$fileO[2];
                               if(is_dir($cover_upload_path)){
                                  $fileO = scandir($cover_upload_path);
                                  $img = "uploads/product_" . $row['product_id'] . "/cover/" . $fileO[2];
                                  
                               }else{
                                  $img = "/uploads/product_" . $row['product_id']."/".$fileO[2];
                               }
                              
          
                            }
                            
                         }
                ?>
                    <div class="d-flex w-100 justify-content-between  mb-2 py-2 border-bottom cart-item">
                        <div class="d-flex justify-content-between align-items-center col-8">
                            <img src="<?php echo validate_image($img) ?>" loading="lazy" class="cart-prod-img mr-2 mr-sm-2" alt="">
                            <div>
                            <span class="mr-2 pr-2"><a class="font-weight-bold" href="./?p=checkout&product=<?php echo ($row['inventory_id']) ?>&goto='rent_to_buy'" data-id="<?php echo $row['id'] ?>" style="font-size:larger; color:black; text-decoration:none">Buy</a></span>
                            </div>
                            
                        </div>
                    </div>
                <?php endwhile; ?>
                
            </div>
        </div>
    </div>
</section>
<script>
    function calc_total(){
        var total  = 0

        $('.total-amount').each(function(){
            amount = $(this).text()
            amount = amount.replace(/\,/g,'')
            amount = parseFloat(amount)
            total += amount
        })
        $('#grand-total').text(parseFloat(total).toLocaleString('en-US'))
    }
    function qty_change($type,_this){
        var qty = _this.closest('.cart-item').find('.cart-qty').val()
        var price = _this.closest('.cart-item').find('.price').text()
            price = price.replace(/,/g,'')
            console.log(price)
        var cart_id = _this.closest('.cart-item').find('.cart-qty').attr('data-id')
        var new_total = 0
        start_loader();
        if($type == 'minus'){
            qty = parseInt(qty) - 1
        }else{
            qty = parseInt(qty) + 1
        }
        price = parseFloat(price)
        // console.log(qty,price)
        new_total = parseFloat(qty * price).toLocaleString('en-US')
        _this.closest('.cart-item').find('.cart-qty').val(qty)
        _this.closest('.cart-item').find('.total-amount').text(new_total)
        calc_total()

        $.ajax({
            url:'classes/Master.php?f=update_cart_qty',
            method:'POST',
            data:{id:cart_id, quantity: qty},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    end_loader()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    function rem_item(id){
        $('.modal').modal('hide')
        var _this = $('.rem_item[data-id="'+id+'"]')
        var id = _this.attr('data-id')
        var item = _this.closest('.cart-item')
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=delete_cart',
            method:'POST',
            data:{id:id},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    item.hide('slow',function(){ item.remove() })
                    calc_total()
                    end_loader()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    function empty_cart(){
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=empty_cart',
            method:'POST',
            data:{},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                   location.reload()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    $(function(){
        calc_total()
        $('.min-qty').click(function(){
            qty_change('minus',$(this))
        })
        $('.plus-qty').click(function(){
            qty_change('plus',$(this))
        })
        $('#empty_cart').click(function(){
            // empty_cart()
            _conf("Are you sure to empty your cart list?",'empty_cart',[])
        })
        $('.rem_item').click(function(){
            _conf("Are you sure to remove the item in cart list?",'rem_item',[$(this).attr('data-id')])
        })
    })
</script>