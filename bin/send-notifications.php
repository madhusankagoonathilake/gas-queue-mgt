<?php

include_once '../common/dbh.php';
include_once '../common/logger.php';
include_once '../common/sms.php';
include_once '../common/buyer.php';

try {
    $dbh = getDbh();

    $messageTemplate = file_get_contents('../templates/buyer-notification.txt');
    $messageReplacementStrings = ['${agencyName}', '${queueOTP}'];

    $stmt = $dbh->prepare("
SELECT b.id, b.telephone, b.otp, a.name AS agency_name, a.city
FROM buyer b 
LEFT JOIN agency a on a.id = b.agency_id
WHERE b.status = 'PENDING_NOTIFICATION' 
LIMIT 5;
");
    $stmt->execute();
    $notifyList = $stmt->fetchAll(\PDO::FETCH_NUM);

    $updateStmt = $dbh->prepare("UPDATE buyer SET status = ?, notified_on = ? WHERE id = ?");

    foreach ($notifyList as $buyer) {

        list($id, $telephone, $queueOTP, $agencyName, $city) = $buyer;

        $messageText = str_replace($messageReplacementStrings, [$agencyName, $queueOTP], $messageTemplate);
        $maskedTelephoneNumber = maskTelephoneNumber($telephone);
        $timestamp = date('Y-m-d H:i:s');

        try {
            $success = sendSMS($telephone, $messageText);

            if ($success) {
                $updateStmt->execute(['NOTIFIED', $timestamp, $id]);
                notification_log("Successfully notified {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city}");
            } else {
                $updateStmt->execute(['FAILED_TO_NOTIFY', $timestamp, $id]);
                notification_log("Failed to notify {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city}");
            }
        } catch (\Exception $e) {
            $updateStmt->execute(['FAILED_TO_NOTIFY', $timestamp, $id]);
            notification_log("Failed to notify {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city} due to error: {$e->getMessage()}");
        }

    }

} catch (\Exception $e) {
    app_log('EROR', $e->getMessage());
}

