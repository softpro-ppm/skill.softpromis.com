<?php
// Script to convert HTML files to PHP
$base_dir = __DIR__ . '/pages';
$exclude_dirs = ['.', '..', '.git', 'node_modules'];

function convertHtmlToPhp($dir) {
    global $exclude_dirs;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if (in_array($file, $exclude_dirs)) continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            convertHtmlToPhp($path);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'html') {
            $php_path = dirname($path) . '/' . pathinfo($path, PATHINFO_FILENAME) . '.php';
            
            // Read HTML content
            $content = file_get_contents($path);
            
            // Add PHP header
            $php_content = "<?php\nrequire_once '../../config/config.php';\n?>\n" . $content;
            
            // Replace .html extensions with .php
            $php_content = str_replace('.html', '.php', $php_content);
            
            // Add base_url to asset paths
            $php_content = str_replace('src="../../', 'src="<?php echo $base_url; ?>', $php_content);
            $php_content = str_replace('href="../../', 'href="<?php echo $base_url; ?>', $php_content);
            
            // Add includes for topbar and sidebar
            $php_content = str_replace('<body>', "<body>\n    <?php include '../../components/topbar.php';\n    include '../../components/sidebar.php'; ?>", $php_content);
            
            // Write PHP file
            file_put_contents($php_path, $php_content);
            
            // Delete original HTML file
            unlink($path);
            
            echo "Converted: $path to $php_path\n";
        }
    }
}

// Start conversion
convertHtmlToPhp($base_dir);
echo "Conversion complete!\n";
?> 