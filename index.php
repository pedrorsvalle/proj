<?php

/*
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;

require_once './vendor/autoload.php';

*/

// biblioyteca qr code
include 'phpqrcode/qrlib.php';

session_start();


if( !isset($_SESSION['login']) || $_SESSION['login'] == "" ) {
	$login = "";
} else {
	$login = $_SESSION['login'];
}

//die($login.":");

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'sair' ){
	session_destroy();
	header("Location: index.php");
	die();
}



/* 
 * Parte do código que monta a tela de
 * imprimir certificado em pdf 
 */
 if(!empty($_GET['acao']))
 if( $_GET['acao'] == 'pdfCertificado'){
	require_once('mysql.php');
	$sql = " select * from palestra order by nome";
	$result = $conn->query($sql);
	$palestra = "<table class='table table-bordered'>";
	while($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$palestra .= "<tr><td><a href='index.php?acao=pdfCertificadoDownload&id={$row['id']}'>{$row['nome']}</a></td><td><img width='25px' src='./certificados/{$row2['arquivo']}'</td></tr>";
	}
	
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php'>Voltar</a>
				<hr>
				<?php echo $palestra; ?>
			</div>

		</div>
	</div>     

	<?php
	include("footer.html");		
	die();
}



////////////////////////////////
if(!empty($_GET['acao']))
if( $_GET['acao'] == 'verCertificado'){
	require_once('mysql.php');
	$sql = " select * from palestra order by nome";
	$result = $conn->query($sql);
	$palestra = "<table class='table table-bordered'>";
	while($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$palestra .= "<tr><td><a href='index.php?acao=verCertificadoAluno&id={$row['id']}'>{$row['nome']}</a></td><td><img width='25px' src='./certificados/{$row2['arquivo']}'</td></tr>";
	}
	
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php'>Voltar</a>
				<hr>
				<?php echo $palestra; ?>
			</div>

		</div>
	</div>    

	<?php
	include("footer.html");		
	die();
}

/////////////////////////////////////////////////
// O Site vai criar o pdf com a biblioteca TCPDF.
// Busca as informações no banco de dados 
// realiza os graficos imagettftext e importa o arquivo
// de imagem com a biblioteca TCPDF.
/////////////////////////////////////////////////
if(!empty($_GET['acao']))
if( $_GET['acao'] == 'pdfCertificadoDownload'){

	require_once('mysql.php');
	require_once('./TCPDF/examples/tcpdf_include.php');


	// biblioteca de pdf do php
	
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	///////////////////////////////////////////////////////////////////////////////////////////////
	// busca as informações no banco de dados	
	
	require_once('mysql.php');
	$sql = " 
	select
		aluno.id as alunoid, 
		aluno.nome as alunonome,
		aluno.email as alunoemail,
		palestra.nome as palestranome,
		certificadoAluno.id as certificadoAlunoid,
		data,
		curso,
		palestra,
		palestrante,
		instituicao,
		certificado_id
	FROM certificadoAluno  
	LEFT JOIN aluno on aluno.id = certificadoAluno.id_aluno
	LEFT JOIN palestra ON palestra.id = certificadoAluno.id_palestra
	where palestra.id = {$_GET['id']}
	";
	
	$result = $conn->query($sql);

	while ( $row = $result->fetch_assoc()  ) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		//die($sql2);
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();


		// funções para a criação de imagem no PHP
		//
		// fuções de criação de imagem

		// Create Image From Existing File
		$jpg_image = imagecreatefromjpeg('certificados/'.$row2['arquivo']);

		// funções de cor da gd

		#$white = imagecolorallocate($jpg_image, 255, 255, 255);
		$white = imagecolorallocate($jpg_image, 255, 0, 0);

		// configurando as fontes

		// Set Path to Font File
		#$font_path = './VelomiaVanora.ttf';
		// usamos uma fonte ttf para escrever 
		$font_path = './VelomiaVanora.ttf';

		// Set Text to Be Printed On Image
		$text = "This is a sunset!";

		// Imprimir o texto na imágem
		// usando a função imagettftext
		
		//imagettftext($jpg_image, $size, 0, 75, 300, $white, $font_path, $text);
		//imagettftext($jpg_image, $size, 0, $x, $y, $white, $font_path, $text);
		imagettftext($jpg_image, $row2['nome_fonte'], 0, $row2['nomex'], $row2['nomey'], $white, $font_path, $row['alunonome']);
		imagettftext($jpg_image, $row2['data_fonte'], 0, $row2['datax'], $row2['datay'], $white, $font_path, $row['data']);
		imagettftext($jpg_image, $row2['curso_fonte'], 0, $row2['cursox'], $row2['cursoy'], $white, $font_path, $row['curso']);
		imagettftext($jpg_image, $row2['palestra_fonte'], 0, $row2['palestrax'], $row2['palestray'], $white, $font_path, $row['palestra']);
		imagettftext($jpg_image, $row2['palestrante_fonte'], 0, $row2['palestrantex'], $row2['palestrantey'], $white, $font_path, $row['palestrante']);
		imagettftext($jpg_image, $row2['instituicao_fonte'], 0, $row2['instituicaox'], $row2['instituicaoy'], $white, $font_path, $row['instituicao']);

		// código de verificação do certificado 
		$str = $row['alunonome'].$row['alunoemail'].$row['palestra'];
		$str = hash('sha1',$str);
		imagettftext($jpg_image, 12, 0, 150, 700, $white, './VelomiaVanora.ttf', $str);


		// código para fazer o qrcode, usa o endereço atual para codificar a url
		// do certificado
		$text = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		$text.= "?acao=prnCertificado&id={$row['certificadoAlunoid']}";


		QRcode::png($text,'out.png');

		imagejpeg($jpg_image, 'out.jpg');


		$png = imagecreatefrompng('out.png');
		$jpg = imagecreatefromjpeg('out.jpg');

		$sourcefile_width=imageSX($jpg);
		$sourcefile_height=imageSY($jpg);
		$watermarkfile_width=imageSX($png);
		$watermarkfile_height=imageSY($png);

		$dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
		$dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 ) + 150;

		imagecopy($jpg, $png, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height);
		imagejpeg($jpg, "{$row['alunoid']}.jpg");


		/* Parte do código que cria uma página A4 Landscape 
		* de PDF e adiciona o Certificado
		*/

		$pdf->AddPage('L', 'A4');

		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = "{$row['alunoid']}.jpg";
		$pdf->Image($img_file, 0, 0, 300, 200, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();

		if(file_exists($img_file))
			unlink($img_file);

	}

	//////////////////////////////////////////////////////////////////////////////
	// saida do pdf 
	$pdf->Output('certificados-todos.pdf', 'D');
		
}



