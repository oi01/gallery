<?

  /************************************************************************************************
   * oi01 - Gallery
   *
   * Description:
   * - Resetting gallery: removing index files
   ************************************************************************************************/

  /**
   * Load configuration
   */
  include_once("../conf.php");

  // Common functions
  function resultExe($res,$text)
  {
    global $icon_ok;
    global $icon_fail;

    $icon = ($res) ? $icon_ok : $icon_fail;
    print("<p><img src=\"../".$icon."\" /> ".$text."</p>\n");
  }

  /**
   * Remove directory recursively
   **/   
  function rrmdir($dir)
  {
    // Check for valid content
    if ($dir=="") return;
	
    // Parse all files and directories
    foreach(glob($dir . '/*') as $file)
	{
	    // Check if directory or file
        if(is_dir($file))
	    {
		    // Parse new directory
            rrmdir($file);
		}
        else
		{
		    // Remove file
		    print("<!-- Removing file: ".$file." -->\n");
            if (!unlink($file)) exit();
		}
    }
	
	// Remove directory
	print("<!-- Removing directory: ".$dir." -->\n");
    rmdir($dir);
  }

  /**
   * Remove files
   **/
  // Log
  print("<h1>Resetting...</h1>\n");
  
  // Remove index (cached)
  $file="../".$file_index;
  $i=unlink($file);
  resultExe($i,"Removing index file: ".$file);
  
  // Remove tags
  $dir="../".$dir_tags;
  rrmdir($dir);

  // Recreate tag directory again
  $i=mkdir($dir,0755);
  resultExe($i,"Creating tag directory: ".$dir);
  
  // Remove galleries (cached) when no update is set
  if (!isset($_GET["update"]))
  {
    $dir="../".$dir_cache;
    rrmdir($dir);
  
    // Recreate cache directory again
    $i=mkdir($dir,0755);
    resultExe($i,"Creating index directory: ".$dir);
  }
  
  // Log
  print("<p><a href=\"..\">Next...</a></p>");

?>