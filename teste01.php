<form action="teste.php">
<input type="image" name="teste" src="teste.jpg">
</form>
<img src="out.jpg">
<pre>
<?php
var_dump($_GET);
?>
</pre>
<?php
#if($_GET['teste_x'] && $_GET['teste_y'] ){
	$png = imagecreatefrompng('./x.png');
	$jpeg = imagecreatefromjpeg('./teste.jpg');
	list($width, $height) = getimagesize('./teste.jpg');
	list($newwidth, $newheight) = getimagesize('./x.png');
	echo $newwidth." ".$newheight; die();
	$out = imagecreatetruecolor($newwidth, $newheight);
	imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	imagecopyresampled($out, $png, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
	imagejpeg($out, 'out.jpg', 100);
#}
?>
