# PNG to ZPL and ZPL to PNG Converter

This project provides two PHP scripts: `IMG2ZPL.php` for converting PNG images to ZPL (Zebra Programming Language) format, and `ZPL2IMG.php` for converting ZPL strings back into PNG images. These scripts are useful for generating printable labels for Zebra printers directly from images and vice versa.

## Features

- **IMG2ZPL.php**: Converts a PNG image into a ZPL string that can be sent to a Zebra printer.
- **ZPL2IMG.php**: Converts a ZPL string back into a PNG image, allowing for visual verification of the label.

## Requirements

- PHP 8 or higher
- GD extension enabled in PHP

## Usage

### IMG2ZPL.php

To convert a PNG image to ZPL:

1. Place your PNG image in the same directory as `IMG2ZPL.php`.
2. Open `IMG2ZPL.php` and modify the image file path if necessary.
3. Run the script. The output will be the ZPL string, which you can send to your Zebra printer.

### ZPL2IMG.php

To convert a ZPL string back to a PNG image:

1. Open `ZPL2IMG.php` and replace the example ZPL string with your own ZPL code.
2. Run the script. The output will be a PNG file generated in the same directory.

**Note**: For QR code and barcode generation, you need to add the appropriate PHP libraries and update the code accordingly.
