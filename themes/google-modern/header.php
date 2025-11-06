<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? sanitize($pageDescription) : SITE_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo isset($pageKeywords) ? sanitize($pageKeywords) : SITE_KEYWORDS; ?>">
    
    <?php if(isset($canonicalUrl)): ?>
        <link rel="canonical" href="<?php echo $canonicalUrl; ?>">
    <?php endif; ?>
    
<!-- ✅ GOOGLE ANALYTICS (GTAG.JS) -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_OLCUM_ID"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'GA_OLCUM_ID');
    </script>    

<!-- ✅ GOOGLE SITE VERIFICATION -->
    <meta name="google-site-verification" content="doğrulama-kodunuz" />

<!-- ✅ BING VERIFICATION -->
    <meta name="msvalidate.01" content="BING_DOGRULAMA_KODU" />

<!-- ✅ FACEBOOK PIXEL -->
    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'PIXEL_IDNIZ');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=PIXEL_IDNIZ&ev=PageView&noscript=1"
    /></noscript>

<!-- ✅ GOOGLE ADSENSE -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=CA-PUB-XXXXXXXXXXXXXXX"
            crossorigin="anonymous"></script>

<!-- ✅ YANDEX WEBMASTER TOOLS -->
    <meta name="yandex-verification" content="YANDEX_DOGRULAMA_KODUNUZ" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    
