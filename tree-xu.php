<?php 

/**
 * A PHP CLI script to mimic the output of `tree -XU` on unix-like systems for use with
 * any sufficiently* modern PHP install with CLI capabilities (Unix, Windows, OS X).
 *
 * Usage:
 *
 *  > php tree-xu.php path_to_directory
 */

// directory to mimic tree -XU output on
$target_directory = trim($argv[1]);

/*  Example tree -XU output:

<?xml version="1.0" encoding="UTF-8"?>
<tree>
  <directory name="singlechild/">
    <directory name="2131">
      <directory name="2130">
        <file name="MODS.xml"></file>
        <file name="OBJ.pdf"></file>
      </directory>
      <file name="MODS.xml"></file>
      <file name="structure.cpd"></file>
    </directory>
  </directory>
  <report>
    <directories>2</directories>
    <files>4</files>
  </report>
</tree>
*/

// Include basenames to exclude.
$exclude_array = array('..', '.DS_Store', 'Thumbs.db');

$dir = new RecursiveDirectoryIterator($target_directory);
$iterator = new RecursiveIteratorIterator($dir);

$xmlstring = "<tree>\n";
$xml = new DOMDocument( "1.0");
// create root element
$root = $xml->createElement('tree');
$xml->appendChild($root);

// assign a key to each element created.
$elementMap = array();

// Map of keys for parent element and value array of one or more direct child nodes.
$parentChildrenMap = array();

foreach ($iterator as $fileinfo) {

    //var_dump($fileinfo);
    $message = $fileinfo->getPathname();
    $directory_path = $fileinfo->getPath();
    $basename = $fileinfo->getbasename();
    
    $tempxmlelement = null;
    
    if($fileinfo->isDir() && includeBasename($basename)){
        echo $message . " is a directory with basename $basename "  . PHP_EOL;
        $basename = basename($directory_path); 
        $xmlstring .= "<directory name='". $basename . "'/>\n";
        $directories = trackDirectories($directory_path, $basename );
        $tempxmlelement = $xml->createElement('directory');
        $tempxmlelement->setAttribute('name', $basename);
        // array push retuns number of elements in the array;
        //$keyplusone = array_push($elementMap,$tempxmlelement);
        $elementMap[$basename] = $tempxmlelement;
        
    }
    
    if($fileinfo->isFile() && includeBasename($basename)){
        //echo $message . "   is a file with basename $basename" . PHP_EOL;
        $xmlstring .= "<file name='".basename($fileinfo->getPathname()) . "'/>\n";
    }


}
$xmlstring .= "</tree>\n";

function parentDirectChildFromDirectoryPaths($directory_path, $directories) {
    static $parentChildrenMap = array();
    foreach($directories as $key => $path) {
        $dirbase = basename($directory_path);
        $needle = "\\" . $key . "\\" . $dirbase;
    
    }
    
    return $parentChildrenMap;

}

//print_r($xmlstring);
//print_r($elementMap);
//print_r($directories);
/*
foreach($elementMap as $element){
   $root->appendChild($element); 
}
*/
// Parse the XML.
//print $xml->saveXML();


function includeBasename($basename) {
    // Include basenames to exclude.
    $exclude_array = array('..', '.DS_Store', 'Thumbs.db');
    if (in_array($basename, $exclude_array) ) {
        return false;
    } else {
        return true;
    }
}

function trackDirectories($directory_path, $basename){
    static $directory_array = array();
    
    if(!in_array($directory_path, $directory_array)){
        $directory_array[$basename] = $directory_path;
    }

    return $directory_array;
}


