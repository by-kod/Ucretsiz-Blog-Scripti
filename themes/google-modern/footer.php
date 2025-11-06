<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Site Bilgileri -->
            <div class="footer-section">
                <h5>
                    <i class="fas fa-blog me-2" style="color: #ff6b35;"></i><?php echo SITE_NAME; ?>
                </h5>
                <p class="mb-3"><?php echo SITE_DESCRIPTION; ?></p>
                <div class="social-links">
                    <a href="#" class="me-3" title="Facebook" style="color: #3b5998;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-3" title="Twitter" style="color: #1da1f2;"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3" title="Instagram" style="color: #e4405f;"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-3" title="YouTube" style="color: #ff0000;"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="me-3" title="LinkedIn" style="color: #0077b5;"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" title="Telegram" style="color: #0088cc;"><i class="fab fa-telegram"></i></a>
                </div>
            </div>

            <!-- Hızlı Linkler (10+ link) -->
            <div class="footer-section">
                <h5 style="color: #ff6b35;">Hızlı Linkler</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" style="color: #ddd;"><i class="fas fa-home me-2" style="color: #4ecdc4;"></i>Ana Sayfa</a></li>
                    <li class="mb-2"><a href="about.php" style="color: #ddd;"><i class="fas fa-info-circle me-2" style="color: #45b7d1;"></i>Hakkımızda</a></li>
                    <li class="mb-2"><a href="contact.php" style="color: #ddd;"><i class="fas fa-envelope me-2" style="color: #96ceb4;"></i>İletişim</a></li>
                    <li class="mb-2"><a href="privacy.php" style="color: #ddd;"><i class="fas fa-shield-alt me-2" style="color: #feca57;"></i>Gizlilik Politikası</a></li>
                    <li class="mb-2"><a href="terms.php" style="color: #ddd;"><i class="fas fa-file-contract me-2" style="color: #ff9ff3;"></i>Kullanım Şartları</a></li>
                    <li class="mb-2"><a href="sitemap.php" style="color: #ddd;"><i class="fas fa-sitemap me-2" style="color: #54a0ff;"></i>Site Haritası</a></li>
                    <li class="mb-2"><a href="authors.php" style="color: #ddd;"><i class="fas fa-users me-2" style="color: #5f27cd;"></i>Yazarlar</a></li>
                    <li class="mb-2"><a href="archives.php" style="color: #ddd;"><i class="fas fa-archive me-2" style="color: #00d2d3;"></i>Arşiv</a></li>
                    <li class="mb-2"><a href="popular.php" style="color: #ddd;"><i class="fas fa-fire me-2" style="color: #ff9f43;"></i>Popüler Yazılar</a></li>
                    <li class="mb-2"><a href="recent.php" style="color: #ddd;"><i class="fas fa-clock me-2" style="color: #0abde3;"></i>Son Yazılar</a></li>
                    <li class="mb-2"><a href="newsletter.php" style="color: #ddd;"><i class="fas fa-newspaper me-2" style="color: #ee5253;"></i>Bülten</a></li>
                    <li class="mb-2"><a href="rss.php" style="color: #ddd;"><i class="fas fa-rss me-2" style="color: #f368e0;"></i>RSS Beslemesi</a></li>
                </ul>
            </div>

            <!-- Kategoriler (10+ kategori) -->
            <div class="footer-section">
                <h5 style="color: #ff6b35;">Kategoriler</h5>
                <ul class="list-unstyled">
                    <?php
                    $footer_categories = $pdo->query("
                        SELECT name, slug 
                        FROM categories 
                        WHERE parent_id IS NULL 
                        ORDER BY sort_order ASC, name ASC 
                        LIMIT 12
                    ")->fetchAll();
                    
                    $category_colors = ['#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd', '#00d2d3', '#ff9f43', '#0abde3', '#ee5253', '#f368e0'];
                    $color_index = 0;
                    
                    foreach($footer_categories as $cat): ?>
                        <li class="mb-2">
                            <a href="category.php?slug=<?php echo urlencode($cat['slug']); ?>" style="color: #ddd;">
                                <i class="fas fa-folder me-2" style="color: <?php echo $category_colors[$color_index % count($category_colors)]; ?>;"></i>
                                <?php echo decodeHtml($cat['name']); ?>
                            </a>
                        </li>
                    <?php 
                    $color_index++;
                    endforeach; ?>
                </ul>
            </div>

            <!-- İletişim & Sosyal Medya -->
            <div class="footer-section">
                <h5 style="color: #ff6b35;">İletişim</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2" style="color: #ff9f43;"></i>
                        <a href="mailto:mail@blog.blog" style="color: #ddd;">mail@blog.blog</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2" style="color: #0abde3;"></i>
                        <a href="tel:+905555555555" style="color: #ddd;">+90 555 555 5555</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2" style="color: #ee5253;"></i>
                        <span style="color: #ddd;">Ankara, Türkiye</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock me-2" style="color: #10ac84;"></i>
                        <span style="color: #ddd;">Pazartesi - Cuma: 09:00 - 18:00</span>
                    </li>
                </ul>

                <h5 class="mt-4" style="color: #ff6b35;">Bizi Takip Edin</h5>
                <div class="social-links">
                    <a href="#" class="me-2 btn btn-sm" style="background: #3b5998; color: white; border: none;" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="me-2 btn btn-sm" style="background: #1da1f2; color: white; border: none;" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="me-2 btn btn-sm" style="background: #e4405f; color: white; border: none;" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="me-2 btn btn-sm" style="background: #ff0000; color: white; border: none;" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="btn btn-sm" style="background: #0077b5; color: white; border: none;" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Ek Linkler -->
        <div class="footer-links mt-4">
            <div class="footer-section">
                <h6 style="color: #ff9f43;">Memur Kaynakları</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="memur-maaslari.php" style="color: #ddd;">Memur Maaşları</a></li>
                    <li class="mb-1"><a href="memur-haklari.php" style="color: #ddd;">Memur Hakları</a></li>
                    <li class="mb-1"><a href="kamu-personeli.php" style="color: #ddd;">Kamu Personeli</a></li>
                    <li class="mb-1"><a href="devlet-memuru.php" style="color: #ddd;">Devlet Memuru</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h6 style="color: #0abde3;">Yasal Bilgiler</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="disclaimer.php" style="color: #ddd;">Sorumluluk Reddi</a></li>
                    <li class="mb-1"><a href="cookie-policy.php" style="color: #ddd;">Çerez Politikası</a></li>
                    <li class="mb-1"><a href="gdpr.php" style="color: #ddd;">KVKK</a></li>
                    <li class="mb-1"><a href="legal.php" style="color: #ddd;">Yasal Uyarılar</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h6 style="color: #10ac84;">Yardım & Destek</h6>
                <ul class="list-unstyled">
                    <li class="mb-1"><a href="help.php" style="color: #ddd;">Yardım Merkezi</a></li>
                    <li class="mb-1"><a href="faq.php" style="color: #ddd;">Sıkça Sorulan Sorular</a></li>
                    <li class="mb-1"><a href="support.php" style="color: #ddd;">Teknik Destek</a></li>
                    <li class="mb-1"><a href="feedback.php" style="color: #ddd;">Geri Bildirim</a></li>
                </ul>
            </div>
        </div>

        <hr style="border-color: rgba(255,255,255,0.2);">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0" style="color: #bbb;">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tüm hakları saklıdır.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0" style="color: #bbb;">
                    💻 Kod ve Tasarım: <i class="fas fa-heart" style="color: #e74c3c;"></i>  
                    <a href="https://linktr.ee/bykod" target="_blank" style="color:#ff9f43; text-decoration:none; font-weight:500;">
                        Serhat
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 3rem 0 2rem;
        margin-top: 4rem;
        border-top: 4px solid #ff6b35;
    }
    
    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .footer-section h5 {
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .footer-section h6 {
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    
    .footer a {
        color: #ddd;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .footer a:hover {
        color: #ff6b35;
        transform: translateX(5px);
    }
    
    .social-links a {
        display: inline-block;
        transition: all 0.3s ease;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        text-align: center;
        line-height: 35px;
    }
    
    .social-links a:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .footer-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 2rem;
    }
    
    .list-unstyled li {
        transition: all 0.3s ease;
    }
    
    .list-unstyled li:hover {
        transform: translateX(3px);
    }
    
    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .footer-links {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .footer {
            padding: 2rem 0 1rem;
        }
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Scroll animasyonları
    document.addEventListener('DOMContentLoaded', function() {
        const fadeElements = document.querySelectorAll('.fade-in-up');
        
        const fadeInOnScroll = function() {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.style.opacity = "1";
                    element.style.transform = "translateY(0)";
                }
            });
        };
        
        // İlk yükleme
        fadeInOnScroll();
        
        // Scroll event
        window.addEventListener('scroll', fadeInOnScroll);
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Footer sosyal medya butonları
    document.querySelectorAll('.social-links a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Link hover efektleri
    document.querySelectorAll('.footer a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#ff6b35';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.color = '#ddd';
        });
    });
</script>