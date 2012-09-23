<?

  /************************************************************************************************
   * oi01 - Gallery
   *
   * Description:
   * - Common functions
   ************************************************************************************************/

  /**
   * Return the given step and its result as image in HTML
   *
   * @param unknown_type $res
   * @param unknown_type $text
   */
  function resultExe($res,$text,$dir="")
  {
    global $icon_ok;
    global $icon_fail;
    global $isExeInfo;

    $icon = ($res) ? $icon_ok : $icon_fail;
    if ($isExeInfo===true)
    {
      print("<p><img src=\"".$dir.$icon."\" /> ".$text."</p>\n");
    }
  }

  /**
   * Generate an index depending on the tag list
   **/
  function genIndex($arr_tags)
  {
    // Define global variables
    global $conf;
    global $arr_topics;
    global $arr_count;
    global $version_string;

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
      
      // Check for sorting
      if ($conf["sort_index"]=="reverse")
      {
	    $cur_tagdirs=array_reverse($cur_tagdirs);
      }

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
     		    $tag_link=$conf["dir_cache"];
     		  }
   
     		  // Add next tag
            if ($tag != $cur_tag)
            {
     		    $buffer_index.=" <a href=\"../".$tag_link."\">".$tag_name."</a>";
            }
            else
            {
     		    // Emphasize current tag
     		    $buffer_index.=" <span class=\"emph\">".$tag_name."</span>";
            }
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
   
        // Generate filenames
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
  }

  /**
   * Create thumbnail from image
   **/
  function createThumb($dir_pics,$dir_thumbs,$dir,$file,$write)
  {
    // Set variables
    global $conf;

    // Check for a valid image
	$im=@imagecreatefromjpeg($dir_pics.$dir."/".$file);
    
	// Validate image
	if (!$im)
	{
      return false;
	}
    
    // Check if thumb shall be written
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
    
    return true;
  }

  /**
   * Create cache from name
   *
   * Returns true when directory is hidden otherwise false
   **/
  function createCache($dir_pics,$dir_thumbs,$dir_cache,$dir,$name,$write)
  {
    // Set variables
  	global $conf;
    global $arr_tags;
	global $arr_topics;
    global $arr_count;

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

    $latestpic_file=null;
    $latestpic_time=0;

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
      $valid=createThumb($dir_pics,$dir_thumbs,$dir,$file,$write);

      // Flag as valid image
      if ($valid)
      {
        // Add to valid images
        debug("- Status: valid");
	    array_push($arr_pics,$file);

        // Check date
        $filetime = filemtime($dir_pics.$dir."/".$file);
        if ($filetime>$latestpic_time)
        {
          // Set new latest pic
          $latestpic_file=$file;
          $latestpic_time=$filetime;
        }
      }
      else
      {
        debug("- Status: invalid");
      }
	}

	/**
	 * Save an image for the index
	 **/
	$var_hidden=false;
	$var_indexpic="";
    $var_reverse=false;
	$var_tags=array();

	$info="";

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
        if ($var["index"]==="%latest%")
        {
          $var_indexpic=$latestpic_file;
        }
        else
        {
	      $var_indexpic=$var["index"];
        }
	    debug("#index: ".$var_indexpic);
	  }

	  // Reverse sort
	  if (array_key_exists("reverse",$var))
	  {
	    debug("#reverse: ".$var["reverse"]);
	    $var_reverse=$var["reverse"];
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
    if ($var_reverse)
    {
      // Reverse array
      $arr_pics=array_reverse($arr_pics,true);
    }

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

    return $var_hidden;
  }

  /**
   * Generate new picture filename
   */
  function getFilenameDate($filename)
  {
    // Set variables
    global $date_file;

    // Check if date is available
    if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/",$filename)==1)
    {
      return $filename;
    }

    // Return filename with current date
    return $date_file."_".$filename;
  }

  /**
   * Debug messages
   **/
  function debug($text)
  {
    global $conf;
    if ($conf["is_debug"]) print("<!-- ".$text." -->\n");
  }

  /**
   * Parse directory (no recursion)
   *
   * @param dir The path to the directory
   * @param arr_files A reference to the array where the files shall be stored
   * @param arr_dirs A reference to the array where the directories shall be stored
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
   * Generate the IOTD
   **/
  function genIotd()
  {
    // Set variables
    global $conf;
    global $date_file;
    global $date_show;
    global $version_string;

	// Parse files
	$arr_dirs=array();
	$arr_files=array();
	$arr_pics=array();
	$arr_ext=array();
	$file_iotd="";

    // Check if image with same date already exists in IOTD directory
	$conf["is_debug"]=false;
	parse_dir($conf["dir_pics"]."Image Of The Day/",$arr_files,$arr_dirs);
	$conf["is_debug"]=true;

	// Check for the daily image
	foreach($arr_files as $file)
	{
	  // Check for file with current date
	  if (substr($file,0,10)==$date_file)
	  {
        // Select file as IOTD file
        $file_iotd=$file;
      }
    }

    // Check if IOTD does not exist for today
    if ($file_iotd=="")
    {
      // Init extensions
      array_push($arr_ext,".jpg");
      array_push($arr_ext,".png");
      array_push($arr_ext,".gif");
      array_push($arr_ext,".svg");
  
      // Parse upload directory
      $conf["is_debug"]=false;
      $arr_dirs=array();
      $arr_files=array();
      parse_dir($conf["dir_upload"],$arr_files,$arr_dirs);
      $conf["is_debug"]=true;
  
      // Check for the daily image
      foreach($arr_files as $file)
      {
        // Check for file with current date
        if (substr($file,0,10)==$date_file)
        {
          // Move file from upload to gallery
          $file_iotd=getFilenameDate($file);
          rename(dirname($_SERVER["SCRIPT_FILENAME"])."/".$conf["dir_upload"].$file,dirname($_SERVER["SCRIPT_FILENAME"])."/".$conf["dir_ImageOfTheDay"].$file_iotd);
  
         // Stop searching
         break;
       }
  
       // Add image to array if file extension matches
       foreach($arr_ext as $extension)
       {
          if (strtolower(substr($file,-strlen($extension))) == $extension)
          {
            array_push($arr_pics,$file);
            break;
          }
        }
      }
  
      // None found, select image randomly
      if ($file_iotd=="" && count($arr_pics)>0)
      {
        $r=rand(0,count($arr_pics)-1);
        $file=$arr_pics[$r];
  
        // Move file from upload to gallery
        $file_iotd=getFilenameDate($file);
        rename(dirname($_SERVER["SCRIPT_FILENAME"])."/".$conf["dir_upload"].$file,dirname($_SERVER["SCRIPT_FILENAME"])."/".$conf["dir_ImageOfTheDay"].$file_iotd);
      }
  
      // Check if still no image wants to be IOTD
      if ($file_iotd=="")
      {
        // Continue to gallery index
        header("Location: ".$conf["dir_cache"]);
        exit();
      }
    }

	// Show info if available
	$info="";
	if (file_exists($conf["dir_ImageOfTheDay"]."/".$conf["file_info"]))
	{
	  // Read file
	  $info=file($conf["dir_ImageOfTheDay"]."/".$conf["file_info"]);
	  $info=trim(join("<br />",$info));
	  $info="<p style=\"text-align:center;\">".$info."</p>";
	}

	// Show image
	$buffer_file=file_get_contents($conf["file_iotd_tpl"]);
	$buffer_file=str_replace("%title%",$conf["text_title"],$buffer_file);
    $buffer_file=str_replace("%version%",$version_string,$buffer_file);
	$buffer_file=str_replace("%date%",$date_show,$buffer_file);
	$buffer_file=str_replace("%info%",$info,$buffer_file);
	$buffer_file=str_replace("%archive%","../".$conf["dir_cache"]."Image_Of_The_Day.html",$buffer_file);
	$buffer_file=str_replace("%gallery%","../".$conf["dir_cache"],$buffer_file);
	$buffer_file=str_replace("%iotd%","../".$conf["dir_ImageOfTheDay"].$file_iotd,$buffer_file);

	// Save file
    $i=file_put_contents($conf["file_ImageOfTheDay"],$buffer_file);

	// Update IOTD gallery
    // Build path to cache file
	
    // $dir="Image Of The Day";
	// $name=str_replace(" ","_",$dir);
	// $name.=".html";

    // Create cache for IOTD only
    //createCache($conf["dir_pics"],$conf["dir_thumbs"],$conf["dir_cache"],$dir,$name,true);

    // Remove IOTD index file
    //unlink($conf["dir_cache"].$name);
  }

  /**
   * Parse all directories and get an overview of the numbers of the images in each gallery
   **/
  function getOverview()
  {
    // Declare variables
    global $conf;

    // Pre-define variables
    $arr_files=array();
    $arr_dirs=array();

    // Parse root
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
  }

?>