if(!empty($_GET['acao']))
if( $_GET['acao'] == 'minhasPalestras' ){
	require_once('mysql.php');
	$sql = 'SELECT ca.id as certificadoAlunoid, p.nome as palestra, p.certificado_id, p.data, p.palestrante
	FROM certificadoaluno ca
		INNER JOIN aluno a ON ca.id_aluno = a.id
		INNER JOIN usuario u ON a.usuario_id = u.id
		INNER JOIN palestra p ON ca.id_palestra = p.id
	WHERE u.email = "' . $_SESSION['login'] . '"
		AND p.data >= "' . date('Y-m-d', strtotime('-3 hours')) . '"
	ORDER BY p.data';

	$result = $conn->query($sql);
	$msg="<a href='index.php?acao=palestraInscricao'>Inscrição</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$msg.="<a href='index.php'>Voltar</a>";
	$msg.= "<hr>";
	$msg.="<table class='table table-bordered' >";
	$msg .=  "<tr>
		<td>Data</td>
		<td>Palestra</td>
		<td>Palestrante</td>
		<td>Ação</td>
		</tr>";
	while($row = $result->fetch_assoc()) {
		$msg .= "<tr>
			<td>" . date('d/m/Y', strtotime($row['data'])) ."</td>
			<td>{$row['palestra']}</td>
			<td>{$row['palestrante']}</td>
			<td> <a href='index.php?acao=palestraAlunoCancelar&id={$row['certificadoAlunoid']}'>Cancelar</td> 
			</tr>";
	}
	$msg .= "</table>";
	$acao = "msg";

	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				  <?php echo $msg; ?>
			</div>

		</div>
	</div>

	<?php
	include("footer.html");
	die();
}

if(!empty($_GET['acao']))
if ($_GET['acao'] == 'palestraInscricao') {
    require_once('mysql.php');


    $sql = "SELECT * FROM palestra 
		WHERE data >= '" . date('Y-m-d', strtotime('-3 hours')) . "'
		 AND id not in (SELECT id_palestra FROM certificadoaluno WHERE id_aluno = " . $_SESSION['aluno_id'] . ") 
		ORDER BY data, nome";
    $result = $conn->query($sql);
    $palestra = '';
    $palestra_count = 0;
    while ($row = $result->fetch_assoc()) {
        $palestra_count++;
        $palestra .= "<option value='{$row['id']}'>{$row['nome']} - " . date('d/m/Y', strtotime($row['data'])) . "</option>";
    }

    if (isset($_POST['OK'])) {
        $sql = "INSERT INTO certificadoAluno (id_aluno, id_palestra ) VALUES ({$_SESSION['aluno_id']}, {$_POST['palestra']})";
        $result = $conn->query($sql);
        //die($sql);
        if ($result == true) {
            include("header.html");
            ?>
			
				<div class='h-100 d-flex align-items-center justify-content-center'>
					<div style='background:green'>
				
						<div class='alert alert-success' role='alert'>
							Inscrição realizada com sucesso, para prosseguir clique <a href='index.php?acao=minhasPalestras'>aqui</a>
						</div>

					</div>
				</div>      
				<?php
                include("footer.html");
            die();
        } else {
            $msg = "Erro ao inserir dados inseridos clique <a href='index.php?acao=certificadoAluno'>aqui</a>";
            $msg .= "Error: " . $sql . "<br>" . $conn->error;
            $acao = "msg";
        }
    }
    ?>

	<?php
        include("header.html");
    ?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php?acao=minhasPalestras'>Voltar</a>
				<hr>
				<?php if ($palestra_count > 0) { ?>
				<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
				<input type="hidden" name="id" value="<?php echo $_POST['id'] ?? '' ?>">
				<table class='table table-bordered'>
					<tr>
						<td>Palestra</td>
						<td>  <select name='palestra'> <?php echo $palestra; ?> </select> </td>
						<td><?php echo $palestra_erro ?? '' ?></td>
					</tr>
					<tr>
						<td><input type="submit" name="OK" value="OK"></td>
					</tr>
				</table>
				<?php } else { ?>
				<p>Nenhuma palestra disponível no momento.</p>
				<?php } ?>
			</div>

		</div>
	</div>      

	<?php
		include("footer.html");		
	die();			

}


