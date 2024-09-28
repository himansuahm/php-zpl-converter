<?php
// Load the PNG image into a GD image resource
$gdImage = imagecreatefromstring(file_get_contents('output.png'));
imagepalettetotruecolor($gdImage); // Convert to true color

$width = (int) ceil(imagesx($gdImage) / 8); // Width in bytes
$height = imagesy($gdImage);
$bitmap = ''; // Initialize bitmap string
$lastRow = null; // Track last row for compression

// Loop through each row of the image
for ($y = 0; $y < $height; $y++) {
    $bits = ''; // Initialize bits for current row

    // Create a binary string for the row
    for ($x = 0; $x < imagesx($gdImage); $x++) {
        $bits .= (imagecolorat($gdImage, $x, $y) & 0xFF) < 127 ? '1' : '0'; // 1 for black, 0 for white
    }

    // Convert bits to bytes
    $bytes = str_split($bits, 8);
    $bytes[] = str_pad(array_pop($bytes), 8, '0'); // Pad last byte if necessary

    // Convert bytes to hex and compress
    $row = implode('', array_map(fn($byte) => sprintf('%02X', bindec($byte)), $bytes));

    // Check for row repetition
    $bitmap .= ($row === $lastRow) ? ':' : compressRow(preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row));
    $lastRow = $row; // Update last row
}

// Prepare ZPL command parameters
$byteCount = $width * $height;
$parameters = ['GF', 'A', $byteCount, $byteCount, $width, $bitmap];
$command = strtoupper(array_shift($parameters));
$parameters = array_map(fn($parameter) => is_bool($parameter) ? ($parameter ? 'Y' : 'N') : $parameter, $parameters);

// Output the final ZPL string
echo implode('', array_merge(['^XA'], ['^' . $command . implode(',', $parameters)], ['^XZ']));

// Function to compress the row data
function compressRow(string $row): string
{
    // Compress repeated characters using regex
    return preg_replace_callback('/(.)(\1{2,})/', fn($matches) => compressSequence($matches[0]), $row);
}

// Function to handle compression of a character sequence
function compressSequence(string $sequence): string
{
    $repeat = strlen($sequence);
    $count = '';

    // Handle sequences longer than 400
    if ($repeat > 400) {
        $count .= str_repeat('z', floor($repeat / 400));
        $repeat %= 400;
    }

    // Handle sequences longer than 20
    if ($repeat > 19) {
        $count .= chr(ord('f') + floor($repeat / 20));
        $repeat %= 20;
    }

    // Add remaining characters
    if ($repeat > 0) {
        $count .= chr(ord('F') + $repeat);
    }

    return $count . substr($sequence, 1, 1); // Return compressed sequence
}
