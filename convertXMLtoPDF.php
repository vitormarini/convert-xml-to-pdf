<?php

/* 
 * Description: Programa que permite ao usuário Converter XML para PDF de Forma simples e EFICAZ
 * Author: Vitor Hugo Marini
 * Data: 25/05/2022
 */

?>
<div class="panel-body text-center">
    <form action="danfeXML.php" id="formLoadFileReturn" name="formLoadFileReturn" class="text-center margin-top-20" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" id="arquivo_xml" name="arquivo_xml" class="inputfile inputfile-1" accept=".xml">
            <label for="arquivo_retorno">
                <span class="glyphicon glyphicon-arrow-up margin-right-10"></span>                        
                <span class="nome-arquivo">Escolha um arquivo...</span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary margin-left-10 disabled" id="btnLerArquivo">
            <span class="glyphicon glyphicon-file"></span> Ler Arquivo
        </button>
    </form>
</div>
<!-- <div class="panel-footer text-center">
    <button type="button" class="btn btn-primary margin-left-10 disabled" id="btnLerArquivo">
        <span class="glyphicon glyphicon-file"></span> Ler Arquivo
    </button>
</div> -->


<script type="text/JavaScript">
// $(document).ready(function () {
//Botão ler arquivo de retorno...
    document.getElementById("btnLerArquivo").onclick = function(){
        // $.ajax({
        //     url: document.getElementById("btnLerArquivo").prop("action"),
        //     method: "post",
        //     dataType: "json",
        //     processData: false,
        //     contentType: false,
        //     data: new FormData($("#formLoadFileReturn")[0]),
        //     error: function(){
        //         alert("Ocorreu um erro imprevisto. Contate o administrador do sistema.");
        //     },
        //     beforeSend: function(){
        //         $("#btnProsseguir").addClass("disabled");
        //         $("#btnProsseguir").html("Processando...");
        //     },
        //     success: function(retorno){
        //         console.log(retorno);
        //     }
        // });
    }
    function readXML(){
        alert("Vitor")
       
    }

// });
    
</script>

