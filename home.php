<?php 
$title = "";
$sub_title = "";
if(isset($_GET['c']) && isset($_GET['s'])){
    $cat_qry = $conn->query("SELECT * FROM categories where md5(id) = '{$_GET['c']}'");
    if($cat_qry->num_rows > 0){
        $result =$cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
    }
 $sub_cat_qry = $conn->query("SELECT * FROM sub_categories where md5(id) = '{$_GET['s']}'");
    if($sub_cat_qry->num_rows > 0){
        $result =$sub_cat_qry->fetch_assoc();
        $sub_title = $result['sub_category'];
        $sub_cat_description = $result['description'];
    }
}
elseif(isset($_GET['c'])){
    $cat_qry = $conn->query("SELECT * FROM categories where md5(id) = '{$_GET['c']}'");
    if($cat_qry->num_rows > 0){
        $result =$cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
    }
}
elseif(isset($_GET['s'])){
    $sub_cat_qry = $conn->query("SELECT * FROM sub_categories where md5(id) = '{$_GET['s']}'");
    if($sub_cat_qry->num_rows > 0){
        $result =$sub_cat_qry->fetch_assoc();
        $sub_title = $result['sub_category'];
        $sub_cat_description = $result['description'];
    }
}
?>

<!-- Header-->
<header class="bg-dark py-5" id="main-header">
   <div class="container px-4 px-lg-5 my-5">
      <div class="text-center text-white">
         <h1 class="display-4 fw-bolder">Welcome to DVD SWOP</h1>

      </div>
   </div>
</header>
<!-- Section-->
<style>
   .CD-cover {
      object-fit: contain !important;
      height: 30px !important;
      object-fit: cover !important;
   }
   .btn-filter{
      cursor: pointer;
   }
  

   .product-item .row{
      flex-direction: column;
      margin-bottom: 5px;
   }
</style>




