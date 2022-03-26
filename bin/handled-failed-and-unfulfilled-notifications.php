<?php

include_once '../common/dbh.php';
include_once '../common/logger.php';
include_once '../common/sms.php';
include_once '../common/buyer.php';

/*
Removing the buyers who are matching the following criteria  and notifying the next in line
- Ones who were failed to notify and are older than a day
- Ones who were notified but still hasn't made the purchase
*/
$dbh = getDbh();
try {

    $notificationTemplate = file_get_contents('../templates/buyer-notification.txt');
    $expiredMessage = file_get_contents('../templates/buyer-expiration.txt');
    $messageReplacementStrings = ['${agencyName}', '${queueOTP}'];

    $dbh->beginTransaction();

    $aDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));
    $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));

    $failedToNotifyStmt = $dbh->prepare("
SELECT id, agency_id, telephone, status FROM buyer WHERE notified_on <= ? AND status = 'FAILED_TO_NOTIFY'
UNION 
SELECT id, agency_id, telephone, status FROM buyer WHERE notified_on <= ? AND status = 'NOTIFIED'
");
    $failedToNotifyStmt->execute([$aDayAgo, $threeDaysAgo]);
    $failedToNotifyBuyers = $failedToNotifyStmt->fetchAll(\PDO::FETCH_NUM);

    $deleteStmt = $dbh->prepare("DELETE FROM buyer WHERE id = ?");
    $updateStmt = $dbh->prepare("UPDATE buyer SET status = ?, notified_on = ? WHERE id = ?");
    $replacementStmt = $dbh->prepare("SELECT b.id, b.telephone, b.otp, a.name AS agency_name, a.city
FROM buyer b 
LEFT JOIN agency a on a.id = b.agency_id
WHERE 
      b.status = 'PENDING_NOTIFICATION' AND 
      b.agency_id = ?
LIMIT 1");


    foreach ($failedToNotifyBuyers as $buyer) {
        list($id, $agencyId, $telephone, $status) = $buyer;

        $deleteStmt->execute([$id]);

        if ($status === 'NOTIFIED') {
            $maskedTelephoneNumber = maskTelephoneNumber($telephone);
            try {
                $success = sendSMS($telephone, $expiredMessage);
                if ($success) {
                    notification_log("Successfully sent the reject notice to {$maskedTelephoneNumber}");
                } else {
                    notification_log("Failed to send the sent the reject notice to {$maskedTelephoneNumber}");
                }
            } catch (\Exception $e) {
                notification_log("Failed to send the sent the reject notice to {$maskedTelephoneNumber}");
            }

        }

        $replacementStmt->execute([$agencyId]);
        $replacement = $replacementStmt->fetch(\PDO::FETCH_NUM);

        list($replacementId, $replacementTelephone, $queueOTP, $agencyName, $city) = $replacement;

        $messageText = str_replace($messageReplacementStrings, [$agencyName, $queueOTP], $notificationTemplate);
        $maskedTelephoneNumber = maskTelephoneNumber($replacementTelephone);
        $timestamp = date('Y-m-d H:i:s');

        try {
            $success = sendSMS($replacementTelephone, $messageText);

            if ($success) {
                $updateStmt->execute(['NOTIFIED', $timestamp, $replacementId]);
                notification_log("Successfully notified {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city}");
            } else {
                $updateStmt->execute(['FAILED_TO_NOTIFY', $timestamp, $replacementId]);
                notification_log("Failed to notify {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city}");
            }
        } catch (\Exception $e) {
            $updateStmt->execute(['FAILED_TO_NOTIFY', $timestamp, $replacementId]);
            notification_log("Failed to notify {$maskedTelephoneNumber} for the queue of {$agencyName} - {$city} due to error: {$e->getMessage()}");
        }

    }

    $dbh->commit();
} catch (\Exception $e) {
    $dbh->rollBack();
    app_log('EROR', $e->getMessage());
}

