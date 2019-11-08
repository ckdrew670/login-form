<?php
session_start();
// Delete certain session
unset($_SESSION['logged_in']);

header('Location: login.php?logged-out=true');
?>