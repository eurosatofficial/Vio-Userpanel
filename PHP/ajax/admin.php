<?php
/*************************************************
***** Copyright (c) FirstOne Media 2014-2018 *****
**************************************************/
include("../cfg.php");

$CP = new CP($mySQLcon);

if ($CP->Admin) {
    if (isset($_GET['action'])) {
        ob_start();
        
        // Initialize mtaServer
        $mtaServer = new mta(MTA_IP, MTA_HTTP_PORT, MTA_USER, MTA_PASS);
        $resource = $mtaServer->getResource(MTA_RESOURCE_NAME);

        // Safe GET input
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        $player = filter_input(INPUT_GET, 'player', FILTER_SANITIZE_STRING);
        $reason = filter_input(INPUT_GET, 'reason', FILTER_SANITIZE_STRING);
        $time = filter_input(INPUT_GET, 'time', FILTER_SANITIZE_STRING);

        // Execute actions based on input
        $result = '';
        switch ($action) {
            case 'kick':
                $result = $resource->call("kickPlayerWeb", $CP->Name, $player, $reason);
                break;
            case 'permaban':
                if ($CP->Adminlvl >= 2) {
                    $result = $resource->call("permaBanWeb", $CP->Name, $player, $reason);
                } else {
                    die("Nicht genügend Rechte.");
                }
                break;
            case 'timeban':
                if ($CP->Adminlvl >= 2) {
                    $result = $resource->call("timeBanWeb", $CP->Name, $player, $time, $reason);
                } else {
                    die("Nicht genügend Rechte.");
                }
                break;
            case 'unban':
                if ($CP->Adminlvl >= 3) {
                    $result = $resource->call("unbanWeb", $CP->Name, $player);
                } else {
                    die("Nicht genügend Rechte.");
                }
                break;
            case 'playerlist':
                $result = $resource->call("listAllPlayers");
                break;
            case 'screen':
                $result = $resource->call("makePlayerScreenshot", $player);
                break;
            case 'screen-result':
                $result = $resource->call("getScreenResult");
                break;
            default:
                echo "Ungültige Aktion.";
                exit;
        }
        
        ob_end_clean();
        
        // Output result
        if (isset($result[0]) && $result[0] == "true") {
            echo "Aktion erfolgreich ausgeführt.";
        } else {
            echo $result[0] ?? "Unknown error";
        }
    }
}
?>
