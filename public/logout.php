<?php
require_once __DIR__ . '/../src/models/auth.php';
Auth::logout();
redirect('login.php');