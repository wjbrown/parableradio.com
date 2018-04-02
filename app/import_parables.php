<?php

require '../config/bootstrap.php';
require ROOT . DS . 'app' . DS . 'TextToSpeech.php';

$tts = new TextToSpeech($polly);

$mp3_dir    = ROOT . DS . 'parables' . DS . 'mp3';

$rows = $db->q("SELECT * FROM parables WHERE status = 'queued'");

foreach ($rows as $row) {
    // sluggify the name
    $dst_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($row['name'])) . '.mp3';
    $dst_name = str_replace(' ', '_', $dst_name);

    try {
        file_put_contents($mp3_dir . DS . $dst_name, $tts->convert(
            $row['body']
        ));
        $db->update('parables', ['status' => 'processed', 'polly_errors' => '', 'mp3_file' => $dst_name], ['id' => $row['id']]);
    }
    catch (Exception $e) {
        $db->update('parables', ['polly_errors' => $e->getMessage(), 'mp3_file' => $dst_name], ['id' => $row['id']]);
    }
}

return;




$import_dir = ROOT . DS . 'parables' . DS . 'import';
$txt_dir    = ROOT . DS . 'parables' . DS . 'txt';
$ssml_dir   = ROOT . DS . 'parables' . DS . 'ssml';
$mp3_dir    = ROOT . DS . 'parables' . DS . 'mp3';


// scan the import directory, import each file
foreach (glob($import_dir . DS . '*.txt') as $import_file) {

    $txt_file = $txt_dir . DS . basename($import_file);

    // copy the import file to the txt dir
    copy($import_file, $txt_file);

    // create db record
    $id = $db->insertReturnId('parables', ['text_file' => basename($import_file)]);

    $ssml_file = $ssml_dir . DS . preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($import_file)) . '.ssml';
    
    // update db with ssml file info
    $db->update('parables', ['ssml_file' => basename($ssml_file)], ['id' => $id]);
    
    // crate ssml file
    $tts->txtToSsml($import_file, $ssml_file);

    unlink($import_file);
}

// convert imported files into mp3s
$rows = $db->run('SELECT * FROM parables WHERE ssml_file IS NOT NULL AND mp3_file IS NULL');

foreach ($rows as $row) {
    $ssml_file = $ssml_dir . DS . $row['ssml_file'];
    $mp3_file = $mp3_dir . DS . preg_replace('/\\.[^.\\s]{3,4}$/', '', $row['ssml_file']) . '.mp3';
    $tts->ssmlToMp3($ssml_file, $mp3_file);

    $db->update('parables', ['mp3_file' => basename($mp3_file)], ['ssml_file' => basename($ssml_file)]);
}


