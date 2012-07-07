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
    //print(file_get_contents($conf["file_index"]));
	header("Location: ".$conf["dir_cache"]);
	exit();
  }
  
  // Check if software needs to be installed
  if (file_exists($conf["file_install"]))
  {
	header("Location: ".$conf["file_install"]);
	exit();
  }
  
  /**
   * Debug messages
   **/
  function debug($text)
  {
    global $conf;
    if ($conf["is_debug"]) print("<!-- ".$text." -->");
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
  }

  /**
   * Create cache from name
   **/
  function createCache($dir_pics,$dir_thumbs,$dir_cache,$dir,$name,$write)
  {
  	global $conf;
  	 
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
        $w=$conf["thumb_width"];
        $h=$conf["thumb_height"];

        // Check orientation
        if (imagesy($im)>imagesx($im))
        {
          $w=$h*$conf["thumb_height"] / $conf["thumb_width"];
        }

		// Resize image				
		$im_thumb=imagecreatetruecolor($w,$h);
        imagecopyresampled($im_thumb,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

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
	global $conf;
	
	/**
     * Read description
	 **/
    debug("Reading info file: ".$conf["dir_pics"].$dir."/".$conf["file_info"]);
	if (file_exists($conf["dir_pics"].$dir."/".$conf["file_info"]))
	{  
	  // Read file
	  $info=file($conf["dir_pics"].$dir."/".$conf["file_info"]);
	  $info=trim(join("<br />",$info));
	}
	
	/**
     * Read gallery config
	 **/
    global $arr_tags;  

	debug("Reading config file: ".$conf["dir_pics"].$dir."/".$conf["file_conf_gallery"]);
	if (file_exists($conf["dir_pics"].$dir."/".$conf["file_conf_gallery"]))
	{
	  // Read file
	  $var=parse_ini_file($conf["dir_pics"].$dir."/".$conf["file_conf_gallery"]);
	  
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
	  if ($conf["is_tags"] && array_key_exists("tags",$var))
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
	  $buffer_pics="<div class=\"info\">\n";
	  $buffer_pics.=$info;
	  $buffer_pics.="</div>\n";
	  $buffer_pics.="<br />\n";
	}
	
    // Generate image codes
	natsort($arr_pics);
	foreach($arr_pics as $pic)
	{
		$buffer_pics.="    <a class=\"oi01-gallery\" href=\"../".$conf["dir_pics"].$dir."/".$pic."\" title=\"".$pic."\"><img src=\"../".$conf["dir_thumbs"].$dir."/".$pic."\" /></a>\n";
	}
	
    // Create template
    $buffer_file=file_get_contents($conf["file_cache"]);
	$buffer_file=str_replace("%pics%",$buffer_pics,$buffer_file);
	$buffer_file=str_replace("%title%",$dir,$buffer_file);
	$buffer_file=str_replace("%home%",$conf["text_home"],$buffer_file);
	
    // Save file
    $file=$dir_cache.$name;
	if ($write) file_put_contents($file,$buffer_file);

    // Show link to hidden gallery
    if ($var_hidden)
    {
      global $icon_link;
      print("<p><img src=\"".$icon_link."\"> Link to hidden gallery: <a href=\"".$file."\" target=\"_blank\">".$dir."</a></p>\n");
    }
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

  // Sort tags
  uksort($arr_tags, 'strnatcmp');
  
  // Parse tag list
  foreach($arr_tags as $cur_tag => $cur_tagdirs)
  {
    // Reset counter
    $counter=0;
    $page=0;
    $max_images=$conf["table_row"] * $conf["table_col"];
    $max_pages=ceil(sizeof($cur_tagdirs) / $max_images);

    // Sort galleries
    usort($cur_tagdirs, 'strnatcmp');
    $cur_tagdirs=array_reverse($cur_tagdirs);

    // Prepare pages
    while($counter < sizeof($cur_tagdirs))
    {
      // Reset buffer
	  $buffer_index="";
	  
	  // Show tags only if other tags exist
	  if (sizeof($arr_tags)>1)
	  {
		$buffer_index.="<h2>";
		
		foreach($arr_tags as $tag => $tag_dirs)
		{
		  $tag_name="#".$tag;
		  $tag_link=$conf["dir_tags"].$tag.".html";
		  
		  if ($tag == "*")
		  {
		    $tag_name="*";
		    $tag_link="";
		  }
		
		  // Add next tag
		  $buffer_index.=" <a href=\"../".$tag_link."\"";
		
		  // Emphasize current tag
		  if ($tag == $cur_tag) $buffer_index.=" class=\"emph\"";
		  $buffer_index.=">".$tag_name."</a>";
		}
		
		$buffer_index.="</h2>\n";
	  }
	  
	  // Show galleries
	  $buffer_index.="<table align=\"center\">\n";
	  
	  $n=0;
	  for ($i=$counter;$i<sizeof($cur_tagdirs) && $n<$max_images;$i++)
	  {
        // Get name of the gallery
        $name=$cur_tagdirs[$i];

        // Get index image
        $file=$arr_topics[$name];

		// Show gallery
		if ($n % $conf["table_col"] == 0)
		{
		  $buffer_index.="  <tr>\n";
		}

		$buffer_index.="   <td align=\"center\">\n";
		$buffer_index.="     <a href=\"../".$conf["dir_cache"].str_replace(" ","_",$name).".html\"><img src=\"../".$conf["dir_thumbs"].$name."/".$file."\"/><br />".$name." (".$arr_count[$name].")</a>\n";
		$buffer_index.="   </td>\n";

		if ($n % $conf["table_col"] == $conf["table_col"]-1)
		{
		  $buffer_index.="  </tr>\n";
		}
		
        // Increase counters
		$n++;
        $counter++;
	  }

	  // Pad
	  $n=4 - $n % $conf["table_col"];
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

      // Generate names
      $ext_page_cur="";
      $ext_page_prev="";
      $ext_page_next="_".($page+1);

      if ($page>0)
      {
        $ext_page_cur="_".$page;
        $ext_page_prev="_".($page-1);

        if ($page==1) $ext_page_prev="";
      }

	  $file_out=$conf["dir_cache"]."index".$ext_page_cur.".html";
	  if ($cur_tag!="*")
      {
        $file_out=$conf["dir_tags"].$cur_tag.$ext_page_cur.".html";
      }

      $file_prev=$conf["dir_cache"]."index".$ext_page_prev.".html";
      $file_next=$conf["dir_cache"]."index".$ext_page_next.".html";
      if ($page>1)
      {
        $file_prev=$conf["dir_tags"].$cur_tag.$ext_prev.".html";
      }

      // Add prev/next buttons
      if ($max_pages>0)
      {
        $buffer_index.="<table align=\"center\">";
        $buffer_index.="  <tr>";

        if ($page>0)
        {
          $buffer_index.="    <td style=\"text-align:left;\"><a href=\"../".$file_prev."\"><img class=\"step\" src=\"../".$icon_prev."\"></a></td>";
        }

        if ($page<($max_pages-1))
        {
          $buffer_index.="    <td style=\"text-align:right;\"><a href=\"../".$file_next."\"><img class=\"step\" src=\"../".$icon_next."\"></a></td>";
        }

        $buffer_index.="  </tr>\n";
        $buffer_index.="</table>\n";
      }

	  // Prepare content
	  $buffer_file=file_get_contents($conf["file_index_tpl"]);
      $buffer_file=str_replace("%index%",$buffer_index,$buffer_file);
	  $buffer_file=str_replace("%title%",$conf["text_title"],$buffer_file);
	  $buffer_file=str_replace("%home%",$conf["text_home"],$buffer_file);
      $buffer_file=str_replace("%version%",$version_string,$buffer_file);

	  // Save file
      $i=file_put_contents($file_out,$buffer_file);
      resultExe(($i>0),"Updating index file: ".$file_out);

      // Increase counter
      $page++;
    }
  }

  // Log
  print("<p><a href=\".\">Next...</a></p>");

?>