if(!empty($_GET['acao']))
if( $_GET['acao'] == 'palestraAlunoCancelar'){
		
		$sql = "DELETE FROM certificadoAluno where id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		
		if ($result == TRUE){
		
		
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Palestra cancelada com sucesso, para prosseguir clique <a href='index.php?acao=minhasPalestras'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();	
		
		} else {
			$msg = "Erro ao cancelar a palestra, clique <a href='index.php?acao=minhasPalestras'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();

}

/////////////////////////////////////////////////
// Meus Certificados em PDF 
/////////////////////////////////////////////////
if(!empty($_GET['acao']))
if( $_GET['acao'] == 'meuCertificadoPdf'){

	require_once('mysql.php');
	require_once('./TCPDF/examples/tcpdf_include.php');


	// biblioteca de pdf do php
	
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	///////////////////////////////////////////////////////////////////////////////////////////////
	// busca as informações no banco de dados	
	
	require_once('mysql.php');
	$sql = " 
	select
		aluno.id as aluno_id,
		aluno.nome as alunonome,
		aluno.email as alunoemail,
		palestra.nome as palestranome,
		data,
		curso,
		palestra,
		palestrante,
		instituicao,
		certificado_id
	FROM certificadoAluno  
	LEFT JOIN aluno on aluno.id = certificadoAluno.id_aluno
	LEFT JOIN palestra ON palestra.id = certificadoAluno.id_palestra
	where certificadoAluno.id = {$_GET['id']}
	";
	//die($sql);
	$result = $conn->query($sql);
	
	if( $result )
	$row = $result->fetch_assoc();
	$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
	//die($sql2);
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();



	// Create Image From Existing File
	$jpg_image = imagecreatefromjpeg('certificados/'.$row2['arquivo']);

	// funções de cor da gd

	#$white = imagecolorallocate($jpg_image, 255, 255, 255);
	$white = imagecolorallocate($jpg_image, 255, 0, 0);

	// configurando as fontes

	// Set Path to Font File
	#$font_path = './VelomiaVanora.ttf';
	// usamos uma fonte ttf para escrever 
	$font_path = './VelomiaVanora.ttf';

	// Set Text to Be Printed On Image
	$text = "This is a sunset!";

	// Imprimir o texto na imágem
	// usando a função imagettftext
	
	//imagettftext($jpg_image, $size, 0, 75, 300, $white, $font_path, $text);
	//imagettftext($jpg_image, $size, 0, $x, $y, $white, $font_path, $text);
	imagettftext($jpg_image, $row2['nome_fonte'], 0, $row2['nomex'], $row2['nomey'], $white, $font_path, $row['alunonome']);
	imagettftext($jpg_image, $row2['data_fonte'], 0, $row2['datax'], $row2['datay'], $white, $font_path, $row['data']);
	imagettftext($jpg_image, $row2['curso_fonte'], 0, $row2['cursox'], $row2['cursoy'], $white, $font_path, $row['curso']);
	imagettftext($jpg_image, $row2['palestra_fonte'], 0, $row2['palestrax'], $row2['palestray'], $white, $font_path, $row['palestra']);
	imagettftext($jpg_image, $row2['palestrante_fonte'], 0, $row2['palestrantex'], $row2['palestrantey'], $white, $font_path, $row['palestrante']);
	imagettftext($jpg_image, $row2['instituicao_fonte'], 0, $row2['instituicaox'], $row2['instituicaoy'], $white, $font_path, $row['instituicao']);

	// código de verificação do certificado 
	$str = $row['alunonome'].$row['alunoemail'].$row['palestra'];
	$str = hash('sha1',$str);
	imagettftext($jpg_image, 12, 0, 150, 700, $white, './VelomiaVanora.ttf', $str);


	// código para fazer o qrcode, usa o endereço atual para codificar a url
	// do certificado
	$text = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	$text.= "?acao=prnCertificado&id={$row['certificado_id']}";


	QRcode::png($text,'out.png');

	imagejpeg($jpg_image, 'out.jpg');


	$png = imagecreatefrompng('out.png');
	$jpg = imagecreatefromjpeg('out.jpg');

	$sourcefile_width=imageSX($jpg);
	$sourcefile_height=imageSY($jpg);
	$watermarkfile_width=imageSX($png);
	$watermarkfile_height=imageSY($png);

	$dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
	$dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 ) + 150;

	imagecopy($jpg, $png, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height);
	imagejpeg($jpg, "{$row['aluno_id']}.jpg");


	/* Parte do código que cria uma página A4 Landscape 
	* de PDF e adiciona o Certificado
	*/

	$pdf->AddPage('L', 'A4');

	$bMargin = $pdf->getBreakMargin();
	// get current auto-page-break mode
	$auto_page_break = $pdf->getAutoPageBreak();
	// disable auto-page-break
	$pdf->SetAutoPageBreak(false, 0);
	// set bacground image
	$img_file = "{$row['aluno_id']}.jpg";
	$pdf->Image($img_file, 0, 0, 300, 200, '', '', '', false, 300, '', false, false, 0);
	// restore auto-page-break status
	$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
	// set the starting point for the page content
	$pdf->setPageMark();

	$today = date('YmdHis');
	//////////////////////////////////////////////////////////////////////////////
	// saida do pdf 
	$pdf->Output('meu-certificado-'. $today . '.pdf', 'D');

	if(file_exists($img_file))
		unlink($img_file);
		
}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'meusCertificados'){

	require_once('mysql.php');
	$sql = 'SELECT ca.id as certificadoAlunoid, p.nome, p.certificado_id, p.data
	FROM certificadoaluno ca
		INNER JOIN aluno a ON ca.id_aluno = a.id
		INNER JOIN usuario u ON a.usuario_id = u.id
		INNER JOIN palestra p ON ca.id_palestra = p.id
	WHERE u.email = "' . $_SESSION['login'] . '"
		AND ca.status = 2';
		// AND p.data < "' . date('Y-m-d', strtotime('-3 hours')) . '"';


	//die($sql);
	$result = $conn->query($sql);
	if ($result) {
		$palestra = "<table class='table table-bordered'>";
		$palestra .=  "<tr>
			<td>Data</td>
			<td>Palestra</td>
			<td></td>
		</tr>";
		$count=0;
		while ($row = $result->fetch_assoc()) {
			$count++;
			$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
			//die($sql2);
			$result2 = $conn->query($sql2);
			$row2 = $result2->fetch_assoc();
			$palestra .= "<tr><td>". date('d/m/Y', strtotime($row['data'])) ."</td><td><a href='index.php?acao=meuCertificadoPdf&id={$row['certificadoAlunoid']}'>{$row['nome']}</a></td><td><img width='25px' src='./certificados/{$row2['arquivo']}'</td></tr>";
		}
		
	} 
	if($count == 0)
		$palestra = '<p>Nenhum certificado disponível.</p>';
	
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php'>Voltar</a>
				<hr>
				<?php echo $palestra; ?>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");		
	
	
	die();
//zzz
}




if(!empty($_GET['acao']))
if( $_GET['acao'] == 'verCertificadoAluno'){

	require_once('mysql.php');
	$sql = " 
	select certificadoAluno.id as certificadoAlunoid, aluno.nome as nome, certificado_id
	FROM palestra 
	LEFT JOIN certificadoAluno on palestra.id = certificadoAluno.id_palestra 
	LEFT JOIN aluno ON aluno.id = certificadoAluno.id_aluno
	WHERE palestra.id = {$_GET['id']} AND certificadoAluno.status = 2
	";
	//die($sql);
	$result = $conn->query($sql);
	$palestra = "<table class='table table-bordered'>";
	if( $result )
	while($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		//die($sql2);
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$palestra .= "<tr><td><a href='index.php?acao=prnCertificado&id={$row['certificadoAlunoid']}'>{$row['nome']}</a></td><td><img width='25px' src='./certificados/{$row2['arquivo']}'</td></tr>";
	}
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php?acao=verCertificado'>Voltar</a>
				<hr>
				<?php echo $palestra; ?>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");		
	
	
	die();
//zzz
}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'prnCertificado'){
	require_once('mysql.php');
	$sql = " 
	select
		aluno.nome as alunonome,
		aluno.email as alunoemail,
		palestra.nome as palestranome,
		data,
		curso,
		palestra,
		palestrante,
		instituicao,
		certificado_id
	FROM certificadoAluno  
	LEFT JOIN aluno on aluno.id = certificadoAluno.id_aluno
	LEFT JOIN palestra ON palestra.id = certificadoAluno.id_palestra
	where certificadoAluno.id = {$_GET['id']}
	";
	//die($sql);
	$result = $conn->query($sql);
	
	if( $result )
	$row = $result->fetch_assoc();
	$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
	//die($sql2);
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();



	// Create Image From Existing File
	$jpg_image = imagecreatefromjpeg('certificados/'.$row2['arquivo']);

	// Allocate A Color For The Text
	$white = imagecolorallocate($jpg_image, 255, 0, 0);

	// Set Path to Font File
	#$font_path = './VelomiaVanora.ttf';
	$font_path = './VelomiaVanora.ttf';

	// Set Text to Be Printed On Image
	$text = "This is a sunset!";

	// Print Text On Image

	imagettftext($jpg_image, $row2['nome_fonte'], 0, $row2['nomex'], $row2['nomey'], $white, $font_path, $row['alunonome']);
	imagettftext($jpg_image, $row2['data_fonte'], 0, $row2['datax'], $row2['datay'], $white, $font_path, date('d/m/Y', strtotime($row['data'])));
	imagettftext($jpg_image, $row2['curso_fonte'], 0, $row2['cursox'], $row2['cursoy'], $white, $font_path, $row['curso']);
	imagettftext($jpg_image, $row2['palestra_fonte'], 0, $row2['palestrax'], $row2['palestray'], $white, $font_path, $row['palestra']);
	imagettftext($jpg_image, $row2['palestrante_fonte'], 0, $row2['palestrantex'], $row2['palestrantey'], $white, $font_path, $row['palestrante']);
	imagettftext($jpg_image, $row2['instituicao_fonte'], 0, $row2['instituicaox'], $row2['instituicaoy'], $white, $font_path, $row['instituicao']);


	$text = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	$text.= "?acao=prnCertificado&id= id= {$_GET['id']}";


	QRcode::png($text,'out.png');

	imagejpeg($jpg_image, 'out.jpg');


	$png = imagecreatefrompng('out.png');
	$jpg = imagecreatefromjpeg('out.jpg');

	$sourcefile_width=imageSX($jpg);
	$sourcefile_height=imageSY($jpg);
	$watermarkfile_width=imageSX($png);
	$watermarkfile_height=imageSY($png);

	$dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
	$dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 ) + 150;

	imagecopy($jpg, $png, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height);
	imagejpeg($jpg, 'out.jpg');

