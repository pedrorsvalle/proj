<style>
input[type='number']{
    width: 50px;
} 
</style>

<pre>
<?php //var_dump($_POST); ?>
</pre>


<?php 
if(isset($_POST['Salvar']))
	if( $_POST['Salvar'] == 'Salvar' )
	{

		if( $_POST['nome_fonte'] == "" ) $_POST['nome_fonte'] = 0;
		if( $_POST['nomex'] == "" ) $_POST['nomex'] = 0;
		if( $_POST['nomey'] == "" ) $_POST['nomey'] = 0;
		if( $_POST['data_fonte'] == "" ) $_POST['data_fonte'] = 0;
		if( $_POST['datax'] == "" ) $_POST['datax'] = 0;
		if( $_POST['datay'] == "" ) $_POST['datay'] = 0;
		if( $_POST['curso_fonte'] == "" ) $_POST['curso_fonte'] = 0;
		if( $_POST['cursox'] == "" ) $_POST['cursox'] = 0;
		if( $_POST['cursoy'] == "" ) $_POST['cursoy'] = 0;
		if( $_POST['palestra_fonte'] == "" ) $_POST['palestra_fonte'] = 0;
		if( $_POST['palestrax'] == "" ) $_POST['palestrax'] = 0;
		if( $_POST['palestray'] == "" ) $_POST['palestray'] = 0;
		if( $_POST['palestrante_fonte'] == "" ) $_POST['palestrante_fonte'] = 0;
		if( $_POST['palestrantex'] == "" ) $_POST['palestrantex'] = 0;
		if( $_POST['palestrantey'] == "" ) $_POST['palestrantey'] = 0;
		if( $_POST['instituicao_fonte'] == "" ) $_POST['instituicao_fonte'] = 0;
		if( $_POST['instituicaox'] == "" ) $_POST['instituicaox'] = 0;
		if( $_POST['instituicaoy'] == "" ) $_POST['instituicaoy'] = 0;

		require_once('mysql.php');
			
		$sql = " UPDATE certificado SET
			nome_fonte        = {$_POST['nome_fonte']}        , nomex        = {$_POST['nomex']}, nomey = {$_POST['nomey']}, 
			data_fonte        = {$_POST['data_fonte']}        , datax        = {$_POST['datax']}, datay = {$_POST['datay']}, 
			curso_fonte       = {$_POST['curso_fonte']}       , cursox       = {$_POST['cursox']},cursoy= {$_POST['cursoy']}, 
			palestra_fonte    = {$_POST['palestra_fonte']}    , palestrax    = {$_POST['palestrax']}, palestray = {$_POST['palestray']} ,
			palestrante_fonte = {$_POST['palestrante_fonte']} , palestrantex = {$_POST['palestrantex']}, palestrantey = {$_POST['palestrantey']}, 
			instituicao_fonte = {$_POST['instituicao_fonte']} , instituicaox = {$_POST['instituicaox']}, instituicaoy = {$_POST['instituicaoy']} 
			WHERE id = {$_GET['certificado']} ";

		if ($conn->query($sql) === TRUE) 
		{
			include("header_msg.html");
			echo "
				<div class='h-100 d-flex align-items-center justify-content-center'>
					<div style='background:green'>
				
						<div class='alert alert-success' role='alert'>
							Dado Atualizado com sucesso, clique <a href='index.php'>aqui</a> para continuar. 
						</div>
			
					</div>
				</div>      ";
			include("footer_msg.html");
			die();

		} else {
			echo "Error updating record: " . $conn->error;
			die();
		}
		
	}

