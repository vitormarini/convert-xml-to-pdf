<?php    
/*
 * Description: Programa que auxilia e documenta a exportação de arquivo XML para PDF
 * DATA: 25/05/2021
 */
$pasta = "arquivos/";
$temp  = $_FILES['arquivo_xml']['tmp_name'];
$xmlName   = $_FILES['arquivo_xml']['name'];

ini_set( "display_errors" , "on");
error_reporting( E_ERROR );
define('FPDF_FONTPATH', "{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/fpdf/font/");

include_once ("{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/fpdf/code128.php");
require_once "{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/functions/functions.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/readXML/toPDF/responseXML.php";
    
$xml   = readXML($xmlName);
$dados = retornaDados($xml,$xmlName);

function cabecalho( $pdf, $pagina, $total ) {
    global $dados;
    
    $pdf->SetLineWidth( 0.1 );
    $pdf->SetFillColor( 209, 209, 209 );
    $pdf->SetDrawColor( 177, 177, 177 );

    $intLinha = 5;
    $pdf->SetFont('helvetica',  '', 8);
    $pdf->Rect(  5, $intLinha     , 155, 8 );
    $pdf->SetXY( 5, $intLinha + 1 );
    $razao_social = strtoupper($dados['razao_social_emitente']);
    $pdf->MultiCell( 155, 3, utf8_decode( "RECEBEMOS DE {$razao_social} OS PRODUTOS/SERVIÇOS CONSTANTES NA NOTA FISCAL INDICADA AO LADO" ),0,'L');

    $pdf->SetFont('helvetica',  '', 6);
    $pdf->Rect(  5, $intLinha +  8,  50, 9 );
    $pdf->Text(  6, $intLinha + 11, utf8_decode( "DATA DE RECEBIMENTO" ) );
    $pdf->Rect( 55, $intLinha +  8, 105, 9 );                      
    $pdf->Text( 56, $intLinha + 11, utf8_decode( "IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR" ) );

    $pdf->SetFont('helvetica', 'B' , 14);
    $pdf->Rect(  160, $intLinha , 45, 17 );       
    $pdf->SetXY( 160, $intLinha + 1 );
    $pdf->Cell(   45, 5, utf8_decode( "NF-e" ), 0, 0, 'C' );        

    $numero_nota = '';
    for ( $intCont = 0; $intCont < strlen(str_pad($dados['numero_nota'],9,'0',STR_PAD_LEFT)); $intCont += 3 ) {
        $numero_nota .= substr(str_pad($dados['numero_nota'],9, '0',STR_PAD_LEFT ), $intCont,3 ) . ".";
    }
    $numero_nota = substr($numero_nota,0,-1 );

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY( 160, $intLinha + 6 );
    $pdf->Cell(   45, 6, utf8_decode( "Nº {$numero_nota}" ), 0, 0, 'C' );
    $pdf->SetXY( 160, $intLinha + 10 );
    $pdf->Cell(   45, 5, utf8_decode( "Série: {$dados['serie']}" ), 0, 0, 'C' );

    $intLinha = 25;
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text( 5, $intLinha, str_pad("-",189,"-",STR_PAD_RIGHT ) );

    $intLinha = 25;
    $pdf->Rect( 5, $intLinha, 79, 33 );

    $tam_razao_social = strlen($dados['razao_social_emitente']) > 30 ? 10 : 12 ;
    $p = strlen($dados['razao_social_emitente']) > 30 ? 4 : 0;
    $pdf->SetFont('helvetica', 'I', 7);
    $pdf->Text(   27, $intLinha + 3, utf8_decode( "IDENTIFICAÇÃO DO EMITENTE" ) );
    
    
    $pdf->SetFont('helvetica', 'B', $tam_razao_social);
    $pdf->SetXY( 8, $intLinha + 6 + $p );
    $pdf->MultiCell( 80, 4, utf8_decode( $dados['razao_social_emitente'] ),0,'C');
    
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5, $intLinha + 14 + $p );
    $pdf->MultiCell( 80, 4, utf8_decode( $dados['endereco_emitente'] .', '.$dados['numero_emitente'] ),0,'C');
    
    $cont_endereco = strlen("ROD ANHANGUERA KM 52 + 350m C/ROD PRES");
    $pdf->Cell( 70, $intLinha - 22, utf8_decode( $dados['bairro_emitente'] .' - '. $dados['cep_emitente'] ),0 ,0, 'C' );
    $pdf->Cell( -70   , $intLinha - 15, utf8_decode( $dados['municipio_emitente'] .' - '. $dados['uf_emitente'] .' - '. mascaraTelefone($dados['fone_emitente'])    ),0 ,0, 'C' );

    $pdf->Rect( 84, $intLinha, 38, 33 );
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Text(  98, $intLinha + 4, utf8_decode( "DANFE" ) );
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetXY( 83, $intLinha + 5  );
    $pdf->MultiCell( 40, 4, utf8_decode( "Documento Auxiliar da Nota Fiscal Eletrônica" ),0,'C');

    $pdf->Text(  87, $intLinha + 17, utf8_decode( "0 - ENTRADA" ) );
    $pdf->Text(  87, $intLinha + 20, utf8_decode( "1 - SAÍDA" ) );
    $pdf->Rect( 108, $intLinha + 15, 6, 4 );
    $pdf->Text( 110, $intLinha + 18, utf8_decode( $dados['tipo_nf'])); #($dados['serie'] == '4' ? '0' : '1' ) ) );

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Text( 87, $intLinha +   25, utf8_decode( "Nº {$numero_nota}" ) );
    $pdf->Text( 87, $intLinha + 28.5, utf8_decode( "SÉRIE: ".$dados['serie'] ) );
    $pdf->Text( 87, $intLinha +   32, utf8_decode( "FOLHA: {$pagina}/{$total}" ) );

    $pdf->Rect( 122, $intLinha     , 83, 13 );               
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor( 0 );
    $pdf->SetDrawColor( 0 );
    $pdf->Code128( 123.5, 26.5, $dados['chave_acesso'], 80, 10 );

    $pdf->SetFillColor( 209, 209, 209 );
    $pdf->SetDrawColor( 177, 177, 177 );

    $pdf->Rect( 122, $intLinha + 13, 83, 10, "DF" );
    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text( 123, $intLinha + 15, utf8_decode( "CHAVE DE ACESSO" ) );
    $pdf->SetFont('helvetica', 'B', 8.5);
    $strChave = '';
    for ( $intCont = 0; $intCont < strlen( $dados['chave_acesso'] ); $intCont += 4 ) {
        $strChave .= substr( $dados['chave_acesso'], $intCont, 4 ) . " ";
    }
    $pdf->Text( 122.5, $intLinha + 19.5, utf8_decode( $strChave ) );

    $pdf->Rect( 122, $intLinha + 23, 83, 10 );
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Text( 136, $intLinha + 26, utf8_decode( "Consulta de autenticidade no portal nacional da" ) );
    $pdf->Text( 143, $intLinha + 29, utf8_decode( "NF-e www.nfe.fazenda.gov.br/portal" ) );
    $pdf->Text( 146, $intLinha + 32, utf8_decode( "ou no site da Sefaz Autorizadora" ) );

    $pdf->Rect( 5, $intLinha + 33, 117, 8 );
    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text( 5.5, $intLinha + 35.5, utf8_decode( "NATUREZA DA OPERAÇÃO" ) );        
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text( 6, $intLinha + 40, utf8_decode( "{$dados['natureza_operacao']}" ) );

    $pdf->Rect( 122, $intLinha + 33,  83, 8, "DF" );
    $pdf->SetFont('helvetica', '', 6);        
    $pdf->Text( 122.5, $intLinha + 35.5, utf8_decode( "PROTOCOLO DE AUTORIZAÇÃO DE USO" ) );
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetXY( 125, $intLinha + 37 );
    $pdf->Cell( 76, 4, utf8_decode( "{$dados['protocolo_autorizacao']}" ), 0, 0, 'C' );

    $intLinha = 66;
    $pdf->Rect( 5, $intLinha, 66, 8 );
    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text( 5.5, $intLinha + 2.5, utf8_decode( "INSCRIÇÃO ESTADUAL" ) );
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text( 6, $intLinha + 7, utf8_decode( $dados['ie_emitente'] ));

    $pdf->Rect( 71, $intLinha, 67, 8 );
    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text( 71.5, $intLinha + 2.5, utf8_decode( "INSCRIÇÃO ESTADUAL SUB. TRIBUTARIA" ) );
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text( 72, $intLinha + 7, utf8_decode("") );

    $pdf->Rect(138, $intLinha, 67, 8 );
    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(138.5, $intLinha + 2.5, utf8_decode( "CNPJ" ) );
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(139, $intLinha + 7, utf8_decode( formataCpfCnpj($dados['cnpj_emitente'])) );
}

