<?

  /**
   * Static variables
   **/
  $file_conf_global="global.conf";
  
  /**
   * User-defined variables
   **/
  $dir_pics="pics/";
  $dir_thumbs="thumbs/";
  $dir_cache="index/";
  $dir_tags="tags/";
  $file_cache="cache.tpl";
  $file_index=$dir_cache."index.html";
  $file_index_tpl="index.tpl";
  $file_info="info.txt";
  $file_conf_gallery="gallery.conf";
  $file_install="install.php";
  $file_template="install.tpl";
  $file_reset="tools/reset.php";
  $file_rss="rss/rss.xml";
  $dir_rss=dirname($file_rss);
  $table_col=4;
  $thumb_width=160;
  $thumb_height=120;
  $is_debug=true;
  $is_tags=true;
  $text_title="oi01 - Gallery";
  $text_home="Home";
  
  // Load optional global configuration file
  if (file_exists($file_conf_global))
  {
    $conf=parse_ini_file($file_conf_global);
	
	if (array_key_exists("dir_pics",$conf)) $dir_pics=$conf["dir_pics"];
	if (array_key_exists("dir_thumbs",$conf)) $dir_thumbs=$conf["dir_thumbs"];
	if (array_key_exists("dir_cache",$conf)) $dir_cache=$conf["dir_cache"];
	if (array_key_exists("dir_tags",$conf)) $dir_tags=$conf["dir_tags"];
	if (array_key_exists("file_cache",$conf)) $file_cache=$conf["file_cache"];
	if (array_key_exists("file_index",$conf)) $file_index=$conf["file_index"];
	if (array_key_exists("file_conf_gallery",$conf)) $file_conf_gallery=$conf["file_conf_gallery"];
	if (array_key_exists("file_install",$conf)) $file_install=$conf["file_install"];
	if (array_key_exists("file_reset",$conf)) $file_reset=$conf["file_reset"];
	if (array_key_exists("table_col",$conf)) $table_col=$conf["table_col"];
	if (array_key_exists("thumb_width",$conf)) $thumb_width=$conf["thumb_width"];
	if (array_key_exists("thumb_height",$conf)) $thumb_height=$conf["thumb_height"];
	if (array_key_exists("is_debug",$conf)) $is_debug=$conf["is_debug"];
	if (array_key_exists("is_tags",$conf)) $is_tags=$conf["is_tags"];
	if (array_key_exists("text_title",$conf)) $text_title=$conf["text_title"];
	if (array_key_exists("text_home",$conf)) $text_home=$conf["text_home"];
  }

?>