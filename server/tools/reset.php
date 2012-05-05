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
		    print("- Removing file: ".$file."<br />\n");
            if (!unlink($file)) exit();
		}
    }
	
	// Remove directory
	print("- Removing directory: ".$dir."<br />\n");
    rmdir($dir);
  }

  /**
   * Remove files
   **/
  // Log
  print("<code>\n");
  print("Resetting gallery...<br />\n");
  
  // Remove index (cached)
  print("- Removing index: ../".$file_index."<br />\n");
  unlink("../".$file_index);
  
  // Remove tags
  rrmdir("../".$dir_tags);

  // Recreate tag directory again
  print("- Create directory: ../".$dir_tags."<br />\n");
  mkdir("../".$dir_tags,0755);
  
  // Remove galleries (cached) when no update is set
  if (!isset($_GET["update"]))
  {
    rrmdir("../".$dir_cache);
  
    // Recreate cache directory again
    print("- Create directory: ../".$dir_cache."<br />\n");
    mkdir("../".$dir_cache,0755);
  }
  
  // Log
  print("Done...");
  print("</code>\n");
  print("<br />\n<br />\n");
  print("<a href=\"..\">Next...</a>");

?>