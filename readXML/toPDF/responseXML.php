<?php
/*
 * Author: Vitor Hugo Nunes Marini
 * Data Criação: 13/08/2020
 * Response XML to PDF
 */
include_once "{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/functions/functions.php";  

chdir(dirname(__FILE__));

function readXML($valor){
    
    $valor_real  = $valor;
    $valor = str_replace("NFe", "", $valor);
    
    $aa         = substr($valor,2,2);
    $mm         = substr($valor,4,2);
    $aamm       = substr($valor,2,4);
    $dataChave  = substr($aamm,2)."/".substr($aamm,0,2);
    $emitente   = substr($valor,6,14);    

    $link = "{$_SERVER['DOCUMENT_ROOT']}/convertXMLtoPDF/arquivos/{$valor_real}";
    
    $xml = simplexml_load_file($link);
    
    $arrDanfe = array();    
    #CABEÇALHO NFE
    $arrDanfe["cabecalho"] = array();
    $arrDanfe["cabecalho"]["cUF"]                                                   = utf8_encode($xml->NFe->infNFe->ide->cUF);
    $arrDanfe["cabecalho"]["cNF"]                                                   = utf8_decode($xml->NFe->infNFe->ide->cNF);
    $arrDanfe["cabecalho"]["natOp"]                                                 = utf8_decode($xml->NFe->infNFe->ide->natOp);
    $arrDanfe["cabecalho"]["mod"]                                                   = utf8_decode($xml->NFe->infNFe->ide->mod);
    $arrDanfe["cabecalho"]["serie"]                                                 = utf8_decode($xml->NFe->infNFe->ide->serie);
    $arrDanfe["cabecalho"]["nNF"]                                                   = utf8_decode($xml->NFe->infNFe->ide->nNF);
    $arrDanfe["cabecalho"]["dhEmi"]                                                 = utf8_decode($xml->NFe->infNFe->ide->dhEmi)    != "" ? dataBrasil(explode("T",utf8_decode($xml->NFe->infNFe->ide->dhEmi))[0])    : utf8_decode($xml->NFe->infNFe->ide->dhEmi);
    $arrDanfe["cabecalho"]["dhSaiEnt"]                                              = utf8_decode($xml->NFe->infNFe->ide->dhSaiEnt) != "" ? dataBrasil(explode("T",utf8_decode($xml->NFe->infNFe->ide->dhSaiEnt))[0]) : "";
    $arrDanfe["cabecalho"]["hrSaida"]                                               = utf8_decode($xml->NFe->infNFe->ide->dhSaiEnt) != "" ? explode("-",(explode("T",utf8_decode($xml->NFe->infNFe->ide->dhSaiEnt))[1]))[0] : "";    
    $arrDanfe["cabecalho"]["tpNF"]                                                  = utf8_decode($xml->NFe->infNFe->ide->tpNF);
    $arrDanfe["cabecalho"]["idDest"]                                                = utf8_decode($xml->NFe->infNFe->ide->idDest);
    $arrDanfe["cabecalho"]["cMunFG"]                                                = utf8_decode($xml->NFe->infNFe->ide->cMunFG);
    $arrDanfe["cabecalho"]["tpImp"]                                                 = utf8_decode($xml->NFe->infNFe->ide->tpImp);
    $arrDanfe["cabecalho"]["tpEmis"]                                                = utf8_decode($xml->NFe->infNFe->ide->tpEmis);
    $arrDanfe["cabecalho"]["cDV"]                                                   = utf8_decode($xml->NFe->infNFe->ide->cDV);
    $arrDanfe["cabecalho"]["tpAmb"]                                                 = utf8_decode($xml->NFe->infNFe->ide->tpAmb);
    $arrDanfe["cabecalho"]["finNFe"]                                                = utf8_decode($xml->NFe->infNFe->ide->finNFe);
    $arrDanfe["cabecalho"]["indFinal"]                                              = utf8_decode($xml->NFe->infNFe->ide->indFinal);
    $arrDanfe["cabecalho"]["indPres"]                                               = utf8_decode($xml->NFe->infNFe->ide->indPres);
    $arrDanfe["cabecalho"]["procEmi"]                                               = utf8_decode($xml->NFe->infNFe->ide->procEmi);
    $arrDanfe["cabecalho"]["verProc"]                                               = utf8_decode($xml->NFe->infNFe->ide->verProc);

    #EMITENTE    
    $arrDanfe["emitente"] = array();
    $arrDanfe["emitente"]["CNPJ"]                                                   = utf8_decode($xml->NFe->infNFe->emit->CNPJ);
    $arrDanfe["emitente"]["xNome"]                                                  = utf8_decode($xml->NFe->infNFe->emit->xNome);
    $arrDanfe["emitente"]["xFant"]                                                  = utf8_decode($xml->NFe->infNFe->emit->xFant);
    $arrDanfe["emitente_endereco"] = array();
    $arrDanfe["emitente_endereco"]["xLgr"]                                          = utf8_decode($xml->NFe->infNFe->emit->enderEmit->xLgr);
    $arrDanfe["emitente_endereco"]["nro"]                                           = utf8_decode($xml->NFe->infNFe->emit->enderEmit->nro);
    $arrDanfe["emitente_endereco"]["xBairro"]                                       = utf8_decode($xml->NFe->infNFe->emit->enderEmit->xBairro);
    $arrDanfe["emitente_endereco"]["cMun"]                                          = utf8_decode($xml->NFe->infNFe->emit->enderEmit->cMun);
    $arrDanfe["emitente_endereco"]["mun"]                                           = utf8_decode($xml->NFe->infNFe->emit->enderEmit->xMun);
    $arrDanfe["emitente_endereco"]["UF"]                                            = utf8_decode($xml->NFe->infNFe->emit->enderEmit->UF);
    $arrDanfe["emitente_endereco"]["CEP"]                                           = utf8_decode($xml->NFe->infNFe->emit->enderEmit->CEP);
    $arrDanfe["emitente_endereco"]["cPais"]                                         = utf8_decode($xml->NFe->infNFe->emit->enderEmit->cPais);
    $arrDanfe["emitente_endereco"]["xPais"]                                         = utf8_decode($xml->NFe->infNFe->emit->enderEmit->xPais);
    $arrDanfe["emitente_endereco"]["fone"]                                          = utf8_decode($xml->NFe->infNFe->emit->enderEmit->fone);
    $arrDanfe["emitente_endereco"]["ie"]                                            = utf8_decode($xml->NFe->infNFe->emit->IE);

    #DESTINATÁRIO
    $arrDanfe["destinatario"] = array();
    $arrDanfe["destinatario"]["CNPJ"]                                               = utf8_decode($xml->NFe->infNFe->dest->CNPJ);
    $arrDanfe["destinatario"]["tpPessoa"]                                           = strlen(utf8_decode($xml->NFe->infNFe->dest->CNPJ)) > 11  ? "J" : "F";
    $arrDanfe["destinatario"]["xNome"]                                              = utf8_decode($xml->NFe->infNFe->dest->xNome);
    $arrDanfe["destinatario"]["xFant"]                                              = utf8_decode($xml->NFe->infNFe->dest->xFant);
    $arrDanfe["destinatario_endereco"] = array();
    $arrDanfe["destinatario_endereco"]["xLgr"]                                      = utf8_decode($xml->NFe->infNFe->dest->enderDest->xLgr);
    $arrDanfe["destinatario_endereco"]["nro"]                                       = utf8_decode($xml->NFe->infNFe->dest->enderDest->nro);
    $arrDanfe["destinatario_endereco"]["xBairro"]                                   = utf8_decode($xml->NFe->infNFe->dest->enderDest->xBairro);
    $arrDanfe["destinatario_endereco"]["cMun"]                                      = utf8_decode($xml->NFe->infNFe->dest->enderDest->cMun);
    $arrDanfe["destinatario_endereco"]["nomeCidade"]                                = $xml->NFe->infNFe->dest->enderDest->cMun; // PODE SER SUBSTITUĨDO PELO NOME DA CIDADE
    $arrDanfe["destinatario_endereco"]["UF"]                                        = utf8_decode($xml->NFe->infNFe->dest->enderDest->UF);
    $arrDanfe["destinatario_endereco"]["CEP"]                                       = utf8_decode($xml->NFe->infNFe->dest->enderDest->CEP);
    $arrDanfe["destinatario_endereco"]["cPais"]                                     = utf8_decode($xml->NFe->infNFe->dest->enderDest->cPais);
    $arrDanfe["destinatario_endereco"]["xPais"]                                     = utf8_decode($xml->NFe->infNFe->dest->enderDest->xPais);
    $arrDanfe["destinatario_endereco"]["fone"]                                      = utf8_decode($xml->NFe->infNFe->dest->enderDest->fone);
    $arrDanfe["destinatario_endereco"]["indIEDest"]                                 = utf8_decode($xml->NFe->infNFe->dest->indIEDest);
    $arrDanfe["destinatario_endereco"]["IE"]                                        = utf8_decode($xml->NFe->infNFe->dest->IE);
    $arrDanfe["destinatario_endereco"]["email"]                                     = utf8_decode($xml->NFe->infNFe->dest->email);
    
    #ITENS
    $contItem = 1;
    $cont= 0;
    $valorDet = "";
    $arrDanfe["itens"] = array();                       
    while ( $cont < 60 ){

        if ( utf8_decode($xml->NFe->infNFe->det[$cont]->prod->cProd) != "" ){
            $valorDet = $xml->NFe->infNFe->det[$cont] != "" ? "det[{$cont}]" : "det";
               
            $arrDanfe["itens"][$cont] = array();
            $arrDanfe["itens"][$cont]["item"]                                       = $contItem;
            $arrDanfe["itens"][$cont]["cProd"]                                      = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->cProd);
            $arrDanfe["itens"][$cont]["cEAN"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->cEAN);
            $arrDanfe["itens"][$cont]["xProd"]                                      = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->xProd);
            $arrDanfe["itens"][$cont]["NCM"]                                        = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->NCM);
            $arrDanfe["itens"][$cont]["CFOP"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->CFOP);
            $arrDanfe["itens"][$cont]["uCom"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->uCom);
            $arrDanfe["itens"][$cont]["qCom"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->qCom);
            $arrDanfe["itens"][$cont]["vUnCom"]                                     = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->vUnCom);
            $arrDanfe["itens"][$cont]["vProd"]                                      = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->vProd);
            $arrDanfe["itens"][$cont]["cEANTrib"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->cEANTrib);
            $arrDanfe["itens"][$cont]["uTrib"]                                      = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->uTrib);
            $arrDanfe["itens"][$cont]["qTrib"]                                      = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->qTrib);
            $arrDanfe["itens"][$cont]["vUnTrib"]                                    = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->vUnTrib);
            $arrDanfe["itens"][$cont]["indTot"]                                     = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->indTot);
            $arrDanfe["itens"][$cont]["xPed"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->xPed);
            $arrDanfe["itens"][$cont]["nFCI"]                                       = utf8_decode($xml->NFe->infNFe->det[$cont]->prod->nFCI);
            $arrDanfe["itens"][$cont]["infAdProd"]                                  = str_replace("FCI", "FCI\n", utf8_decode($xml->NFe->infNFe->det[$cont]->infAdProd));                       
            
            #VALIDA QUAL O CAMPO DO ARRAY É BUSCADO PARA O ICMS
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS00->CST != "" ? "ICMS00" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS10->CST != "" ? "ICMS10" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS20->CST != "" ? "ICMS20" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS30->CST != "" ? "ICMS30" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS40->CST != "" ? "ICMS40" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS41->CST != "" ? "ICMS41" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS50->CST != "" ? "ICMS50" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS51->CST != "" ? "ICMS51" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS60->CST != "" ? "ICMS60" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS70->CST != "" ? "ICMS70" : $valorICMS;
            $valorICMS = $xml->NFe->infNFe->det[$cont]->imposto->ICMS->ICMS90->CST != "" ? "ICMS90" : $valorICMS;
            
            #ICMS
            $arrDanfe["itens"][$cont]["ICMS_orig"]                                  = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->orig);
            $arrDanfe["itens"][$cont]["ICMS_CST"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->CST);
            $arrDanfe["itens"][$cont]["ICMS_modBC"]                                 = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->modBC);
            $arrDanfe["itens"][$cont]["ICMS_vBC"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->vBC);
            $arrDanfe["itens"][$cont]["ICMS_pICMS"]                                 = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->pICMS);
            $arrDanfe["itens"][$cont]["ICMS_vICMS"]                                 = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->ICMS->children()->children()->vICMS);          
            
            
            #VALIDA QUAL O CAMPO DO ARRAY SERÁ BUSCADO PARA O IPI
            $valorIPI = $xml->NFe->infNFe->det[$cont]->imposto->IPI->IPITrib->CST != "" ? "IPITrib" : $valorIPI;
            $valorIPI = $xml->NFe->infNFe->det[$cont]->imposto->IPI->IPINT->CST   != "" ? "IPINT"   : $valorIPI;
            
            #IPI
            $arrDanfe["itens"][$cont]["IPI_cEnq"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->IPI->cEnq);
            $arrDanfe["itens"][$cont]["IPI_CST"]                                    = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->IPI->$valorIPI->CST);
            $arrDanfe["itens"][$cont]["IPI_vBC"]                                    = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->IPI->$valorIPI->vBC);
            $arrDanfe["itens"][$cont]["IPI_pIPI"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->IPI->$valorIPI->pIPI);
            $arrDanfe["itens"][$cont]["IPI_vIPI"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->IPI->$valorIPI->vIPI);

            #PIS
            $arrDanfe["itens"][$cont]["PIS_CST"]                                    = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->PIS->children()->children()->CST);
            $arrDanfe["itens"][$cont]["PIS_vBC"]                                    = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->PIS->children()->children()->vBC);
            $arrDanfe["itens"][$cont]["PIS_pPIS"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->PIS->children()->children()->pPIS);
            $arrDanfe["itens"][$cont]["PIS_vPIS"]                                   = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->PIS->children()->children()->vPIS);

            #COFINS
            $arrDanfe["itens"][$cont]["COFINS_CST"]                                 = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->COFINS->children()->children()->CST);
            $arrDanfe["itens"][$cont]["COFINS_vBC"]                                 = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->COFINS->children()->children()->vBC);
            $arrDanfe["itens"][$cont]["COFINS_pCOFINS"]                             = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->COFINS->children()->children()->pCOFINS);
            $arrDanfe["itens"][$cont]["COFINS_vCOFINS"]                             = utf8_decode($xml->NFe->infNFe->det[$cont]->imposto->COFINS->children()->children()->vCOFINS);
            
            #IPI DEVOLUCAO
            $arrDanfe["itens"][$cont]["IPI_pIPIDevol"]                              = ($xml->NFe->infNFe->det[$cont]->impostoDevol->IPI->vIPIDevol) > 0 ? number_format(utf8_decode($xml->NFe->infNFe->det[$cont]->impostoDevol->IPI->vIPIDevol)*100/utf8_decode($xml->NFe->infNFe->det[$cont]->prod->vProd),2,".","") : "0,00";
            $arrDanfe["itens"][$cont]["IPI_vIPIDevol"]                              = utf8_decode($xml->NFe->infNFe->det[$cont]->impostoDevol->IPI->vIPIDevol);
        }
        $cont ++;    
        $contItem ++;
    }

    #TOTAL NFE
    $arrDanfe["total"] = array();
    $arrDanfe["total"]["vBC"]                                                       = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vBC);
    $arrDanfe["total"]["vICMS"]                                                     = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vICMS);
    $arrDanfe["total"]["vICMSDeson"]                                                = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vICMSDeson);
    $arrDanfe["total"]["vFCP"]                                                      = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vFCP);
    $arrDanfe["total"]["vBCST"]                                                     = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vBCST);
    $arrDanfe["total"]["vST"]                                                       = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vST);
    $arrDanfe["total"]["vFCPST"]                                                    = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vFCPST);
    $arrDanfe["total"]["vFCPSTRet"]                                                 = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vFCPSTRet);
    $arrDanfe["total"]["vProd"]                                                     = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vProd);
    $arrDanfe["total"]["vFrete"]                                                    = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vFrete);
    $arrDanfe["total"]["vSeg"]                                                      = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vSeg);
    $arrDanfe["total"]["vDesc"]                                                     = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vDesc);
    $arrDanfe["total"]["vII"]                                                       = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vII);
    $arrDanfe["total"]["vIPI"]                                                      = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vIPI);
    $arrDanfe["total"]["vIPIDevol"]                                                 = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vIPIDevol);
    $arrDanfe["total"]["vPIS"]                                                      = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vPIS);
    $arrDanfe["total"]["vCOFINS"]                                                   = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vCOFINS);
    $arrDanfe["total"]["vOutro"]                                                    = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vOutro);
    $arrDanfe["total"]["vNF"]                                                       = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vNF);
    $arrDanfe["total"]["vTotImp"]                                                   = utf8_decode($xml->NFe->infNFe->total->ICMSTot->vIPI) + utf8_decode($xml->NFe->infNFe->total->ICMSTot->vICMS);

    #TRANSPORTE
    $arrDanfe["transporte"] = array();
    $arrDanfe["transporte"]["modFrete"]                                             = utf8_decode($xml->NFe->infNFe->transp->modFrete);
    $arrDanfe["transporte"]["CNPJ"]                                                 = utf8_decode($xml->NFe->infNFe->transp->transporta->CNPJ);
    $arrDanfe["transporte"]["xNome"]                                                = utf8_decode($xml->NFe->infNFe->transp->transporta->xNome);
    $arrDanfe["transporte"]["IE"]                                                   = utf8_decode($xml->NFe->infNFe->transp->transporta->IE);
    $arrDanfe["transporte"]["xEnder"]                                               = utf8_decode($xml->NFe->infNFe->transp->transporta->xEnder);
    $arrDanfe["transporte"]["xMun"]                                                 = utf8_decode($xml->NFe->infNFe->transp->transporta->xMun);
    $arrDanfe["transporte"]["uf"]                                                   = utf8_decode($xml->NFe->infNFe->transp->transporta->UF);
    $arrDanfe["transporte"]["placa"]                                                = utf8_decode($xml->NFe->infNFe->transp->veicTransp->placa);
    $arrDanfe["transporte"]["uf"]                                                   = utf8_decode($xml->NFe->infNFe->transp->transporta->UF);
    $arrDanfe["transporte"]["qVol"]                                                 = utf8_decode($xml->NFe->infNFe->transp->vol->qVol);
    $arrDanfe["transporte"]["pesoL"]                                                = utf8_decode($xml->NFe->infNFe->transp->vol->pesoL);
    $arrDanfe["transporte"]["pesoB"]                                                = utf8_decode($xml->NFe->infNFe->transp->vol->pesoB);
    $arrDanfe["transporte"]["esp"]                                                  = utf8_decode($xml->NFe->infNFe->transp->vol->esp);
    
    #COBRANÇA
    $arrDanfe["cobranca"]["fatura"] = array();
    $arrDanfe["cobranca"]["fatura"]["nFat"]                                         = utf8_decode($xml->NFe->infNFe->cobr->fat->nFat); 
    $arrDanfe["cobranca"]["fatura"]["vOrig"]                                        = utf8_decode($xml->NFe->infNFe->cobr->fat->vOrig); 
    $arrDanfe["cobranca"]["fatura"]["vDesc"]                                        = utf8_decode($xml->NFe->infNFe->cobr->fat->vDesc); 
    $arrDanfe["cobranca"]["fatura"]["vLiq"]                                         = utf8_decode($xml->NFe->infNFe->cobr->fat->vLiq); 

    $cont= 0;
    while ( $cont < 25 ){
        if ( utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->nDup) != "" ){
            $arrDanfe["cobranca"]["duplicata"][$cont] = array();
            $arrDanfe["cobranca"]["duplicata"][$cont]["nDup"]                       = utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->nDup); 
            $arrDanfe["cobranca"]["duplicata"][$cont]["dVenc"]                      = utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->dVenc) != "" ? dataBrasil(utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->dVenc)) : utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->dVenc); 
            $arrDanfe["cobranca"]["duplicata"][$cont]["vDup"]                       = utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->vDup) != "" ? number_format(utf8_decode($xml->NFe->infNFe->cobr->dup[$cont]->vDup),2,",",".") : ""; 
        }
        $cont ++;    
        $contItem ++;
    }

    #PAGAMENTO
    $arrDanfe["pagamento"] = array();
    $arrDanfe["pagamento"]["tPag"]                                                  = utf8_decode($xml->NFe->infNFe->pag->detPag->tPag); 
    $arrDanfe["pagamento"]["vPag"]                                                  = utf8_decode($xml->NFe->infNFe->pag->detPag->vPag); 

    #INFO ADICIONAL
    $arrDanfe["infAdic"] = array();
    $arrDanfe["infAdic"]["infCpl"]                                                  = utf8_decode($xml->NFe->infNFe->infAdic->infCpl); 
    $arrDanfe["infAdic"]["infAdFisco"]                                              = utf8_decode($xml->NFe->infNFe->infAdic->infAdFisco); 

    #INFORMAÇÕES NOTA
    $arrDanfe["informacoes"] = array();
    $arrDanfe["informacoes"]["tpAmb"]                                               = utf8_decode($xml->protNFe->infProt->tpAmb); 
    $arrDanfe["informacoes"]["verAplic"]                                            = utf8_decode($xml->protNFe->infProt->verAplic); 
    $arrDanfe["informacoes"]["chNFe"]                                               = utf8_decode($xml->protNFe->infProt->chNFe); 
    $arrDanfe["informacoes"]["dhRecbto"]                                            = utf8_decode($xml->protNFe->infProt->dhRecbto); 
    $arrDanfe["informacoes"]["nProt"]                                               = utf8_decode($xml->protNFe->infProt->nProt); 
    $arrDanfe["informacoes"]["digVal"]                                              = utf8_decode($xml->protNFe->infProt->digVal); 
    $arrDanfe["informacoes"]["cStat"]                                               = utf8_decode($xml->protNFe->infProt->cStat); 
    $arrDanfe["informacoes"]["xMotivo"]                                             = utf8_decode($xml->protNFe->infProt->xMotivo);            
    
    return $arrDanfe;
}