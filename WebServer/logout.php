<?php
// Allows the user to logout by removing their session
session_start();
session_destroy();
header("Location: index.php");
exit();
?>