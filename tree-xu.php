<?php 

/**
 * A PHP CLI script to mimic the output of `tree -XU` on unix-like systems for use with
 * any sufficiently* modern PHP install with CLI capabilities (Unix, Windows, OS X).
 * Does not include the report element of `tree -XU`.
 *
 * Usage:
 *
 *  > php tree-xu.php path_to_directory
 */
 
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

// directory to mimic tree -XU output on
$target_directory = trim($argv[1]);

if(!is_dir($target_directory)){
    exit("Please check that you have provided a full path to a directory as the input argument." . PHP_EOL);
}

$xmlstring = "<tree>";
$xmlstring .= directoryXML($target_directory);
$xmlstring .= "</tree>";
$xml = new DOMDocument( "1.0");
$xml->loadXML($xmlstring);
$xml->formatOutput = true;
echo $xml->saveXML();

/** 
 * Recursively create XML string of directory structure/
 * Based on psuedo-code from http://stackoverflow.com/a/15096721/850828 
 */
function directoryXML($directory_path) {
    
    //  basenames to exclude.
    $exclude_array = array('..', '.DS_Store', 'Thumbs.db', '.');
    
    $dir_name = basename($directory_path);
    $xml = "<directory name='" . $dir_name . "'>";
    
    $pathbase = pathinfo($directory_path, PATHINFO_BASENAME);

    $stuffindirectory = scandir($directory_path);
    
    foreach($stuffindirectory as $subdirOrfile){
        
        $subdirOrfilepath = $directory_path . DIRECTORY_SEPARATOR  . $subdirOrfile;
        
        if(!in_array($subdirOrfile, $exclude_array) && is_file($subdirOrfilepath)){
          $xml .= "<file name='". $subdirOrfile . "' />";
        
        }
    
        if(!in_array($subdirOrfile, $exclude_array) && is_dir($subdirOrfilepath)){
            $xml .= directoryXML($subdirOrfilepath);        
        }
        
    }

    $xml .= "</directory>";

    return $xml;
}

?>