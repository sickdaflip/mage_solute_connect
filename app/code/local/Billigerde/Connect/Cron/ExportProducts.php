<?php
require_once 'app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$start = time();
$now = Mage::getModel('core/date')->timestamp($start);
echo 'START Time is: ' . date('y-m-d H:i:s', $now) . "\n";

try {

    /** @var Billigerde_Connect_Model_Service_Export $service */
    $service = Mage::getModel('billigerde_connect/service_export');
    $service->exportProducts();

} catch (Exception $e) {
    Mage::logException($e);
    echo $e;
}

$end = time();
$now = Mage::getModel('core/date')->timestamp($end);
echo 'STOP Time is: ' . date('y-m-d H:i:s', $now) . ' Took : ' . number_format(($end-$start)/60,2) .  " minutes. \n";
