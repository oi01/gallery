<?

  /************************************************************************************************
   * oi01 - Gallery
   *
   * Description:
   * - Installing gallery
   ************************************************************************************************/

  /**
   * Load configuration
   */
  include_once("common.php");

  /**
   * Check for a POST
   **/
  if (isset($_POST["user"]) && isset($_POST["pass"]))
  {
    /**
     * Create .htaccess
     **/
    // Create tool directory
    $dir_tools=dirname($_SERVER["SCRIPT_FILENAME"])."/tools/";

    // Create .htaccess
    $file_htaccess=$dir_tools.".htaccess";

    $buffer="AuthType Basic\n";
    $buffer.="AuthName \"Access request\"\n";   
    $buffer.="AuthUserFile ".$file_htaccess."\n";
    $buffer.="require valid-user\n";

    // Save file
    print("<h1>Installing...</h1>\n");
    $i=file_put_contents($file_htaccess,$buffer);
    resultExe(($i>0),"Creating .htaccess: ".$file_htaccess);


    /**
     * Create .htpasswd
     **/
    $file_htpasswd=$dir_tools.".htpasswd";

    // Add user and encrypt password
    $buffer=$_POST["user"].":".crypt($_POST["pass"], base64_encode($_POST["pass"]));

    // Save file
    $i=file_put_contents($file_htpasswd,$buffer);
    resultExe(($i>0),"Creating .htpasswd: ".$file_htpasswd);

    /**
     * Create further directories
     **/
    // Create index directory
    $i=mkdir($dir_cache,0755);
    resultExe($i,"Creating index directory: ".$conf["dir_cache"]);

    // Create tag directory
    $i=mkdir($dir_tags,0755);
    resultExe($i,"Creating tags directory: ".$conf["dir_tags"]);

    // Create thumbs directory
    $i=mkdir($dir_thumbs,0755);
    resultExe($i,"Creating thumbs directory: ".$conf["dir_thumbs"]);

    /**
     * Remove installation files
     **/
    $i=unlink($file_template);
    resultExe($i,"Removing install template: ".$conf["file_template"]);

    $i=unlink($_SERVER["SCRIPT_FILENAME"]);
    resultExe($i,"Removing install script: ".$_SERVER["SCRIPT_FILENAME"]);

    // Finish
    print("<p><a href=\".\">Next...</a></p>\n");
    exit();
  }

  /**
   * Show setup formular
   **/
  print(file_get_contents($file_template));

?>