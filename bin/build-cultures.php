<?php

/**
 * Available Cultures Resource File
 *
 * According to
 *
 *    https://www.textcontrol.com/blog/2017/10/13/
 *
 * ReportingCloud uses the culture values as defined at
 *
 *   https://msdn.microsoft.com/en-us/library/ee825488(v=cs.20).aspx
 *
 * This script downloads these culture values and writes them to the file:
 *
 *    resource/cultures.php
 *
 * The package maintainer should execute this script, whenever new cultures are added to the backend.
 *
 */

include_once 'bootstrap.php';

use TxTextControl\ReportingCloud\Console\Helper;
use TxTextControl\ReportingCloud\Exception\RuntimeException;
use TxTextControl\ReportingCloud\Validator\Culture as Validator;

$validator = new Validator();

// ---------------------------------------------------------------------------------------------------------------------

$url    = 'https://msdn.microsoft.com/en-us/library/ee825488(v=cs.20).aspx';
$values = [];

libxml_use_internal_errors(true);

$dom = new DomDocument;
$dom->loadHtmlFile($url);

$xpath = new DomXPath($dom);
$nodes = $xpath->query("//tr/td[1]");
foreach ($nodes as $node) {
    $values[] = trim($node->nodeValue);
}

if (0 === count($values)) {
    $format  = 'Cannot download the available cultures from %s';
    $message = sprintf($format, $url);
    throw new RuntimeException($message);
}

natcasesort($values);
$values = array_values($values);

// ---------------------------------------------------------------------------------------------------------------------

Helper::varExportToFile($validator->getFilename(), $values);

echo PHP_EOL;
echo sprintf('The available cultures (%d) are %s.', count($values), implode(', ', $values));
echo PHP_EOL;
echo PHP_EOL;
echo sprintf('Written resource file to %s', $validator->getFilename());
echo PHP_EOL;
echo PHP_EOL;

// ---------------------------------------------------------------------------------------------------------------------
