<?php
session_start();
session_destroy();
header("Location: userreg.php");
exit();
