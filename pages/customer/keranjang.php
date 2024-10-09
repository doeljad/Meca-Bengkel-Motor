<?php
include('pages/controller/connection.php');

// session_start();
// Periksa apakah semua variabel sesi yang diperlukan ada
if (
    !isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] ||
    !isset($_SESSION['id_user']) ||
    !isset($_SESSION['username']) ||
    !isset($_SESSION['name']) ||
    !isset($_SESSION['role'])
) {
    // Jika salah satu variabel sesi tidak ada, tampilkan notifikasi Swal dan arahkan ke halaman logout
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Anda harus login terlebih dahulu',
            showConfirmButton: false,
            timer: 2000,
            didClose: () => {
                window.location.href = 'logout.php';
            }
        });
    </script>";
    exit;
}
$id_user = $_SESSION['id_user'];
$sql = "SELECT 
    detail_keranjang.*, 
    produk.nama, 
    produk.harga, 
    produk.stok, 
    (SELECT nama_file 
     FROM gambar_produk 
     WHERE gambar_produk.id_produk = produk.id_produk 
     LIMIT 1) AS nama_file 
FROM 
    detail_keranjang 
INNER JOIN 
    produk ON detail_keranjang.id_produk = produk.id_produk 
WHERE 
    detail_keranjang.id_keranjang = (
        SELECT keranjang.id_keranjang 
        FROM keranjang 
        INNER JOIN pelanggan ON keranjang.id_pelanggan = pelanggan.id_pelanggan
        WHERE pelanggan.id_user = $id_user AND keranjang.status = 'aktif'
        LIMIT 1
    );
";



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $weight = $_POST['weight'];
    $courier = $_POST['courier'];

    $shippingData = getShippingCost($origin, $destination, $weight, $courier);

    echo json_encode($shippingData);
}
$result = $conn->query($sql);
?>
<!-- Breadcrumb Start -->
<div class="container-fluid mt-5">
    <div class="row px-xl-5">
        <div class="col-12">
            <nav class="breadcrumb bg-light mb-30">
                <a class="breadcrumb-item text-dark" href="#">Home</a>
                <a class="breadcrumb-item text-dark" href="#">Belanja</a>
                <span class="breadcrumb-item active">Keranjang Belanja</span>
            </nav>
        </div>
    </div>
</div>

<!-- Breadcrumb End -->


