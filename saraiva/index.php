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
	
	//Request com busca da ISBN
	$html = file_get_html("https://busca.saraiva.com.br/busca?q=".$isbn['isbn']); //URL do site
	?>

	<!-- Mostra o nº de identf. da request -->
	<p><span><?= $cont ?></span> - <?= $isbn['isbn'] ?></p>
	
	<?php

	//Se NÃO localizar div de produto não encontrado E localizar div que contém o preço...
	if (!$search = $html->find('div[class=nm-not-found-container]') && $search2 = $html->find('div[class=nm-price-value]')) {
		
		//Busca a li que contém o preço
		$search = $html->find('li[class=nm-product-item]',0);
		//Captura a SKU do produto
		$sku = $search->getAttribute('data-pid');

		//Captura o JSON da api referente a SKU
		$json = file_get_contents("https://api.saraiva.com.br/sc/produto/pdp/$sku/0/0/1/");

		//Decoda o JSON
		$decodedData = json_decode($json);

		//Extrai o preço do JSON
		$string_number = $decodedData->price_block->price->final;	

		//Transforma vírgulas em pontos, para o MySql
		$valor = floatval(str_replace(',', '.', str_replace('.', '', $string_number)));

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

	} else { //Não localizado valor entao...

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
    saraiva = '{$valor}'
    where ISBN = '{$isbn}'
	";

	return mysqli_query($conexao, $query);
}
