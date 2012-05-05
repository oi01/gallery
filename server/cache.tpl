<html>
	<head>
	    <title>oi01.de - Gallery</title>
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
		<h1><a href="../">%home%</a> &gt; %title%</h1>
	    %pics%
		<p class="footer"><a class="footer" href="http://www.oi01.de/gallery" target="_blank">oi01-Gallery</a></p>
	</body>
</html>
