<?php
session_start();
session_destroy();
header("Location: /sistema_empresa/auth/login.php");
exit;
