<?php
/**
 * Description: Documento composto para funções que irão agregar o projeto.
 * Author: Vitor Hugo Marini
 * Data:25/05/2022
 */
   date_default_timezone_set('America/Sao_Paulo');
    
    //Constantes para a função de máscara
    const RETIRA_MASCARA = 1;
    const MASCARA_CNPJ   = 2;
    const MASCARA_CPF    = 3;
    const MASCARA_CEP    = 4;
    const MASCARA_RG     = 5;
    const MASCARA_FONE   = 6;
    
    //Constantes para a função trataQuebraLinha
    const RECUPERACAO = 1;
    const ENVIO       = 2;
    
    /**
     * Converte a data de (YYYY-mm-dd) MySql para (dd-mm-YYYY) Brasil
     * @param string $dt Data a ser convertida
     * @return string Data formatada
     */
    function data_format(&$dt){
        return date('d/m/Y', strtotime($dt));
    }
    
    /**
     * Alias para a função data_format.<br>
     * Formata uma data no formato brasileiro.
     * @param string $data Data a ser formatada
     * @return string Data em formato brasileiro
     */
    function dataBrasil(&$data){
        return data_format($data);
    }    

/**
     * Retira máscara ou formata o dado passado para CNPJ, CPF, CEP, RG e TELEFONE.
     * @param string $objeto Dado a ser aplicado ou retirado a máscara
     * @param int $tipo Operação a ser realizada com os dados. Pode ser <b>RETIRA_MASCARA</b>, <b>MASCARA_CNPJ</b>, 
     * <b>MASCARA_CPF</b>, <b>MASCARA_CEP</b>, <b>MASCARA_RG</b>, <b>MASCARA_FONE</b>
     * @return string Dado formatado
     */
     function mascaras($objeto, $tipo){            
        //Tratamentos
        $remover = array(".", "-", "/", "(", ")", " ");       
        $dados   = retira($remover, $objeto);
        
        //Retorna string vazia caso seja um valor validado como vazio
        if(empty($objeto)) return "";        
        
        //Valida o tipo de operação
        switch($tipo){
            
            case RETIRA_MASCARA :{ return $dados; }break;
            case MASCARA_CNPJ   :{ return substr($dados, 0, 2) . "." . substr($dados, 2, 3) . "." . substr($dados, 5, 3) . "/" . substr($dados, 8, 4) . "-" . substr($dados, 12, 2); }break;
            case MASCARA_CPF    :{ return substr($dados, 0, 3) . "." . substr($dados, 3, 3) . "." . substr($dados, 6, 3) . "-" . substr($dados, 9, 2)                              ; }break;
            case MASCARA_CEP    :{ return substr($dados, 0, 2) . "." . substr($dados, 2, 3) . "-" . substr($dados, 5, 3)                                                           ; }break;
            case MASCARA_RG     :{ return substr($dados, 0, 2) . "." . substr($dados, 2, 3) . "." . substr($dados, 5, 3) . "-" . substr($dados, 8, 1)                              ; }break;            
            case MASCARA_FONE   :{ return mascaraTelefone($dados)                                                                                                                  ; }break;             
        }      
     }

     /**
      * Retira todas as ocorrências do elemento de uma string ou array. 
      * @param mixed $remover String ou Array contendo o elemento a ser removido do objeto
      * @param mixed $objeto String ou Array de onde o elemento será retirado.
      * @return string String ou Array sem ocorrência do elemento passado
      */
       function retira($remover, $objeto){
          return str_replace($remover, "", $objeto);        
      }

    /**
     * Formata números telefônicos no formato ddd+número
     * @param string $objeto Número a ser formatado (somente digitos)
     */
    function mascaraTelefone($objeto){ 
           
        //Retira qualquer possível formatação existente
        $numero = mascaras($objeto, RETIRA_MASCARA);

        //Formata numero passado
        if(empty($numero)) return $numero;
        else if(substr($numero,0,4) == "0800") return substr($numero,0,4) .' '. substr($numero,4,3) .' '. substr($numero,7,4);
        else if(strlen($numero) <= 10) return '('.substr($numero,0,2).') '.substr($numero,2,4).'-'.substr($numero,6,4);
        else return '('.substr($numero,0,2).') '.substr($numero,2,5).'-'.substr($numero,7,4);
        
    }    


    /**
     * Aplica a máscara para o documento informado. Utiliza $pessoa para aplicar a máscara
     * correta ou calcula o tamanho do documento passado para determinar qual máscara será
     * usada. Quando utlizando o tamanho do documento a máscara será definida seguindo o 
     * seguinte princípio: 14 caracteres para CNPJ, 11 para CPF e o restante Exterior.
     * 
     * @param String $documento Número do documento onde aplicar a máscara
     * 
     * @param Char $pessoa Pessoa do documento, podendo ser: <br/> F - Pessoa Física<br/> 
     * J - Pessoa jurídica<br/> E - Exterior
     * 
     * @return String Retorna o documento com a máscara adequada aplicada ou string vazia
     * em caso de erro.
     */
    function formataCpfCnpj($documento, $pessoa = null){
        
     //Despresa valores maiores que 14, retornando erro
     if(strlen($documento) > 14) return "";       
         
     //Aplica máscara para pessoa física
     if($pessoa == "F" || strlen($documento) == 11) 
         return substr($documento, 0, 3). '.' .substr($documento, 3, 3). '.' .substr($documento, 6, 3). '-' .substr($documento, 9, 2);

     //Aplica máscara para pessoa jurídica
     else if($pessoa == "J" || strlen($documento) == 14)
         return substr($documento, 0, 2). '.' .substr($documento, 2, 3). '.' .substr($documento, 5, 3). '/' .substr($documento, 8, 4). '-' .substr($documento, 12, 2);

     //Devolve documento sem máscara. Nesse caso máscara não disponível
     else if($pessoa == "E" || strlen($documento) < 11 ) return $documento;
     
 }    