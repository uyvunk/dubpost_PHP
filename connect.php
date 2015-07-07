<?php
//connect.php
$db = new PDO("mysql:dbname=dubpost_database", "admin", "P455w0Rd");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>