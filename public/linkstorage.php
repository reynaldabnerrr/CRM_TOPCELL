<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (is_link($link)) {
    echo "Symlink already exists\n";
} else {
    if (file_exists($link)) {
        if (is_dir($link)) {
            rmdir($link);
        } else {
            unlink($link);
        }
    }
    
    if (symlink($target, $link)) {
        echo "Symlink created successfully\n";
    } else {
        echo "Failed to create symlink\n";
    }
}
