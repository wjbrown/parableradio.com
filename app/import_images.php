<?php

require '../config/bootstrap.php';

$img_src_dir = '../www/img/src/';
$img_dst_dir = '../www/img/';

// rename all image files as their md5_file
foreach (glob($img_src_dir . '*.jpg') as $img) {

    // resize image
    $pimg = \Proteus\Image::create($img);
    $pimg->resize(
        'adaptive',
        1920,
        1080
    );
    file_put_contents($img, $pimg);

    // generate md5
    $md5 = md5_file($img);

    copy($img, $img_dst_dir . $md5 . '.jpg');

    unlink($img);

}

// remove all images from the database
$db->q('DELETE FROM `backgrounds` WHERE 1');

// add all images to the database
foreach (glob($img_dst_dir . '*.jpg') as $img) {
    $db->run('INSERT INTO backgrounds (image) VALUES (?)', basename($img));
}