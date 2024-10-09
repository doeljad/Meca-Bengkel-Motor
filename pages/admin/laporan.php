<?php
include('pages/controller/connection.php');
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-4">Laporan</h2>
                    <p class="card-description mb-4">Home / <code>Laporan</code></p>
                    <div class="mdc-layout-grid">
                        <div class="mdc-layout-grid__inner">
                            <!-- Laporan Pesanan -->
                            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 mdc-layout-grid__cell--span-6-desktop stretch-card">
                                <div class="mdc-card">
                                    <h5 class="card-title">Laporan Penjualan</h5>
                                    <div class="template-demo">
                                        <div class="row">
                                            <!-- Formulir Laporan Perbulan -->
                                            <div class="col-md-6 mb-3">
                                                <form method="post" action="pages/admin/export/export-pesanan.php">
                                                    <div class="form-group mb-3">
                                                        <label for="report_month" class="form-label">Laporan Perbulan</label>
                                                        <select class="form-control" id="report_month" name="report_month" required>
                                                            <option value="1">Januari</option>
                                                            <option value="2">Februari</option>
                                                            <option value="3">Maret</option>
                                                            <option value="4">April</option>
                                                            <option value="5">Mei</option>
                                                            <option value="6">Juni</option>
                                                            <option value="7">Juli</option>
                                                            <option value="8">Agustus</option>
                                                            <option value="9">September</option>
                                                            <option value="10">Oktober</option>
                                                            <option value="11">November</option>
                                                            <option value="12">Desember</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="generate_report" value="monthly" class="btn btn-primary">Cetak PDF Bulanan</button>
                                                </form>
                                            </div>
                                            <!-- Formulir Laporan Pertahun -->
                                            <div class="col-md-6 mb-3">
                                                <form method="post" action="pages/admin/export/export-pesanan.php">
                                                    <div class="form-group mb-3">
                                                        <label for="report_year" class="form-label">Laporan Pertahun</label>
                                                        <select class="form-control" id="report_year" name="report_year" required>
                                                            <?php
                                                            $currentYear = date('Y');
                                                            $years = [$currentYear - 2, $currentYear - 1, $currentYear];
                                                            foreach ($years as $year) {
                                                                echo "<option value=\"$year\">$year</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="generate_report" value="yearly" class="btn btn-primary">Cetak PDF Tahunan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Pelanggan -->
                            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 mdc-layout-grid__cell--span-6-desktop stretch-card">
                                <div class="mdc-card">
                                    <h5 class="card-title">Laporan Pelanggan</h5>
                                    <div class="template-demo">
                                        <form method="post" action="pages/admin/export/export-pelanggan.php">
                                            <button type="submit" class="btn btn-primary">Cetak PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Kategori -->
                            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 mdc-layout-grid__cell--span-6-desktop stretch-card">
                                <div class="mdc-card">
                                    <h5 class="card-title">Laporan Kategori</h5>
                                    <div class="template-demo">
                                        <form method="post" action="pages/admin/export/export-kategori.php">
                                            <button type="submit" class="btn btn-primary">Cetak PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Produk -->
                            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 mdc-layout-grid__cell--span-6-desktop stretch-card">
                                <div class="mdc-card">
                                    <h5 class="card-title">Laporan Produk</h5>
                                    <div class="template-demo">
                                        <form method="post" action="pages/admin/export/export-produk.php">
                                            <button type="submit" class="btn btn-primary">Cetak PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
    <style>
        /* Card */
        .mdc-card {
            border-radius: 10px;
            /* Rounded corners for cards */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            padding: 20px;
            background-color: #fff;
            transition: box-shadow 0.3s ease;
        }

        .mdc-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            /* Enhanced shadow on hover */
        }

        /* Grid Layout */
        .mdc-layout-grid {
            margin: 0 -15px;
            /* Remove gutter space */
        }

        .mdc-layout-grid__cell {
            padding: 15px;
            /* Add padding around grid cells */
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3, #00408d);
            color: #fff;
            border: none;
            border-radius: 25px;
            /* Rounded corners for buttons */
            padding: 10px 20px;
            font-size: 16px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #003d7a, #002a55);
            transform: scale(1.05);
            /* Zoom effect on hover */
        }

        /* Form Controls */
        .form-control {
            border-radius: 5px;
            /* Rounded corners for form controls */
            border: 1px solid #ced4da;
            /* Light border */
            padding: 10px;
            font-size: 16px;
        }
    </style>


    <?php
    $conn->close();
    ?>