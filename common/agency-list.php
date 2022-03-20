<?php

require_once '../common/agency.php';

$agencyList = getAgencyList();
?>
<datalist id="agencyList">
    <?php foreach ($agencyList as $agency): ?>
        <option><?php echo "{$agency['name']} - {$agency['city']}"; ?></option>
    <?php endforeach; ?>
</datalist>