<!-- Cart Start -->
<div class="container-fluid">
    <div class="row px-xl-5">
        <!-- Bagian Produk dalam Keranjang -->
        <div class="col-lg-8 table-responsive mb-5">
            <table class="table table-light table-borderless table-hover text-center mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th></th>
                        <th>Products</th>
                        <th>Harga</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    <?php
                    $no = 1;
                    $total_belanja = 0;
                    while ($row = $result->fetch_assoc()) :
                        $total = $row['harga'] * $row['jumlah'];
                        $total_belanja += $total;
                    ?>
                        <tr>
                            <td><?php echo $no++; ?>. </td>
                            <td><img src="assets/images/produk/<?php echo $row['nama_file']; ?>" alt="" class="img-fluid" style="width: 50px; height: auto;"></td>
                            <td class="align-middle"><?php echo $row['nama'] ?></td>
                            <td class="align-middle">Rp<?php echo number_format($row['harga']); ?></td>
                            <td class="align-middle">
                                <div class="input-group quantity mx-auto" style="width: 100px;">

                                    <input type="text" class="form-control form-control-sm border-0 text-center" value="<?php echo $row['jumlah']; ?>" readonly>

                                </div>
                            </td>
                            <td class="align-middle">Rp<?php echo number_format($total); ?></td>
                            <td class="align-middle"><button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $row['id_detail_keranjang']; ?>)"><i class="fa fa-times"></i></button></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Bagian Ringkasan dan Checkout -->
        <div class="col-lg-4">
            <!-- Form Pengiriman -->
            <form id="shipping-form">
                <div class="form-group">
                    <label for="destination">Kota Tujuan:</label>
                    <select id="destination" class="form-control" name="destination" required>
                        <!-- Options will be dynamically populated here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="courier">Pilih Kurir:</label>
                    <select id="courier" class="form-control" name="courier" required>
                        <option value="">Pilih Kurir</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                        <!-- Add more couriers if needed -->
                    </select>
                </div>
            </form>

            <!-- Ringkasan Keranjang -->
            <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-light pr-3">Ringkasan Keranjang</span></h5>
            <div class="bg-light p-30 mb-5">
                <div class="border-bottom pb-2">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Subtotal</h6>
                        <h6 id="subtotal">Rp<?php echo number_format($total_belanja); ?></h6>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Ongkos Kirim</h6>
                        <h6 id="ongkir">Rp0</h6>
                    </div>
                    <div class="d-flex justify-content-between mb-3" id="shipping-cost">
                        <h6>Biaya Layanan</h6>
                        <h6 id="biaya_layanan">Rp0</h6>
                    </div>
                </div>
                <div class="pt-2">
                    <div class="d-flex justify-content-between mt-2">
                        <h5>Total</h5>
                        <h5 id="total">Rp<?php echo number_format($total_belanja); ?></h5>
                    </div>
                    <button id="checkout-button" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Checkout</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cart End -->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchCities();

        // Add event listeners to form fields
        document.getElementById('courier').addEventListener('change', fetchShippingCost);
        document.getElementById('destination').addEventListener('change', fetchShippingCost);

        // Initial cart total update without shipping cost
        updateCartTotal(0);
    });

    // Function to fetch city data
    function fetchCities() {
        fetch('pages/customer/controller/get-city.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.rajaongkir && data.rajaongkir.results) {
                    populateCities(data.rajaongkir.results);
                } else {
                    console.error('Error fetching city data.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Function to populate the destination dropdown with cities
    function populateCities(cities) {
        const $destination = document.getElementById('destination');
        $destination.innerHTML = ''; // Clear existing options

        // Add placeholder option
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Pilih Kota Tujuan';
        $destination.appendChild(placeholderOption);

        // Populate cities
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.city_id;
            option.textContent = city.city_name;
            $destination.appendChild(option);
        });
    }

    function fetchShippingCost() {
        const courier = document.getElementById('courier').value;
        const origin = 445; // Assuming 445 is a fixed origin city ID
        const destination = document.getElementById('destination').value;
        const weight = 1000; // Assuming the weight is fixed at 1000 grams

        if (courier && origin && destination && weight) {
            getShippingCost(courier, origin, destination, weight);
        } else {
            // Hide the shipping cost if any field is incomplete
            document.getElementById('ongkir').textContent = '';
            document.getElementById('shipping-cost').style.display = 'none';
            updateCartTotal(0); // Ensure total is updated without shipping cost
        }
    }

    function getShippingCost(courier, origin, destination, weight) {
        fetch('pages/customer/controller/get-shipping-cost.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    courier: courier,
                    origin: origin,
                    destination: destination,
                    weight: weight
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.cost !== undefined) {
                    const ongkir = data.cost;
                    document.getElementById('ongkir').textContent = `Rp${ongkir.toLocaleString()}`;
                    document.getElementById('shipping-cost').style.display = 'block';
                    updateCartTotal(ongkir); // Update cart total with the shipping cost
                } else {
                    document.getElementById('shipping-cost').style.display = 'none';
                    alert('Failed to calculate shipping cost.');
                    updateCartTotal(0); // Update total without shipping cost if failed
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the shipping cost.');
                updateCartTotal(0); // Update total without shipping cost if an error occurs
            });
    }

    // Function to update the cart total including shipping cost
    function updateCartTotal(ongkir = 0) {
        fetch('pages/customer/controller/total-keranjang.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `ongkir=${ongkir}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('subtotal').innerText = 'Rp' + data.subtotal.toLocaleString();
                    document.getElementById('biaya_layanan').innerText = 'Rp' + data.biaya_layanan.toLocaleString();
                    document.getElementById('ongkir').innerText = 'Rp' + data.ongkir.toLocaleString();
                    document.getElementById('total').innerText = 'Rp' + data.total.toLocaleString();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }



    $('#destination').select2({
        placeholder: 'Pilih Kota Tujuan',
        allowClear: true
    });
    // $('#courier').select2({});



    document.getElementById('checkout-button').onclick = function() {
        const totalText = document.getElementById('total').innerText;
        const cleanedTotal = totalText.replace(/[^\d]/g, '');
        const amount = parseInt(cleanedTotal);

        fetch('pages/customer/controller/checkout-keranjang.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    snap.pay(data.token, {
                        onSuccess: function(result) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Pembayaran Anda Berhasil',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                saveTransaction(result.order_id, result, 1);
                                window.location.href = '?page=keranjang';
                            });
                        },
                        onPending: function(result) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran Ditunda',
                                text: 'Pembayaran Anda Tertunda',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                saveTransaction(result.order_id, result, 0);
                                window.location.href = '?page=keranjang';
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Pembayaran!',
                                text: 'Terjadi kesalahan saat memproses pembayaran Anda',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                saveTransaction(result.order_id, result, 0);
                                window.location.href = '?page=keranjang';
                            });
                        },
                        onClose: function(result) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Popup pembayaran ditutup',
                                text: 'Anda menutup popup tanpa menyelesaikan pembayaran.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                if (result) {
                                    result.status = 'cancel'; // Menandai bahwa pembayaran dibatalkan
                                    saveTransaction(result.order_id, result, 0);
                                }
                                window.location.href = '?page=keranjang';
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an error with your request.',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
    };

    function saveTransaction(pesananId, paymentResult, statusPesanan) {
        var transactionData = {
            id_pesanan: pesananId,
            total: paymentResult.gross_amount,
            metode: paymentResult.payment_type,
            status: paymentResult.transaction_status,
            statusPesanan: statusPesanan
        };

        $.ajax({
            url: 'pages/admin/controller/simpan-transaksi.php',
            type: 'POST',
            data: transactionData,
            success: function(response) {
                var responseData = JSON.parse(response);
                if (responseData.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: responseData.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: responseData.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan transaksi. Silakan coba lagi.'
                });
            }
        });
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

    function removeFromCart(id_detail_keranjang) {
        $.ajax({
            url: 'pages/customer/controller/hapus-keranjang.php',
            type: 'POST',
            data: {
                id_detail_keranjang: id_detail_keranjang
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert(data.message); // Show error message from server
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghapus produk dari keranjang.');
            }
        });
    }
</script>