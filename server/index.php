<?

  /************************************************************************************************
   * oi01 - Gallery 1.3.2 (19.10.2012)
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
        readfile($conf["file_ImageOfTheDay"]);
	    exit();
	  }
	}

	// Generate IOTD
    genIotd();
    unlink($conf["dir_cache"]."Image_Of_The_Day.html");
    unlink($conf["file_index"]);

    // Write new index file
    getOverview();
    $isExeInfo=false;
    genIndex($arr_tags);
    $isExeInfo=true;

    // Show image of the day pages
    readfile($conf["file_ImageOfTheDay"]);
	exit();
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

  // Show header
  print("<h1>Configuring...</h1>\n");

  // Write IOTD first to have the correct file structure
  if ($conf["is_ImageOfTheDay"])
  {
    unlink($conf["dir_cache"]."Image_Of_The_Day.html");
    genIotd();
  }

  // Get image information
  getOverview();

  /**
   * Write an updated index
   **/
  genIndex($arr_tags);

  // Log
  print("<p><a href=\".\">Next...</a></p>");

?>