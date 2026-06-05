<?php

require_once __DIR__ . '/../config/db.php';

echo "<pre>";
print_r(Database::test("SELECT * FROM artists"));
echo "</pre>";

?>

<a href="login.php">Login</a>
<br>
<a href="dashboard/index.php">Dashboard</a>