function rodape( $pdf ) {
    global $dados;
    
    #validações do campo observacao
    $cont_obs   = strlen($dados['informacoes_adicionais']);
    $tamanho    = $cont_obs > 700 ?  34 : 21;        
    $font       = $cont_obs > 700 ?   5 : 6;        
    $pula_linha = $cont_obs > 700 ? 160 : 130;  
    
    $intLinha = $cont_obs > 700 ? 245 : 253 ;
    $pdf->Line( 5, $intLinha, 205, $intLinha );
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text( 5, $intLinha + 3, utf8_decode( "CÁLCULO DO ISSQN" ) );

    $intLinha = $cont_obs > 700 ? 249 : 257 ; 
    $pdf->Rect(   5, $intLinha, 50, 8 );
    $pdf->Rect(  55, $intLinha, 50, 8 );
    $pdf->Rect( 105, $intLinha, 50, 8 );
    $pdf->Rect( 155, $intLinha, 50, 8 );

    $pdf->SetFont('helvetica', '', 7);
    $pdf->Text(   6, $intLinha + 3, utf8_decode( "INSCRIÇÃO MUNICIPAL" ) );
    $pdf->Text(  56, $intLinha + 3, utf8_decode( "VALOR TOTAL DOS SERVIÇOS" ) );
    $pdf->Text( 106, $intLinha + 3, utf8_decode( "BASE DE CALCULO DO ISSQN" ) );
    $pdf->Text( 156, $intLinha + 3, utf8_decode( "VALOR DO ISSQN" ) );                                                                                                     

    $pdf->SetFont('helvetica', '', 7);
    $pdf->Text(  56, $intLinha + 7, utf8_decode( "" ) );
    $pdf->Text(  56, $intLinha + 7, utf8_decode( "0,00" ) );
    $pdf->Text( 106, $intLinha + 7, utf8_decode( "0,00" ) );
    $pdf->Text( 156, $intLinha + 7, utf8_decode( "0,00" ) );

    $pdf->Rect(   5, $intLinha + 11.5, 150, $tamanho );
    $pdf->Rect( 155, $intLinha + 11.5,  50, $tamanho );

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text( 5, $intLinha + 11, utf8_decode( "DADOS ADICIONAIS" ) );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(   6, $intLinha + 14, utf8_decode( "INFORMAÇÕES COMPLEMENTARES ") );
    $pdf->Text( 156, $intLinha + 14, utf8_decode( "RESERVADO AO FISCO" ) );                

    $pdf->SetFont('helvetica', '', $font);
    $arrObservacao = explode( '\n', wordwrap( utf8_decode( $dados['informacoes_adicionais']), $pula_linha, '\n' ) );

    $intLinha += 17;
    foreach ( $arrObservacao as $texto ) {
        $pdf->Text( 7, $intLinha, $texto );
        $intLinha += $cont_obs > 700 ? 2 : 3;
    }
}

    $intNota = 1;
    $pdf = new PDF_Code128( 'P', 'mm', 'A4' );

    $pdf->AddPage();

    $pdf->AliasNbPages();
    $intPagina      = 1;
    $qtde_itens = count($dados['itens']);    
    if($qtde_itens >= 6 && $qtde_itens < 23 ){
        $intTotalPagina = 2;
    }else {            
        $intTotalPagina = ceil( $qtde_itens / 9 );
    }
    //Pode ser que precisaremos inserir mais condições aqui.
    cabecalho( $pdf, $intPagina, $intTotalPagina );

    $intLinha = 78;
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text( 5, $intLinha, utf8_decode( "DESTINATÁRIO/REMETENTE" ));

    $intLinha = 79;
    $pdf->Rect(  5, $intLinha, 124, 8 );
    $pdf->Rect(129, $intLinha,  41, 8 );
    $pdf->Rect(170, $intLinha,  35, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "NOME/RAZÃO SOCIAL" ) );
    $pdf->Text(129.5, $intLinha + 2.5, utf8_decode( "CNPJ/CPF"          ) );
    $pdf->Text(170.5, $intLinha + 2.5, utf8_decode( "DATA DA EMISSÃO"   ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(  6, $intLinha + 6.5, utf8_decode( "{$dados['razao_social_destinatario']}" ) );
    $pdf->Text(135, $intLinha + 6.5, utf8_decode( formataCpfCnpj( "{$dados['cnpj_destinatario']}",  "J" ) ) );
    $pdf->Text(177, $intLinha + 6.5, utf8_decode( "{$dados['data_emissao']}" ) );

    $intLinha = 87;
    $pdf->Rect(  5, $intLinha, 95, 8 );
    $pdf->Rect(100, $intLinha, 50, 8 );
    $pdf->Rect(150, $intLinha, 20, 8 );
    $pdf->Rect(170, $intLinha, 35, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "ENDEREÇO"              ) );
    $pdf->Text(100.5, $intLinha + 2.5, utf8_decode( "BAIRRO/DISTRITO"       ) );
    $pdf->Text(150.5, $intLinha + 2.5, utf8_decode( "CEP"                   ) );
    $pdf->Text(170.5, $intLinha + 2.5, utf8_decode( "DATA DE SAÍDA/ENTRADA" ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(  6, $intLinha + 6.5, utf8_decode( substr($dados['endereco_destinatario'],0,45) .','. $dados['numero_destinatario'] ) );
    $pdf->Text(101, $intLinha + 6.5, utf8_decode( substr("{$dados['bairro_destinatario']}",0,23) ) );
    $pdf->Text(152, $intLinha + 6.5, utf8_decode( mascaras( "{$dados['cep_destinatario']}", 4 ) ) );
    $pdf->Text(177, $intLinha + 6.5, utf8_decode( "{$dados['data_saida_entrada']}" ) );

    $intLinha = 95;
    $pdf->Rect(  5, $intLinha, 65, 8 );
    $pdf->Rect( 70, $intLinha, 50, 8 );
    $pdf->Rect(120, $intLinha, 10, 8 );
    $pdf->Rect(130, $intLinha, 40, 8 );
    $pdf->Rect(170, $intLinha, 35, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "MUNICÍPIO"             ) );
    $pdf->Text( 70.5, $intLinha + 2.5, utf8_decode( "FONE/FAX"              ) );
    $pdf->Text(120.5, $intLinha + 2.5, utf8_decode( "UF"                    ) );
    $pdf->Text(130.5, $intLinha + 2.5, utf8_decode( "INSCRIÇÃO ESTADUAL"    ) );
    $pdf->Text(170.5, $intLinha + 2.5, utf8_decode( "HORA DE SAÍDA"         ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(  6, $intLinha + 6.5, utf8_decode( "{$dados['municipio_destinatario']}"  ) );    
    $pdf->Text( 71, $intLinha + 6.5, utf8_decode( mascaraTelefone($dados['fone_destinatario'])       ) );
    $pdf->Text(123, $intLinha + 6.5, utf8_decode( "{$dados['uf_destinatario']}"         ) );
    $pdf->Text(131, $intLinha + 6.5, utf8_decode( "{$dados['ie_destinatario']}"         ) );
    $pdf->Text(180, $intLinha + 6.5, utf8_decode( "{$dados['hora_saida']}"              ) );

    $intLinha = 106;
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text( 5, $intLinha, utf8_decode( "FATURA" ) );

    $pdf->Rect( 5, $intLinha + 1, 200, 4 );
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Text( 6, $intLinha + 4, utf8_decode( "Número"     ) );
    $pdf->Text(24, $intLinha + 4, utf8_decode( "Data Vcto"  ) );
    $pdf->Text(40, $intLinha + 4, utf8_decode( "Valor"      ) );

    $pdf->Text(56, $intLinha + 4, utf8_decode( "Número"     ) );
    $pdf->Text(74, $intLinha + 4, utf8_decode( "Data Vcto"  ) );
    $pdf->Text(90, $intLinha + 4, utf8_decode( "Valor"      ) );

    $pdf->Text(106, $intLinha + 4, utf8_decode( "Número"    ) );
    $pdf->Text(124, $intLinha + 4, utf8_decode( "Data Vcto" ) );
    $pdf->Text(140, $intLinha + 4, utf8_decode( "Valor"     ) );

    $pdf->Text(156, $intLinha + 4, utf8_decode( "Número"    ) );
    $pdf->Text(174, $intLinha + 4, utf8_decode( "Data Vcto" ) );
    $pdf->Text(190, $intLinha + 4, utf8_decode( "Valor"     ) );

    $pdf->SetFont('helvetica', '', 8);
    $intCont   = 1;
    $arrCol[1] = array(   6,  24,  40 );
    $arrCol[2] = array(  56,  74,  90 );
    $arrCol[3] = array( 106, 124, 140 );
    $arrCol[4] = array( 156, 174, 190 );

    $intLinha  = 114;
    $intLinhas = 0;   
    $x         = 0;
    foreach ( $dados['fatura'] as $dupl ){
        
        if ( $intCont > 4 ) {
            $intLinha += 3;
            $intLinhas++;
            $intCont = 1;
        }
        
        $pdf->Text( $arrCol[$intCont][0], $intLinha, utf8_decode( $dados['fatura'][$x]['numero']          ));
        $pdf->Text( $arrCol[$intCont][1], $intLinha, utf8_decode( $dados['fatura'][$x]['data_vencimento'] ));
        $pdf->Text( $arrCol[$intCont][2], $intLinha, utf8_decode( $dados['fatura'][$x]['valor_duplicata'] ));
        $intCont++;    
        $x++;
    }

    $pdf->Rect( 5, 111, 200, ( 3.5 * $intLinhas ) );

    $pdf->SetFont('helvetica', 'B', 9);
    $intLinha += 3;
    $pdf->Text( 5, $intLinha, utf8_decode( "CÁLCULO DO IMPOSTO" ) );

    $intLinha += 1;
    $pdf->Rect(   5, $intLinha, 40, 8 );
    $pdf->Rect(  45, $intLinha, 40, 8 );
    $pdf->Rect(  85, $intLinha, 40, 8 );
    $pdf->Rect( 125, $intLinha, 40, 8 );
    $pdf->Rect( 165, $intLinha, 40, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "BASE DE CÁLCULO DE ICMS" ) );
    $pdf->Text( 45.5, $intLinha + 2.5, utf8_decode( "VALOR DO ICMS" ) );
    $pdf->Text( 85.5, $intLinha + 2.5, utf8_decode( "BASE DE CÁLCULO ICMS ST" ) );
    $pdf->Text(125.5, $intLinha + 2.5, utf8_decode( "VALOR DO ICMS SUBSTITUIÇÃO" ) );
    $pdf->Text(165.5, $intLinha + 2.5, utf8_decode( "VALOR TOTAL DOS PRODUTOS" ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5 , $intLinha + 1.5 );
    $pdf->Cell( 40, 8, utf8_decode( number_format( "{$dados['bc_icms']}"                , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 40, 8, utf8_decode( number_format( "{$dados['valor_icms']}"             , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 40, 8, utf8_decode( number_format( "{$dados['bc_icms_subs']}"           , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 40, 8, utf8_decode( number_format( "{$dados['valor_icms_subs']}"        , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 40, 8, utf8_decode( number_format( "{$dados['valor_total_produto']}"    , 2, ',', '.' ) ), 0, 0, 'C' );

    $intLinha += 8;
    $pdf->Rect(   5, $intLinha, 24, 8 );
    $pdf->Rect(  29, $intLinha, 26, 8 );
    $pdf->Rect(  55, $intLinha, 18, 8 );
    $pdf->Rect(  73, $intLinha, 37, 8 );
    $pdf->Rect( 110, $intLinha, 20, 8 );
    $pdf->Rect( 130, $intLinha, 39, 8 );
    $pdf->Rect( 169, $intLinha, 36, 8, 'DF' );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(   5.5, $intLinha + 2.5, utf8_decode( "VALOR DO FRETE"           ) );
    $pdf->Text(  29.5, $intLinha + 2.5, utf8_decode( "VALOR DO SEGURO"          ) );
    $pdf->Text(  55.5, $intLinha + 2.5, utf8_decode( "DESCONTO"                 ) );
    $pdf->Text(  73.5, $intLinha + 2.5, utf8_decode( "OUTRAS DESP. ACESSÓRIAS"  ) );
    $pdf->Text( 110.5, $intLinha + 2.5, utf8_decode( "VALOR DO IPI"             ) );
    $pdf->Text( 130.5, $intLinha + 2.5, utf8_decode( "VALOR TOTAL DOS IMPOSTOS" ) );
    $pdf->Text( 169.5, $intLinha + 2.5, utf8_decode( "VALOR TOTAL DA NOTA"      ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5 , $intLinha + 2 );
    $pdf->Cell( 24, 8, utf8_decode( number_format( "{$dados['valor_frete']}"                , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 26, 8, utf8_decode( number_format( "{$dados['valor_seguro']}"               , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 18, 8, utf8_decode( number_format( "{$dados['valor_desconto']}"             , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 37, 8, utf8_decode( number_format( "{$dados['outras_despesas']}"            , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 20, 8, utf8_decode( number_format( "{$dados['valor_ipi']}"                  , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->Cell( 39, 8, utf8_decode( number_format( "{$dados['valor_total_impostos']}"       , 2, ',', '.' ) ), 0, 0, 'C' );
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell( 36, 8, utf8_decode( number_format( "{$dados['valor_nf']}"                   , 2, ',', '.' ) ), 0, 0, 'C' );

    $pdf->SetFont('helvetica', 'B', 9);
    $intLinha += 11;
    $pdf->Text( 5, $intLinha, utf8_decode( "TRANSPORTADOR/VOLUMES TRANSPORTADOS" ) );

    $intLinha += 1;
    $pdf->Rect(  5, $intLinha, 100, 8 );
    $pdf->Rect(105, $intLinha, 100, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "RAZÃO SOCIAL" ) );
    $pdf->Text(105.5, $intLinha + 2.5, utf8_decode( "FRETE POR CONTA" ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5, $intLinha + 1.5 );
    $pdf->Cell(100, 8, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['razao_social_transportadora']  : "") ), 0, 0 );

    $arrFrete[0] = "0 - Contratação do Frete por conta do Remetente (CIF)";
    $arrFrete[1] = "1 - Contratação do Frete por conta do Destinatário (FOB)";
    $arrFrete[2] = "2 - Contratação do Frete por conta de Terceiros";
    $arrFrete[3] = "3 - Transporte Próprio por conta do Remetente";
    $arrFrete[4] = "4 - Transporte Próprio por conta do Destinatário";
    $arrFrete[9] = "9 - Sem Ocorrência de Transporte";

    $pdf->Cell( 30, 8, utf8_decode( $arrFrete[$dados['frete_por_conta']] ), 0, 0 );

    $pdf->SetFont('helvetica', '', 6);
    $intLinha += 8;
    $pdf->Rect(  5, $intLinha, 50, 8 );
    $pdf->Rect( 55, $intLinha, 40, 8 );
    $pdf->Rect( 55, $intLinha, 50, 8 );
    $pdf->Rect(105, $intLinha, 100, 8 );

    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "CÓDIGO ANTT" ) );
    $pdf->Text( 55.5, $intLinha + 2.5, utf8_decode( "PLACA DO VEÍCULO" ) );
    $pdf->Text( 95.5, $intLinha + 2.5, utf8_decode( "UF" ) );
    $pdf->Text(105.5, $intLinha + 2.5, utf8_decode( "CNPJ/CPF" ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5 , $intLinha + 2 );

    $placa = ($dados['placa_veiculo'] != "AAA-0000" ? utf8_decode( $dados['placa_veiculo'] ) : "");        
    $pdf->Cell(50, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['codigo_antt'] : "" ) ), 0, 0 );
    $pdf->Cell(40, 7, $placa , 0, 0 );
    $pdf->Cell(10, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['uf_veiculo_transportadora'] : "" )), 0, 0 );
    $pdf->Cell(40, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? mascaras( "{$dados['cnpj_transportadora']}", 2 ): "" )), 0, 0 );        

    $intLinha += 8;
    $pdf->Rect(  5, $intLinha, 100, 8 );
    $pdf->Rect(105, $intLinha,  55, 8 );
    $pdf->Rect(160, $intLinha,  10, 8 );
    $pdf->Rect(170, $intLinha,  35, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "ENDEREÇO" ) );
    $pdf->Text(105.5, $intLinha + 2.5, utf8_decode( "MUNICÍPIO" ) );
    $pdf->Text(160.5, $intLinha + 2.5, utf8_decode( "UF" ) );
    $pdf->Text(170.5, $intLinha + 2.5, utf8_decode( "INSCRIÇÃO ESTADUAL" ) );

    // $endereco = (($dados['frete_por_conta'] != "9" ? $dados['cnpj_transportadora'] == "00000000000000" ? "" : $dados['endereco_transportadora'] : ""));
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5 , $intLinha + 2 );
    $pdf->Cell(100, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $endereco : "")), 0, 0 );
    $pdf->Cell( 50, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['municipio_transportadora']  : "") ), 0, 0 );
    $pdf->Cell( 20, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['uf_transportadora']         : "") ), 0, 0 , "C" );
    $pdf->Cell( 35, 7, utf8_decode( ($dados['frete_por_conta'] != "9" ? $dados['ie_transportadora']         : "") ), 0, 0 );
    
    $intLinha += 8;
    $pdf->Rect(  5, $intLinha, 20, 8 );
    $pdf->Rect( 25, $intLinha, 30, 8 );
    $pdf->Rect( 55, $intLinha, 50, 8 );
    $pdf->Rect(105, $intLinha, 40, 8 );
    $pdf->Rect(145, $intLinha, 30, 8 );
    $pdf->Rect(175, $intLinha, 30, 8 );

    $pdf->SetFont('helvetica', '', 6);
    $pdf->Text(  5.5, $intLinha + 2.5, utf8_decode( "QUANTIDADE"    ) );
    $pdf->Text( 25.5, $intLinha + 2.5, utf8_decode( "ESPÉCIE"       ) );
    $pdf->Text( 55.5, $intLinha + 2.5, utf8_decode( "MARCA"         ) );
    $pdf->Text(105.5, $intLinha + 2.5, utf8_decode( "NUMERAÇÃO"     ) );
    $pdf->Text(145.5, $intLinha + 2.5, utf8_decode( "PESO BRUTO"    ) );
    $pdf->Text(175.5, $intLinha + 2.5, utf8_decode( "PESO LIQUIDO"  ) );

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY( 5 , $intLinha + 2 );
    $pdf->Cell(20, 8, utf8_decode( number_format($dados['volume'],2,",",".") ), 0, 0 );
    $pdf->Cell(30, 8, utf8_decode( $dados['especie']   ), 0, 0 );
    $pdf->Cell(50, 8, utf8_decode( $dados['marca']     ), 0, 0 );
    $pdf->Cell(40, 8, utf8_decode( $dados['numeracao'] ), 0, 0 );
    $pdf->Cell(30, 8, utf8_decode( number_format($dados['peso_bruto']     ,2,",",".") ), 0, 0 );
    $pdf->Cell(30, 8, utf8_decode( number_format($dados['peso_liquido']   ,2,",",".") ), 0, 0 );

    $pdf->SetFont('helvetica', 'B', 9);
    $intLinha += 11.2;
    $pdf->Text( 5, $intLinha, utf8_decode( "DADOS DO PRODUTO/SERVIÇO" ) );

    if($dados['valor_ipi_dev'] > 0 ){
        $intLinha += 1;
        $pdf->Rect(   5, $intLinha, 15, 7, 'DF' );
        $pdf->Rect(  20, $intLinha, 43, 7, 'DF' );
        $pdf->Rect(  63, $intLinha, 10, 7, 'DF' );
        $pdf->Rect(  73, $intLinha,  5, 7, 'DF' );
        $pdf->Rect(  78, $intLinha,  6, 7, 'DF' );
        $pdf->Rect(  84, $intLinha,  6, 7, 'DF' );        
        $pdf->Rect( 90, $intLinha, 9, 7, 'DF' );
        
        $pdf->Rect(  99, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 112, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 125, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 138, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 151, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 164, $intLinha, 20, 7, 'DF' );
        
        $pdf->Rect( 164, $intLinha + 3.5,  10, 3.5, 'DF' );
        $pdf->Rect( 174, $intLinha + 3.5,  10, 3.5, 'DF' );
        
        
        $pdf->Rect( 184, $intLinha, 21, 7, 'DF' );
        $pdf->Rect( 184, $intLinha + 3.5,  8, 3.5, 'DF' );
        $pdf->Rect( 192, $intLinha + 3.5,  13, 3.5, 'DF' );

        $pdf->SetFont('helvetica', 'B', 5);

        $pdf->Text( 5.5, $intLinha + 4.5, utf8_decode( "COD.PRODUTO"                  ) );
        $pdf->Text(25, $intLinha + 4.5, utf8_decode( "DESCRIÇÃO DO PRODUTO/SERVIÇO"   ) );
        $pdf->Text(64.5, $intLinha + 4.5, utf8_decode( "NCM SH"                       ) );
        $pdf->Text(73.8, $intLinha + 4.5, utf8_decode( "CST"                          ) );
        $pdf->Text(78.5, $intLinha + 4.5, utf8_decode( "CFOP"                         ) );
        $pdf->Text(84.5, $intLinha + 4.5, utf8_decode( "UNID."                        ) );        
        $pdf->Text( 91, $intLinha + 4.5, utf8_decode( "QUANT."                        ) );
        
        $pdf->Text( 102, $intLinha +   3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 101, $intLinha +   6, utf8_decode( "UNITÁRIO"                     ) );
        $pdf->Text( 115, $intLinha +   3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 115, $intLinha +   6, utf8_decode( "TOTAL"                        ) );
        $pdf->Text( 128, $intLinha +   3, utf8_decode( "B.CALC"                       ) );
        $pdf->Text( 129, $intLinha +   6, utf8_decode( "ICMS"                         ) );
        $pdf->Text( 141, $intLinha +   3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 142, $intLinha +   6, utf8_decode( "ICMS"                         ) );
        $pdf->Text( 154, $intLinha +   3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 156, $intLinha +   6, utf8_decode( "IPI"                          ) );
        $pdf->Text( 169, $intLinha +   3, utf8_decode( "ALIQUOTAS"                    ) );

        $pdf->Text( 166, $intLinha + 6, utf8_decode( "ICMS"                           ) );
        $pdf->Text( 178, $intLinha + 6, utf8_decode( "IPI"                            ) );
        
        $pdf->Text( 188, $intLinha +   3, utf8_decode( "DEVOLUÇÃO"                    ) );        
        $pdf->Text( 186, $intLinha +   6, utf8_decode( "ALIQ."                        ) );
        $pdf->Text(195.5, $intLinha +  6, utf8_decode( "VALOR"                        ) );
        
        $pdf->SetFont('helvetica', '', 5);
    }else{    
        $intLinha += 1;
        $pdf->Rect(   5, $intLinha, 18, 7, 'DF' );
        $pdf->Rect(  23, $intLinha, 47, 7, 'DF' );
        $pdf->Rect(  70, $intLinha, 12, 7, 'DF' );
        $pdf->Rect(  82, $intLinha,  8, 7, 'DF' );
        $pdf->Rect(  90, $intLinha,  8, 7, 'DF' );
        $pdf->Rect(  98, $intLinha,  8, 7, 'DF' );
        $pdf->Rect( 106, $intLinha, 12, 7, 'DF' );
        $pdf->Rect( 118, $intLinha, 16, 7, 'DF' );
        $pdf->Rect( 134, $intLinha, 16, 7, 'DF' );
        $pdf->Rect( 150, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 163, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 176, $intLinha, 13, 7, 'DF' );
        $pdf->Rect( 189, $intLinha, 16, 7, 'DF' );
        $pdf->Rect( 189, $intLinha + 3.5,  8, 3.5, 'DF' );
        $pdf->Rect( 197, $intLinha + 3.5,  8, 3.5, 'DF' );

        $pdf->SetFont('helvetica', 'B', 6.5);

        $pdf->Text( 5.5, $intLinha + 4.5, utf8_decode( "COD.PRODUTO"                  ) );
        $pdf->Text(25.5, $intLinha + 4.5, utf8_decode( "DESCRIÇÃO DO PRODUTO/SERVIÇO" ) );
        $pdf->Text(71.5, $intLinha + 4.5, utf8_decode( "NCM SH"                       ) );
        $pdf->Text(83.7, $intLinha + 4.5, utf8_decode( "CST"                          ) );
        $pdf->Text(90.7, $intLinha + 4.5, utf8_decode( "CFOP"                         ) );
        $pdf->Text(  99, $intLinha + 4.5, utf8_decode( "UNID."                        ) );
        $pdf->Text( 108, $intLinha + 4.5, utf8_decode( "QUANT."                       ) );
        $pdf->Text( 121, $intLinha + 3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 120, $intLinha + 6, utf8_decode( "UNITÁRIO"                     ) );
        $pdf->Text( 138, $intLinha + 3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 138, $intLinha + 6, utf8_decode( "TOTAL"                        ) );
        $pdf->Text( 152, $intLinha + 3, utf8_decode( "B.CALC"                       ) );
        $pdf->Text( 154, $intLinha + 6, utf8_decode( "ICMS"                         ) );
        $pdf->Text( 165, $intLinha + 3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 166, $intLinha + 6, utf8_decode( "ICMS"                         ) );
        $pdf->Text( 178, $intLinha + 3, utf8_decode( "VALOR"                        ) );
        $pdf->Text( 181, $intLinha + 6, utf8_decode( "IPI"                          ) );
        $pdf->Text( 190, $intLinha + 3, utf8_decode( "ALIQUOTAS"                    ) );
        $pdf->Text( 190, $intLinha + 6, utf8_decode( "ICMS"                         ) );
        $pdf->Text( 200, $intLinha + 6, utf8_decode( "IPI"                          ) );
        
        $pdf->SetFont('helvetica', '', 6);
    }
    
//    print "<pre> 21321 "; print_r($dados['valor_ipi_dev']);
//            exit;

    
    $pdf->SetXY( 5 , $intLinha + 7 );
    $intCont   = 0;
    $booRodape = false;
    $obs_fci = "";

    $qtde_itens = count($dados['itens']);
    foreach ( $dados['itens'] as $item ){
        if ( $intCont == 6 || $intCont == 34) {
            if ( !$booRodape ) {
                rodape( $pdf );
                $booRodape = true;
            }
            $pdf->AddPage();
            $intPagina++;
            if( $qtde_itens <= 24 ){
                cabecalho( $pdf, $intPagina, $intTotalPagina ); 
                $intLinha = 80;
            }else{
                $intLinha = 5;
            }
            
            if( $dados['valor_ipi_dev'] > 0 ){
                $intLinha += 1;
                $pdf->Rect(   5, $intLinha, 15, 7, 'DF' );
                $pdf->Rect(  20, $intLinha, 43, 7, 'DF' );
                $pdf->Rect(  63, $intLinha, 10, 7, 'DF' );
                $pdf->Rect(  73, $intLinha,  5, 7, 'DF' );
                $pdf->Rect(  78, $intLinha,  6, 7, 'DF' );
                $pdf->Rect(  84, $intLinha,  6, 7, 'DF' );        
                $pdf->Rect( 90, $intLinha, 9, 7, 'DF' );

                $pdf->Rect(  99, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 112, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 125, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 138, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 151, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 164, $intLinha, 20, 7, 'DF' );

                $pdf->Rect( 164, $intLinha + 3.5,  10, 3.5, 'DF' );
                $pdf->Rect( 174, $intLinha + 3.5,  10, 3.5, 'DF' );


                $pdf->Rect( 184, $intLinha, 21, 7, 'DF' );
                $pdf->Rect( 184, $intLinha + 3.5,  8, 3.5, 'DF' );
                $pdf->Rect( 192, $intLinha + 3.5,  13, 3.5, 'DF' );

                $pdf->SetFont('helvetica', 'B', 5);

                $pdf->Text( 5.5, $intLinha + 4.5, utf8_decode( "COD.PRODUTO"                  ) );
                $pdf->Text(25, $intLinha + 4.5, utf8_decode( "DESCRIÇÃO DO PRODUTO/SERVIÇO"   ) );
                $pdf->Text(64.5, $intLinha + 4.5, utf8_decode( "NCM SH"                       ) );
                $pdf->Text(73.8, $intLinha + 4.5, utf8_decode( "CST"                          ) );
                $pdf->Text(78.5, $intLinha + 4.5, utf8_decode( "CFOP"                         ) );
                $pdf->Text(84.5, $intLinha + 4.5, utf8_decode( "UNID."                        ) );        
                $pdf->Text( 91, $intLinha + 4.5, utf8_decode( "QUANT."                        ) );

                $pdf->Text( 102, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 101, $intLinha +   6, utf8_decode( "UNITÁRIO"                     ) );
                $pdf->Text( 115, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 115, $intLinha +   6, utf8_decode( "TOTAL"                        ) );
                $pdf->Text( 128, $intLinha +   3, utf8_decode( "B.CALC"                       ) );
                $pdf->Text( 129, $intLinha +   6, utf8_decode( "ICMS"                         ) );
                $pdf->Text( 141, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 142, $intLinha +   6, utf8_decode( "ICMS"                         ) );
                $pdf->Text( 154, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 156, $intLinha +   6, utf8_decode( "IPI"                          ) );
                $pdf->Text( 169, $intLinha +   3, utf8_decode( "ALIQUOTAS"                    ) );

                $pdf->Text( 166, $intLinha + 6, utf8_decode( "ICMS"                           ) );
                $pdf->Text( 178, $intLinha + 6, utf8_decode( "IPI"                            ) );

                $pdf->Text( 188, $intLinha +   3, utf8_decode( "DEVOLUÇÃO"                    ) );        
                $pdf->Text( 186, $intLinha +   6, utf8_decode( "ALIQ."                        ) );
                $pdf->Text(195.5, $intLinha +  6, utf8_decode( "VALOR"                        ) );

                $pdf->SetFont('helvetica', '', 5);
            }else {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Text( 5, $intLinha, utf8_decode( "DADOS DO PRODUTO/SERVIÇO"  ) );

                $intLinha += 2;

                $pdf->Rect(   5, $intLinha, 18, 7, 'DF' );
                $pdf->Rect(  23, $intLinha, 47, 7, 'DF' );
                $pdf->Rect(  70, $intLinha, 12, 7, 'DF' );
                $pdf->Rect(  82, $intLinha,  8, 7, 'DF' );
                $pdf->Rect(  90, $intLinha,  8, 7, 'DF' );
                $pdf->Rect(  98, $intLinha,  8, 7, 'DF' );
                $pdf->Rect( 106, $intLinha, 12, 7, 'DF' );
                $pdf->Rect( 118, $intLinha, 16, 7, 'DF' );
                $pdf->Rect( 134, $intLinha, 16, 7, 'DF' );
                $pdf->Rect( 150, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 163, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 176, $intLinha, 13, 7, 'DF' );
                $pdf->Rect( 189, $intLinha, 16, 7, 'DF' );
                $pdf->Rect( 189, $intLinha + 3.5,  8, 3.5, 'DF' );
                $pdf->Rect( 197, $intLinha + 3.5,  8, 3.5, 'DF' );

                $pdf->SetFont('helvetica', 'B', 6.5);

                $pdf->Text( 5.5, $intLinha + 4.5, utf8_decode( "COD.PRODUTO"                  ) );
                $pdf->Text(25.5, $intLinha + 4.5, utf8_decode( "DESCRIÇÃO DO PRODUTO/SERVIÇO" ) );
                $pdf->Text(71.5, $intLinha + 4.5, utf8_decode( "NCM SH"                       ) );
                $pdf->Text(83.7, $intLinha + 4.5, utf8_decode( "CST"                          ) );
                $pdf->Text(90.7, $intLinha + 4.5, utf8_decode( "CFOP"                         ) );
                $pdf->Text(  99, $intLinha + 4.5, utf8_decode( "UNID."                        ) );
                $pdf->Text( 108, $intLinha + 4.5, utf8_decode( "QUANT."                       ) );
                $pdf->Text( 121, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 120, $intLinha +   6, utf8_decode( "UNITÁRIO"                     ) );
                $pdf->Text( 138, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 138, $intLinha +   6, utf8_decode( "TOTAL"                        ) );
                $pdf->Text( 152, $intLinha +   3, utf8_decode( "B.CALC"                       ) );
                $pdf->Text( 154, $intLinha +   6, utf8_decode( "ICMS"                         ) );
                $pdf->Text( 165, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 166, $intLinha +   6, utf8_decode( "ICMS"                         ) );
                $pdf->Text( 178, $intLinha +   3, utf8_decode( "VALOR"                        ) );
                $pdf->Text( 181, $intLinha +   6, utf8_decode( "IPI"                          ) );
                $pdf->Text( 190, $intLinha +   3, utf8_decode( "ALIQUOTAS"                    ) );
                $pdf->Text( 190, $intLinha +   6, utf8_decode( "ICMS"                         ) );
                $pdf->Text( 200, $intLinha +   6, utf8_decode( "IPI"                          ) );
            }
            $pdf->SetXY( 5 , $intLinha );
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetXY( 5 , $intLinha + 7 );
        }
        
        $obs_fci = !empty($item['numero_fci']) ? "\nRES DO SENADO FED. N 13/12, NUM DA FCI\n{$item['numero_fci']}" : "";

        $intTamanho = strlen( $item[$intCont]['descricao'] );

        if( $intTamanho > 120 ){
            $intAltura = 25;
        }else if( $intTamanho > 20 && $intTamanho < 120  ){
            $intAltura = ($qtde_itens >= 5 ? 12.5 : 15);
        }else{
            $intAltura = 9;
        }            
        
        $intAlturaDesc = !empty($item['numero_fci']) ? 2 : 0;
        $intAltura += $intAlturaDesc;
        
        $quantidade         = number_format($item['quantidade']     ,2,",",".");
        $valor_unitario     = number_format($item['valor_unitario'] ,2,",",".");
        $valor_total        = number_format($item['valor_total']    ,2,",",".");
        $bc_icms            = !empty($item['bc_icms'])       ? number_format($item['bc_icms']        ,2,",",".") : "0,00";
        $valor_icms         = !empty($item['valor_icms'])    ? number_format($item['valor_icms']     ,2,",",".") : "0,00";
        $valor_ipi          = !empty($item['valor_ipi'])     ? number_format($item['valor_ipi']      ,2,",",".") : "0,00";
        $aliquota_icms      = !empty($item['aliquota_icms']) ? number_format($item['aliquota_icms']  ,2,",",".") : "0,00";
        $aliquota_ipi       = !empty($item['aliquota_ipi'])  ? number_format($item['aliquota_ipi']   ,2,",",".") : "0,00";
        $aliquota_ipi_dev   = !empty($item['aliquota_ipi_dev'])  ? number_format($item['aliquota_ipi_dev']   ,2,",",".") : "0,00";
        $valor_ipi_dev   = !empty($item['valor_ipi_dev'])  ? number_format($item['valor_ipi_dev']   ,2,",",".") : "0,00";
        $cst                = $item['cst_icms_origem'].$item['cst_icms_trib'];
        
        if( $dados['valor_ipi_dev'] > 0 ){
            $pdf->SetXY( 4, $intLinha + 10  );
            $pdf->Rect(  5, $intLinha + 7, 15, $intAltura );
            $pdf->MultiCell( 16, 3, utf8_decode( "{$item['codigo_produto']}" ),0,'C');                
            $pdf->SetXY( 20, $intLinha + 10 - $intAlturaDesc );
            $pdf->Rect(  20, $intLinha + 7, 43, $intAltura );        
            $pdf->MultiCell( 43, 2.5, utf8_decode( "{$item['descricao']}{$obs_fci}" ), 0, 'L' );        
            $pdf->SetXY( 63, $intLinha + 7 );
            $pdf->Cell(  10, $intAltura, utf8_decode( "{$item['ncm']}"                      ), 1, 0, 'C' );
            $pdf->Cell(   5, $intAltura, utf8_decode( "{$cst}"                              ), 1, 0, 'C' );
            $pdf->Cell(   6, $intAltura, utf8_decode( "{$item['cfop']}"                     ), 1, 0, 'C' );
            $pdf->Cell(   6, $intAltura, utf8_decode( "{$item['unidade_medida']}"           ), 1, 0, 'C' );
            $pdf->Cell(   9, $intAltura, utf8_decode( "{$quantidade}"                       ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_unitario}"                   ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_total}"                      ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$bc_icms}"                          ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_icms}"                       ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_ipi}"                        ), 1, 0, 'C' );
            $pdf->Cell(  10, $intAltura, utf8_decode( "{$aliquota_icms}"                    ), 1, 0, 'C' );
            
            $pdf->Cell( 10, $intAltura, utf8_decode( "{$aliquota_ipi}"                      ), 1, 0, 'C' );
            $pdf->Cell(  8, $intAltura, utf8_decode( "{$aliquota_ipi_dev}"                  ), 1, 0, 'C' );
            $pdf->Cell( 13, $intAltura, utf8_decode( "{$valor_ipi_dev}"                     ), 1, 0, 'C' );
        }else {

//        $pdf->Cell(  18, $intAltura, utf8_decode( "{$item['codigo_produto']}" ), 1, 0, 'C' );
            $pdf->SetXY( 4, $intLinha + 10  );
            $pdf->Rect(  5, $intLinha + 7, 18, $intAltura );
            $pdf->MultiCell( 20, 3, utf8_decode( "{$item['codigo_produto']}" ),0,'C');                
            $pdf->SetXY( 23, $intLinha + 10 - $intAlturaDesc );
            $pdf->Rect(  23, $intLinha + 7, 47, $intAltura );        
            $pdf->MultiCell( 48, 2.5, utf8_decode( "{$item['descricao']}{$obs_fci}" ), 0, 'L' );

            $pdf->SetXY( 70, $intLinha + 7 );
            $pdf->Cell(  12, $intAltura, utf8_decode( "{$item['ncm']}"                      ), 1, 0, 'C' );
            $pdf->Cell(   8, $intAltura, utf8_decode( "{$cst}"                              ), 1, 0, 'C' );
            $pdf->Cell(   8, $intAltura, utf8_decode( "{$item['cfop']}"                     ), 1, 0, 'C' );
            $pdf->Cell(   8, $intAltura, utf8_decode( "{$item['unidade_medida']}"           ), 1, 0, 'C' );
            $pdf->Cell(  12, $intAltura, utf8_decode( "{$quantidade}"                       ), 1, 0, 'C' );
            $pdf->Cell(  16, $intAltura, utf8_decode( "{$valor_unitario}"                   ), 1, 0, 'C' );
            $pdf->Cell(  16, $intAltura, utf8_decode( "{$valor_total}"                      ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$bc_icms}"                          ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_icms}"                       ), 1, 0, 'C' );
            $pdf->Cell(  13, $intAltura, utf8_decode( "{$valor_ipi}"                        ), 1, 0, 'C' );
            $pdf->Cell(   8, $intAltura, utf8_decode( "{$aliquota_icms}"                    ), 1, 0, 'C' );
            $pdf->Cell(   8, $intAltura, utf8_decode( "{$aliquota_ipi}"                     ), 1, 1, 'C' );
        }
        $intLinha += $intAltura;
        $pdf->SetXY( 5 , $intLinha + 7 );
        $intCont++;
    }

    if ( !$booRodape ) {
        rodape( $pdf );
        $booRodape = true;
    }
    
    $pdf->SetDisplayMode( 'fullwidth' , 'default');
    $strNomeArquivo = "{$dados['chave_acesso']}.pdf";
    $pdf->Output();
    $pdf->Output( "I", $strNomeArquivo );
?>


<?php 
    function retornaDados($xml,$chave_acesso){
        global $bd;

        $dadosItens             = "";
        $cabecalho              = $xml['cabecalho'];        
        $emitente               = $xml['emitente'];
        $emitente_endereco      = $xml['emitente_endereco'];
        $destinatario           = $xml['destinatario'];
        $destinatario_endereco  = $xml['destinatario_endereco'];
        $itens                  = $xml['itens'];
        $total                  = $xml['total'];
        $transporte             = $xml['transporte'];
        $cobranca               = $xml['cobranca'];
        
        $observacao             = $xml['infAdic'];
        $informacoes            = $xml['informacoes'];
        
        $cont = 0;
        $dadosItens = $itens;               
        $arrItem = array();
        foreach ( $dadosItens as $item ){
            array_push($arrItem,
                array(
                    'numero_item'               => $cont++, 
                    'codigo_produto'            => $item["cProd"],
                    'ean'                       => $item["cEAN"],
                    'descricao'                 => $item["xProd"],
                    'ncm'                       => $item["NCM"],
                    'cfop'                      => $item["CFOP"],
                    'unidade_medida'            => $item["uCom"],
                    'quantidade'                => $item["qCom"],
                    'valor_unitario'            => $item["vUnCom"],
                    'valor_total'               => $item["vProd"],
                    'ean_trib'                  => $item["cEANTrib"],
                    'unidade_medida_trib'       => $item["uTrib"],
                    'quantidade_trib'           => $item["qTrib"],
                    'valor_unitario_trib'       => $item["vUnTrib"],
                    'numero_fci'                => $item["nFCI"],
                    
                    'cst_icms_origem'           => $item["ICMS_orig"],
                    'cst_icms_trib'             => $item["ICMS_CST"],
                    'bc_icms'                   => $item["ICMS_vBC"],
                    'aliquota_icms'             => $item["ICMS_pICMS"],
                    'valor_icms'                => $item["ICMS_vICMS"],
                    
                    'enquadramento_ipi'         => $item["IPI_cEnq"],
                    'cst_ipi'                   => $item["IPI_CST"],
                    'bc_ipi'                    => $item["IPI_vBC"],
                    'aliquota_ipi'              => $item["IPI_pIPI"],
                    'valor_ipi'                 => $item["IPI_vIPI"],
                    
                    'cst_pis'                   => $item["PIS_CST"],
                    'bc_pis'                    => $item["PIS_vBC"],
                    'aliquota_pis'              => $item["PIS_pPIS"],
                    'valor_pis'                 => $item["PIS_vPIS"],
                    
                    'cst_cofins'                => $item["COFINS_CST"],
                    'bc_cofins'                 => $item["COFINS_vBC"],
                    'aliquota_cofins'           => $item["COFINS_pCOFINS"],
                    'valor_cofins'              => $item["COFINS_vCOFINS"],
                    
                    'aliquota_ipi_dev'          => $item['IPI_pIPIDevol'],
                    'valor_ipi_dev'             => $item['IPI_vIPIDevol']
                )
            );
        }
        
        ## cobrança       
        $arrFatura = array(
            'qtde_fatura'                       => $cobranca["fatura"]["nFat"],
            'valor_bruto_fatura'                => $cobranca["fatura"]["vOrig"],
            'valor_desconto_fatura'             => $cobranca["fatura"]["vDesc"],
            'valor_liquido_fatura'              => $cobranca["fatura"]["vLiq"],
        );
          
        $titulos = $cobranca['duplicata'];                
        foreach ( $titulos as $titulo ){
            array_push($arrFatura,
                array(
                    'numero'                    => $cabecalho["nNF"].'/'.$titulo['nDup'],
                    'data_vencimento'           => $titulo['dVenc'],
                    'valor_duplicata'           => $titulo['vDup'],
                )
            );
        }
        
        $info_adicionais = $observacao["infCpl"];
        $info_adicionais .= empty($info_adicionais) ? $observacao["infAdFisco"] : '\n'.$observacao["infAdFisco"];

        $arrDanfe = array(
            ## cabeçalho 
            'codigo_numero'                     => $cabecalho["cNF"],
            'natureza_operacao'                 => $cabecalho["natOp"],
            'modelo'                            => $cabecalho["mod"],
            'serie'                             => $cabecalho["serie"],
            'numero_nota'                       => $cabecalho["nNF"],
            'data_emissao'                      => $cabecalho["dhEmi"],
            'data_saida_entrada'                => $cabecalho["dhSaiEnt"],
            'hora_saida'                        => $cabecalho["hrSaida"],
            'tipo_nf'                           => $cabecalho["tpNF"],
            'ind_pres'                          => $cabecalho["indPres"],
            
            ## emitente
            'cnpj_emitente'                     => $emitente["CNPJ"],
            'razao_social_emitente'             => $emitente["xNome"],
            'endereco_emitente'                 => $emitente_endereco["xLgr"],
            'numero_emitente'                   => $emitente_endereco["nro"],
            'bairro_emitente'                   => $emitente_endereco["xBairro"],
            'municipio_emitente'                => $emitente_endereco["mun"],
            'uf_emitente'                       => $emitente_endereco["UF"],
            'cep_emitente'                      => $emitente_endereco["CEP"],
            'codigo_pais_emitente'              => $emitente_endereco["cPais"],
            'pais_emitente'                     => $emitente_endereco["xPais"],
            'fone_emitente'                     => $emitente_endereco["fone"],
            'ie_emitente'                       => $emitente_endereco["ie"],
            
            ## destinatario
            'cnpj_destinatario'                 => $destinatario["CNPJ"],
            'tipo_pessoa'                       => $destinatario["tpPessoa"],
            'razao_social_destinatario'         => $destinatario["xNome"],
            'nome_fantasia_destinatario'        => $destinatario["xFant"],
            'endereco_destinatario'             => $destinatario_endereco["xLgr"],
            'numero_destinatario'               => $destinatario_endereco["nro"],
            'bairro_destinatario'               => $destinatario_endereco["xBairro"],
            'cod_municipio_destinatario'        => $destinatario_endereco["cMun"],
            'municipio_destinatario'            => $destinatario_endereco["nomeCidade"],
            'uf_destinatario'                   => $destinatario_endereco["UF"],
            'cep_destinatario'                  => $destinatario_endereco["CEP"],
            'codigo_pais_destinatario'          => $destinatario_endereco["cPais"],
            'pais_destinatario'                 => $destinatario_endereco["xPais"],
            'fone_destinatario'                 => $destinatario_endereco["fone"],
            'ie_destinatario'                   => $destinatario_endereco["IE"],
            'email_destinatario'                => $destinatario_endereco["email"],
            
            ## dados
            'itens'                             => $arrItem,
                
            ## total
            'bc_icms'                           => $total["vBC"],
            'valor_icms'                        => $total["vICMS"],
            'valor_total_produto'               => $total["vProd"],
            'valor_frete'                       => $total["vFrete"],
            'valor_seguro'                      => $total["vSeg"],
            'valor_desconto'                    => $total["vDesc"],
            'valor_imposto_importacao'          => $total["vII"],
            'valor_ipi'                         => $total["vIPI"],
            'valor_ipi_dev'                     => $total["vIPIDevol"],
            'valor_pis'                         => $total["vPIS"],
            'valor_cofins'                      => $total["vCOFINS"],
            'outras_despesas'                   => $total["vOutro"],
            'valor_nf'                          => $total["vNF"],
            'valor_total_impostos'              => $total["vTotImp"],
            
            ## transportadora
            'frete_por_conta'                   => $transporte["modFrete"],
            'cnpj_transportadora'               => $transporte["CNPJ"],
            'razao_social_transportadora'       => $transporte["xNome"],
            'ie_transportadora'                 => $transporte["IE"],
            'endereco_transportadora'           => $transporte["xEnder"],
            'municipio_transportadora'          => $transporte["xMun"],
            'uf_transportadora'                 => $transporte["uf"],
            'placa_veiculo'                     => $transporte["placa"],
            'uf_veiculo_transportadora'         => $transporte["uf"],
            'volume'                            => $transporte["qVol"],
            'peso_liquido'                      => $transporte["pesoL"],
            'peso_bruto'                        => $transporte["pesoB"],
            'especie'                           => $transporte["esp"],
                        
            ## fatura/cobrança
            'fatura'                            => $arrFatura,
            
            ## informações adicionais
            'informacoes_adicionais'            => $info_adicionais,
            'chave_acesso'                      => $informacoes["chNFe"],
            
            'protocolo_autorizacao'             => $informacoes["nProt"] .' '. dataBrasil($informacoes["dhRecbto"]) . substr($informacoes["dhRecbto"],10,100)
        );

        return $arrDanfe ;
    }
?>