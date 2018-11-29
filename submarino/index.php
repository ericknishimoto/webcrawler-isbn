<?php
	
	include("../simple_html_dom.php");

	$listaIsbn = ["9788547200121", "978854720012", "9788547200145"]; //Localizado, não localizado, sem estoque

	foreach ($listaIsbn as $isbn) {

		$html = file_get_html("https://www.submarino.com.br/busca/" . $isbn); //URL do site
		
		if ($search = $html->find('div[class=EmptyPage__Container-s1u8xkxt-3]')) {
			?>
			<p><?= $isbn ?><span>: Não localizado.</span></p>
			<?php ;
		} elseif ($html->find('div[class=card-product-price text-highlight-1] span[!content=]')) {
			$valor = $html->find('div[class=card-product-price text-highlight-1] span[!content=]',0)->plaintext;
			?>
			<p><?= $isbn ?><span>: <?= $valor ?></span></p>
			<?php ;
		} else {
			?>
			<p><?= $isbn ?><span>: Localizado, mas sem preço.</span></p>
			<?php ;
		}

	}