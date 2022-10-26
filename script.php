<?php
set_time_limit(0);
define( 'MYSQL_HOST', 'localhost' );
define( 'MYSQL_USER', 'admin' );
define( 'MYSQL_PASSWORD', '' );
define( 'MYSQL_DB_NAME', 'aibuy' );

try
{
    $PDO = new PDO( 'mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
}
catch ( PDOException $e )
{
    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
}
$PDO->exec("set names utf8");
$PDO->exec("SET time_zone = '-02:00'");
foreach ($_POST['dados'] as $key => $value) {
    if (!empty($value['jogo'])) {
	    $jogo = html_entity_decode(trim($value['jogo']));
	    $sth = $PDO->prepare('SELECT id, nome FROM jogos WHERE nome = "' . $jogo . '"');
		$sth->execute();
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	    if (!empty($rows)) {
	        $jogo_id = $rows[0]["id"];
	    } else {
	        // Insere o jogo, caso nao exista na base
	        $sql = 'INSERT INTO jogos (nome, imagem) VALUES ("' . $jogo . '", "' . $value['img'] . '")';
	        $stmt = $PDO->prepare( $sql );
	        $result = $stmt->execute();
			$jogo_id = $PDO->lastInsertId();
	    }
	
	    if (!empty($value['link'])) {
			$sth = $PDO->prepare('SELECT link FROM anuncios WHERE link = "' . $value['link'] . '"');
			$sth->execute();
			$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		    if (empty($rows)) {
				$sql = 'SELECT preco, link, data, descricao FROM anuncios WHERE jogo_id = "' . $jogo_id . '" ORDER BY preco ASC, data DESC';
		    	$result = $PDO->query( $sql );
		    	$rows = $result->fetchAll( PDO::FETCH_ASSOC ); 
				$precos = '<ul>';
				foreach ($rows as $row) {
					$precos .= '<li><a href="' . $row['link'] . '">R$ ' . number_format($row['preco'], 2, ',', '.') . '</a> ' .  $row['descricao'] . ' (' . date('d/m/Y H:i:s', strtotime($row['data'])) . ')</li>';
				}
				$precos .= '</ul>';
	
		        // Insere o anuncio, caso nao exista na base
		        $sql = 'INSERT INTO anuncios (jogo_id, tipo, jogo, descricao, vendedor, local, situacao, preco, link) VALUES ("' .
		            $jogo_id . '", "' .
		            html_entity_decode(trim($value['tipo'])) . '", "' .
		            $jogo . '", "' .
		            html_entity_decode(trim($value['descricao'])) . '", "' .
		            html_entity_decode(trim($value['vendedor'])) . '", "' .
		            html_entity_decode(trim($value['local'])) . '", "' .
		            html_entity_decode(trim($value['situacao'])) . '", ' .
		            str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $value['preco']))) . ', "' .
		            $value['link'] . '")';
		            print_r($sql);echo "\n\n";
		        $stmt = $PDO->prepare( $sql );
		        $result = $stmt->execute();
			
				$to = 'teste@gmail.com';
				$subject = '[AIBuy] ' . $jogo . ' - ' . $value['preco'] . ' (' . $value['local'] . ')';
	
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
				// Additional headers
				//$headers .= 'To: ' . $to . "\r\n";
				$headers .= 'From: teste@gmail.com' . "\r\n";
	
				$body = (
					"<!DOCTYPE html>
					<html>
					<body>" .
					'<a href="' . $value['jogo_url'] . '">' . $jogo . '</a><br />' . 
				        '<img src="' . $value['img'] . '" /><br />' . 
					$value['local'] . "<br />" .
					'Vendedor: <a href="' . $value['vendedor_url'] . '">' . $value['vendedor'] . '</a><br />' .
					"Descrição: " . $value['descricao'] . "<br />" . 
					"Situação: " . $value['situacao'] . "<br />" . 
					$value['link'] . "<br /><br />" . 
					"Preço no anúncio: " . $value['preco'] . "<br /><br />" . 
					"Todos os preços anteriores: <br /><br />" .
					$precos . 
					"</body>
					</html>"
				);
	
		        mail($to, $subject, $body, $headers);
		    }
		}
    }
}
$PDO = null;