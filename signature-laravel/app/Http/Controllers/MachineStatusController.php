<?php

// Temporary bridge: the real MachineStatusController implementation exists in
// the test workspace. We delegate to it to avoid “Class not found / 500” errors
// from routes referencing this controller.
require_once '/home/signature-in-house-test/htdocs/test.signature-in-house.com/signature-laravel/app/Http/Controllers/MachineStatusController.php';

