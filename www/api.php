<?php

require '../config/bootstrap.php';

$last = isset($_GET['last']) ? $_GET['last'] : false;

// if no previous id passed, start from the beginning
if (!$last || empty($last)) {
    $parable = $db->row('SELECT * FROM `parables` WHERE `name` = "Parable Radio"');
}
// otherwise, attempt to get the next one
else {
    $parable = $db->row('SELECT * FROM `parables` WHERE `id` > ?', $last);
}

// if we have a parable to share
if ($parable) {

    // select a random background image
    $background = $db->row('SELECT * FROM `backgrounds` ORDER BY RAND()');

    

    $response = [
        'status'     => 'success',
        'parable'    => $parable,
        'background' => $background
    ];

}
else {
    $response = ['status' => 'error'];
}

header("Content-type: application/json");

echo json_encode($response);