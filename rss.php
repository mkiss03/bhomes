<?php
// rss.php - RSS Feed for Blog
header("Content-Type: application/rss+xml; charset=UTF-8");

include_once 'config/database.php';
include_once 'models/Blog.php';

$database = new Database();
$db = $database->getConnection();
$blog = new Blog($db);

try {
    $posts = $blog->getRecentPosts(10);
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Best Homes Espana Blog</title>
        <link>https://besthomesespana.com/blog.html</link>
        <description>Szakértői tanácsok és friss hírek a Costa Blanca ingatlanpiacáról</description>
        <language>hu-HU</language>
        <lastBuildDate><?php echo date('r'); ?></lastBuildDate>
        <generator>Best Homes Espana</generator>
        <atom:link href="https://besthomesespana.com/rss.php" rel="self" type="application/rss+xml" />
        
        <?php foreach ($posts as $post): ?>
        <item>
            <title><![CDATA[<?php echo htmlspecialchars($post['title']); ?>]]></title>
            <link>https://besthomesespana.com/blog-detail.html?slug=<?php echo urlencode($post['slug']); ?></link>
            <description><![CDATA[<?php echo htmlspecialchars($post['excerpt']); ?>]]></description>
            <pubDate><?php echo date('r', strtotime($post['publish_at'])); ?></pubDate>
            <guid>https://besthomesespana.com/blog-detail.html?slug=<?php echo urlencode($post['slug']); ?></guid>
            <?php if ($post['cover_image']): ?>
            <enclosure url="https://besthomesespana.com/<?php echo ltrim($post['cover_image'], './'); ?>" type="image/jpeg" length="0"/>
            <?php endif; ?>
        </item>
        <?php endforeach; ?>
        
    </channel>
</rss>
<?php
} catch (Exception $e) {
    http_response_code(500);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<error>RSS feed temporarily unavailable</error>';
}
?>