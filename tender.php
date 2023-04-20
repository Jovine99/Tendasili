<?php

// Set the URL of the website to crawl
$url = "https://tenders.go.ke/";

// Set the directory where the tender data will be stored
$dataDirectory = "/.../";

// Get today's date in the format "Y-m-d"
$todayDate = date("Y-m-d");

// Create a filename for today's tender data
$filename = $todayDate . ".txt";
$filePath = $dataDirectory . $filename;

// Check if today's tender data has already been scraped
if (file_exists($filePath)) {
    echo "Today's tender data has already been scraped.\n";
    exit;
}

// Create a new DOMDocument object and load the HTML from the website
$doc = new DOMDocument();
$doc->loadHTMLFile($url);

// Find the tender table on the page
$tables = $doc->getElementsByTagName("table");
$table = null;

foreach ($tables as $t) {
    if ($t->getAttribute("class") == "tender_table") {
        $table = $t;
        break;
    }
}

// Check if the tender table was found
if (!$table) {
    echo "Error: tender table not found on page.\n";
    exit;
}

// Loop through the rows in the tender table
$rows = $table->getElementsByTagName("tr");

foreach ($rows as $row) {
    // Get the cells in the row
    $cells = $row->getElementsByTagName("td");
    
    // Check if the row has the right number of cells (i.e. it's a tender row)
    if ($cells->length == 6) {
        // Get the tender number, description, and closing date
        $tenderNumber = trim($cells->item(0)->nodeValue);
        $tenderDescription = trim($cells->item(1)->nodeValue);
        $closingDate = trim($cells->item(5)->nodeValue);
        
        // Check if the tender closing date is today or later
        if (strtotime($closingDate) >= strtotime($todayDate)) {
            // Append the tender data to the file
            $line = "$tenderNumber\t$tenderDescription\t$closingDate\n";
            file_put_contents($filePath, $line, FILE_APPEND);
        }
    }
}


echo "Tender data for $todayDate has been scraped and saved to $filePath.\n";

?>
