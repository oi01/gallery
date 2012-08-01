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
    // Show header
    print("<h1>Installing...</h1>\n");

    /**
     * Create further directories
     **/
    // Create index directory
    $i=mkdir($conf["dir_cache"],0755);
    resultExe($i,"Creating index directory: ".$conf["dir_cache"]);

    // Create tag directory
    $i=mkdir($conf["dir_tags"],0755);
    resultExe($i,"Creating tags directory: ".$conf["dir_tags"]);

    // Create thumbs directory
    $i=mkdir($conf["dir_thumbs"],0755);
    resultExe($i,"Creating thumbs directory: ".$conf["dir_thumbs"]);

    /**
     * Create static .htaccess
     **/
    // Set static directories
    $dir_tools=dirname($_SERVER["SCRIPT_FILENAME"])."/tools/";
    $dir_etc=dirname($_SERVER["SCRIPT_FILENAME"])."/etc/";

     // Set filenames
    $file_htpasswd=$dir_etc.".htpasswd";

    // Create .htaccess in /tools and /etc
    $buffer="AuthType Basic\n";
    $buffer.="AuthName \"Access request\"\n";   
    $buffer.="AuthUserFile ".$file_htpasswd."\n";
    $buffer.="require valid-user\n";

    // Save files

    // Secure /tools
    $file_htaccess=$dir_tools.".htaccess";
    $i=file_put_contents($file_htaccess,$buffer);
    resultExe(($i>0),"Creating .htaccess: ".$file_htaccess);

    // Secure /etc
    $file_htaccess=$dir_etc.".htaccess";
    $i=file_put_contents($file_htaccess,$buffer);
    resultExe(($i>0),"Creating .htaccess: ".$file_htaccess);

    // Secure /upload
    $file_htaccess=$conf["dir_upload"].".htaccess";
    $i=file_put_contents($file_htaccess,$buffer);
    resultExe(($i>0),"Creating .htaccess: ".$file_htaccess);

    /**
     * Create .htpasswd
     **/
    // Add user and encrypt password
    $buffer=$_POST["user"].":".crypt($_POST["pass"], base64_encode($_POST["pass"]));

    // Secure path /etc
    $out_htpasswd=$dir_etc.".htpasswd";
    $i=file_put_contents($out_htpasswd,$buffer);
    resultExe(($i>0),"Creating .htpasswd: ".$out_htpasswd);

    /**
     * Remove installation files
     **/
    $i=unlink($conf["file_template"]);
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
  $out=file_get_contents($conf["file_template"]);
  $out=str_replace("%version%",$version_string,$out);
  print($out);

?>