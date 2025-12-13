
  <!DOCTYPE html>
  <html lang="id">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Schedify — Sistem Penjadwalan Fakultas Pintar</title>
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
      <link rel="stylesheet" href="{{ asset('css/landing.css') }}" />
      <meta name="description" content="Otomatisasi penjadwalan fakultas dengan algoritma cerdas. Eliminasi konflik jadwal dan optimalkan penggunaan ruang kuliah." />
    </head>

    <body>
      <div class="page-gradient">
        <header class="site-header">
          <div class="container header-inner">
            <div class="brand">
              <img src="{{ asset('images/img crop.png') }}" alt="Schedify logo" class="logo-img" />
              <span class="brand-text">Schedify</span>
            </div>
            <nav class="main-nav" aria-label="Main Navigation">
              <a href="#features">Fitur</a>
              <a href="#about">About Us</a>
              <a href="#contact">Kontak</a>
              <a href="{{ route('login') }}" class="btn btn-outline btn-login">Login</a>
            </nav>
          </div>
        </header>

        <main>
          <section class="hero" id="home">
            <div class="container hero-grid">
              <div class="hero-copy">
                <div class="hero-badge">
                  <span class="badge-new">🚀 Platform Terbaru</span>
                </div>
                <h1>Sistem Penjadwalan <span class="accent">Fakultas Pintar</span></h1>
                <p class="hero-description">Otomatisasi penjadwalan fakultas dengan algoritma cerdas. Eliminasi konflik jadwal dan optimalkan penggunaan ruang kuliah untuk satu fakultas dengan mudah.</p>
                <div class="hero-stats">
                  <div class="stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Akurasi</div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Fakultas</div>
                  </div>
                </div>
                <div class="hero-actions">
                  <a class="btn btn-primary" href="{{ route('landing.learn-more') }}">
                    <span class="btn-text">Pelajari Lebih Lanjut</span>
                    <span class="btn-icon">→</span>
                  </a>
                  <a class="btn btn-outline" href="{{ route('login') }}">
                    <span class="btn-text">Login Sekarang</span>
                    <span class="btn-icon">🔑</span>
                  </a>
                </div>
              </div>
              <div class="hero-media">
                <div class="media-frame">
                  <img src="{{ asset('images/ruang.jpg') }}" alt="Ruang kelas" />
                  <div class="media-overlay"></div>
                </div>
              </div>
            </div>
          </section>

          <section class="section section-white" id="features">
            <div class="container">
              <div class="section-header center">
                <span class="badge">✨ Fitur Unggulan</span>
                <h2>Solusi Lengkap Penjadwalan Kuliah</h2>
                <p class="muted">Dapatkan semua fitur yang Anda butuhkan untuk mengelola jadwal fakultas dengan efisien dan efektif</p>
              </div>

              <div class="grid features-grid">
                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch pink">⚡</div>
                    <span class="chip">AI Powered</span>
                  </div>
                  <h3>Algoritma Cerdas</h3>
                  <p class="muted">Sistem AI yang mengoptimalkan penjadwalan secara otomatis dengan mempertimbangkan berbagai constraint dan preferensi.</p>
                </article>

                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch purple">⏱️</div>
                    <span class="chip">Real-time</span>
                  </div>
                  <h3>Deteksi Konflik</h3>
                  <p class="muted">Identifikasi otomatis konflik jadwal ruang, dosen, dan waktu sebelum jadwal dipublikasikan.</p>
                </article>

                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch teal">📍</div>
                    <span class="chip">Smart</span>
                  </div>
                  <h3>Manajemen Ruang</h3>
                  <p class="muted">Optimalisasi penggunaan ruang kuliah dengan tracking kapasitas, fasilitas, dan ketersediaan.</p>
                </article>

                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch pink">👥</div>
                    <span class="chip">Secure</span>
                  </div>
                  <h3>Multi-Role Access</h3>
                  <p class="muted">Akses berbeda untuk admin fakultas, admin prodi, dan dosen dengan permission yang sesuai peran masing-masing.</p>
                </article>

                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch purple">📊</div>
                    <span class="chip">Insights</span>
                  </div>
                  <h3>Analytics Dashboard</h3>
                  <p class="muted">Visualisasi data penggunaan ruang, beban mengajar dosen, dan statistik penjadwalan.</p>
                </article>

                <article class="card">
                  <div class="card-top">
                    <div class="icon-swatch teal">⚙️</div>
                    <span class="chip">Flexible</span>
                  </div>
                  <h3>Customisasi</h3>
                  <p class="muted">Sesuaikan aturan penjadwalan, periode akademik, dan preferensi sesuai kebutuhan fakultas.</p>
                </article>
              </div>

              <div class="center verified-note">
                <span class="ok">✔</span>
                <span>Semua fitur tersedia dalam satu platform</span>
              </div>
            </div>
          </section>

          <section class="section section-white" id="about">
            <div class="container about-grid">
              <div class="about-copy">
                <span class="badge">Tentang Kami</span>
                <h2>Mempermudah Manajemen Jadwal Fakultas</h2>
                <p class="lead">Schedify hadir sebagai solusi modern untuk mengatasi kompleksitas penjadwalan di tingkat fakultas. Dengan teknologi AI dan algoritma cerdas, kami membantu fakultas mengoptimalkan penggunaan sumber daya akademik.</p>
                <p class="muted">Tim kami memahami tantangan yang dihadapi dalam menyusun jadwal kuliah yang efisien. Mulai dari koordinasi antara dosen, mahasiswa, ruang kelas, hingga menghindari konflik waktu. Schedify mengotomatisasi seluruh proses ini dengan akurasi tinggi.</p>

                <div class="values-grid">
                  <div class="value-item">
                    <div class="swatch pink">🎯</div>
                    <div>
                      <h3 class="value-title">Fokus pada Solusi</h3>
                      <p class="value-desc">Kami berkomitmen menghadirkan solusi penjadwalan yang tepat sasaran untuk kebutuhan fakultas.</p>
                    </div>
                  </div>
                  <div class="value-item">
                    <div class="swatch purple">👥</div>
                    <div>
                      <h3 class="value-title">Kemudahan Pengguna</h3>
                      <p class="value-desc">Interface yang intuitif memungkinkan admin dan dosen menggunakan sistem dengan mudah.</p>
                    </div>
                  </div>
                  <div class="value-item">
                    <div class="swatch teal">💡</div>
                    <div>
                      <h3 class="value-title">Inovasi Berkelanjutan</h3>
                      <p class="value-desc">Terus mengembangkan fitur-fitur baru dengan teknologi AI untuk optimalisasi penjadwalan.</p>
                    </div>
                  </div>
                  <div class="value-item">
                    <div class="swatch pink">🏆</div>
                    <div>
                      <h3 class="value-title">Kualitas Terjamin</h3>
                      <p class="value-desc">Sistem yang telah teruji dengan algoritma canggih untuk menghasilkan jadwal yang optimal.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="about-media">
                <div class="media-frame tall">
                  <img src="{{ asset('images/ftmm.jpg') }}" alt="FTMM" />
                  <div class="media-overlay"></div>
                </div>
                <div class="mission-card" role="note" aria-label="Misi Kami">
                  <div class="mission-title">Misi Kami</div>
                  <p>Menghadirkan teknologi penjadwalan yang mudah, efisien, dan akurat untuk setiap fakultas</p>
                </div>
              </div>
            </div>
          </section>

          <section class="section section-soft" id="benefits">
            <div class="container narrow">
              <div class="section-header">
                <span class="badge">Keunggulan</span>
                <h2>Mengapa Memilih Schedify?</h2>
                <p class="muted">Rasakan manfaat sistem penjadwalan otomatis untuk fakultas Anda dengan teknologi terdepan</p>
              </div>

              <div class="benefits-list">
                <div class="benefit">
                  <div class="swatch pink small">⏰</div>
                  <div>
                    <h3>Hemat Waktu</h3>
                    <p class="muted">Proses penjadwalan yang biasanya berhari-hari kini selesai dalam hitungan jam.</p>
                  </div>
                </div>
                <div class="benefit">
                  <div class="swatch purple small">✅</div>
                  <div>
                    <h3>Zero Konflik Jadwal</h3>
                    <p class="muted">Algoritma cerdas memastikan tidak ada bentrok ruang, dosen, atau waktu.</p>
                  </div>
                </div>
                <div class="benefit">
                  <div class="swatch teal small">📈</div>
                  <div>
                    <h3>Optimasi Penggunaan Ruang</h3>
                    <p class="muted">Maksimalkan utilisasi ruang kuliah dengan penjadwalan efisien.</p>
                  </div>
                </div>
                <div class="benefit">
                  <div class="swatch pink small">👤</div>
                  <div>
                    <h3>Kepuasan Stakeholder</h3>
                    <p class="muted">Dosen mendapat jadwal yang sesuai preferensi.</p>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section class="section section-soft" id="testimonials">
            <div class="container">
              <div class="section-header center">
                <span class="badge">💬 Testimoni</span>
                <h2>Kata Mereka Tentang Schedify</h2>
                <p class="muted">Dengarkan pengalaman pengguna yang telah merasakan manfaat sistem penjadwalan otomatis</p>
              </div>
              
              <div class="testimonials-grid">
                <div class="testimonial-card">
                  <div class="testimonial-content">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Schedify benar-benar mengubah cara kami mengelola jadwal. Proses yang biasanya memakan waktu berhari-hari kini selesai dalam hitungan jam!"</p>
                  </div>
                  <div class="testimonial-author">
                    <div class="author-avatar">👨‍💼</div>
                    <div class="author-info">
                      <div class="author-name">Dr. Ahmad Wijaya</div>
                      <div class="author-title">Ketua Prodi Teknik Informatika</div>
                    </div>
                  </div>
                </div>
                
                <div class="testimonial-card">
                  <div class="testimonial-content">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Sistem AI-nya sangat cerdas dalam mendeteksi konflik. Tidak pernah lagi ada bentrok jadwal yang mengganggu proses belajar mengajar."</p>
                  </div>
                  <div class="testimonial-author">
                    <div class="author-avatar">👩‍🏫</div>
                    <div class="author-info">
                      <div class="author-name">Prof. Siti Nurhaliza</div>
                      <div class="author-title">Dekan Fakultas Teknik</div>
                    </div>
                  </div>
                </div>
                
                <div class="testimonial-card">
                  <div class="testimonial-content">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Interface yang user-friendly membuat semua staf admin bisa menggunakan sistem ini dengan mudah. Highly recommended!"</p>
                  </div>
                  <div class="testimonial-author">
                    <div class="author-avatar">👨‍💻</div>
                    <div class="author-info">
                      <div class="author-name">Budi Santoso</div>
                      <div class="author-title">Admin Prodi Sistem Informasi</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section class="cta">
            <div class="cta-bg" aria-hidden="true"></div>
            <div class="container center">
              <div class="cta-content">
                <h2>Siap Mengimplementasikan Schedify?</h2>
                <p class="lead white">Bergabunglah dengan ratusan fakultas yang telah merasakan kemudahan sistem penjadwalan otomatis</p>
                <div class="cta-actions">
                  <a href="{{ route('login') }}" class="btn btn-primary">
                    <span class="btn-text">Mulai Sekarang</span>
                    <span class="btn-icon">🚀</span>
                  </a>
                  <a href="{{ route('landing.learn-more') }}" class="btn btn-outline">
                    <span class="btn-text">Pelajari Lebih Lanjut</span>
                    <span class="btn-icon">📚</span>
                  </a>
                </div>
                <div class="cta-stats">
                  <div class="cta-stat">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Fakultas Aktif</div>
                  </div>
                  <div class="cta-stat">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Jadwal Terbuat</div>
                  </div>
                  <div class="cta-stat">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                  </div>
                </div>
              </div>
              <div class="emoji-rating" aria-label="Penilaian kepuasan">
                <div class="emoji-row">
                  <button class="emoji" data-score="1" title="Sangat tidak puas">😡</button>
                  <button class="emoji" data-score="2" title="Tidak puas">😕</button>
                  <button class="emoji" data-score="3" title="Cukup">🙂</button>
                  <button class="emoji" data-score="4" title="Puas">😀</button>
                  <button class="emoji" data-score="5" title="Sangat puas">🤩</button>
                </div>
                <div class="emoji-note">Bagaimana kepuasan Anda?</div>
              </div>
            </div>
          </section>
          
        </main>

        <footer id="contact" class="site-footer">
          <div class="container footer-grid">
            <div>
              <div class="brand footer-brand">
                <span class="logo" aria-hidden="true">📅</span>
                <span class="brand-text gradient">Schedify</span>
              </div>
              <p class="muted">Solusi penjadwalan kuliah terdepan yang membantu fakultas mengoptimalkan penggunaan sumber daya akademik dengan teknologi AI.</p>
              <ul class="contact-list">
                <li><span class="i">📍</span> Jakarta, Indonesia</li>
                <li><span class="i">☎</span> +62 21 1234 5678</li>
                <li><span class="i">✉</span> info@schedify.com</li>
              </ul>
            </div>

            <div>
              <h4>Produk</h4>
              <ul class="link-list">
                <li><a href="#features">Fitur Utama</a></li>
                <li><a href="#features">Integrasi</a></li>
                <li><a href="#">API</a></li>
                <li><a href="#">Mobile App</a></li>
              </ul>
            </div>

            <div>
              <h4>Dukungan</h4>
              <ul class="link-list">
                <li><a href="#">Dokumentasi</a></li>
                <li><a href="#">Tutorial</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#contact">Hubungi Kami</a></li>
              </ul>
            </div>
          </div>

          <div class="footer-bottom">
            <div class="container footer-bottom-inner">
              <p>© 2025 Schedify. Semua hak cipta dilindungi.</p>
              <div class="legal-links">
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
              </div>
            </div>
          </div>
        </footer>
      </div>

      <script src="{{ asset('js/landing.js') }}"></script>
    </body>
  </html>
  