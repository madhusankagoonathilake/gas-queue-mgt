<?php

require_once '../common/dbh.php';
require_once '../common/session.php';
require_once '../common/agency.php';

function isAlreadyInAQueue($telephone): bool
{
    $stmt = getDbh()->prepare("SELECT IF(COUNT(*) > 0, 'Yes', 'No') FROM buyer WHERE telephone = ?;");
    $stmt->execute([$telephone]);
    return ($stmt->fetch(\PDO::FETCH_COLUMN) === 'Yes');
}

function generateBuyerActivationOTP(): string
{
    return str_shuffle(rand(100000, 999999));
}

function generateQueueOTP(): string
{
    try {
        $opt = bin2hex(random_bytes(4));
    } catch (\Exception $e) {
        $opt = substr(str_shuffle(md5(uniqid('__otp'))), 0, 8);
    }
    return $opt;
}

function prepareBuyerActivationMessage($buyerActivationOTP): string
{
    $replacements = ['${buyerActivationOTP}' => $buyerActivationOTP];
    $messageTemplate = file_get_contents('../templates/buyer-activation.txt');
    return str_replace(array_keys($replacements), array_values($replacements), $messageTemplate);
}

function isBuyerActivationOTPValid($buyerActivationOTP): bool
{
    try {
        return (getSessionValue('buyerActivationOTP') == $buyerActivationOTP);
    } catch (\Exception $e) {
        return false;
    }

}

function addBuyerToQueue(string $telephone, string $agency): int
{
    $dbh = getDbh();
    try {
        list($agencyId, $queueLength) = findAgencyDetailsByDisplayName($agency);

        $dbh->beginTransaction();
        $insertStmt = $dbh->prepare("INSERT INTO buyer (telephone, agency_id, otp) VALUES (?, ?, ?)");
        $insertStmt->execute([$telephone, $agencyId, generateQueueOTP()]);

        $updateStmt = $dbh->prepare("UPDATE agency SET queue_length = queue_length + 1 WHERE id = ? ;");
        $updateStmt->execute([$agencyId]);

        $dbh->commit();

        return $queueLength + 1;
    } catch (\Exception $e) {
        $dbh->rollBack();
        throw new \Exception("Failed to add {$telephone} to the queue of {$agency}.");
    }
}

function maskTelephoneNumber(string $telephone): string
{
    return substr($telephone, 0, 3) . 'XXXXX' . substr($telephone, strlen($telephone) - 2);
}
