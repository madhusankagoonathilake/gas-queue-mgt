<?php

require_once '../common/dbh.php';

function getAgencyList(): array
{
    $stmt = getDbh()->prepare("SELECT name, city FROM agency ORDER BY city;");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

function findAgencyDetailsByDisplayName(string $displayName): array
{
    $stmt = getDbh()->prepare("SELECT id, queue FROM agency WHERE name = ? AND city = ?");
    $stmt->execute(explode(' - ', $displayName));
    list($agencyId, $queueJson) = $stmt->fetch(\PDO::FETCH_NUM);

    $queue = empty($queueJson) ? [] : json_decode($queueJson, true);

    return [$agencyId, $queue];
}

function findAgencyDetailsByTelephone(string $telephone): array
{
    $stmt = getDbh()->prepare("SELECT id, queue FROM agency WHERE telephone = ?");
    $stmt->execute([$telephone]);
    list($agencyId, $queueJson) = $stmt->fetch(\PDO::FETCH_NUM);

    $queue = empty($queueJson) ? [] : json_decode($queueJson, true);

    return [$agencyId, $queue];
}

function agencyExists($displayName): bool
{
    $nameParts = explode(' - ', $displayName);

    if (count($nameParts) === 2) {
        $stmt = getDbh()->prepare("SELECT IF(COUNT(*) = 1, 'Yes', 'No') FROM agency WHERE name = ? AND city = ?");
        $stmt->execute($nameParts);
        return ($stmt->fetchColumn() === 'Yes');
    } else {
        return false;
    }
}

function isTelephoneNumberInUse($telephone): bool
{
    $stmt = getDbh()->prepare("SELECT IF(COUNT(*) = 1, 'Yes', 'No') FROM agency WHERE telephone = ?");
    $stmt->execute([$telephone]);
    return ($stmt->fetchColumn() === 'Yes');
}

function generateAgencyActivationOTP(): string
{
    return str_shuffle(rand(100000, 999999));
}

function prepareAgencyActivationMessage($agencyActivationOTP): string
{
    $replacements = ['${agencyActivationOTP}' => $agencyActivationOTP];
    $messageTemplate = file_get_contents('../templates/agency-activation.txt');
    return str_replace(array_keys($replacements), array_values($replacements), $messageTemplate);
}

function isAgencyActivationOTPValid($agencyActivationOTP): bool
{
    try {
        return (getSessionValue('agency-activationOTP') == $agencyActivationOTP);
    } catch (\Exception $e) {
        return false;
    }
}

function addAgency($name, $city, $telephone): void
{
    try {
        $insertStmt = getDbh()->prepare("INSERT INTO agency (name, city, telephone, queue) VALUES (?, ?, ?, '[]')");
        $insertStmt->execute([$name, $city, $telephone]);
    } catch (\Exception $e) {
        throw new \Exception("Failed to add agency {$name}.");
    }
}

function generateAgencyLoginOTP(): string
{
    return str_shuffle(rand(100000, 999999));
}

function prepareAgencyLoginOTPMessage($agencyLoginOTP): string
{
    $replacements = ['${agencyLoginOTP}' => $agencyLoginOTP];
    $messageTemplate = file_get_contents('../templates/agency-login-otp.txt');
    return str_replace(array_keys($replacements), array_values($replacements), $messageTemplate);
}

function isAgencyLoginOTPValid($agencyLoginOTP): bool
{
    try {
        return (getSessionValue('agency-loginOTP') == $agencyLoginOTP);
    } catch (\Exception $e) {
        return false;
    }
}
