<?php
require_once("../../../../../../../config/dbconnect.php");
if(!isset($_SESSION['username'])) {
    exit;
}

//session_destroy();
//header("Location: imgbrowser.php");
