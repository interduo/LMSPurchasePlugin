﻿<?php

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if (isset($_GET['attid'])) {
    $attid = intval($_GET['attid']);
}

if (empty($attid) && empty($id)) {
    access_denied();
}

$PURCHASES = LMSPurchasesPlugin::getPurchasesInstance();

$attachmentlocation = empty($id) ? array('attid' => $attid) : array('pdid' => $id);

if (!$PURCHASES->isLoggedUserAllowedToViewThisAttachment($attachmentlocation)) {
    die(trans('No access - check privileges for categories'));
}

$files = $PURCHASES->getPurchaseFiles($attachmentlocation);

if (!empty($files)) {
    $file = array_shift(array_values($files));

    $fullfilepath = ConfigHelper::getConfig(
        'pd.storage_dir',
        STORAGE_DIR . DIRECTORY_SEPARATOR . 'pd'
    ) . DIRECTORY_SEPARATOR . $file['filepath'] . DIRECTORY_SEPARATOR . $file['filename'];

    if (!file_exists($fullfilepath)) {
        die(trans("No attachment for this purchase."));
    }

    $file['content'] = file_get_contents($fullfilepath);

    header('Content-Disposition: inline; filename=' . $file['filename']);
    header('Content-Type: ' . $file['type']);
    header('Content-Length: ' . filesize($fullfilepath));
    header('Pragma: public');
    header('Cache-Control: private, max-age=0, must-revalidate');
    ini_set('zlib.output_compression', '0');

    die($file['content']);
}
