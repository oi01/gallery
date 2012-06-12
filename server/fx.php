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

    $icon = ($res) ? $icon_ok : $icon_fail;
    print("<p><img src=\"".$dir.$icon."\" /> ".$text."</p>\n");
  }
  
?>
