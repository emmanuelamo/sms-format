<?php

// Function to parse SMS log entries
function parse_sms_log($log) {
    preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $log, $date_match);
    preg_match('/ACT:(\w+)/', $log, $username_match);
    preg_match('/from:(\w+)/', $log, $sender_match);
    preg_match('/to:(\d+)/', $log, $msisdn_match);
    preg_match('/SMSC:(\w+)/', $log, $network_match);
    preg_match('/msg:\d+:(.+?)\[udh/s', $log, $message_match);
    preg_match('/FID:(\d+)/', $log, $fid_match);

    return [
        'sent_date' => $date_match[1],
        'username' => $username_match[1],
        'sender' => $sender_match[1],
        'msisdn' => $msisdn_match[1],
        'network' => $network_match[1],
        'message' => trim($message_match[1]),
        'fid' => $fid_match[1],
        'page_count' => 1,
        'status' => 'UNDELIV',
        'delivered_date' => null
    ];
}

// Function to parse delivery status log entries
function parse_delivery_status($log) {
    preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $log, $date_match);
    preg_match('/FID:(\d+)/', $log, $fid_match);
    preg_match('/stat:(\w+)/', $log, $status_match);

    return [
        'fid' => $fid_match[1],
        'delivered_date' => $date_match[1],
        'status' => $status_match[1]
    ];
}

// Read SMS logs from file
$sms_logs = file('sms_logs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Read delivery status from file
$delivery_status_logs = file('delivery-status.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Parse SMS logs
$sms_data = array_map('parse_sms_log', $sms_logs);

// Parse delivery status logs
$status_data = array_map('parse_delivery_status', $delivery_status_logs);

// Create a map for quick lookup of delivery status by fid
$status_map = [];
foreach ($status_data as $status) {
    $status_map[$status['fid']] = $status;
}

// Update SMS data with delivery status
foreach ($sms_data as &$sms) {
    if (isset($status_map[$sms['fid']])) {
        $sms['status'] = $status_map[$sms['fid']]['status'];
        $sms['delivered_date'] = $status_map[$sms['fid']]['delivered_date'];
    }
}

// Remove fid column
foreach ($sms_data as &$sms) {
    unset($sms['fid']);
}

// Define CSV file path
$csv_file = 'formatted_sms_logs.csv';

// Open CSV file for writing
$fp = fopen($csv_file, 'w');

// Write header row
fputcsv($fp, array_keys($sms_data[0]));

// Write data rows
foreach ($sms_data as $sms) {
    fputcsv($fp, $sms);
}

// Close CSV file
fclose($fp);

echo "CSV file created successfully: $csv_file\n";
?>
