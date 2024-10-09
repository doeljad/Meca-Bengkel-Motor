<?php
// Include file koneksi ke database
include 'pages/controller/connection.php';

// Ambil id_produk dari URL
$id_produk = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk mengambil data produk
$query = "SELECT p.*, k.nama_kategori FROM produk p
          JOIN kategori k ON p.id_kategori = k.id_kategori
          WHERE p.id_produk = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_produk);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

// Query untuk mengambil gambar produk
$query_gambar = "SELECT * FROM gambar_produk WHERE id_produk = ?";
$stmt_gambar = $conn->prepare($query_gambar);
$stmt_gambar->bind_param('i', $id_produk);
$stmt_gambar->execute();
$result_gambar = $stmt_gambar->get_result();
$gambar_produk = $result_gambar->fetch_all(MYSQLI_ASSOC);

// Rating Rata rata
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
$empty_stars = 5 - $full_stars - $half_star;
?>

<!-- Shop Detail Start -->
<div class="container-fluid pb-5 ">
    <!-- Carousel and Product Information -->
    <div class="row px-xl-5 p-4 bg-light">
        <!-- Product Carousel -->
        <div class="col-lg-5 mb-4">
            <div id="product-carousel" class="carousel slide shadow-sm rounded" data-ride="carousel">
                <div class="carousel-inner bg-white rounded">
                    <?php
                    $active = 'active';
                    foreach ($gambar_produk as $gambar) {
                        echo '<div class="carousel-item ' . $active . '">';
                        echo '<img class="d-block w-100 rounded" src="assets/images/produk/' . htmlspecialchars($gambar['nama_file']) . '" alt="Image">';
                        echo '</div>';
                        $active = '';
                    }
                    ?>
                </div>
                <!-- Carousel Controls -->
                <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                    <i class="fa fa-2x fa-angle-left text-dark"></i>
                </a>
                <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                    <i class="fa fa-2x fa-angle-right text-dark"></i>
                </a>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-7 mb-4">
            <div class="bg-white p-4 rounded shadow-sm">
                <h1 class="mb-3"><?php echo htmlspecialchars($produk['nama']); ?></h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning mr-2">
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
                    </div>
                    <small class="text-muted">(<?php echo htmlspecialchars($total_reviews); ?> Reviews)</small>
                </div>
                <h3 class="font-weight-bold mb-3 text-success">Rp<?php echo number_format($produk['harga']); ?></h3>
                <p class="mb-2"><strong>Stok Produk:</strong> <?php echo number_format($produk['stok']); ?></p>
                <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
                <!-- Add to Cart Button -->
                <div class="d-flex align-items-center">
                    <a class="btn btn-primary px-4 
                        <?php echo ($produk['stok'] <= 0) ? 'disabled' : ''; ?>"
                        href="javascript:showCustomProductModal(<?php echo $_GET['id']; ?>)">
                        <i class="fa fa-shopping-cart mr-2"></i> Tambah ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Product Modal -->
    <div class="modal fade" id="customProductModal" tabindex="-1" role="dialog" aria-labelledby="customProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                            <label for="note">Catatan <span class="text-muted">(opsional)</span></label>
                            <textarea class="form-control" id="note"></textarea>
                        </div>

                        <button type="button" id="addtocart" class="btn btn-primary btn-block" onclick="checkStockBeforeAdding()">Tambah ke Keranjang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description and Reviews -->
    <div class="row px-xl-5 mt-4">
        <div class="col bg-white rounded shadow-sm p-4">
            <div class="nav nav-tabs mb-4">
                <a class="nav-item nav-link text-dark active" data-toggle="tab" href="#tab-pane-1">Deskripsi</a>
                <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-3">Ulasan</a>
            </div>
            <div class="tab-content">
                <!-- Product Description -->
                <div class="tab-pane fade show active" id="tab-pane-1">
                    <h4 class="mb-3">Deskripsi Produk</h4>
                    <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
                </div>
                <!-- Product Reviews -->
                <div class="tab-pane fade" id="tab-pane-3">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $query_reviews = $conn->prepare("
                                SELECT r.rating, r.review_text, r.created_at, u.name, r.id_review 
                                FROM reviews r
                                INNER JOIN users u ON r.id_user = u.id_user
                                WHERE r.id_produk = ?
                            ");
                            $query_reviews->bind_param('i', $produk['id_produk']);
                            $query_reviews->execute();
                            $result_reviews = $query_reviews->get_result();
                            $review_count = $result_reviews->num_rows;
                            ?>

                            <h4 class="mb-4"><?php echo $review_count; ?> ulasan untuk "<?php echo htmlspecialchars($produk['nama']); ?>"</h4>

                            <?php while ($review = $result_reviews->fetch_assoc()) : ?>
                                <div class="media mb-4 border-bottom pb-3">
                                    <img src="assets/images/faces/face1.jpg" alt="Image" class="img-fluid mr-3 rounded-circle" style="width: 45px;">
                                    <div class="media-body">
                                        <h6><?php echo htmlspecialchars($review['name']); ?><small> - <i><?php echo date('d M Y', strtotime($review['created_at'])); ?></i></small></h6>
                                        <div class="text-warning mb-2">
                                            <?php
                                            for ($i = 0; $i < 5; $i++) {
                                                echo $i < $review['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                            }
                                            ?>
                                        </div>
                                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                                        <!-- Display images/videos for reviews -->
                                        <?php
                                        $query_media = $conn->prepare("SELECT nama_file FROM gambar_review WHERE id_review = ?");
                                        $query_media->bind_param('i', $review['id_review']);
                                        $query_media->execute();
                                        $result_media = $query_media->get_result();

                                        if ($result_media->num_rows > 0) :
                                            while ($media = $result_media->fetch_assoc()) :
                                                $file_path = 'assets/images/upload-file/' . htmlspecialchars($media['nama_file']);
                                                $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                                $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                                <a href="<?php echo $file_path; ?>" target="_blank">
                                                    <?php if ($is_image) : ?>
                                                        <img src="<?php echo $file_path; ?>" alt="Media" style="width: 50px; height: 50px; object-fit: cover;" class="mr-2 mb-2 rounded">
                                                    <?php else : ?>
                                                        <video width="50" height="50" controls>
                                                            <source src="<?php echo $file_path; ?>" type="video/<?php echo $file_ext; ?>">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    <?php endif; ?>
                                                </a>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shop Detail End -->

<script>
    // Pastikan fungsi ini berada di file JavaScript yang di-load
    function showCustomProductModal(id_produk) {
        // Set product ID in modal hidden input
        document.getElementById('modalProductId').value = id_produk;
        // Show the modal
        $('#customProductModal').modal('show');
    }
    async function checkStockBeforeAdding() {
        const productId = <?php echo json_encode($_GET['id']); ?>; // Dapatkan ID produk dari PHP
        const quantity = parseInt(document.getElementById('quantity').value, 10); // Dapatkan jumlah input dari pengguna
        console.log(productId, quantity);

        try {
            // Menggunakan fetch untuk mendapatkan stok produk
            const response = await fetch(`pages/customer/controller/get-stock.php?id=${productId}`);
            const data = await response.json();

            if (data.success) {
                const stok = data.stok; // Ambil stok dari respons

                if (stok <= 0) {
                    // Jika stok kurang dari atau sama dengan 0, tampilkan notifikasi
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Habis',
                        text: 'Produk ini tidak tersedia untuk ditambahkan ke keranjang.',
                    });
                } else if (quantity > stok) {
                    // Jika jumlah beli melebihi stok, tampilkan notifikasi
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Melebihi Stok',
                        text: `Stok produk hanya tersedia ${stok} unit. Silakan kurangi jumlah pembelian.`,
                    });
                } else {
                    console.log("Tambahkan ke keranjang");

                    // Jika stok cukup, lanjutkan dengan menambahkan produk ke keranjang
                    var formData = new FormData();

                    var id_produk = document.getElementById('modalProductId').value;
                    formData.append('id_produk', id_produk);

                    formData.append('jumlah', quantity);
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
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mendapatkan data stok produk.',
                });
            }
        } catch (error) {
            console.error('Error fetching stock:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menghubungi server.',
            });
        }
    }



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