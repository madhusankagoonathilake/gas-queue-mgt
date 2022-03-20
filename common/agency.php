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
