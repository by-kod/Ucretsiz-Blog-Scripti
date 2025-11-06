<?php
// Ana kategorileri ve alt kategorileri getir
function getNavCategories($pdo) {
    $stmt = $pdo->prepare("
        SELECT c1.*, 
               (SELECT COUNT(*) FROM posts WHERE category_id = c1.id AND status = 'published') as post_count
        FROM categories c1 
        WHERE c1.parent_id IS NULL 
        ORDER BY c1.sort_order ASC, c1.name ASC
    ");
    $stmt->execute();
    $main_categories = $stmt->fetchAll();
    
    $result = [];
    foreach($main_categories as $main_cat) {
        // Alt kategorileri getir
        $child_stmt = $pdo->prepare("
            SELECT c2.*, 
                   (SELECT COUNT(*) FROM posts WHERE category_id = c2.id AND status = 'published') as post_count
            FROM categories c2 
            WHERE c2.parent_id = ? 
            ORDER BY c2.sort_order ASC, c2.name ASC
        ");
        $child_stmt->execute([$main_cat['id']]);
        $child_categories = $child_stmt->fetchAll();
        
        $main_cat['children'] = $child_categories;
        $result[] = $main_cat;
    }
    
    return $result;
}

$nav_categories = getNavCategories($pdo);
?>

<!-- Navigation Menu -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Ana Sayfa</a>
                </li>
                
                <li class="nav-item">
    <a class="nav-link" href="change-password.php">
        <i class="fas fa-key"></i>è™ﬂifre Deè´ªiè´ﬂtir
    </a>
</li>
                
                <?php foreach($nav_categories as $category): ?>
                    <?php if(empty($category['children'])): ?>
                        <!-- Alt kategorisi yoksa normal link -->
                        <li class="nav-item">
                            <a class="nav-link" href="/kategori/<?php echo $category['slug']; ?>">
                                <?php echo decodeHtml($category['name']); ?>
                                <?php if($category['post_count'] > 0): ?>
                                    <span class="badge bg-primary ms-1"><?php echo $category['post_count']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Alt kategorisi varsa dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="/kategori/<?php echo $category['slug']; ?>" 
                               id="navbarDropdown<?php echo $category['id']; ?>" 
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo decodeHtml($category['name']); ?>
                                <?php if($category['post_count'] > 0): ?>
                                    <span class="badge bg-primary ms-1"><?php echo $category['post_count']; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $category['id']; ?>">
                                <li>
                                    <a class="dropdown-item" href="/kategori/<?php echo $category['slug']; ?>">
                                        T√ºm <?php echo decodeHtml($category['name']); ?>
                                        <?php if($category['post_count'] > 0): ?>
                                            <span class="badge bg-secondary ms-1"><?php echo $category['post_count']; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach($category['children'] as $child): ?>
                                    <li>
                                        <a class="dropdown-item" href="/kategori/<?php echo $child['slug']; ?>">
                                            <?php echo decodeHtml($child['name']); ?>
                                            <?php if($child['post_count'] > 0): ?>
                                                <span class="badge bg-secondary ms-1"><?php echo $child['post_count']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>