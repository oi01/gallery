<html>
	<head>
		<title>oi01 - Gallery</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
	    <h1>oi01 - Gallery: Setup</h1>
		
		<form action="install.php" method="post">
			<table width="100%">
				<tr>
					<td class="title" colspan="2">Reset</td>
				</tr>
				<tr>
					<td class="desc" colspan="2">Before a reset of the gallery is executed the initiator will be asked for a user and a password.</td>
				</tr>
				<tr>
					<td width="25%">User:</td>
					<td width="75%"><input type="text" name="user" maxlength="28" /></td>
				</tr>
				<tr>
					<td width="25%">Password:</td>
					<td width="75%"><input type="password" name="pass" maxlength="28" /></td>
				</tr>
				<tr>
					<td width="25%">&nbsp;</td>
					<td width="75%"><input type="submit" value="OK" /></td>
				</tr>
			</table>
		</form>
	</body>
</html>