<section class="py-5">

   <div class="container">

      <nav class="navbar navbar-expand navbar-dark bg-white">
         <div class="container-fluid px-4 px-lg-5 ">

            <div class="collapse navbar-collapse" id="navbarSupportedContenth">
               <a class="nav-link dropdown-toggle text-dark text-lg" id="navbarDropdownCat" href="#" role="button"
                  data-toggle="dropdown" aria-expanded="false">
                  <?php echo ("<span class='font-weight-bold'>Categories</span>") ?>
               </a>
               <ul class="dropdown-menu  p-0" aria-labelledby="navbarDropdownCat">

                  <li class="nav-item"></li>
                  <?php
                        $cat_qry = $conn->query("SELECT * FROM categories where status = 1  limit 3");
                        $count_cats = $conn->query("SELECT * FROM categories where status = 1 ")->num_rows;
                        while ($crow = $cat_qry->fetch_assoc()):
                           $sub_qry = $conn->query("SELECT * FROM sub_categories where status = 1 and parent_id = '{$crow['id']}'");
                           if ($sub_qry->num_rows <= 0):
                        ?>
                  <li class="nav-item">
                     <a class="nav-link text-dark text-lg" aria-current="page"
                        href="./?p=home&c=<?php echo md5($crow['id']) ?>">
                        <?php echo $crow['category'] ?>
                     </a>
                  </li>

                  <?php else: ?>

                  <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle text-dark text-lg" id="navbarDropdown<?php echo $crow['id'] ?>" href="#"
                        role="button" data-toggle="dropdown" aria-expanded="false">
                        <?php echo $crow['category'] ?>
                     </a>
                     <ul class="dropdown-menu  p-0" aria-labelledby="navbarDropdown<?php echo $crow['id'] ?>">
                        <?php while ($srow = $sub_qry->fetch_assoc()): ?>
                        <li>
                           <a class="dropdown-item border-bottom text-dark text-lg"
                              href="./?p=home&c=<?php echo md5($crow['id']) ?>&s=<?php echo md5($srow['id']) ?>">
                              <?php echo $srow['sub_category'] ?>
                           </a>
                           <a class="nav-link text-dark text-lg" aria-current="page"
                              href="./?p=home&c=<?php echo md5($crow['id']) ?>">
                              <?php echo $crow['category'] ?>
                           </a>

                           <!-- <a class="nav-link text-dark text-lg" href="./?p=view_categories">All Categories</a> -->


                        </li>
                        <?php endwhile; ?>
                     </ul>
                  </li>
                  <?php endif; ?>
                  <?php endwhile; ?>
                  <?php if ($count_cats > 3): ?>

                  <?php endif; ?>
               </ul>

            </div>
         
         </div>


         <div class="container-fluid px-4 px-lg-5 ">
         <a class="nav-link dropdown-toggle text-dark text-lg" id="navbarDropdownFilter" href="#" role="button"
                  data-toggle="dropdown" aria-expanded="false">
                  <?php echo ("<span class='font-weight-bold'>Filter</span>") ?>
               </a>
               <ul class="dropdown-menu p-0" aria-labelledby="navbarDropdownFilter">

                 
                  <div class="d-flex flex-wrap">
                  <?php foreach (range('A', 'Z') as $char) {
                     $color = '#0d6efd';
                     if(isset($_GET['filter'])){
                        if($char == $_GET['filter']){
                           $color = 'red';
                        }
                     }


                     ?>

                        <li class="nav-item">
                           <a class="nav-link text-lg text-bold" aria-current="page"
                              href="./?p=home&filter=<?php echo ($char) ?>" style="color:<?= $color ?>">
                              <?php echo $char?>
                           </a>
                        </li>
                        <?php } ?>
                     </div>
               </ul>
         </div>
      </nav>

   </div>

   <!-- Products Showing -->

   <div class="container px-4 px-lg-5 mt-5">
      <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 justify-content-center">
         <?php
         $end_listing = false;
            $whereData = "";
            if(isset($_GET['search']))
               $whereData = " and (title LIKE '%{$_GET['search']}%' or author LIKE '%{$_GET['search']}%' or description LIKE '%{$_GET['search']}%')";
            elseif(isset($_GET['c']) && isset($_GET['s']))
               $whereData = " and (md5(category_id) = '{$_GET['c']}' and md5(sub_category_id) = '{$_GET['s']}')";
            elseif(isset($_GET['c']) && !isset($_GET['s']))
               $whereData = " and md5(category_id) = '{$_GET['c']}' ";
            elseif(isset($_GET['s']) && !isset($_GET['c']))
               $whereData = " and md5(sub_category_id) = '{$_GET['s']}' ";
            elseif(isset($_GET['filter']))
               $whereData = " and (title LIKE '{$_GET['filter']}%')";

            $products = $conn->query("SELECT * FROM `products` where status = 1 {$whereData} order by rand() ");

            $listing = 0;
            $listing_rank = 0;
            
            while ($row = $products->fetch_assoc()):
               $end_listing = false;
               $upload_path = base_app . '/uploads/product_' . $row['id'];
               $img = "";
               if (is_dir($upload_path)) {
                  $fileO = scandir($upload_path);
                  if (isset($fileO[2])){

                     $cover_upload_path = base_app . '/uploads/product_' . $row['id'].'/'.$fileO[2];
                     if(is_dir($cover_upload_path)){
                        $fileO = scandir($cover_upload_path);
                        $img = "uploads/product_" . $row['id'] . "/cover/" . $fileO[2];
                        
                     }else{
                        $img = "/uploads/product_" . $row['id']."/".$fileO[2];
                     }
                    

                  }
                  
               }
               foreach ($row as $k => $v) {
                  $row[$k] = trim(stripslashes($v));
               }
               $inventory = $conn->query("SELECT * FROM inventory where product_id = " . $row['id']);
               $inv = array();
               while ($ir = $inventory->fetch_assoc()) {
                  $inv[] = number_format($ir['price']);
               }
            ?>


         <!-- Product listing style 2 -->
        <?php if($listing == 0){ $listing_rank++ ?>
         
         <div class="col mb-5" style="width: 40%;">
         <p style="font-weight: bold;">Rank <?= $listing_rank ?></p>
            <div class="card product-item p-2">
         <?php  } $listing++ ?>
        
                  
                  <a href=".?p=view_product&id=<?php echo md5($row['id']) ?>" data-gallery="portfolioDetailsGallery"
                        data-glightbox="type: external" class="portfolio-details-lightbox "
                        title=""><div class="row">
                     <img class="CD-cover" src="<?php echo validate_image($img) ?>" alt="..." style="width: 500px;"/>
                  </div></a>
                  
         <?php if($listing == 20){ $listing = 0; $end_listing = true; ?>
            </div>
         </div>
         <?php } ?>

         <?php endwhile; ?>
         <?php if(!$end_listing){ ?>
            </div>
               </div>
         <?php } ?>
      </div>
   </div>
</section>

<script>

</script>