<!-- ✅ FAVICON -->
    <link rel="icon" type="image/x-icon" href="/uploads/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/uploads/favicon.png">
    <link rel="apple-touch-icon" href="/uploads/favicon.png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-purple: #7c3aed;
            --accent-teal: #0d9488;
            --accent-amber: #d97706;
            --accent-rose: #e11d48;
            --neutral-gray: #6b7280;
            --neutral-light: #f8fafc;
            --neutral-dark: #1f2937;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --error-red: #ef4444;
            --shadow: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.12);
            --gradient-primary: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            --gradient-hero: linear-gradient(135deg, #4f46e5 0%, #7e22ce 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            color: var(--neutral-dark);
            line-height: 1.6;
        }
        
        .main-container {
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-blue) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link {
            color: var(--neutral-gray) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.2rem;
        }
        
        .nav-link:hover {
            color: var(--primary-blue) !important;
            background: var(--neutral-light);
        }
        
        .nav-link.active {
            color: var(--primary-blue) !important;
            background: rgba(37, 99, 235, 0.1);
        }
        
        /* Hero Section */
        .hero-section {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>');
            background-size: cover;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #fff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        /* Blog Cards */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            padding: 3rem 0;
        }
        
        .blog-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .blog-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }
        
        .blog-card-image {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        
        .blog-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .blog-card:hover .blog-card-image img {
            transform: scale(1.1);
        }
        
        .blog-card-content {
            padding: 1.5rem;
        }
        
        .blog-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }
        
        .category-badge {
            background: var(--primary-blue);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .category-badge:hover {
            background: var(--accent-teal);
            color: white;
            text-decoration: none;
        }
        
        .post-date {
            color: var(--neutral-gray);
            font-size: 0.8rem;
        }
        
        .blog-card-title {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
        
        .blog-card-title a {
            color: var(--neutral-dark);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .blog-card-title a:hover {
            color: var(--primary-blue);
        }
        
        .blog-card-excerpt {
            color: var(--neutral-gray);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .blog-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        
        .author-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--neutral-gray);
        }
        
        .post-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--neutral-gray);
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        /* Featured Post */
        .featured-post {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-hover);
        }
        
        .featured-content {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .featured-badge {
            background: var(--accent-rose);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        /* Pagination */
        .pagination {
            justify-content: center;
            margin: 3rem 0;
        }
        
        .page-link {
            border: none;
            color: var(--neutral-gray);
            padding: 0.75rem 1.25rem;
            margin: 0 0.25rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: var(--primary-blue);
            color: white;
        }
        
        .page-item.active .page-link {
            background: var(--primary-blue);
            color: white;
        }
        
        /* Footer */
        .footer {
            background: var(--neutral-dark);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
        
        .footer h5 {
            color: var(--accent-amber);
            margin-bottom: 1rem;
        }
        
        .footer a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--primary-blue);
        }

        /* Footer Grid Layout */
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section {
            margin-bottom: 2rem;
        }

        .footer-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Buttons */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
        }
        
        .btn-success {
            background: var(--success-green);
            border: none;
        }
        
        .btn-warning {
            background: var(--warning-orange);
            border: none;
        }
        
        .btn-danger {
            background: var(--error-red);
            border: none;
        }
        
        /* Badges */
        .bg-primary { background-color: var(--primary-blue) !important; }
        .bg-success { background-color: var(--success-green) !important; }
        .bg-warning { background-color: var(--warning-orange) !important; }
        .bg-danger { background-color: var(--error-red) !important; }
        .bg-info { background-color: var(--accent-teal) !important; }
        
        /* Text Colors */
        .text-primary { color: var(--primary-blue) !important; }
        .text-success { color: var(--success-green) !important; }
        .text-warning { color: var(--warning-orange) !important; }
        .text-danger { color: var(--error-red) !important; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .blog-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .featured-post {
                grid-template-columns: 1fr;
            }
            
            .navbar-nav {
                text-align: center;
                margin-top: 1rem;
            }

            .footer-links {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-blog"></i>
            <?php echo SITE_NAME; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php
                // Ana kategorileri getir (parent_id NULL olanlar)
                $categories = $pdo->query("
                    SELECT c1.*, 
                           (SELECT COUNT(*) FROM posts WHERE category_id = c1.id AND status = 'published') as post_count
                    FROM categories c1 
                    WHERE c1.parent_id IS NULL 
                    ORDER BY c1.sort_order ASC, c1.name ASC
                ")->fetchAll();
                
                foreach($categories as $category): 
                    // Alt kategorileri kontrol et
                    $child_count = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
                    $child_count->execute([$category['id']]);
                    $has_children = $child_count->fetchColumn() > 0;
                ?>
                    <?php if($has_children): ?>
                        <!-- Alt kategorisi olanlar için dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="category.php?slug=<?php echo urlencode($category['slug']); ?>" 
                               id="navbarDropdown<?php echo $category['id']; ?>" 
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo decodeHtml($category['name']); ?>
                                <?php if($category['post_count'] > 0): ?>
                                    <span class="badge bg-primary ms-1"><?php echo $category['post_count']; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $category['id']; ?>">
                                <li>
                                    <a class="dropdown-item" href="category.php?slug=<?php echo urlencode($category['slug']); ?>">
                                        Tüm <?php echo decodeHtml($category['name']); ?>
                                        <?php if($category['post_count'] > 0): ?>
                                            <span class="badge bg-info ms-1"><?php echo $category['post_count']; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php
                                // Alt kategorileri getir
                                $child_categories = $pdo->prepare("
                                    SELECT c2.*, 
                                           (SELECT COUNT(*) FROM posts WHERE category_id = c2.id AND status = 'published') as post_count
                                    FROM categories c2 
                                    WHERE c2.parent_id = ? 
                                    ORDER BY c2.sort_order ASC, c2.name ASC
                                ");
                                $child_categories->execute([$category['id']]);
                                $children = $child_categories->fetchAll();
                                
                                foreach($children as $child): ?>
                                    <li>
                                        <a class="dropdown-item" href="category.php?slug=<?php echo urlencode($child['slug']); ?>">
                                            <?php echo decodeHtml($child['name']); ?>
                                            <?php if($child['post_count'] > 0): ?>
                                                <span class="badge bg-info ms-1"><?php echo $child['post_count']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Alt kategorisi olmayanlar için normal link -->
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?slug=<?php echo urlencode($category['slug']); ?>">
                                <?php echo decodeHtml($category['name']); ?>
                                <?php if($category['post_count'] > 0): ?>
                                    <span class="badge bg-primary ms-1"><?php echo $category['post_count']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            
            <form class="d-flex" action="search.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Blogda ara..." aria-label="Search">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title fade-in-up"><?php echo SITE_NAME; ?></h1>
                <p class="hero-subtitle fade-in-up"><?php echo SITE_DESCRIPTION; ?></p>
                <div class="hero-stats fade-in-up" style="display: flex; gap: 2rem; justify-content: center; font-size: 1.1rem;">
                    <?php
                    $total_posts = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'published'")->fetch()['count'];
                    $total_categories = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'];
                    $total_comments = $pdo->query("SELECT COUNT(*) as count FROM comments WHERE status = 'approved'")->fetch()['count'];
                    ?>
                    <div><i class="fas fa-newspaper me-1"></i> <?php echo $total_posts; ?> Yazı</div>
                    <div><i class="fas fa-folder me-1"></i> <?php echo $total_categories; ?> Kategori</div>
                    <div><i class="fas fa-comments me-1"></i> <?php echo $total_comments; ?> Yorum</div>
                </div>
            </div>
        </div>

        <div class="container">