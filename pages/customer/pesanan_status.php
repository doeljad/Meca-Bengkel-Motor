<?php if ($result->num_rows > 0) : ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Tanggal Pesanan</th>
                <th>Total</th>
                <th>Detail Produk</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_pesanan']); ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pesanan']); ?></td>
                    <td><?= htmlspecialchars(number_format($row['total'], 2)); ?></td>
                    <td>
                        <div class="row">
                            <?php
                            // Fetch details for the current order
                            $query_detail = $conn->prepare("
                                SELECT 
                                    dp.id_produk,
                                    dp.id_detail_pesanan, 
                                    p.nama, 
                                    dp.ukuran, 
                                    dp.jumlah, 
                                    dp.catatan, 
                                    p.harga 
                                FROM 
                                    detail_pesanan dp
                                INNER JOIN 
                                    produk p ON dp.id_produk = p.id_produk
                                WHERE 
                                    dp.id_pesanan = ?
                            ");
                            $query_detail->bind_param('s', $row['id_pesanan']);
                            $query_detail->execute();
                            $result_detail = $query_detail->get_result();

                            while ($detail = $result_detail->fetch_assoc()) {
                                // Fetch the image for the current product
                                $query_gambar = $conn->prepare("SELECT nama_file FROM gambar_produk WHERE id_produk = ?");
                                $query_gambar->bind_param('i', $detail['id_produk']);
                                $query_gambar->execute();
                                $result_gambar = $query_gambar->get_result();
                                $gambar = $result_gambar->fetch_assoc();
                                $nama_file = $gambar['nama_file'] ?? 'default.jpg'; // Default image if not found

                                // Check if the product has already been reviewed
                                $query_review = $conn->prepare("
                                    SELECT id_review 
                                    FROM reviews 
                                    WHERE id_produk = ? 
                                    AND id_detail_pesanan = ?
                                ");
                                $query_review->bind_param('is', $detail['id_produk'], $detail['id_detail_pesanan']);
                                $query_review->execute();
                                $result_review = $query_review->get_result();
                                $already_reviewed = $result_review->num_rows > 0;
                            ?>
                                <div class="col-md-12 mb-2">
                                    <div class="card">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3">
                                                <img src="assets/images/produk/<?php echo htmlspecialchars($nama_file); ?>" alt="Product Image" style="width: 50px; height: auto;">
                                            </div>
                                            <div class="text-start">
                                                <?php echo htmlspecialchars($detail['nama']); ?>
                                                <p class="card-text">
                                                    <strong>Ukuran:</strong> <?php echo htmlspecialchars($detail['ukuran']); ?><br>
                                                    <strong>Jumlah:</strong> <?php echo htmlspecialchars($detail['jumlah']); ?><br>
                                                    <strong>Harga:</strong> <?php echo htmlspecialchars(number_format($detail['harga'], 2)); ?><br>
                                                    <strong>Catatan:</strong> <?php echo htmlspecialchars($detail['catatan']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Show the Review Produk button only if the product hasn't been reviewed yet -->
                                <?php if ($row['status'] == 4 && !$already_reviewed) : ?>
                                    <!-- Button to trigger modal -->
                                    <!-- Button to trigger modal -->
                                    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#reviewModal-<?= htmlspecialchars($detail['id_produk'] . '-' . $detail['id_detail_pesanan']); ?>">
                                        Review Produk
                                    </button>

                                <?php endif; ?>
                                <hr>

                                <!-- Review Modal -->
                                <div class="modal fade text-left" id="reviewModal-<?= htmlspecialchars($detail['id_produk'] . '-' . $detail['id_detail_pesanan']); ?>" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="pages/customer/controller/pesanan-action.php" method="post" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="reviewModalLabel">Review Produk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="aksi" value="review">
                                                    <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">
                                                    <input type="hidden" name="id_detail_pesanan" value="<?= htmlspecialchars($detail['id_detail_pesanan']); ?>">
                                                    <input type="hidden" name="id_produk" value="<?= htmlspecialchars($detail['id_produk']); ?>">
                                                    <div class="mb-3">
                                                        <label for="rating" class="form-label">Rating (1-5)</label>
                                                        <select name="rating" id="rating" class="form-select" required>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="review_text" class="form-label">Review</label>
                                                        <textarea name="review_text" id="review_text" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="media" class="form-label">Upload Gambar/Video <small>(opsional)</small></label>
                                                        <input type="file" name="media[]" id="media" class="form-control" accept="image/*,video/*" multiple>
                                                        <small class="form-text text-muted">Anda dapat mengunggah beberapa gambar atau video.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>


                            <?php
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($row['status'] == 1) : ?>
                            <form action="pages/customer/controller/pesanan-action.php" method="post" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                <input type="hidden" name="aksi" value="batalkan">
                                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">
                                <button type="submit" class="btn btn-danger">Batalkan</button>
                            </form>
                        <?php elseif ($row['status'] == 0) : ?>
                            <form id="bayar-form-<?= htmlspecialchars($row['id_pesanan']); ?>" method="post" class="mb-2" onsubmit="return confirm('Apakah Anda yakin ingin membayar pesanan ini?');">
                                <input type="hidden" name="aksi" value="bayar">
                                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">
                                <button type="button" class="btn btn-success" onclick="bayarPesanan('<?= htmlspecialchars($row['id_pesanan']); ?>')">Bayar</button>
                            </form>
                            <form action="pages/customer/controller/pesanan-action.php" method="post" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                <input type="hidden" name="aksi" value="batalkan">
                                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">
                                <button type="submit" class="btn btn-danger">Batalkan</button>
                            </form>
                        <?php elseif ($row['status'] == 3) : ?>
                            <form action="pages/customer/controller/pesanan-action.php" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?');">
                                <input type="hidden" name="aksi" value="diterima">
                                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">
                                <button type="submit" class="btn btn-success">Diterima</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>Belum ada pesanan untuk status ini.</p>
<?php endif; ?>

<script>
    function bayarPesanan(id_pesanan) {
        fetch('pages/customer/controller/pesanan-action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id_pesanan': id_pesanan,
                    'aksi': 'bayar'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment Success:', result);
                            saveTransactionData(result, id_pesanan, 1); // Save transaction and update order status
                        },
                        onPending: function(result) {
                            console.log('Payment Pending:', result);
                            saveTransactionData(result, id_pesanan, 0); // Save transaction and update order status
                        },
                        onClose: function() {
                            console.log('Pembayaran dibatalkan');
                            updateOrderStatus(id_pesanan, 0); // Only update order status
                        },
                        onError: function(result) {
                            console.log('Payment Error:', result);
                            updateOrderStatus(id_pesanan, 0); // Only update order status
                            alert('Payment failed. Please try again.');
                        }
                    });
                } else {
                    alert(data.error || 'Error generating payment token.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing payment.');
            });
    }

    function saveTransactionData(result, id_pesanan, status) {
        fetch('pages/customer/controller/pesanan-action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id_pesanan': id_pesanan,
                    'aksi': 'save_transaction',
                    'status': status,
                    'id_transaksi': result.transaction_id,
                    'tanggal_pembayaran': result.transaction_time,
                    'total': result.gross_amount,
                    'metode': result.payment_type,
                    'transaksi_status': result.transaction_status
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log('Transaction saved and status updated:', data);
                window.location.reload(); // Reload the page to reflect changes
            })
            .catch(error => {
                console.error('Error saving transaction data:', error);
            });
    }

    function updateOrderStatus(id_pesanan, status) {
        fetch('pages/customer/controller/pesanan-action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id_pesanan': id_pesanan,
                    'aksi': 'update_status',
                    'status': status
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log('Order status updated:', data);
                window.location.reload(); // Reload the page to reflect status changes
            })
            .catch(error => {
                console.error('Error updating order status:', error);
            });
    }
</script>