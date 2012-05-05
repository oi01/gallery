<?

  /************************************************************************************************
   * oi01 - Gallery 1.0 (01.04.2012)
   *
   * Author: Jürgen Kniephoff <git@oi01.de>
   * Web:    http://www.oi01.de/gallery
   ************************************************************************************************/

  /**
   * Load configuration
   */
  include_once("conf.php");
   
  /**
   * Global variables
   **/
  $arr_topics=array();
  $arr_count=array();
  $arr_tags=array("*" => array());
  $var_conf_global=array();
  
  /**
   * Check for relocations
   */
  // Check if index and gallerie cache shall be removed
  if (isset($_GET["reset"]))
  {
	header("Location: ".$file_reset);
	exit();
  }

  // Check if index only shall be removed
  if (isset($_GET["update"]))
  {
	header("Location: ".$file_reset."?update=1");
	exit();
  }
  
  // Check for an updated index file
  if (file_exists($file_index))
  {
    //print(file_get_contents($file_index));
	header("Location: ".$dir_cache);
	exit();
  }
  
  // Check if software needs to be installed
  if (file_exists($file_install))
  {
	header("Location: ".$file_install);
	exit();
  }
  
  /**
   * Debug messages
   **/
  function debug($text)
  {
    global $is_debug;
    if ($is_debug) print($text."<br />\n");
  }
  
  /**
   * Parse directories
   **/
  function parse_dir($dir,&$arr_files,&$arr_dirs)
  {
    // Log
    debug("Parsing directory...");
	debug("- dir: ".$dir);
	
    // Pre-Define used variables
    $dh=null;
    $file=null;

	// Check for a valid directory
    if (is_dir($dir))
    {
	  // Open directory
	  if ($dh=opendir($dir))
	  {
	    // Read files
	    while(($file=readdir($dh)) !== false)
	    {
		  // Skip . and ..
		  if ($file=="." || $file=="..")
		  {
		    continue;
	      }
		
		  // Check for file
		  if (is_dir($dir.$file))
		  {
		    array_push($arr_dirs,$file);
		  }
		  
		  // Check for dir
		  if (is_file($dir.$file))
		  {
		    array_push($arr_files,$file);
		  }
	    }
	  
	    // Close directory
	    closedir($dh);
	  }
	}
	
	// Log
    debug("- arr_files: ".var_dump($arr_files));
	debug("- arr_dirs: ".var_dump($arr_dirs));
  }
  
  /**
   * Create cache from name
   **/
  function createCache($dir_pics,$dir_thumbs,$dir_cache,$dir,$name,$write)
  {
    // Log
    debug("Creating cache...");
	debug("- dir_pics: ".$dir_pics);
	debug("- dir_thumbs: ".$dir_thumbs);
	debug("- dir_cache: ".$dir_cache);
	debug("- dir: ".$dir);
	debug("- name: ".$name);
	
	// Pre-define variables
	$arr_files=array();
	$arr_dirs=array();
	$arr_pics=array();
	$im=null;
	$file=null;
	
	// Parse for files
	parse_dir($dir_pics.$dir."/",$arr_files,$arr_dirs);
	
    /**
	 * Create thumbnail
	 **/
    foreach($arr_files as $file)
	{
	  // Log
	  debug("Creating thumbnail...");
	  debug("- File: ".$file);
	  
	  // Load image without printing errors
	  $im=@imagecreatefromjpeg($dir_pics.$dir."/".$file);
	  
	  // Validate image
	  if (!$im)
	  {
		debug("- Status: invalid");
		continue;
	  }
  
	  // Log
	  debug("- Status: valid");
	  // Flag as valid image
	  array_push($arr_pics,$file);

	  if ($write)
	  {
		
		// Resize image
		global $thumb_width, $thumb_height;
		$im_thumb=imagecreatetruecolor($thumb_width,$thumb_height);
		imagecopyresampled($im_thumb,$im,0,0,0,0,$thumb_width,$thumb_height,imagesx($im),imagesy($im));
		
		// Save image
		debug("- Saving to: ".$dir_thumbs.$dir."/".$file);
		if (!file_exists($dir_thumbs.$dir))
		{
			mkdir($dir_thumbs.$dir,0755,true);
		}
		imagejpeg($im_thumb,$dir_thumbs.$dir."/".$file,75);
	  }
	}
		
	/**
	 * Save an image for the index
	 **/
	$var_hidden=false;
	$var_indexpic="";
	$var_tags=array();

	$info="";
	
	// Check info of gallery
	global $file_info;
	global $file_conf_gallery;
	
	/**
     * Read description
	 **/
    debug("Reading info file: ".$dir_pics.$dir."/".$file_info);
	if (file_exists($dir_pics.$dir."/".$file_info))
	{  
	  // Read file
	  $info=file($dir_pics.$dir."/".$file_info);
	  $info=trim(join("<br />",$info));
	}
	
	/**
     * Read gallery config
	 **/
    global $is_tags;
    global $arr_tags;  

	debug("Reading config file: ".$dir_pics.$dir."/".$file_conf_gallery);
	if (file_exists($dir_pics.$dir."/".$file_conf_gallery))
	{
	  // Read file
	  $var=parse_ini_file($dir_pics.$dir."/".$file_conf_gallery);
	  
	  // Hidden
	  if (array_key_exists("hidden",$var))
	  {
	    debug("#hidden: ".$var["hidden"]);
	    $var_hidden=$var["hidden"];
	  }
	  
	  // Index picture
	  if (array_key_exists("index",$var))
	  {
	    debug("#index: ".$var["index"]);
	    $var_indexpic=$var["index"];
	  }
	  
 	  // Tags
	  if ($is_tags && array_key_exists("tags",$var))
	  {
	    debug("#tags: ".$var["tags"]);
	    $var_tags=explode(" ",$var["tags"]);
		
		foreach ($var_tags as $tag)
		{
		  // Check if tag already exists
		  if (array_key_exists($tag,$arr_tags))
		  {
		    // Add gallery
		    array_push($arr_tags[$tag],$dir);
		  }
		  else
		  {
		    // Create tag
			$arr_tags[$tag]=array($dir);
		  }
		}
	  }	  
	}
	
	// Add gallery
	if ($var_hidden==false)
	{
	  global $arr_topics;
	  global $arr_count;
	  
	  // Check for index picture
	  if ($var_indexpic=="")
	  {
	    // Random picture
	    $i=rand(0,sizeof($arr_pics)-1);
		$arr_topics[$dir]=$arr_pics[$i];
      }
	  else
	  {
	    // User input
	    $arr_topics[$dir]=$var_indexpic;
	  }
	  
	  // Apply number of pictures
	  $arr_count[$dir]=sizeof($arr_pics);
	  
      // Add gallery to main tag
      array_push($arr_tags["*"],$dir);
	}
	
    /**
	 * Create HTML file
	 **/
    // Create replacement text
	$buffer_pics="";
	
	// Check for information text
	if (strlen($info) > 0)
	{
	  $buffer_pics="<p class=\"info\">\n";
	  $buffer_pics.=$info;
	  $buffer_pics.="</p>\n";
	}
	
    // Generate image codes
	natsort($arr_pics);
	foreach($arr_pics as $pic)
	{
		$buffer_pics.="    <a class=\"oi01-gallery\" href=\"../".$dir_pics.$dir."/".$pic."\" title=\"".$pic."\"><img src=\"../".$dir_thumbs.$dir."/".$pic."\" /></a>\n";
	}
	
    // Create template
	global $file_cache;
	global $text_home;
    $buffer_file=file_get_contents($file_cache);
	$buffer_file=str_replace("%pics%",$buffer_pics,$buffer_file);
	$buffer_file=str_replace("%title%",$dir,$buffer_file);
	$buffer_file=str_replace("%home%",$text_home,$buffer_file);
	
    // Save file
	if ($write) file_put_contents($dir_cache.$name,$buffer_file);
  }
  
  /**
   * Read global configuration
   **/
  print("<code>\n");

  if (file_exists($file_conf_global))
  {
    debug("Reading global configuration...");
    $var_conf_global=parse_ini_file($file_conf_global);
  }

  /**
   * Check for an updated cache
   **/
  // Pre-define variables
  $arr_files=array();
  $arr_dirs=array();
  
  // Parse original galleries
  parse_dir($dir_pics,$arr_files,$arr_dirs);
  
  // Check all directories
  foreach($arr_dirs as $dir)
  {
    // Build path to cache file
	$name=str_replace(" ","_",$dir);
	$name.=".html";
	
	// Check if exists and write cache
    createCache($dir_pics,$dir_thumbs,$dir_cache,$dir,$name,!file_exists($dir_cache.$name));
  }

  /**
   * Write an updated index
   **/

  // Sort tags
  uksort($arr_tags, 'strnatcmp');
  
  // Sort galleries
  uksort($arr_topics, 'strnatcmp');
  $arr_topics=array_reverse($arr_topics);

  // Parse tag list
  foreach($arr_tags as $cur_tag => $cur_tagdirs)
  {
      // Reset buffer
	  $buffer_index="";
	  
	  // Show tags only in index and only if other tags exist
	  if ($cur_tag=="*" && sizeof($arr_tags)>1)
	  {
		$buffer_index.="<h2>";
		foreach($arr_tags as $tag => $tag_dirs)
		{
		  // Skip default tag
		  if ($tag == $cur_tag) continue;
          
		  // Add next tag
		  $buffer_index.=" <a href=\"../".$dir_tags.$tag.".html\">#".$tag."</a>";
		}
		$buffer_index.="</h2>\n";
	  }
	  
	  // Show galleries
	  $buffer_index.="<table align=\"center\">\n";
	  
	  $n=0;
	  foreach($arr_topics as $name => $file)
	  {
	    // Check if gallery shall be shown
		if (!in_array($name,$cur_tagdirs)) continue;
		
		// Show gallery
		if ($n % $table_col == 0)
		{
		  $buffer_index.="  <tr>\n";
		}

		$buffer_index.="   <td align=\"center\">\n";
		$buffer_index.="     <a href=\"../".$dir_cache.str_replace(" ","_",$name).".html\"><img src=\"../".$dir_thumbs.$name."/".$file."\"/><br />".$name." (".$arr_count[$name].")</a>\n";
		$buffer_index.="   </td>\n";

		if ($n % $table_col == $table_col-1)
		{
		  $buffer_index.="  </tr>\n";
		}
		
		$n++;
	  }

	  // Pad
	  $n=4 - $n % $table_col;
	  if ($n>=0)
	  {
		while($n >= 0)
		{
		  $buffer_index.="  <td>&nbsp;</td>";
		  $n--;
		}
		
		$buffer_index.="  </tr>";
	  }
	  
	  $buffer_index.="</table>\n";

	  // Prepare content
	  $buffer_file=file_get_contents($file_index_tpl);
      $buffer_file=str_replace("%index%",$buffer_index,$buffer_file);
	  $buffer_file=str_replace("%title%",$text_title,$buffer_file);
	  $buffer_file=str_replace("%home%",$text_home,$buffer_file);

      // Save file
	  $file_out=$file_index;
	  if ($cur_tag!="*") $file_out=$dir_tags.$cur_tag.".html";
	  
      debug("Updating index file: ".$file_out);
      file_put_contents($file_out,$buffer_file);
  }
  
  /**
   * Write an RSS file
   **/
  // <link rel="alternate" type="application/rss+xml" title="Golem.de RSS Feed" href="http://rss.golem.de/rss.php?feed=RSS1.0">
  // <link href="/favicon.ico" rel="shortcut icon" />
  // set the default timezone to use
  // date_default_timezone_set('UTC');
  
  $buffer_rss="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
  $buffer_rss.="<feed xmlns=\"http://www.w3.org/2005/Atom\">\n";
  $buffer_rss.="  <title>".$text_title."</title>\n";
  // $buffer_rss.="  <subtitle></subtitle>\n";
  // $buffer_rss.="  <link href=\"".$_SERVER["SCRIPT_URI"]."\" />\n"; // TODO
  $buffer_rss.="  <link rel=\"self\" href=\"".$_SERVER["SCRIPT_URI"]."\" />\n";
  $buffer_rss.="  <updated>".date(DATE_ATOM,time())."</updated>\n";
  $buffer_rss.="  <author>\n";
  $buffer_rss.="    <name>oi01 - Gallery</name>\n";
  $buffer_rss.="  </author>\n";
  // $buffer_rss.="  <id></id>\n"; // TODO

  foreach($arr_topics as $name => $file)
  {
    $buffer_rss.="  <entry>\n";
    $buffer_rss.="    <title>".$name."</title>\n";
    $buffer_rss.="    <link href=\"../".$dir_cache.str_replace(" ","_",$name).".html\" />\n";
    $buffer_rss.="    <id>../".$dir_cache.str_replace(" ","_",$name).".html</id>\n";
    $buffer_rss.="    <published>".date(DATE_ATOM,time())."</published>\n";
    $buffer_rss.="    <updated>".date(DATE_ATOM,time())."</updated>\n";
    $buffer_rss.="    <summary>".$name."</summary>\n";
    $buffer_rss.="    <link rel=\"enclosure\" href=\"../".$dir_thumbs.$name."/".$file."\" type=\"image/jpg\" />\n";
    $buffer_rss.="  </entry>\n";
  }
    
  $buffer_rss.="</feed>\n";

  // Write RSS file
  debug("Updating rss file: ".$file_rss);
  file_put_contents($file_rss,$buffer_rss);

  /**
   * Finalize
   **/
  debug("Done...");

  print("</code>");
  print("<a href=\".\">Next...</a>");
  
?>