?>

<img src="out.jpg">

<?php

//////////////////////////////////////////////////////////////////////////////
	die();

}



////////////////////////////////
if(!empty($_GET['acao']))
if( $_GET['acao'] == 'certificadoAlunoApaga'){
		
		$sql = "SELECT * FROM certificadoAluno WHERE id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		if( $result ) 
			$row = $result->fetch_assoc();

		$sql = "DELETE FROM certificadoAluno where id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		
		if ($result == TRUE){
		
		
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Dados apagados com sucesso, para prosseguir clique <a href='index.php?acao=certificadoAluno&id=<?php echo $row['id_palestra']?>'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();	
		
		} else {
			$msg = "Erro ao apagar dados clique <a href='index.php?acao=certificadoAluno'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();

}

if(!empty($_GET['acao']))
if ($_GET['acao'] == 'certificadoAlunoPublica') {
    
	$sql = "SELECT * FROM certificadoaluno WHERE id = {$_GET['id']}";

	require_once('mysql.php');
	$result = $conn->query($sql);
	if ($result) 
		$row2 = $result->fetch_assoc();

	$sql = "UPDATE certificadoaluno SET status = 2 WHERE id= {$_GET['id']}  ";
	require_once('mysql.php');
	$result = $conn->query($sql);

	if ($result == TRUE){
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Certificado publicado com sucesso, para prosseguir clique <a href='index.php?acao=certificadoAluno&id=<?php echo $row2['id_palestra'] ?>'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();			

		} else {
			$msg = "Erro ao publicar um certificado, clique <a href='index.php?acao=aluno'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();
    
}

if(!empty($_GET['acao']))
if ($_GET['acao'] == 'certificadoAlunoPublicaTodos') {
    
	$sql = "UPDATE certificadoaluno SET status = 2 WHERE id_palestra = {$_GET['id']}  ";
	require_once('mysql.php');
	$result = $conn->query($sql);

	if ($result == TRUE){
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Todos os certificados foram publicados com sucesso, para prosseguir clique <a href='index.php?acao=certificadoAluno&id=<?php echo $_GET['id'] ?>'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();			

		} else {
			$msg = "Erro ao publicar todos os certificados, clique <a href='index.php?acao=aluno'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();
    
}


if(!empty($_GET['acao']))
if( $_GET['acao'] == 'certificadoAlunoNova' || $_GET['acao'] == 'certificadoAlunoEdit' ){

	if( $_GET['acao'] == 'certificadoAlunoEdit' && empty($_POST['OK'])) {
		$sql = "SELECT c.id as certificado_aluno_id, a.nome, a.email, c.id_aluno as aluno_id
		FROM certificadoaluno c
			INNER JOIN aluno a ON c.id_aluno = a.id
		WHERE c.id = ". $_GET['id'];
	
		require_once('mysql.php');
		$result = $conn->query($sql);
		if ($result) {
			$row = $result->fetch_assoc();
			$_POST['nome']=$row['nome'];
			$_POST['email']=$row['email'];
			$_POST['id'] = $_GET['id'];
		}
	}



	if( isset($_POST['OK']) ) {

		$form = 1;
		$erro=0;
		if( !$_POST['aluno'] ) { 
			$nome_erro = "o aluno estar vazio"; $erro = 1; 
		}
		if( !$_POST['palestra'] ) { 
			$data_erro = "a palestra não pode estar vazio"; $erro = 1; 
		}
		if( !$erro ){
			if($_POST['id'] ) {
				$sql = "UPDATE certificadoAluno
				SET 
					id_aluno ='{$_POST['aluno']}',
					id_palestra ='{$_POST['palestra']}'
				where id = {$_POST['id']}";
			} else {
				$sql = "INSERT INTO certificadoAluno (id_aluno, id_palestra ) VALUES ({$_POST['aluno']}, {$_POST['palestra']})";
			}
			$row['aluno_id'] = $_POST['aluno'];

			require_once('mysql.php');		
			$result = $conn->query($sql);
			//die($sql);
			if ($result == TRUE){
				include("header.html");
			?>
			
			<div class='h-100 d-flex align-items-center justify-content-center'>
				<div style='background:green'>
			
					<div class='alert alert-success' role='alert'>
						Dados inseridos com sucesso, para prosseguir clique <a href='index.php?acao=certificadoAluno&id=<?php echo $_POST['palestra']?>'>aqui</a>
					</div>

				</div>
			</div>      
			<?php
			include("footer.html");		
			die();			
			} else {
				$msg = "Erro ao inserir dados inseridos clique <a href='index.php?acao=certificadoAluno&id={$_POST['palestra']}'>aqui</a>";
				$msg .= "Error: " . $sql . "<br>" . $conn->error;
				$acao = "msg";
			}

		} else {
?>
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
				<table class='table table-bordered'>
					<tr>
						<td>Nome</td>
						<td><input type="text" name="nome" value="<?php echo $_POST['nome'] ?>"></td>
						<td><?php echo $nome_erro ?></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><input type="text" name="email" value="<?php echo $_POST['email'] ?>"></td>
						<td><?php echo $email_erro ?></td>
					</tr>
					<input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
					<tr>
						<td><input type="submit" name="OK" value="OK"></td>
					</tr>
				</table>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");		
	die();



?>

<?php

		}
		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();


	} else {
		$form = 0;
		require_once('mysql.php');
		if($_GET['acao'] == 'certificadoAlunoEdit')
			$sql = " select * from aluno where id = {$row['aluno_id']} order by nome";
		else 
			$sql = " select * from aluno order by nome";
		
		$aluno = '';
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			$aluno .= "<option value='{$row['id']}'>{$row['nome']}</option>";
		}

		$sql = " select * from palestra order by nome";
		$result = $conn->query($sql);
		$palestra = '';
		while($row = $result->fetch_assoc()) {
			$palestra .= "<option value='{$row['id']}'>{$row['nome']} - " . date('d/m/Y', strtotime($row['data'])) . "</option>";
		}

?>



		<?php

		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
					<table class='table table-bordered'>
					<tr><td>Nome </td><td>  <select name='aluno'> <?php echo $aluno; ?> </select> </td><td><?php echo $aluno_erro ?? '' ?></td></tr>
					<tr><td>Palestra</td><td>  <select name='palestra'> <?php echo $palestra; ?> </select> </td><td><?php echo $palestra_erro ?? '' ?></td></tr>
					<input type="hidden" name="id" value="<?php echo $_POST['id'] ?? '' ?>">
					<tr><td><input type="submit" name="OK" value="OK"></td></tr>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();			

	}

}


////////////////////////////////
if(!empty($_GET['acao']))
if( $_GET['acao'] == 'verPalestras'){
	require_once('mysql.php');
	$sql = " select * from palestra order by nome";
	$result = $conn->query($sql);
	$palestra = "<table class='table table-bordered'>";
	while($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$palestra .= "<tr><td><a href='index.php?acao=certificadoAluno&id={$row['id']}'>{$row['nome']}</a></td><td><img width='25px' src='./certificados/{$row2['arquivo']}'</td></tr>";
	}
	
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<a href='index.php'>Voltar</a>
				<hr>
				<?php echo $palestra; ?>
			</div>

		</div>
	</div>    

	<?php
	include("footer.html");		
	die();
}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'certificadoAluno' ){
	require_once('mysql.php');
	$sql = "SELECT certificadoAluno.id, id_aluno, id_palestra, aluno.nome as nomealuno, palestra.nome as nomepalestra, 
		palestra.curso, palestra.palestrante, palestra.data, certificadoAluno.status  
	FROM certificadoAluno
	LEFT JOIN aluno ON id_aluno = aluno.id
	LEFT JOIN palestra on id_palestra = palestra.id
	WHERE palestra.id = {$_GET['id']} 
	ORDER BY palestra.data, aluno.nome";

	//die($sql);
	$result = $conn->query($sql);
	$msg="<a href='index.php?acao=certificadoAlunoNova'>Novo Certificado para Aluno</a> &nbsp;";
	$msg.="<a href='index.php?acao=verPalestras'>Voltar</a> &nbsp;";
	$msg .= "<a href='index.php?acao=certificadoAlunoPublicaTodos&id={$_GET['id']}'>Publicar Todos</a> &nbsp;";
	if( $result ) {
	$msg.="<table class='table table-bordered'>";
	$msg .=  "<tr>
			<td>Data</td>
			<td>Palestra</td>
			<td>Palestrante</td>
			<td>Nome</td>
			<td>Status</td>
			<td>Ação</td>
		</tr>";
	while($row = $result->fetch_assoc()) {
		$msg .= "<tr>
			<td>". date('d/m/Y', strtotime($row['data'])) ."</td>
			<td>{$row['nomepalestra']}</td>
			<td>{$row['palestrante']}</td>
			<td>{$row['nomealuno']}</td>
			<td>";
		if($row['status'] == 1)
			$msg .= 'Pendente';
		else
			$msg .= 'Publicado';
		$msg .= "</td>
			<td>";
		if($row['status'] == 1)
			$msg .= "<a href='index.php?acao=certificadoAlunoPublica&id={$row['id']}'>Publicar</a> &nbsp;";

		$msg .= "<a href='index.php?acao=certificadoAlunoEdit&id={$row['id']}'>Editar</a> &nbsp;
				<a href='index.php?acao=certificadoAlunoApaga&id={$row['id']}'>Apagar</a>
			</td>
		</tr>";
		
	}
	$msg .= "</table>";
	}
	$acao = "msg";
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<?php echo $msg; ?>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");		
	die();	
}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'alunoApaga'){
		
		$sql = "DELETE FROM aluno where id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		
		if ($result == TRUE){
		
		
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Dados apagados com sucesso, para prosseguir clique <a href='index.php?acao=aluno'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();			

		} else {
			$msg = "Erro ao apagar dados clique <a href='index.php?acao=aluno'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();

}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'alunoNova' || $_GET['acao'] == 'alunoEdit'){

	
	if( $_GET['acao'] == 'alunoEdit' && empty($_POST['OK']) ) {
		$sql = "SELECT * FROM aluno where id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		if( $result ) $row = $result->fetch_assoc();
		$_POST['nome']=$row['nome'];
		$_POST['email']=$row['email'];
		$_POST['id']=$row['id'];
	}

	
	if( !empty($_POST['OK']) ) {
		$form = 1;
		$erro=0;
		if( !$_POST['nome'] ) { $nome_erro = "o nome não pode estar vazio"; $erro = 1; }
		if( !$_POST['email'] ) { $data_erro = "o email não pode estar vazio"; $erro = 1; }
		if( !$erro ){
			if($_POST['id'] != 0) {
				$sql = "UPDATE aluno
				SET 
					nome='{$_POST['nome']}',
					email='{$_POST['email']}'
				where id = {$_POST['id']}";
			} else {
				$sql = "INSERT INTO aluno (nome, email ) VALUES ('{$_POST['nome']}','{$_POST['email']}')";
			}
		require_once('mysql.php');		
		$result = $conn->query($sql);
		//die($sql);
		if ($result == TRUE){	
		
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>
					  Dados inseridos com sucesso, para prosseguir clique <a href='index.php?acao=aluno'>aqui</a>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");		
		die();			
		} else {
			$msg = "Erro ao inserir dados inseridos clique <a href='index.php?acao=aluno'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		} else {
		include("header.html");
		?>
		
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>

					<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
					<table class='table table-bordered'>
					<tr><td>Nome</td><td><input type="text" name="nome" value="<?php echo $_POST['nome'] ?>"><td><?php echo $nome_erro ?></td></tr>
					<tr><td>Email</td><td><input type="text" name="email" value="<?php echo $_POST['email'] ?>"><?php echo $email_erro ?></td></tr>
					<input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
					<tr><td><input type="submit" name="OK" value="OK"></td></tr>

				</div>

			</div>
		</div>      
		<?php
		include("footer.html");	
		}
	
		
		?>
		<div class="div">
			<?php echo $msg; ?>
		</div>
	
		<?php
		die();


	} else {
		$form = 0;
		include("header.html");
		?>
	
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
		
				<div class='alert alert-success' role='alert'>

					<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
					<table class='table table-bordered'>
					<tr><td>Nome</td><td><input type="text" name="nome" value="<?php echo $_POST['nome'] ?? '' ?>"><td><?php echo $nome_erro ?? '' ?></td></tr>
					<tr><td>Email</td><td><input type="text" name="email" value="<?php echo $_POST['email'] ?? '' ?>"><?php echo $email_erro ?? '' ?></td></tr>
					<input type="hidden" name="id" value="0">
					<tr><td><input type="submit" name="OK" value="OK"></td></tr>
				</div>

			</div>
		</div>      
		<?php
		include("footer.html");	
		die();
	}

}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'aluno' ){
	require_once('mysql.php');
	$sql = "SELECT * FROM aluno order by nome";
	$result = $conn->query($sql);
	$msg="<a href='index.php?acao=alunoNova'>Novo Aluno</a>&nbsp;&nbsp;&nbsp;";
	$msg.="<a href='index.php'>Voltar</a>";
	if( $result ) {
	$msg.="<table class='table table-bordered'>";
	$msg .=  "<tr>
		<td>Nome</td>
		<td>Email</td>
		</tr>";
	while($row = $result->fetch_assoc()) {
		$msg .= "<tr>
			<td>{$row['nome']}</td>
			<td>{$row['email']}</td>
			<td><a href='index.php?acao=alunoEdit&id={$row['id']}'>Editar</a> <a href='index.php?acao=alunoApaga&id={$row['id']}'>Apagar</a></td>
 
			</tr>";
		
	}
	$msg .= "</table>";
	}
	$acao = "msg";
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				<?php echo $msg; ?>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");		
	die();		

}