if(isset($_GET['ler']))
if( $_GET['ler'] == 1 ) {
	if( $_GET['certificado'] ) {
		require_once('mysql.php');
		$sql = "SELECT * FROM certificado where id = {$_GET['certificado']} ";

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
	
		$_POST['arquivo'] = $row['arquivo'];
		
		$_POST['nome_fonte'] = $row['nome_fonte'];
		$_POST['nomex'] = $row['nomex'];
		$_POST['nomey'] = $row['nomey'];

		$_POST['data_fonte'] = $row['data_fonte'];
		$_POST['datax'] = $row['datax'];
		$_POST['datay'] = $row['datay'];

		$_POST['curso_fonte'] = $row['curso_fonte'];
		$_POST['cursox'] = $row['cursox'];
		$_POST['cursoy'] = $row['cursoy'];

		$_POST['palestra_fonte'] = $row['palestra_fonte'];
		$_POST['palestrax'] = $row['palestrax'];
		$_POST['palestray'] = $row['palestray'];
		
		$_POST['palestrante_fonte'] = $row['palestrante_fonte'];
		$_POST['palestrantex'] = $row['palestrantex'];
		$_POST['palestrantey'] = $row['palestrantey'];

		$_POST['instituicao_fonte'] = $row['instituicao_fonte'];
		$_POST['instituicaox'] = $row['instituicaox'];
		$_POST['instituicaoy'] = $row['instituicaoy'];		
	}
}



if (isset($_POST['texto'])) {
    if ($_POST['texto'] == 'nome') {
        $nome_ck = "checked";
    }
    if ($_POST['texto'] == 'data') {
        $data_ck = "checked";
    }
    if ($_POST['texto'] == 'curso') {
        $curso_ck = "checked";
    }
    if ($_POST['texto'] == 'palestra') {
        $palestra_ck = "checked";
    }
    if ($_POST['texto'] == 'palestrante') {
        $palestrante_ck = "checked";
    }
    if ($_POST['texto'] == 'instituicao') {
        $instituicao_ck = "checked";
    }

    if ($_POST['texto'] == 'nome') {
        $nomex = $_POST['x'];
        $nomey = $_POST['y'];
        $_POST['nomex'] = $nomex;
        $_POST['nomey'] = $nomey;
    }

    if ($_POST['texto'] == 'data') {
        $datax = $_POST['x'];
        $datay = $_POST['y'];
        $_POST['datax'] = $datax;
        $_POST['datay'] = $datay;
    }

    if ($_POST['texto'] == 'curso') {
        $cursox = $_POST['x'];
        $cursoy = $_POST['y'];
        $_POST['cursox'] = $cursox;
        $_POST['cursoy'] = $cursoy;
    }

    if ($_POST['texto'] == 'palestra') {
        $palestrax = $_POST['x'];
        $palestray = $_POST['y'];
        $_POST['palestrax'] = $palestrax;
        $_POST['palestray'] = $palestray;
    }

    if ($_POST['texto'] == 'palestrante') {
        $palestrantex = $_POST['x'];
        $palestrantey = $_POST['y'];
        $_POST['palestrantex'] = $palestrantex;
        $_POST['palestrantey'] = $palestrantey;
    }

    if ($_POST['texto'] == 'instituicao') {
        $instituicaox = $_POST['x'];
        $instituicaoy = $_POST['y'];
        $_POST['instituicaox'] = $instituicaox;
        $_POST['instituicaoy'] = $instituicaoy;
    }


}
?>

