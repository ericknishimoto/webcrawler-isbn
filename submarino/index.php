<?php

//Tempo de execução máximo
ini_set('max_execution_time', 108000);

//Biblioteca
include("../simple_html_dom.php");

//Conexao BD
require_once '../conecta.php';

//Mostrar echo em tempo real
while(ob_get_level() > 0) ob_end_clean();   

//Lista números ISBN do BD
$listaIsbn = listaIsbn($conexao); 

//Contagem de requisições
$cont = 0;

//Pra cada ISBN da lista faça
foreach ($listaIsbn as $isbn) {

	//Caso pare no meio da array, para continuar
	if ($cont < 21107) {
		$cont++;
		continue;
	}

	//ISBN
	var_dump($isbn['isbn']);

	$html = file_get_html("https://www.submarino.com.br/busca/" . $isbn['isbn']); //URL do site
	
	//Se localizar div de produto não encontrado...
	if ($search = $html->find('div[class=EmptyPage__Container-s1u8xkxt-3]')) {

		?>
		<!-- Mostra valor encontrado -->
		<p><span><?= $cont ?></span> - Não Localizado</p>
		<?php

		//Incrementa contagem de requisições
		$cont++;
	
		//Sleep para mostrar echo em tempo real
		flush();
		sleep(0.1);

	//Senão se localizar div de produto localizado...
	} elseif ($html->find('div[class=card-product-price text-highlight-1] span[!content=]')) {

		//Busca a div com preço e guarda
		$valor = $html->find('div[class=card-product-price text-highlight-1] span[!content=]',0)->plaintext;

		//Transforma vírgulas em pontos, para o MySql
		$valor = floatval(str_replace(',', '.', str_replace('.', '', $valor)));

		//Insere no BD
		insereValor($conexao, $isbn['isbn'], $valor);
		
		?>
		<!-- Mostra valor encontrado -->
		<p>R$ <?= $valor ?></p>

		<?php
		
		//Incrementa contagem de requisições
		$cont++;
		
		//Sleep para mostrar echo em tempo real
		flush();
		sleep(0.1);
	} else {
		
		?>
		<!-- Mostra valor encontrado -->
		<p><span><?= $cont ?></span> - Localizado mas sem preço</p>
		<?php

		//Incrementa contagem de requisições
		$cont++;
	
		//Sleep para mostrar echo em tempo real
		flush();
		sleep(0.1);
	}

}

echo "FINALIZADO";

function listaIsbn($conexao) {
	$listaIsbn = array();
	$query = "SELECT `isbn` FROM `isbn`";
	$resultado = mysqli_query($conexao, $query);
	while ($isbn = mysqli_fetch_assoc($resultado)) {
		array_push($listaIsbn, $isbn);
	}
	
	return $listaIsbn;
}

function insereValor ($conexao, $isbn, $valor) { 
	$query = "UPDATE `isbn` set
    submarino = '{$valor}'
    where ISBN = '{$isbn}'
	";

	return mysqli_query($conexao, $query);
}