if(!empty($_GET['acao']))
if( $_GET['acao'] == 'palestraApaga'){
		
		$sql = "DELETE FROM palestra where id= {$_GET['id']}  ";
		require_once('mysql.php');
		$result = $conn->query($sql);
		
		if ($result == TRUE){
		
		
			include("header.html");
			?>
			
			<div class='h-100 d-flex align-items-center justify-content-center'>
				<div style='background:green'>
			
					<div class='alert alert-success' role='alert'>
						  Dados apagados com sucesso, para prosseguir clique <a href='index.php?acao=palestra'>aqui</a>
					</div>

				</div>
			</div>      
			<?php
			include("footer.html");		
			die();					
		
		} else {
			$msg = "Erro ao apagar dados clique <a href='index.php?acao=palestra'>aqui</a>";
			$msg .= "Error: " . $sql . "<br>" . $conn->error;
			$acao = "msg";
		}

		
	?>
	<div class="div">
	<?php echo $msg; ?>
	</div>
	</div>
	<?php
	die();

}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'palestraNova' || $_GET['acao'] == 'palestraEdit'){

	if( $_GET['acao'] == 'palestraEdit'  ) {
		$go = false;
		if(isset($_POST['OK'])){
			if ($_POST['OK'] != 'OK') {
				$go = true;
			}
		}else
			$go = true;
		if ($go) {
			$sql = "SELECT * FROM palestra where id= {$_GET['id']}  ";
			require_once('mysql.php');
			$result = $conn->query($sql);
			if ($result) {
				$row = $result->fetch_assoc();
			}
			$_POST['nome']=$row['nome'];
			$_POST['data']=$row['data'];
			$_POST['curso']=$row['curso'];
			$_POST['palestra']=$row['palestra'];
			$_POST['palestrante']=$row['palestrante'];
			$_POST['instituicao']=$row['instituicao'];
			$_POST['certificado_id']=$row['certificado_id'];
			$_POST['id']=$row['id'];
		}
	}

	if( isset($_POST['OK']) ){
		$form = 1;
		$erro = 0;
		if( !$_POST['nome'] ) { $nome_erro = "o nome não pode estar vazio"; $erro = 1; }
		if( !$_POST['data'] ) { $data_erro = "o data não pode estar vazio"; $erro = 1; }
		if( !$_POST['curso'] ) { $curso_erro = "o curso não pode estar vazio"; $erro =1; }
		if( !$_POST['palestra'] ) { $palestra_erro = "o palestra não pode estar vazio"; $erro =1; }
		if( !$_POST['palestrante'] ) { $palestrante_erro = "o palestrante não pode estar vazio"; $erro =1; }
		if( !$_POST['instituicao'] ) { $instituicao_erro = "o instituicao não pode estar vazio"; $erro =1; }
		if( !$_POST['certificado_id'] ) { $instituicao_erro = "o certificado não pode estar vazio"; $erro =1; }
		
		if( !$erro ){
			if($_POST['id'] ) {
				$sql = "UPDATE palestra
				SET 
					nome='{$_POST['nome']}',
					data='{$_POST['data']}',
					curso='{$_POST['curso']}',
					palestra='{$_POST['palestra']}',
					palestrante='{$_POST['palestrante']}',
					instituicao='{$_POST['instituicao']}',
					certificado_id='{$_POST['certificado_id']}'
				where id = {$_POST['id']}";
				//echo "<hr>$sql<hr>";
			} else {
				$sql = "INSERT INTO 
				palestra (nome, data, curso, palestra, palestrante, instituicao, certificado_id) VALUES 	('{$_POST['nome']}','{$_POST['data']}','{$_POST['curso']}','{$_POST['palestra']}','{$_POST['palestrante']}','{$_POST['instituicao']}',{$_POST['certificado_id']} )";
			}
			require_once('mysql.php');		
			$result = $conn->query($sql);
			//die($result);
			// zzz 
			if ($result ){
				include("header.html");
				?>
				
				<div class='h-100 d-flex align-items-center justify-content-center'>
					<div style='background:green'>
				
						<div class='alert alert-success' role='alert'>
							Dados inseridos com sucesso, para prosseguir clique <a href='index.php?acao=palestra'>aqui</a>
						</div>

					</div>
				</div>
				
				<?php
				include("footer.html");		
				die();						
			} else {
				$msg = "Erro ao inserir dados inseridos clique <a href='index.php?acao=palestra'>aqui</a>";
				$msg .= "Error: " . $sql . "<br>" . $conn->error;
				$acao = "msg";
			}

		} else {
			include("header.html");
			?>
			
			<div class='h-100 d-flex align-items-center justify-content-center'>
				<div style='background:green'>
			
					<div class='alert alert-success' role='alert'>
							selecione todos os campos <a href='index.php?acao=palestraEdit&id=<?php echo $_POST['id']?>'>aqui</a>
					</div>

				</div>
			</div>      
			<?php
			include("footer.html");		
			die();		
		}
		
		//$msg = "Erro ao inserir dados inseridos clique <a href='index.php?acao=palestra'>aqui</a>";
		//$acao = "msg";		
		
		?>
		<div class="div">
		<?php echo $msg; ?>
		</div>
		</div>
		<?php
		die();

	} else {
		$form = 0;
		require_once('mysql.php');
		$sql = "SELECT * FROM certificado order by id";
		$result = $conn->query($sql);
		$certificado = '';
		if( $result )
			while($row = $result->fetch_assoc()) {
				$selected = "";
				if (isset($_POST['certificado_id'])) 
					if ($row['id'] == $_POST['certificado_id']) 
						$selected = " checked ";
				
				$certificado .= "
				<tr>
					<td><input type='radio' id='{$row['id']}' name='certificado_id' value='{$row['id']}' " . $selected .  "></td>
					<td><img style='width:50px;height:50px;margin-left:5px; float: right;' src='./certificados/{$row['arquivo']}'></td
				</tr>
				";
			}

		include("header.html");
		?>
	
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:green'>
				<div class='alert alert-success' role='alert'>

					<form method="post" action="index.php?acao=<?php echo $_GET['acao'] ?>">
					<table class='table table-bordered'>
						<tr>
							<td>Nome</td>
							<td><input type="text" name="nome" value="<?php echo $_POST['nome'] ?? '' ?>"><td><?php echo $nome_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Data</td>
							<td><input type="date" name="data" value="<?php echo $_POST['data'] ?? '' ?>"><?php echo $data_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Curso</td>
							<td><input type="text" name="curso" value="<?php echo $_POST['curso'] ?? '' ?>"><?php echo $curso_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Palestra</td>
							<td><input type="text" name="palestra" value="<?php echo $_POST['palestra'] ?? '' ?>"><?php echo $palestra_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Palestrante</td>
							<td><input type="text" name="palestrante" value="<?php echo $_POST['palestrante'] ?? '' ?>"><?php echo $palestrante_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Instituição</td>
							<td><input type="text" name="instituicao" value="<?php echo $_POST['instituicao'] ?? '' ?>"><?php echo $instituicao_erro ?? '' ?></td>
						</tr>
						<tr>
							<td>Certificado</td>
							<td>
								<table>
									<?php echo $certificado; ?>
								</table>
								<?php echo $certificado_erro ?? '' ?>
							</td>
						</tr>
					</table>
					<input type="hidden" name="id" value="<?php echo $_GET['id'] ?? '' ?>">
					<input type="submit" name="OK" value="OK"></td></tr>
				</div>
			</div>
		</div> 

		<?php
		include("footer.html");
		die();


	}

}

