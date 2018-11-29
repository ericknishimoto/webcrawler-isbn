<?php
	
	include("../simple_html_dom.php");

	$listaIsbn = ["9788547200121", "978854720012", "856694352X"]; //Localizado, não localizado, sem estoque
	
	foreach ($listaIsbn as $isbn) {
		
		$html = file_get_html("https://www.livrariacultura.com.br/busca?N=0&Ntt=" . $isbn); //URL do site
		
		if (!$html->find('div[class=product-ev-unavailable]') && !$html->find('div[class=price-big-ev]')) {
			?>
			<p><?= $isbn ?><span>: Não localizado.</span></p>
			<?php ;
		} elseif ($html->find('div[class=price-big-ev]')) {
			$valor = $html->find('div[class=price-big-ev]',0)->plaintext;
			?>
			<p><?= $isbn ?><span>: <?= $valor ?></span></p>
			<?php ;
		} else {
			?>
			<p><?= $isbn ?><span>: Localizado, mas sem preço.</span></p>
			<?php ;
		}

	}

