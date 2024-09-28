<?php
function renderZplToPng($zplString, $outputFile = 'output.png') {
    // Set default canvas size
    $imageWidth = 800;  // Width of the output image
    $imageHeight = 1200; // Height of the output image

    // Create a blank image with a white background
    $image = imagecreatetruecolor($imageWidth, $imageHeight);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $fontPath = './arial.ttf';  // Ensure you have this font file in the correct path

    // Fill the background with white
    imagefill($image, 0, 0, $white);

    // Initialize position variables
    $x = 0;
    $y = 0;
    $currentFontSize = 30; // Default font size

    // Split ZPL commands by line breaks
    $commands = preg_split('/[\^]+/', $zplString);

    // Loop through each ZPL command and draw elements
    foreach ($commands as $command) {
        $command = trim($command);

        if (empty($command)) continue;

        // ^FOx,y - Set Field Origin
        if (preg_match('/FO(\d+),(\d+)/', $command, $matches)) {
            $x = (int)$matches[1];
            $y = (int)$matches[2];
        }

        // ^CFfont,fontsize - Change Font
        elseif (preg_match('/CF(\d+),(\d+)/', $command, $fontSizeMatches)) {
            // Extract font size
            $currentFontSize = (int)$fontSizeMatches[2];
        }

        // ^FDtext - Draw Text
        elseif (preg_match('/FD(.*?)(?=\^|$)/', $command, $matches)) {
            $text = trim($matches[1]);
            // Draw text with the current font size
            imagettftext($image, $currentFontSize, 0, $x, $y + $currentFontSize, $black, $fontPath, $text);
        }

        // ^GBw,h,t - Draw Box
        elseif (preg_match('/GB(\d+),(\d+),(\d+)/', $command, $matches)) {
            $boxWidth = (int)$matches[1];
            $boxHeight = (int)$matches[2];
            $thickness = (int)$matches[3];

            if ($thickness > 0) {
                // Draw the outer border with the specified thickness
                imagesetthickness($image, $thickness);
                imagerectangle($image, $x, $y, $x + $boxWidth, $y + $boxHeight, $black);
            } else {
                // Draw a filled rectangle if thickness is zero
                imagefilledrectangle($image, $x, $y, $x + $boxWidth, $y + $boxHeight, $black);
            }
        }
    }

    // Save the rendered image as PNG
    imagepng($image, $outputFile);
    imagedestroy($image); // Free the image resource
    echo "Image saved at $outputFile\n";
}

// Example ZPL code with barcode
$zplCode = '^XA^LT120
^FX Top section
^CFB,25
^FO50,173^FDFROM:^FS
^FO200,173^FDTest sender^FS
^FO200,228^FD10 ABC ROAD LINE^FS
^FO200,283^FDCITY, STATE, PINCODE^FS
^FO50,343^GB706,1,3^FS
^FX Second section with recipient address
^CFB,25
^FO50,363^FDTO:^FS
^FO200,363^FDJohn Smith^FS
^FO200,423^FDAccount Department.^FS
^FO200,473^FD123 Market Street line^FS
^FO200,523^FDCITY, STATE, ZIP^FS
^FO50,4830^GB706,1,3^FS
^XZ';

renderZplToPng($zplCode, 'output.png');
