<?

  /************************************************************************************************
   * oi01 - Gallery 1.1.1 (29.07.2012)
   *
   * Author: Jürgen Kniephoff <git@oi01.de>
   * Web:    http://www.oi01.de/gallery
   ************************************************************************************************/

  /**
   * Load configuration
   */
  include_once("common.php");

  /**
   * Global variables
   **/
  $arr_topics=array();
  $arr_count=array();
  $arr_tags=array("*" => array());

  /**
   * Check for relocations
   */
  // Check if index and gallerie cache shall be removed
  if (isset($_GET["reset"]))
  {
	header("Location: ".$conf["file_reset"]);
	exit();
  }

  // Check if index only shall be removed
  if (isset($_GET["update"]))
  {
	header("Location: ".$conf["file_reset"]."?update=1");
	exit();
  }

  // Check for an updated index file
  if (file_exists($conf["file_index"]))
  {
    // Check if image of the day is disabled
	if (!$conf["is_ImageOfTheDay"])
	{
	  // Continue to gallery index
      header("Location: ".$conf["dir_cache"]);
	  exit();
	}

	// Check for the iotd index
	if (file_exists($conf["file_ImageOfTheDay"]))
	{
	  // Get modified date of file
	  $date_mod=date("Y-m-d", filemtime($conf["file_ImageOfTheDay"]));

	  // Check date with current date
      if ($date_mod==$date_file)
	  {
        header("Location: ".$conf["file_ImageOfTheDay"]);
	    exit();
	  }
	}

	// Generate IOTD
    genIotd();
  }

  // Check if software needs to be installed
  if (file_exists($conf["file_install"]))
  {
	header("Location: ".$conf["file_install"]);
	exit();
  }

  /**
   * Check for an updated cache
   **/
  // Pre-define variables
  $arr_files=array();
  $arr_dirs=array();

  // Parse original galleries
  resultExe(true,"Parsing galleries: ".$conf["dir_pics"]);
  parse_dir($conf["dir_pics"],$arr_files,$arr_dirs);

  // Check all directories
  foreach($arr_dirs as $dir)
  {
    // Build path to cache file
	$name=str_replace(" ","_",$dir);
	$name.=".html";

	// Check if exists and write cache
    createCache($conf["dir_pics"],$conf["dir_thumbs"],$conf["dir_cache"],$dir,$name,!file_exists($conf["dir_cache"].$name));
  }

  /**
   * Write an updated index
   **/
  genIndex($arr_tags);

  // Log
  print("<p><a href=\".\">Next...</a></p>");

?>