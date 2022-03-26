<?php

require_once '../common/dbh.php';
require_once '../common/logger.php';

function getAgencyList(): array
{
    $stmt = getDbh()->prepare("SELECT name, city FROM agency ORDER BY city;");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

function findAgencyDetailsByDisplayName(string $displayName): array
{
    $stmt = getDbh()->prepare("SELECT id, queue_length FROM agency WHERE name = ? AND city = ?");
    $stmt->execute(explode(' - ', $displayName));
    return $stmt->fetch(\PDO::FETCH_NUM);
}

function findAgencyIdByTelephone(string $telephone): int
{
    $stmt = getDbh()->prepare("SELECT id FROM agency WHERE telephone = ?");
    $stmt->execute([$telephone]);
    return (int)$stmt->fetch(\PDO::FETCH_COLUMN);
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
        $insertStmt = getDbh()->prepare("INSERT INTO agency (name, city, telephone) VALUES (?, ?, ?)");
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

function findAgencyDetailsById($id): array
{
    try {
        $stmt = getDbh()->prepare("SELECT name, city, queue_length, current_batch_size, available_amount, issued_amount FROM agency WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_NUM);
    } catch (\Exception $e) {
        throw new \Exception("Error while fetching agency details.");
    }

}

function generateCurrentBatchStatusDonutChart($issuedAmount, $currentBatchSize, $issuedPercentage, $availablePercentage): string
{

    $text = "{$issuedAmount}/{$currentBatchSize}";

    $hue = $availablePercentage * 1.2;
    $color = "hsl({$hue},80%,50%)";

    $dimension = 42;
    $scale = 0.8;

    $textLength = strlen($text);
    $fontSize = 6 - floor($textLength / 6);
    $midPoint = round($dimension / 2);
    $radius = round(pi() * 5, 14); // 15.91549430918954 or closer
    $textX = round(($dimension / $textLength) + ($scale * $textLength), 4);
    $textY = round(($dimension / 2) + ($scale * 2), 4);

    $html = file_get_contents('../templates/donut-chart.svg.html');

    $replacements = [
        '${scalePercentage}' => $scale * 100,
        '${dimension}' => $dimension,
        '${midPoint}' => $midPoint,
        '${radius}' => $radius,
        '${filledPercentage}' => $issuedPercentage,
        '${remainingPercentage}' => $availablePercentage,
        '${text}' => $text,
        '${fontSize}' => $fontSize,
        '${textX}' => $textX,
        '${textY}' => $textY,
        '${color}' => $color,
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $html);
}

function generateQueueGraphic($queueLength, $currentBatchSize, $issuedAmount): string
{

    $dimension = 42;
    $scale = 0.8;

    $displayQueueLength = $queueLength;
    if ($queueLength > 100) {
        $displayQueueLength = 100;
        $currentBatchSize = floor(($currentBatchSize / $queueLength) * 100);
        $issuedAmount = floor(($issuedAmount / $queueLength) * 100);
    }

    $startX = 4.25;
    $startY = 6.5;
    $fontSize = 3.5;
    $x = $startX;
    $y = $startY;
    $tagTemplate = '<text x="${x}" y="${y}" font-size="3.25" fill="${c}">${t}</text>';
    $tagReplacementStrings = ['${x}', '${y}', '${c}', '${t}'];

    if ($queueLength > 0) {
        $queue = '';

        $reset = true;
        for ($i = 1; $i <= $displayQueueLength; $i++) {

            $x = $reset ? $startX : $x + $fontSize;
            $y = $startY + ($fontSize * (ceil($i / 10) - 1));
            $c = $currentBatchSize >= $i ? 'orange' : 'gray';
            $t = $issuedAmount >= $i ? '■' : '□';
            $queue .= str_replace($tagReplacementStrings, [$x, $y, $c, $t], $tagTemplate);

            $reset = ($i % 10 === 0);
        }
    } else {
        $queue = str_replace($tagReplacementStrings, [$startX - 1.5, $startY, 'gray', 'පොරොත්තු ලයිස්තුව හිස්ය.'], $tagTemplate);
    }

    $html = file_get_contents('../templates/queue.svg.html');
    $replacements = [
        '${scalePercentage}' => $scale * 100,
        '${dimension}' => $dimension,
        '${queue}' => $queue,
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $html);
}

function issueBatch(int $agencyId, int $size): bool
{
    $success = false;

    $dbh = getDbh();
    try {

        $dbh->beginTransaction();

        $buyerListStmt = $dbh->prepare("SELECT id FROM buyer WHERE agency_id = ? LIMIT {$size} ");
        $buyerListStmt->execute([$agencyId]);
        $buyerIdList = $buyerListStmt->fetchAll(\PDO::FETCH_COLUMN);

        // Being extra safe, rather than running a direct UPDATE with a LIMIT $size
        $buyerUpdateStmt = $dbh->prepare("UPDATE buyer SET status = 'PENDING_NOTIFICATION' WHERE id = ?");
        foreach ($buyerIdList as $buyerId) {
            $buyerUpdateStmt->execute([$buyerId]);
        }

        $agencyQueueStatsUpdateStmt = $dbh->prepare("UPDATE agency SET current_batch_size = ?, available_amount = ? WHERE id = ?");
        $agencyQueueStatsUpdateStmt->execute([$size, $size, $agencyId]);

        $dbh->commit();

        $success = true;

    } catch (\Exception $e) {
        $dbh->rollBack();
        app_log('ER0R', $e->getMessage());
    }

    return $success;
}

function getBuyerStatusByQueueOTP(int $agencyId, string $otp): string
{
    try {
        $stmt = getDbh()->prepare("SELECT status FROM buyer WHERE agency_id = ? AND otp = ?;");
        $stmt->execute([$agencyId, $otp]);
        return $stmt->fetch(\PDO::FETCH_COLUMN);
    } catch (\Exception $e) {
        app_log('EROR', $e->getMessage());
        return 'ERROR';
    }
}


