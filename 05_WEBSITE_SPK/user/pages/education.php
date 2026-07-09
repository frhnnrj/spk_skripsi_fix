<?php
require_once '../../config/database.php';

$pakar = get_all_pakar();
$current_user = get_current_user();
$is_logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belajar Investasi - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .instrument-card { background: white; border: 2px solid #e5e7eb; border-radius: 8px; padding: 2rem; margin-bottom: 1.5rem; }
        .instrument-card:hover { border-color: var(--primary); box-shadow: 0 4px 12px rgba(59,130,246,0.2); }
        .pros-cons { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1rem 0; }
        .pros, .cons { padding: 1rem; border-radius: 6px; }
        .pros { background: rgba(34,197,94,0.1); border-left: 4px solid var(--success); }
        .cons { background: rgba(239,68,68,0.1); border-left: 4px solid var(--danger); }
        .pros h4, .cons h4 { margin-top: 0; }
        .pros li, .cons li { margin: 0.5rem 0; }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>👤 Panel User</h1>
                <p>Pelajari 5 Instrumen Investasi</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Beranda</a></li>
                <li><a href="education.php" class="active">📚 Belajar</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="assessment.php">📝 Penilaian</a></li>
                    <li><a href="results.php">📊 Hasil</a></li>
                <?php endif; ?>
                <li style="margin-left: auto;">
                    <?php if ($is_logged_in): ?>
                        <span style="color: var(--gray); margin-right: 1rem;">👤 <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        <a href="../logout.php" style="color: #ef4444;">🚪 Logout</a>
                    <?php else: ?>
                        <a href="../login.php">🔐 Login</a>
                        <span style="color: var(--gray); margin: 0 0.5rem;">/</span>
                        <a href="../signup.php">✨ Daftar</a>
                    <?php endif; ?>
                </li>
                <li><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📚 Pelajari 5 Instrumen Investasi</h2>
                <p style="color: var(--gray); margin-bottom: 2rem;">
                    Pahami karakteristik, kelebihan, dan kekurangan dari setiap instrumen investasi sebelum melakukan penilaian.
                </p>

                <!-- 1. Kripto -->
                <div class="instrument-card">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; flex: 1;">🪙 Kripto (Cryptocurrency)</h3>
                        <span class="badge badge-danger" style="font-size: 0.9rem;">RISIKO TINGGI</span>
                    </div>
                    
                    <p><strong>Definisi:</strong> Aset digital terenkripsi yang beroperasi tanpa otoritas pusat (e.g., Bitcoin, Ethereum).</p>
                    
                    <div class="pros-cons">
                        <div class="pros">
                            <h4>✅ Kelebihan</h4>
                            <ul>
                                <li>Potensi return sangat tinggi (bullish market)</li>
                                <li>24/7 trading (tidak ada jam tutup)</li>
                                <li>Likuiditas tinggi di exchange utama</li>
                                <li>Akses mudah via smartphone</li>
                                <li>Transparansi blockchain</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <h4>❌ Kekurangan</h4>
                            <ul>
                                <li>Volatilitas SANGAT tinggi (fluktuasi ekstrem)</li>
                                <li>Regulasi masih uncertain di Indonesia</li>
                                <li>Risiko hacking & kehilangan akses</li>
                                <li>Psychological impact (emotional trading)</li>
                                <li>Biaya transaksi variabel & tinggi</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table style="width: 100%; margin-top: 1rem;">
                        <tr style="background: #f3f4f6;">
                            <td><strong>Return Potensial:</strong></td>
                            <td>20-1000% (sangat tinggi, tidak stabil)</td>
                        </tr>
                        <tr>
                            <td><strong>Risiko:</strong></td>
                            <td>Sangat Tinggi ⚠️⚠️⚠️</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Likuiditas:</strong></td>
                            <td>Tinggi (bisa dicairkan cepat)</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Minimum:</strong></td>
                            <td>Rp 1.000 - Rp 100.000 (satoshi/wei)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Cocok Untuk:</strong></td>
                            <td>Investor agresif dengan tolerance risiko tinggi</td>
                        </tr>
                    </table>
                </div>

                <!-- 2. Saham -->
                <div class="instrument-card">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; flex: 1;">📈 Saham (Equity)</h3>
                        <span class="badge badge-warning" style="font-size: 0.9rem;">RISIKO SEDANG-TINGGI</span>
                    </div>
                    
                    <p><strong>Definisi:</strong> Kepemilikan sebagian dari perusahaan. Investor mendapat dividen & keuntungan capital gains.</p>
                    
                    <div class="pros-cons">
                        <div class="pros">
                            <h4>✅ Kelebihan</h4>
                            <ul>
                                <li>Return jangka panjang menarik (6-15% p.a.)</li>
                                <li>Dividen reguler dari profit perusahaan</li>
                                <li>Regulasi jelas (OJK, BEI)</li>
                                <li>Likuiditas baik di BEI</li>
                                <li>Bisa diversifikasi dengan mudah</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <h4>❌ Kekurangan</h4>
                            <ul>
                                <li>Volatilitas medium (fluktuasi harian)</li>
                                <li>Membutuhkan riset fundamental perusahaan</li>
                                <li>Jam trading terbatas (09:00-16:00 WIB)</li>
                                <li>Biaya broker & pajak capital gains</li>
                                <li>Affected by market sentiment & ekonomi makro</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table style="width: 100%; margin-top: 1rem;">
                        <tr style="background: #f3f4f6;">
                            <td><strong>Return Potensial:</strong></td>
                            <td>6-20% p.a. (jangka panjang)</td>
                        </tr>
                        <tr>
                            <td><strong>Risiko:</strong></td>
                            <td>Sedang-Tinggi ⚠️⚠️</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Likuiditas:</strong></td>
                            <td>Sedang-Tinggi (tergantung saham)</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Minimum:</strong></td>
                            <td>Rp 100.000 - 1.000.000 (100 lot)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Cocok Untuk:</strong></td>
                            <td>Investor jangka panjang dengan risiko sedang</td>
                        </tr>
                    </table>
                </div>

                <!-- 3. SBN Ritel -->
                <div class="instrument-card">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; flex: 1;">🏛️ SBN Ritel (Surat Berharga Negara)</h3>
                        <span class="badge badge-info" style="font-size: 0.9rem;">RISIKO RENDAH</span>
                    </div>
                    
                    <p><strong>Definisi:</strong> Obligasi pemerintah Indonesia yang dijual ke retail. Jaminan dari negara, tidak default.</p>
                    
                    <div class="pros-cons">
                        <div class="pros">
                            <h4>✅ Kelebihan</h4>
                            <ul>
                                <li>Risiko default SANGAT rendah (backed by government)</li>
                                <li>Kupon/bunga dijamin fixed</li>
                                <li>Bebas PPh untuk investor retail</li>
                                <li>Capital preservation terjamin</li>
                                <li>Cocok untuk investor konservatif</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <h4>❌ Kekurangan</h4>
                            <ul>
                                <li>Return moderat (4-7% p.a.)</li>
                                <li>Likuiditas terbatas (secondary market lemah)</li>
                                <li>Jangka waktu panjang (3-20 tahun)</li>
                                <li>Terpengaruh pergerakan suku bunga BI</li>
                                <li>Inflation risk (purchasing power menurun)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table style="width: 100%; margin-top: 1rem;">
                        <tr style="background: #f3f4f6;">
                            <td><strong>Return Potensial:</strong></td>
                            <td>4-7% p.a. (fixed, pasti)</td>
                        </tr>
                        <tr>
                            <td><strong>Risiko:</strong></td>
                            <td>Sangat Rendah ✅</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Likuiditas:</strong></td>
                            <td>Rendah (sulit dijual kembali)</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Minimum:</strong></td>
                            <td>Rp 1.000.000 (minimum pembelian)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Cocok Untuk:</strong></td>
                            <td>Investor konservatif, planning jangka panjang</td>
                        </tr>
                    </table>
                </div>

                <!-- 4. Reksa Dana -->
                <div class="instrument-card">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; flex: 1;">💼 Reksa Dana (Mutual Fund)</h3>
                        <span class="badge badge-warning" style="font-size: 0.9rem;">RISIKO SEDANG</span>
                    </div>
                    
                    <p><strong>Definisi:</strong> Dana yang dikelola profesional, diinvestasikan ke portfolio saham/obligasi dengan diversifikasi.</p>
                    
                    <div class="pros-cons">
                        <div class="pros">
                            <h4>✅ Kelebihan</h4>
                            <ul>
                                <li>Diversifikasi otomatis (mengurangi risiko)</li>
                                <li>Dikelola oleh tim profesional</li>
                                <li>Akses mudah via e-fund/aplikasi</li>
                                <li>Return kompetitif (7-15% p.a. untuk equity)</li>
                                <li>Flexibility (bisa cairkan kapan saja)</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <h4>❌ Kekurangan</h4>
                            <ul>
                                <li>Biaya manager fee (0.5-1.5% p.a.)</li>
                                <li>Risiko tergantung jenis (equity risiko tinggi)</li>
                                <li>Performance tidak guarantee</li>
                                <li>Pajak PPA (PPh 15%) untuk dividen</li>
                                <li>T+3 untuk withdrawal (pencairan)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table style="width: 100%; margin-top: 1rem;">
                        <tr style="background: #f3f4f6;">
                            <td><strong>Return Potensial:</strong></td>
                            <td>5-15% p.a. (tergantung jenis)</td>
                        </tr>
                        <tr>
                            <td><strong>Risiko:</strong></td>
                            <td>Sedang (bisa diatur via jenis RD)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Likuiditas:</strong></td>
                            <td>Tinggi (bisa dicairkan T+3)</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Minimum:</strong></td>
                            <td>Rp 10.000 - 100.000 (investasi awal)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Cocok Untuk:</strong></td>
                            <td>Investor pemula & jangka menengah yang ingin diversifikasi</td>
                        </tr>
                    </table>
                </div>

                <!-- 5. Emas Digital -->
                <div class="instrument-card">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0; flex: 1;">🔘 Emas Digital (E-Gold)</h3>
                        <span class="badge badge-info" style="font-size: 0.9rem;">RISIKO RENDAH-SEDANG</span>
                    </div>
                    
                    <p><strong>Definisi:</strong> Kepemilikan emas fisik dalam bentuk digital (e.g., Antam, Pegadaian). Dijamin dengan emas asli.</p>
                    
                    <div class="pros-cons">
                        <div class="pros">
                            <h4>✅ Kelebihan</h4>
                            <ul>
                                <li>Safe haven asset (stable value)</li>
                                <li>Hedge terhadap inflasi & devaluasi rupiah</li>
                                <li>Akses mudah (bisa lepas online)</li>
                                <li>Tidak ada interest rate risk</li>
                                <li>Backing fisik (bisa ditukar emas)</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <h4>❌ Kekurangan</h4>
                            <ul>
                                <li>Return rendah (hanya capital appreciation)</li>
                                <li>Tidak ada income/dividend</li>
                                <li>Margin spread buy-sell cukup besar</li>
                                <li>Volatilitas terhadap USD & sentimen global</li>
                                <li>Biaya penyimpanan fisik (walau digital berkurang)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table style="width: 100%; margin-top: 1rem;">
                        <tr style="background: #f3f4f6;">
                            <td><strong>Return Potensial:</strong></td>
                            <td>2-8% p.a. (tergantung harga emas global)</td>
                        </tr>
                        <tr>
                            <td><strong>Risiko:</strong></td>
                            <td>Rendah-Sedang ⚠️</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Likuiditas:</strong></td>
                            <td>Tinggi (bisa dijual cepat online)</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Minimum:</strong></td>
                            <td>Rp 10.000 - 100.000 (tergantung platform)</td>
                        </tr>
                        <tr style="background: #f3f4f6;">
                            <td><strong>Cocok Untuk:</strong></td>
                            <td>Investor konservatif yang ingin diversifikasi & hedge inflation</td>
                        </tr>
                    </table>
                </div>

                <!-- Comparison Table -->
                <div class="card" style="margin-top: 2rem;">
                    <h3>📊 Tabel Perbandingan Lengkap</h3>
                    <table style="font-size: 0.9rem;">
                        <thead>
                            <tr style="background: rgba(59,130,246,0.2);">
                                <th>Instrumen</th>
                                <th>Return</th>
                                <th>Risiko</th>
                                <th>Likuiditas</th>
                                <th>Modal Min</th>
                                <th>Jangka Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>🪙 Kripto</strong></td>
                                <td>20-1000%</td>
                                <td>Sangat Tinggi</td>
                                <td>Tinggi</td>
                                <td>Rp 1K</td>
                                <td>Pendek (spekulasi)</td>
                            </tr>
                            <tr style="background: #f9fafb;">
                                <td><strong>📈 Saham</strong></td>
                                <td>6-20%</td>
                                <td>Sedang-Tinggi</td>
                                <td>Sedang-Tinggi</td>
                                <td>Rp 100K</td>
                                <td>Panjang (1-10 tahun)</td>
                            </tr>
                            <tr>
                                <td><strong>🏛️ SBN Ritel</strong></td>
                                <td>4-7%</td>
                                <td>Sangat Rendah</td>
                                <td>Rendah</td>
                                <td>Rp 1M</td>
                                <td>Panjang (3-20 tahun)</td>
                            </tr>
                            <tr style="background: #f9fafb;">
                                <td><strong>💼 Reksa Dana</strong></td>
                                <td>5-15%</td>
                                <td>Sedang</td>
                                <td>Tinggi</td>
                                <td>Rp 10K</td>
                                <td>Menengah (1-10 tahun)</td>
                            </tr>
                            <tr>
                                <td><strong>🔘 Emas Digital</strong></td>
                                <td>2-8%</td>
                                <td>Rendah-Sedang</td>
                                <td>Tinggi</td>
                                <td>Rp 10K</td>
                                <td>Panjang (5-20 tahun)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Next Step -->
                <div class="box" style="margin-top: 2rem; background: linear-gradient(135deg, rgba(34,197,94,0.1), rgba(59,130,246,0.1)); border-left-color: var(--success);">
                    <h3>🎯 Langkah Berikutnya</h3>
                    <p>Setelah memahami karakteristik setiap instrumen, lanjutkan ke <strong><a href="assessment.php">📝 Penilaian</a></strong> untuk menginput preferensi investasi Anda berdasarkan profil risiko.</p>
                    <a href="assessment.php" class="btn btn-primary" style="margin-top: 1rem;">Lanjut ke Penilaian →</a>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; 2026 SPK AHP-TOPSIS | Panel User</p>
            </div>
        </footer>
    </div>
</body>
</html>
