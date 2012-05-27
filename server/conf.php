<?

  /**
   * Static variables
   **/
  $file_conf_global="etc/global.conf";

  /**
   * User-defined variables
   **/
  $conf["dir_pics"]="pics/";
  $conf["dir_thumbs"]="thumbs/";
  $conf["dir_cache"]="index/";
  $conf["dir_tags"]="tags/";
  $conf["dir_icons"]="silk/icons/";
  $conf["file_cache"]="cache.tpl";
  $conf["file_index"]=$conf["dir_cache"]."index.html";
  $conf["file_index_tpl"]="index.tpl";
  $conf["file_info"]="info.txt";
  $conf["file_conf_gallery"]="gallery.conf";
  $conf["file_install"]="install.php";
  $conf["file_template"]="install.tpl";
  $conf["file_reset"]="tools/reset.php";
  $conf["table_col"]=4;
  $conf["thumb_width"]=160;
  $conf["thumb_height"]=120;
  $conf["is_debug"]=true;
  $conf["is_tags"]=true;
  $conf["text_title"]="oi01 - Gallery";
  $conf["text_home"]="Home";

  // Load optional global configuration file
  if (file_exists($file_conf_global))
  {
    $res=parse_ini_file($file_conf_global);
    if ($res)
    {
      $conf=array_merge($conf,$res);
    }
  }

  // Dynamic files
  $icon_ok=$conf["dir_icons"]."accept.png";
  $icon_fail=$conf["dir_icons"]."cancel.png";
  $icon_link=$conf["dir_icons"]."link.png";

?>