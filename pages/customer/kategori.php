<?php
// Ensure the id is set and is a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_kategori = $_GET['id'];

    // Prepare the SQL statement to fetch products by category
    $query = "
        SELECT p.*, gp.nama_file 
        FROM produk p
        LEFT JOIN gambar_produk gp ON gp.id_produk = p.id_produk
        WHERE p.id_kategori = ?
    ";
    $stmt = $conn->prepare($query);

    // Bind the parameter
    $stmt->bind_param("i", $id_kategori);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $produk = $stmt->get_result();

    // Fetch the category name for display
    $query_kategori = "SELECT nama_kategori FROM kategori WHERE id_kategori = ?";
    $stmt_kategori = $conn->prepare($query_kategori);
    $stmt_kategori->bind_param("i", $id_kategori);
    $stmt_kategori->execute();
    $result_kategori = $stmt_kategori->get_result();

    // Check if the category exists
    if ($result_kategori->num_rows > 0) {
        $row_kategori = $result_kategori->fetch_assoc();
        $nama_kategori = $row_kategori['nama_kategori'];
    } else {
        // Handle the case where the category is not found
        $nama_kategori = "Kategori tidak ditemukan";
    }

    // Close the statements
    $stmt->close();
    $stmt_kategori->close();
} else {
    // Handle the case where the id is not valid
    $nama_kategori = "Kategori tidak valid";
    $produk = null; // Set produk to null or an empty array
}
?>
<br><br>
<div class="container-fluid pt-5 mt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Kategori <?= htmlspecialchars($nama_kategori); ?></span></h2>
    <div class="row px-xl-5">
        <?php if ($produk && $produk->num_rows > 0) : ?>
            <?php while ($row = $produk->fetch_assoc()) : ?>
                <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                    <div class="product-item bg-light mb-4">
                        <div class="product-img position-relative overflow-hidden">
                            <img class="img-fluid w-100" src="assets/images/produk/<?php echo htmlspecialchars($row['nama_file']); ?>" alt="">
                            <div class="product-action">
                                <a class="btn btn-outline-dark btn-square" href="javascript:showCustomProductModal(<?php echo $row['id_produk']; ?>)">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                                <a class="btn btn-outline-dark btn-square" href="index.php?page=detail-barang&id=<?= htmlspecialchars($row['id_produk']); ?>"><i class="fa fa-search"></i></a>
                            </div>
                        </div>
                        <div class="text-center py-4">
                            <a class="h6 text-decoration-none text-truncate" href=""><?php echo htmlspecialchars($row['nama']); ?></a>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <h5>Rp<?php echo number_format($row['harga']); ?></h5>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                    <small class="fa fa-star text-primary mr-1"></small>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Data produk untuk kategori <?= htmlspecialchars($nama_kategori); ?> tidak tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="customProductModal" tabindex="-1" role="dialog" aria-labelledby="customProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customProductModalLabel">Kustom Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customProductForm">
                    <input type="hidden" id="modalProductId">

                    <!-- Pilihan Ukuran (Radio Button) -->
                    <div class="form-group" id="sizeOptions">
                        <label for="size">Ukuran</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="size" id="sizeS" value="S" required>
                            <label class="form-check-label" for="sizeS">S</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="size" id="sizeM" value="M" required>
                            <label class="form-check-label" for="sizeM">M</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="size" id="sizeL" value="L" required>
                            <label class="form-check-label" for="sizeL">L</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="size" id="sizeXL" value="XL" required>
                            <label class="form-check-label" for="sizeXL">XL</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="size" id="sizeXXL" value="XXL" required>
                            <label class="form-check-label" for="sizeXXL">XXL</label>
                        </div>
                    </div>

                    <!-- Input Ukuran (Tersembunyi Awal) -->
                    <div class="form-group d-none" id="customSizeInput">
                        <label for="sizeCustom">Ukuran</label>
                        <input type="text" class="form-control" id="sizeCustom">
                    </div>

                    <div class="form-group">
                        <label for="quantity">Jumlah</label>
                        <input type="number" class="form-control" id="quantity" required>
                    </div>

                    <!-- Catatan -->
                    <div class="form-group">
                        <label for="note">Catatan</label>
                        <textarea class="form-control" id="note"></textarea>
                    </div>

                    <!-- Upload Gambar (Tersembunyi Awal) -->
                    <div class="form-group d-none" id="uploadImage">
                        <label for="file">Upload Gambar</label>
                        <input type="file" class="form-control" id="file">
                    </div>

                    <button type="button" id="custom" class="btn btn-primary">Kustom Produk</button>
                    <button type="submit" id="addtocart" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Pastikan fungsi ini berada di file JavaScript yang di-load
    document.getElementById('custom').addEventListener('click', function() {
        // Sembunyikan pilihan ukuran (radio button) dan tampilkan input ukuran
        document.getElementById('sizeOptions').classList.add('d-none');
        document.getElementById('customSizeInput').classList.remove('d-none');

        // Tampilkan opsi upload gambar
        document.getElementById('uploadImage').classList.remove('d-none');
        document.getElementById('custom').classList.add('d-none');
    });
    // Pastikan fungsi ini berada di file JavaScript yang di-load
    function showCustomProductModal(id_produk) {
        // Set product ID in modal hidden input
        document.getElementById('modalProductId').value = id_produk;
        // Show the modal
        $('#customProductModal').modal('show');
    }
    $('.close').on('click', function() {
        // Conditionally hide/show elements based on your logic
        var customSizeInput = $('#sizeCustom').val().trim();
        if (customSizeInput === '') {
            $('#sizeOptions').removeClass('d-none');
            $('#customSizeInput').addClass('d-none');
            $('#uploadImage').addClass('d-none');
            $('#custom').removeClass('d-none');
        } else {
            $('#sizeOptions').addClass('d-none');
            $('#customSizeInput').removeClass('d-none');
        }
        // Hide the modal
        $('#customProductModal').modal('hide');
    });
    document.getElementById('custom').onclick = function() {
        // Mengubah tampilan menjadi mode kustom
        document.getElementById('sizeOptions').classList.add('d-none');
        document.getElementById('customSizeInput').classList.remove('d-none');
        document.getElementById('uploadImage').classList.remove('d-none');
    };

    document.getElementById('addtocart').onclick = function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        var formData = new FormData();

        var id_produk = document.getElementById('modalProductId').value;
        formData.append('id_produk', id_produk);

        var customSizeInput = document.getElementById('customSizeInput');

        if (!customSizeInput.classList.contains('d-none')) {
            var ukuran = document.getElementById('sizeCustom').value;
            console.log('Ukuran kustom:', ukuran); // Debugging log
            if (ukuran) {
                formData.append('ukuran', ukuran);
            } else {
                console.error('Ukuran kustom belum diisi');
                return;
            }

            var fileInput = document.getElementById('file');
            if (fileInput && fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);
            } else {
                console.error('File gambar belum diunggah');
                return;
            }
        } else {
            var ukuran = document.querySelector('input[name="size"]:checked');
            console.log('Ukuran biasa:', ukuran ? ukuran.value : 'Tidak ada ukuran yang dipilih'); // Debugging log
            if (ukuran) {
                formData.append('ukuran', ukuran.value);
            } else {
                console.error('Ukuran belum dipilih');
                return;
            }
        }

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

    function removeFromCart(id_detail_keranjang) {
        $.ajax({
            url: 'pages/customer/controller/hapus-keranjang.php',
            type: 'POST',
            data: {
                id_detail_keranjang: id_detail_keranjang
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghapus produk dari keranjang.');
            }
        });
    }
</script>