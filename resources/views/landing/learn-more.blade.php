<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pelajari Lebih Lanjut — Schedify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/landing-new.css') }}" />
    <meta name="description" content="Pelajari lebih lanjut tentang Schedify: sistem plotting jadwal & ruang kelas yang otomatis, efisien, dan transparan." />
  </head>
  <body class="learn-more-page">
    <div class="page-gradient">
      <header class="site-header">
        <div class="container header-inner">
          <div class="brand">
            <img src="{{ asset('images/img crop.png') }}" alt="Schedify logo" class="logo-img" />
            <span class="brand-text">Schedify</span>
          </div>
          <nav class="main-nav" aria-label="Main Navigation">
            <a href="{{ route('landing.index') }}#features">Fitur</a>
            <a href="{{ route('landing.index') }}#about">About Us</a>
            <a href="{{ route('landing.index') }}#contact">Kontak</a>
            <a class="btn btn-outline btn-login" href="{{ route('login') }}">Login</a>
          </nav>
        </div>
      </header>

      <main>
        <section class="section section-white">
          <div class="container">
            <div class="top-row">
              <a href="{{ route('landing.index') }}" class="btn-back"><span class="chev">←</span><span>Kembali</span></a>
            </div>

            <div class="section-header center lm-hero">
              
              <h2 class="lm-heading">Gambaran Umum Sistem</h2>
              <p class="muted">Schedify adalah sistem plotting jadwal kuliah dan ruang kelas yang dirancang khusus untuk mengatasi kompleksitas penjadwalan di lingkungan fakultas.</p>
            </div>

            <div class="grid lm-panels">
              <div class="panel panel-a">
                <div class="panel-title-row">
                  <img src="{{ asset('images/logo!.png') }}" alt="Logo" class="panel-icon-img" />
                  <h3>Masalah Umum</h3>
                </div>
                <ul>
                  <li>Jadwal bentrok kelas, dosen, atau ruangan</li>
                  <li>Ruang tidak terpakai optimal</li>
                  <li>Koordinasi manual lama dan rawan salah</li>
                </ul>
              </div>
              <div class="panel panel-b">
                <div class="panel-title-row">
                  <img src="{{ asset('images/logocheck.png') }}" alt="Check" class="panel-icon-img" />
                  <h3>Solusi Schedify</h3>
                </div>
                <ul>
                  <li>Otomatisasi penjadwalan cerdas</li>
                  <li>Efisiensi proses dan akurasi tinggi</li>
                  <li>Transparansi perubahan jadwal</li>
                </ul>
              </div>
              <div class="panel panel-c">
                <div class="panel-title-row">
                  <img src="{{ asset('images/logolist.png') }}" alt="List" class="panel-icon-img" />
                  <h3>Sistem Cerdas</h3>
                </div>
                <ul>
                  <li>Algoritma optimasi penjadwalan</li>
                  <li>Deteksi bentrok real-time</li>
                  <li>Visualisasi jadwal yang intuitif</li>
                </ul>
              </div>
            </div>

            <div class="section-header">
              <h2>Fitur Utama</h2>
            </div>
            <div class="grid lm-grid">
              <article class="card lm-card">
                <div class="lm-icon swatch pink">🤖</div>
                <div>
                  <h3>Plotting Otomatis</h3>
                  <p class="muted">Penjadwalan kelas dan ruangan tanpa bentrok berdasarkan aturan dan kapasitas.</p>
                </div>
              </article>
                <article class="card lm-card">
                  <div class="lm-icon swatch purple">📆</div>
                  <div>
                    <h3>Visualisasi Jadwal</h3>
                    <p class="muted">Tampilan visual jadwal mingguan/harian yang mudah dipahami.</p>
                  </div>
                </article>
              <article class="card lm-card">
                <div class="lm-icon swatch teal">🔐</div>
                <div>
                  <h3>Akses Multi-Role</h3>
                  <p class="muted">Admin fakultas, admin prodi, dan dosen memiliki hak akses berbeda.</p>
                </div>
              </article>
            </div>

            <div class="section-header">
              <h2>Manfaat untuk Pengguna</h2>
            </div>
            <div class="grid lm-grid-2">
              <article class="card lm-card">
                <div class="lm-icon swatch purple">🏛️</div>
                <div>
                  <h3>Untuk Fakultas</h3>
                  <p class="muted">Efisiensi pengelolaan ruangan, arsip data otomatis, dan kontrol terpusat.</p>
                </div>
              </article>
              <article class="card lm-card">
                <div class="lm-icon swatch teal">👨‍🏫</div>
                <div>
                  <h3>Untuk Dosen</h3>
                  <p class="muted">Mudah cek jadwal mengajar dan melihat perubahan secara real-time.</p>
                </div>
              </article>
            </div>

              
          </div>
        </section>
      </main>

      <footer class="site-footer">
        <div class="container footer-bottom-inner" style="padding:24px 16px">
          <p>© 2025 Schedify.</p>
          <div class="legal-links"><a href="{{ route('landing.index') }}#contact">Kontak</a></div>
        </div>
      </footer>
    </div>
    <script src="{{ asset('js/landing-new.js') }}"></script>
  </body>
  </html>
