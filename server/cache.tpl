<html>
	<head>
	    <title>oi01 - Gallery</title>
	    <link rel="stylesheet" type="text/css" href="../style.css">
		<link rel="stylesheet" type="text/css" href="../shadowbox/shadowbox.css">
		<script type="text/javascript" src="../shadowbox/shadowbox.js"></script>
		<script type="text/javascript">
			function setup()
			{
				Shadowbox.setup("a.oi01-gallery", {
					gallery:        "oi01",
					continuous:     true,
					counterType:    "skip"
				});
			}
			
			Shadowbox.init({},setup);
		</script>
	</head>
	<body>
		<h1><a href="../index/">%home%</a> &gt; %title%</h1>
		
	    %pics%
		
		<br />
        <p class="footer">
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" title="These images are licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License" /></a><br />
		<br />
		<a class="footer" href="http://www.oi01.de/gallery" target="_blank" title="%version%">oi01-Gallery</a>
		</p>
	</body>
</html>
