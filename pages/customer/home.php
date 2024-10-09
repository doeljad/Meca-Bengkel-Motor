<?php // Query untuk mengambil data kategori dan jumlah produk

$query = "SELECT k.id_kategori, k.nama_kategori, COUNT(p.id_produk) as total_produk
    FROM kategori k
    LEFT JOIN produk p ON k.id_kategori = p.id_kategori
    GROUP BY k.id_kategori, k.nama_kategori";
$result_kategori = $conn->query($query);
$query = "SELECT p.*, gp.nama_file FROM produk p
        LEFT JOIN gambar_produk gp ON gp.id_produk = p.id_produk";
$produk = $conn->query($query);

$query = "SELECT p.*, gp.nama_file FROM produk p
        LEFT JOIN gambar_produk gp ON gp.id_produk = p.id_produk 
        ORDER BY p.created_at limit 8";
$produk_terbaru = $conn->query($query);


?>

<div class="banner_bg_main">
    <!-- header top section start -->

    <!-- header top section start -->
    <!-- logo section start -->
    <div class="logo_section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="logo"><a href="#"><img src="assets/images/logo-white.png" width="140"></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- logo section end -->

    <!-- header section start -->

    <!-- header section end -->
    <!-- banner section start -->
    <div class="banner_section layout_padding">
        <div class="container">
            <div id="my_slider" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col-sm-12 mb-lg-5">
                                <h1 class="banner_taital">Temukan <br> Perlengkapan Motor Terbaik</h1>
                                <!-- <div class="buynow_bt"><a href="#">Belanja Sekarang</a></div> -->
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-sm-12 mb-lg-5">
                                <h1 class="banner_taital">Upgrade <br> Peralatan Motor Anda</h1>
                                <!-- <div class="buynow_bt"><a href="#">Cek Penawaran</a></div> -->
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-sm-12 mb-lg-5">
                                <h1 class="banner_taital">Peralatan Berkualitas <br> untuk Motor Profesional</h1>
                                <!-- <div class="buynow_bt"><a href="#">Belanja Sekarang</a></div> -->
                            </div>
                        </div>
                    </div>

                </div>
                <a class="carousel-control-prev" href="#my_slider" role="button" data-slide="prev">
                    <i class="fa fa-angle-left"></i>
                </a>
                <a class="carousel-control-next" href="#my_slider" role="button" data-slide="next">
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- banner section end -->
</div>


<!-- Fashion Section Start -->
<div class="fashion_section mt-4">
    <div id="main_slider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <!-- Carousel Item Start -->
            <div class="carousel-item active">
                <div class="container">
                    <h1 class="fashion_taital">Produk Terbaru Kami</h1>
                    <div class="fashion_section_2">
                        <div class="row">
                            <?php while ($row = $produk->fetch_assoc()) : ?>
                                <div class="col-lg-4 col-sm-4">
                                    <div class="box_main">
                                        <h4 class="shirt_text"><?php echo htmlspecialchars($row['nama']); ?></h4>
                                        <?php // Rating Rata rata
                                        $id_produk = $row['id_produk'];
                                        $query_avg_rating = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE id_produk = ?");
                                        $query_avg_rating->bind_param('i', $id_produk);
                                        $query_avg_rating->execute();
                                        $result = $query_avg_rating->get_result();
                                        $data = $result->fetch_assoc();

                                        $avg_rating = $data['avg_rating'];
                                        $total_reviews = $data['total_reviews'];

                                        // Determine the average rating for stars display
                                        $full_stars = floor($avg_rating ?? 0);
                                        $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                                        $empty_stars = 5 - $full_stars - $half_star; ?>
                                        <p class="price_text">Harga <span style="color: #262626;">Rp<?php echo number_format($row['harga']); ?></span></p>
                                        <p class="price_text">
                                            <?php
                                            for ($i = 0; $i < $full_stars; $i++) {
                                                echo '<small class="fas fa-star"></small>';
                                            }
                                            if ($half_star) {
                                                echo '<small class="fas fa-star-half-alt"></small>';
                                            }
                                            for ($i = 0; $i < $empty_stars; $i++) {
                                                echo '<small class="far fa-star"></small>';
                                            }
                                            ?>

                                            <small class="pt-1">(<?php echo htmlspecialchars($total_reviews); ?> Reviews)</small>

                                        </p>

                                        <div class="tshirt_img">
                                            <img class="img-fluid" src="assets/images/produk/<?php echo $row['nama_file']; ?>" alt="">
                                        </div>
                                        <div class="btn_main">
                                            <div class="buy_bt">
                                                <a href="javascript:showCustomProductModal(<?php echo $row['id_produk']; ?>)">Beli Sekarang</a>
                                            </div>
                                            <div class="seemore_bt"><a href="index.php?page=detail-barang&id=<?= $row['id_produk']; ?>">Lihat Detail</a></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Carousel Item End -->
        </div>
    </div>
</div>
<!-- Fashion Section End -->


<div class="modal fade" id="customProductModal" tabindex="-1" role="dialog" aria-labelledby="customProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customProductModalLabel">Beli Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customProductForm">
                    <input type="hidden" id="modalProductId">

                    <div class="form-group">
                        <label for="quantity">Jumlah</label>
                        <input type="number" class="form-control" id="quantity" required>
                    </div>

                    <!-- Catatan -->
                    <div class="form-group">
                        <label for="note">Catatan <span>opsional</span></label>
                        <textarea class="form-control" id="note"></textarea>
                    </div>

                    <button type="submit" id="addtocart" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Pastikan fungsi ini berada di file JavaScript yang di-load
    function showCustomProductModal(id_produk) {
        // Set product ID in modal hidden input
        document.getElementById('modalProductId').value = id_produk;
        // Show the modal
        $('#customProductModal').modal('show');
    }



    document.getElementById('addtocart').onclick = function(event) {
        event.preventDefault();
        var formData = new FormData();

        var id_produk = document.getElementById('modalProductId').value;
        formData.append('id_produk', id_produk);

        formData.append('jumlah', document.getElementById('quantity').value);
        formData.append('catatan', document.getElementById('note').value);

        fetch('pages/customer/controller/tambah-keranjang.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => {
                        $('#customProductModal').modal('hide'); // Hide modal after success
                        location.reload(); // Reload the page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.'
                });
            });
    };



    function updateQuantity(id_detail_keranjang, action) {
        $.ajax({
            url: 'pages/customer/controller/update-quantity.php',
            type: 'POST',
            data: {
                id_detail_keranjang: id_detail_keranjang,
                action: action
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengubah kuantitas.');
            }
        });
    }
</script>