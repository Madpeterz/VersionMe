<?php

function upgrade(string $user, string $product): void
{
    if (is_dir("products") == false) {
        mkdir("products");
    }
    if (is_dir("products/" . $product) == false) {
        mkdir("products/" . $product);
    }
    $file = "products/" . $product . "/" . substr($user, 0, 6) . ".txt";
    if (is_file($file) == true) {
        return; // already in the Q
    }
    file_put_contents($file, $user);
}

function delTree($dir, array $skipDirs): bool
{
    $files = array_diff(scandir($dir), ['.','..']);
    $allOk = true;
    if (in_array($dir, $skipDirs) == false) {
        foreach ($files as $file) {
            if (is_dir("$dir/$file") == true) {
                $reply = delTree("$dir/$file", $skipDirs);
                if ($reply == false) {
                    $allOk = false;
                }
                continue;
            }
            unlink("$dir/$file");
        }
        if ($allOk == true) {
            return rmdir($dir);
        }
        return false;
    }
    return $allOk;
}
