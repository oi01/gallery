<?

  /************************************************************************************************
   * oi01 - Gallery
   *
   * Description:
   * - Installing gallery
   ************************************************************************************************/

  /**
   * Load configuration
   **/
   include_once("conf.php");

  /**
   * Check for a POST
   **/
  $state=1;
  if (isset($_POST["user"]) && isset($_POST["pass"]))
  {
    /**
     * Create .htaccess
     **/
    print("<code>\n");
    print("Apply access...<br />\n");

    // Create tool directory
    $dir_tools=dirname($_SERVER["SCRIPT_FILENAME"])."/tools/";

    // Create .htaccess
    print("- Create .htaccess: ".$dir_tools.".htaccess<br />\n");

    $buffer="AuthType Basic\n";
    $buffer.="AuthName \"Access request\"\n";   
    $buffer.="AuthUserFile ".$dir_tools.".htpasswd\n";
    $buffer.="require valid-user\n";

    // Save file
    $i=file_put_contents($dir_tools.".htaccess",$buffer);
    if ($i===0) $state=0;

    /**
     * Create .htpasswd
     **/
    print("- Create .htpasswd: ".$dir_tools.".htpasswd<br />\n");

    // Add user and encrypt password
    $buffer=$_POST["user"].":".crypt($_POST["pass"], base64_encode($_POST["pass"]));

    // Save file
    $i=file_put_contents($dir_tools.".htpasswd",$buffer);
    if ($i===0) $state=0;

    /**
     * Create further directories
     **/
    // Create index directory
    print("Creating index directory");
    mkdir($dir_cache,0755);

    // Create tag directory
    print("Creating tags directory");
    mkdir($dir_tags,0755);

    // Create thumbs directory
    print("Creating thumbs directory");
    mkdir($dir_thumbs,0755);

    // Create rss directory
    print("Creating rss directory");
    mkdir($dir_rss,0755);

    /**
     * Remove installation files
     **/
    print("- Removing: ".$file_template."<br />\n");
    unlink($file_template);

    print("- Removing: ".$_SERVER["SCRIPT_FILENAME"]."<br />\n");
    unlink($_SERVER["SCRIPT_FILENAME"]);

    // Finish
    print("Done...<br />\n<br />\n");
    print("</code>\n");
    print("<a href=\".\">Next...</a>\n");
    exit();
  }

  /**
   * Show setup formular
   **/
  print(file_get_contents($file_template));

?>