if(!empty($_GET['acao']))
if( $_GET['acao'] == 'palestra' ){
	require_once('mysql.php');
	$sql = "SELECT * FROM palestra order by nome";
	$result = $conn->query($sql);
	$msg="<a href='index.php?acao=palestraNova'>Nova Palestra</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$msg.="<a href='index.php'>Voltar</a>";
	$msg.="<table class='table table-bordered' >";
	$msg .=  "<tr>
		<td>Nome</td>
		<td>Data</td>
		<td>Curso</td>
		<td>Palestra</td>
		<td>Palestrante</td>
		<td>instituicao</td>
		<td>Certificado</td>
		<td>Ação</td>
		</tr>";
	while($row = $result->fetch_assoc()) {
		$sql2 = "SELECT * FROM certificado where id= {$row['certificado_id']} ";
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$msg .= "<tr>
			<td>{$row['nome']}</td>
			<td>" . date('d/m/Y', strtotime($row['data'])) ."</td>
			<td>{$row['curso']}</td>
			<td>{$row['palestra']}</td>
			<td>{$row['palestrante']}</td>
			<td>{$row['instituicao']}</td>
			<td><img width='25px' src='./certificados/{$row2['arquivo']}'</td>
			<td><a href='index.php?acao=palestraEdit&id={$row['id']}'>Editar</a> <a href='index.php?acao=palestraApaga&id={$row['id']}'>Apagar</td> 
			</tr>";
	}
	$msg .= "</table>";
	$acao = "msg";
	include("header.html");
	?>
	
	<div class='h-100 d-flex align-items-center justify-content-center'>
		<div style='background:green'>
	
			<div class='alert alert-success' role='alert'>
				  <?php echo $msg; ?>
			</div>

		</div>
	</div>      
	<?php
	include("footer.html");
	die();
}



