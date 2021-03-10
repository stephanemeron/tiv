<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('front', 'dev', true);

$accesGranted = false;
$ips = sfConfig::get('app_controllers-access_ips-allowed');

foreach ($ips as $key => $ip) {
    if (substr(ipTools::getRemoteIp(), 0, strlen($ip)) == $ip) {
        $accesGranted = true;
        break;
    }
}
if ($accesGranted) {
    echo phpinfo();
} else {
	exit('Access denied. Please contact the site administrator with your ip adress'.' <b>'.ipTools::getRemoteIp().'</b>');
}




