<!DOCTYPE HTML>

<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
    
    <title>AI Buy!</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php
//$result = file_get_contents('https://ludopedia.com.br/anuncios?tp_anuncio=M&tp_jogo=1&pagina=1');
$result = file_get_contents('https://ludopedia.com.br/anuncios?tp_anuncio=M&tp_jogo=1&pagina=0');
$totalEncontradosPos = strpos($result, 'Total Encontrados');
$primeiraDivPos = strpos($result, '</span>', $totalEncontradosPos);
//print_r($totalEncontradosPos); echo '<br>';
//print_r($primeiraDivPos); echo '<br>';
$totalEncontrados = substr($result, $totalEncontradosPos, $primeiraDivPos - $totalEncontradosPos);

//print_r($totalEncontrados); echo '<br>';
$ulJogosPos = strpos($result, '<ul class= "row" style="margin-top: 5px;">', $totalEncontradosPos);
$ulJogosFimPos = strpos($result, '<div class="text-center">', $totalEncontradosPos);

$ulJogos = substr($result, $ulJogosPos, $ulJogosFimPos - $ulJogosPos);

print_r($ulJogos);
//print_r(ul_to_array($ulJogos));
//print_r($totalEncontrados);
?>

</body>

<script>
    $(document).ready(function () {
        var arrData = new Array();
        $('ul li').each(function (i) {
            if (i < 100) {
                // Tipo (Venda ou Leilao)
                var tipo = $(this).find('.box-anuncio-title').html();
                // Nome do Jogo
                var jogo = $(this).find('a.link-elipsis').html();
                // Descricao
                var descricao = $(this).find('span.anuncio-sub-titulo').html()
                // Vendedor
                var vendedor = $(this).find('dl dd a').html();
                // Estado - Cidade
                var local = $(this).find('dl dd').eq(1).html();
                // Estado do jogo (Lacrado, Novo ou Usado)
                var situacao = $(this).find('dl dt').last().html().match(/\(([^)]+)\)/)[1];
                // Preco ou Proximo Lance
                var preco = $(this).find('dl dd.proximo_lance').html();
                // Link do anuncio
                var link = $(this).find('dl a.btn-xs').prop('href');
                // Link da imagem
                var img = $(this).find('img.img-rounded').prop('src');

                arrData[i] = {
                    tipo: tipo,
                    jogo: jogo,
                    descricao: descricao,
                    vendedor: vendedor,
                    local: local,
                    situacao: situacao,
                    preco: preco,
                    link: link,
                    img: img
                };
            } else {
                break;
            }
        });

        $.ajax({
            method: "POST",
            url: "script.php",
            data: {dados: arrData}
        })
            .done(function (resposta) {
                console.log(resposta);
            });
    });
</script>

</html>