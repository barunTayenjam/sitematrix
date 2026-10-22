<?php
/**
 * Entry point for the theme-functions TDD suite.
 *
 * Output is buffered so that session_start() (exercised by the admin-session
 * tests) is not blocked by "headers already sent" in the CLI SAPI.
 */

ob_start();

require __DIR__ . '/runner.php';
