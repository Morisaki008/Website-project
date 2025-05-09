<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Current include path: " . get_include_path() . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Parent directory: " . dirname(__DIR__) . "\n";

$include_file = dirname(__DIR__) . '/includes/db_connect.php';
echo "Attempting to include: " . $include_file . "\n";
echo "File exists? " . (file_exists($include_file) ? 'Yes' : 'No') . "\n";
echo "Is readable? " . (is_readable($include_file) ? 'Yes' : 'No') . "\n";

if(file_exists($include_file)) {
    echo "File permissions: " . decoct(fileperms($include_file) & 0777) . "\n";
}
?>