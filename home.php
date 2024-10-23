 <!-- Masthead-->
 <!-- <header class="masthead">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-10 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Welcome to <php echo $_SESSION['setting_name']; ?></h3>
                        <hr class="divider my-4" />
                        <a class="btn btn-primary btn-xl js-scroll-trigger" href="#menu">Order Now</a>

                    </div>
                    
                </div>
            </div>
        </header> -->
 <link href="css/main.css" rel="stylesheet" />
 <section class="home" id="home">
     <div class="container">
         <div class="home-wrapper d-grid">
             <div class="col-left">
                 <h3>Welcome To</h3>
                 <h1>Online Food <br>Ordering Management</h1>

                 <p>Nikmati kebebasan untuk menjelajahi beragam menu makanan, melihat deskripsi yang lengkap, harga yang jelas, dan gambar yang menggugah selera. Anda juga bisa mengatur pesanan sesuai dengan preferensi Anda, seperti menambahkan topping tambahan, memilih tingkat kepedasan, atau memberikan instruksi khusus.</p>
                 <a href="#" class="btn">Order Now</a>

             </div>
             <div class="col-right">
                 <img data-tilt src="./assets/img/hero-img.png" alt="Home image" class="img-fluid">
             </div>
         </div>
     </div>

 </section>
 <section class="page-section" id="menu">
     <div id="menu-field" class="card-deck">
         <?php
            $query = "SELECT * FROM  product_list order by id";
            $result = pg_query($conn, $query);

            while ($row = pg_fetch_assoc($result)) {
            ?>
             <?php "<br>" ?>
             <div class="col-lg-3" style="margin-bottom: 20px;">
                 <div class="card menu-item ">
                     <img src="assets/img/<?php echo $row['img_path'] ?>" class="card-img-top" alt="...">

                     <div class="card-body">
                         <h5 class="card-title"><?php echo $row['name'] ?></h5>
                         <p class="card-text truncate"><?php echo $row['description'] ?></p>
                         <h6 class="card-title">Price: $<?php echo $row['price'] ?></h6>
                         <div class="text-center">
                             <button class="btn btn-sm btn-outline-primary view_prod btn-block" data-id=<?php echo $row['id'] ?>><i class="fa fa-eye"></i> View</button>

                         </div>
                     </div>

                 </div>
             </div>
         <?php } ?>
     </div>
 </section>
 <script>
     $('.view_prod').click(function() {
         uni_modal_right('Product', 'view_prod.php?id=' + $(this).attr('data-id'))
     })
 </script>