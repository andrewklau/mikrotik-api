<?php

$router_address = '192.168.1.1';
$router_user    = 'api';
$router_pass    = 'api';
require('routeros_api.class.php');

$API        = new routeros_api();
$API->debug = false;

if ($API->connect($router_address, $router_user, $router_pass)) {
    
    function ping($address, $rm)
    {
        global $API;
        $PING = $API->comm("/ping", array(
            "address" => "$address",
            "count" => "2",
            "routing-table" => "$rm"
        ));
        $response = $PING['0']['avg-rtt'] ." ms";
        echo ltrim($response, 0);
    }
    
    function queue($rm)
    {
        global $API;
        $QUEUE = $API->comm("/queue/simple/print");
        $MANGLE = $API->comm("/ip/firewall/mangle/print");

        foreach ($QUEUE as $row) {

            if ($row['rate'] == '0bps/0bps') { continue; }
            
            foreach($MANGLE as $row1) {
                if (isset($row['target']) && isset($row1['src-address']) && isset($row1['new-connection-mark'])) {
                $target = $row['target'];
                $srcaddress = $row1['src-address'];
                if (strtok("$target", '/') == strtok("$srcaddress", '/') && $row1['new-connection-mark'] == $rm) {
                    echo "
                        <tr>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['target'] . "</td>
                            <td>" . $row['rate'] . "</td>
                            <td>" . $row['max-limit'] . "</td>
                        </tr>";
                }}
            }
        }
        #print_r($QUEUE);
        #print_r($MANGLE);
    }
    
    # Actions
    if (isset($_GET['action'])) {
        
        switch ($_GET['action']) {
            case "ping":
                if ($_GET['action'] === 'ping' && isset($_GET['address']) && isset($_GET['rm'])) {
                    ping($_GET['address'], $_GET['rm']);
                }
                break;
            
            case "queue":
                if ($_GET['action'] === 'queue' && isset($_GET['rm'])) {
                    queue($_GET['rm']);
                }
                break;
        }
        
    }
    
    $API->disconnect();
    # ROS Connect
} else {
    echo 'Unable to connect to RouterOS';
}

?>

