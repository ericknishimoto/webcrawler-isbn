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

	//ISBN
	var_dump($isbn['isbn']);

	$html = file_get_html("https://www.livrariacultura.com.br/busca?N=0&Ntt=" . $isbn['isbn']); //URL do site
	
	//Se localizar div de produto não encontrado or NÃO localiza div de produto encontrado...
	if ($html->find('div[class=product-ev-unavailable]') or !$html->find('div[class=price-big-ev]')) {
		
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
	} else {

		//Busca a div com preço e guarda
		$valor = $html->find('div[class=price-big-ev]',0)->plaintext;
		$valor = str_replace('R$', '', $valor); // remove spaces
		$valor = str_replace(' ', '', $valor); // remove spaces
		echo $valor;

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
    cultura = '{$valor}'
    where ISBN = '{$isbn}'
	";

	return mysqli_query($conexao, $query);
}