<?php

require_once '../common/dbh.php';

$stmt = $dbh->prepare("SELECT id, name, city FROM agency;");
$stmt->execute();
$result = $stmt->fetchAll(\PDO::FETCH_CLASS);

?>
<datalist id="agencyList">
    <?php foreach ($result as $obj): ?>
        <option value="<?php echo "{$obj->name} - {$obj->city}"; ?>"><?php echo "{$obj->name} - {$obj->city}"; ?></option>
    <?php endforeach; ?>
</datalist>