if( $login ) {
	//var_dump($login, $acao);

	/////////////////////////////////////////////////
	// MENU
	////////////////////////////////////////////////

	if(isset($_GET['acao']) ) {
		if (isset($acao)) {
			if ($acao != 'msg') {
				$acao = $_GET['acao'];
			}
		}else
			$acao = $_GET['acao'];

	} else {
	       
	        include("header.html");

	        echo "<nav class='navbar navbar-expand-sm bg-primary navbar-dark'>
  					<ul class='navbar-nav'> ";
			if ($_SESSION['role'] == 1) {
			echo "		<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=cadastrarCertificado'>Cadastrar Certificado</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=listaCertificado'>Lista Certificado</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=palestra'>Palestra</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=aluno'>Aluno</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=verPalestras'>Certificado do Aluno</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=verCertificado' >Ver Certificados</a>
						</li>
						<li class='nav-item active'>
						<a class='nav-link' href='index.php?acao=pdfCertificado' >Emitir Certificados(PDF)</a>
					   </li>";
			}
			echo "		<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=minhasPalestras' >Minhas Palestras</a>
						</li>
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=meusCertificados' >Meus Certificados</a>
						</li>
											";
			echo "
						<li class='nav-item active'>
							<a class='nav-link' href='index.php?acao=sair'>Sair</a>
						</li>  
					</ul>
				</nav>";


	        /*
		echo "<table><tr>
		<td><a href='index.php?acao=cadastrarCertificado'>Cadastrar Certificado</a></td>
		<td><a href='index.php?acao=listaCertificado'>Lista Certificado</a></td>
		<td><a href='index.php?acao=palestra'>Palestra</a></td>
		<td><a href='index.php?acao=aluno'>Aluno</a></td>
		<td><a href='index.php?acao=certificadoAluno'>Certificado do aluno</a></td>
		<td><a href='index.php?acao=sair'>Sair</a></td>
		</tr></table>";
		*/
	        include("footer.html");
	        die();
		$acao = "msg"; 
	}
} else {


if(isset($_GET['acao']) ) {
	$titulo = $_GET['acao'];
	if($_GET['acao'] == 'novoUsuario'){
		$acao="novoUsuario";
		$nova_acao='criaUsuario';
	}
	if($_GET['acao'] == 'login'){
		$email = $_POST['email'];
		$senha = $_POST['password'];
		$senha = md5($senha);
		require_once('mysql.php');
		$sql = "SELECT * FROM usuario WHERE email = '{$email}' AND senha = '{$senha}'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();

		$ok = 0;
		if ($result->num_rows >= 1){
			$_SESSION['login'] = $email;
			$_SESSION['role'] = $row['perfil_id'];
			$ok = 1;

			$sql = "SELECT * FROM aluno WHERE usuario_id = {$row['id']}";
			$result = $conn->query($sql);
			$row2 = $result->fetch_assoc();
			if($result->num_rows >=1)
				$_SESSION['aluno_id'] = $row2['id'];
			else
				$_SESSION['aluno_id'] = null;
		
		}
		$conn->close();	
	
		if($ok) {
			include("header_msg.html");
			echo "
				<div class='h-100 d-flex align-items-center justify-content-center'>
					<div style='background:green'>
				
						<div class='alert alert-success' role='alert'>
							  Login com sucesso clique <a href='index.php'>aqui para continuar</a>
						</div>
			
					</div>
				</div>      ";
			include("footer_msg.html");
			die();
		} else {
			$acao="msg";
			$msg = "Problema com Login para tentar novamente clique <a href='index.php'>aqui</a>";
		}


	}
	if($_GET['acao'] == 'criaUsuario'){
		$erro = "";
		if( !$_POST['email'] ) $erro .= "preencha o email<br>";
		if( !$_POST['nome']  ) $erro .= "preencha o nome<br>";
		if( !$_POST['password'] ) $erro .= "preencha a senha<br>";
		if( !$_POST['re_password'] ) $erro .= "preencha a senha<br>";
		if( $_POST['password'] != $_POST['re_password'] ) $erro .= "a senha deve ser igual";
		if( $erro ) {
			$acao = 'novoUsuario';
			$erro = "<p class='error'>$erro</p>";
			$nova_acao='criaUsuario';
		} else {
			$senha = md5($_POST['password']);
			require_once('mysql.php');
			//insere novo usuário (por padrão é perfil aluno)
			$sql = "INSERT INTO usuario( email, nome, senha ) 
				values ('{$_POST['email']}', '{$_POST['nome']}', '{$senha}' ) ";

			if ($conn->query($sql) === TRUE) {
				//insere aluno na tabela aluno
				$sql2 = "INSERT INTO aluno( email, nome ) 
				values ('{$_POST['email']}', '{$_POST['nome']}')";
				$conn->query($sql2);

				$sql2 = "update aluno a 
				inner join usuario b on a.nome = b.nome and a.email = b.email
				set a.usuario_id = b.id 
				where b.perfil_id = 2 and a.usuario_id is null";
				$conn->query($sql2);

				$msg = "Usuário criado com sucesso clique <a href='index.php'>aqui para login</a>";
			} else {
				$msg = "problema para inserir usuário, clique <a href='index.php'>aqui</a> e tente novamente";
				$msg = "Error: " . $sql . "<br>" . $conn->error;
			}
			$acao="msg";
			require_once('mysql.php');

		}
	}
} else {
	$titulo = 'LOGIN';
	$acao="";
	$nova_acao="login";
}

} 