<form action="certificado.php?certificado=<?php echo $_GET['certificado'];?>" method="post">

	<input type="radio" name="texto" id="nome" value="nome" <?php echo $nome_ck ?? ''; ?>>
	<label for="nome">Nome</label> <input type="number" name="nome_fonte" size="4" value="<?php echo $_POST['nome_fonte']; ?>"><br>

	<input type="radio" name="texto" id="data" value="data" <?php echo $data_ck ?? ''; ?>>
	<label for="data">Data</label><input type="number" name="data_fonte" size="4" value="<?php echo $_POST['data_fonte']; ?>"> <br>

	<input type="radio" name="texto" id="curso" value="curso" <?php echo $curso_ck ?? ''; ?>>
	<label for="curso">Curso</label><input type="number" name="curso_fonte" size="4" value="<?php echo $_POST['curso_fonte']; ?>"><br>

	<input type="radio" name="texto" id="palestra" value="palestra" <?php echo $palestra_ck ?? ''; ?>>
	<label for="palestra">Palestra</label><input type="number" name="palestra_fonte" size="4" value="<?php echo $_POST['palestra_fonte']; ?>"><br>

	<input type="radio" name="texto" id="palestrante" value="palestrante" <?php echo $palestrante_ck ?? ''; ?>>
	<label for="palestrante">Palestrante</label><input type="number" name="palestrante_fonte" size="4" value="<?php echo $_POST['palestrante_fonte']; ?>"><br>

	<input type="radio" name="texto" id="instituicao" value="instituicao" <?php echo $instituicao_ck ?? ''; ?>>
	<label for="instituicao">Instituição</label><input type="number" name="instituicao_fonte" size="4" value="<?php echo $_POST['instituicao_fonte']; ?>"><br>

	<input type="submit" name="Salvar" value="Salvar"><br>


	<input type="image" src="out.jpg<?php echo"?=".time();?>">


	<input type="hidden" name="nomex" value="<?php echo $_POST['nomex'];?>">
	<input type="hidden" name="nomey" value="<?php echo $_POST['nomey'];?>">

	<input type="hidden" name="datax" value="<?php echo $_POST['datax'];?>">
	<input type="hidden" name="datay" value="<?php echo $_POST['datay'];?>">


	<input type="hidden" name="cursox" value="<?php echo $_POST['cursox'];?>">
	<input type="hidden" name="cursoy" value="<?php echo $_POST['cursoy'];?>">

	<input type="hidden" name="palestrax" value="<?php echo $_POST['palestrax'];?>">
	<input type="hidden" name="palestray" value="<?php echo $_POST['palestray'];?>">

	<input type="hidden" name="palestrantex" value="<?php echo $_POST['palestrantex'];?>">
	<input type="hidden" name="palestrantey" value="<?php echo $_POST['palestrantey'];?>">

	<input type="hidden" name="instituicaox" value="<?php echo $_POST['instituicaox'];?>">
	<input type="hidden" name="instituicaoy" value="<?php echo $_POST['instituicaoy'];?>">

	<input type="hidden" name="arquivo" value="<?php echo $_POST['arquivo'];?>">


</form>

<?php
//Set the Content Type
//header('Content-type: image/jpeg');
if(isset($_POST['x']))
	$x=$_POST['x'];
if(isset($_POST['y']))
	$y=$_POST['y'];
$size=40;

require_once('file.php');

// Create Image From Existing File
$jpg_image = imagecreatefromjpeg($filepath.$_POST['arquivo']);

// Allocate A Color For The Text
#$white = imagecolorallocate($jpg_image, 255, 255, 255);
$white = imagecolorallocate($jpg_image, 255, 50, 0);

// Set Path to Font File
#$font_path = './VelomiaVanora.ttf';
$font_path = $root_file_path.'VelomiaVanora.ttf';

// Set Text to Be Printed On Image
$text = "This is a sunset!";

// Print Text On Image
//imagettftext($jpg_image, $size, 0, 75, 300, $white, $font_path, $text);
//imagettftext($jpg_image, $size, 0, $x, $y, $white, $font_path, $text);

if($_POST['nome_fonte'] != '')
	imagettftext($jpg_image, $_POST['nome_fonte'], 0, $_POST['nomex'], $_POST['nomey'], $white, $font_path, "Texto de nome");
	
if($_POST['data_fonte'] != '')
	imagettftext($jpg_image, $_POST['data_fonte'], 0, $_POST['datax'], $_POST['datay'], $white, $font_path, "Texto de data");

if($_POST['curso_fonte'] != '')
	imagettftext($jpg_image, $_POST['curso_fonte'], 0, $_POST['cursox'], $_POST['cursoy'], $white, $font_path, "Texto do curso");

if($_POST['palestra_fonte'] != '')
	imagettftext($jpg_image, $_POST['palestra_fonte'], 0, $_POST['palestrax'], $_POST['palestray'], $white, $font_path, "Texto da palestra");

if($_POST['palestrante_fonte'] != '')
	imagettftext($jpg_image, $_POST['palestrante_fonte'], 0, $_POST['palestrantex'], $_POST['palestrantey'], $white, $font_path, "Texto da palestrante");

if($_POST['instituicao_fonte'] != '')
	imagettftext($jpg_image, $_POST['instituicao_fonte'], 0, $_POST['instituicaox'], $_POST['instituicaoy'], $white, $font_path, "Texto da instituição");


// Send Image to Browser
//imagejpeg($jpg_image);
imagejpeg($jpg_image, 'out.jpg');


// Clear Memory
imagedestroy($jpg_image);
?>
