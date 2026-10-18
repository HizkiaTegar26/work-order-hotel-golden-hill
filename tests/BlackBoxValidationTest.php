<?php

// Wrapper to run Black Box Validation Test Suite via Spark CLI
$command = 'php ' . escapeshellarg(__DIR__ . '/../spark') . ' test:blackbox';
passthru($command, $exitCode);
exit($exitCode);
