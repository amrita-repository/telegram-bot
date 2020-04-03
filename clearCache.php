<?php
/**
 * Copyright (c) 2020 | RAJKUMAR (http://rajkumaar.co.in)
 * This is used to remove all files downloaded under docs/ directory mostly due to QPapers
 */

$folderName = "docs/";
if (file_exists($folderName)) {
    $count = 0;
    foreach (new DirectoryIterator($folderName) as $fileInfo) {
        if ($fileInfo->isDot()) {
            continue;
        }
        if ($fileInfo->isFile()) {
            unlink($fileInfo->getRealPath());
            $count++;
        }
    }
    echo $count." files deleted.";
}