if(isset($acao))
if( $acao=="msg" ){
?>
<div class="div">
<?php echo $msg; ?>
</div>
	</div>
<?php
}


if(isset($acao))
 if($acao=="") {

?>



<!DOCTYPE html>
<html>
<head>
	<title>TELA LOGIN</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body style="background:#333">
	<form action="index.php?acao=<?php if(isset($nova_acao)) echo $nova_acao; ?>" method="post">
		 <div class="container-fluid">
			 <div class="row">
				 <div class="col">
					 <center><img width="200" src="data:image/png;base64,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" alt="logo da faculdade" class="img-fluid"></center>
<?php 
}


if(isset($acao))
if( $acao=="msg"  && false ){
?>
<div class="div">
<?php echo $msg; ?>
</div>
	</div>
<?php
}else if($acao=="" ){
?>
<form action="index.php?acao=<?php echo $nova_acao; ?>" method="post">
     	     		<?php 
						if(isset($erro)) 
							echo $erro 
					?>
     	     	<label>Digite seu e-mail</label>
     	<input type="email" name="email" placeholder="E-mail"><br>

     	<label>Digite sua senha</label>
     	<input type="password" name="password" placeholder="Senha"><br>


     	<button type="submit">Login</button>
          <a href="index.php?acao=novoUsuario" class="ca">Não possui uma conta? Crie a sua conta aqui</a>
     </form>
<?php
} else if( $acao == 'novoUsuario' ) {
//die("Hello");
//include("header.html");
include("header2.html");
?>



<form action="index.php?acao=<?php echo $nova_acao; ?>" method="post">
     <body style="background:#333">
     	<h2>FAÇA SEU CADASTRO    </h2>
     	
	<?php if(isset($erro)) echo $erro; ?> 

          <label>Digite e-mail</label>
                         <input type="text" 
                      name="email" 
                      placeholder="Digite seu E-mail"><br>
          
          <label>Digite Seu Nome</label>
                         <input type="text" 
                      name="nome" 
                      placeholder="Digite Seu Nome"><br>
          

     	<label>Digite Sua Senha</label>
          <input type="password" 
                 name="password" 
                 placeholder="Digite sua Senha"><br>
     	

          <label>Confirme Sua Senha</label>
          <input type="password" 
                 name="re_password" 
                 placeholder="Confirme Sua Senha"><br>

     	<button type="submit">Inscrever-se</button>
          <a href="index.php" class="ca">Já possui uma conta? Faça seu Login</a>
     </form>
     
     
<?php
    include("footer.html");
} else if( $acao == 'cadastrarCertificado' ) {
?>
	<?php /* include("header.html"); ?>
	<form action="index.php?acao=salvaCertificado" method="post" enctype="multipart/form-data">
	<input type="file" name="certificado"><br>
	<input type="submit" name="Upload" value="Upload">
	</form>
	
	<?php include("footer.html"); */?>
	<?php	
	include("header_msg.html");
	echo "
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:lightblue'>
		
			<form action='index.php?acao=salvaCertificado' method='post' enctype='multipart/form-data'>
				<input type='file' name='certificado'><br>
				<input type='submit' name='Upload' value='Upload'>
			</form>
			<a href='/index.php'>Voltar</a>
			</div>
		</div>      ";
	include("footer_msg.html");
	die();	
	?>
	
	
	
	
	
<?php
} else if( $acao == 'salvaCertificado' ) {

	require_once('file.php');

	echo "<form>";
	$target_dir = $filepath;
	$target_file = $target_dir . basename($_FILES["certificado"]["name"]);
	$res=move_uploaded_file($_FILES["certificado"]["tmp_name"], $target_file);
	//var_dump($res);
	require_once('mysql.php');
	$sql = "INSERT INTO certificado (arquivo) values ('{$_FILES["certificado"]["name"]}')";
	if ($conn->query($sql) === TRUE) {
	
			include("header_msg.html");
			echo "
				<div class='h-100 d-flex align-items-center justify-content-center'>
					<div style='background:green'>
				
						<div class='alert alert-success' role='alert'>
							  Arquivo upload com sucesso <a href='index.php'>aqui para voltar</a>
						</div>
			
					</div>
				</div>      ";
			include("footer_msg.html");
			die();	

	} else {
		$msg = "Error: " . $sql . "<br>" . $conn->error;
		
	}
	echo $msg;
	echo "</form>";

/*
$uploaddir = 'certificados';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";

*/
} else if( $acao == 'listaCertificado' ) {
	require_once('mysql.php');
	include("header_msg.html");
	$sql = "SELECT * FROM certificado";
	
	echo "
		<div class='h-100 d-flex align-items-center justify-content-center'>
			<div style='background:lightblue'>
		
				<div class='alert alert-success' role='alert'>
	";
	
	
	$result = $conn->query($sql);
	echo "<form>";

	while($row = $result->fetch_assoc()) {
		echo "<a href='certificado.php?certificado={$row['id']}&ler=1'><img width='100px' src='./certificados/{$row['arquivo']}' border=1></a>";
	}
	echo "</form>";
	echo "<a href='/index.php'>Voltar</a>";
	echo "
						
						</div>
			
					</div>
				</div>      ";	

	include("footer_msg.html");

} else if( $acao == 'mapearCertificado' ) {
	require_once('certificado.php');
}
?>
</body>
</html>	
