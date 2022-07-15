<?php
include('conexao.php');
$res = pg_query("select pd.datname as name from pg_database pd");
$num = pg_num_rows($res);
for ($i = 0; $i < $num; $i++) {
  $dados = pg_fetch_array($res);
  $arrBanco[$dados['name']] = $dados['name'];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script>
      
      $(function (){
        $( "#dialog-form" ).dialog({
          autoOpen: false,
          height: 300,
          width: 350,
          modal: true,
          buttons: {
            "Criar nova Tabela": function() {
              salvar_tabela(); 
              $( this ).dialog( "close" );
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          },
          close: function() {
            $('#tabela_box').val('');
          }
        });
      });
      
      
      function buscar_tabela(){
        
        var banco = $('#banco').val();
        if(banco){
          var url = 'ajax_buscar_tabela.php?banco='+banco;
          $.get(url, function(dataReturn) {
            $('#load_tabela').html(dataReturn);
          });
        }
      }
      
      function nova_tabela(){
        if($('#banco').val()==''){
          alert('Selecione o banco');
        }else{
          $( "#dialog-form" ).dialog( "open" );
        }
        
      }
      
      function salvar_tabela(){
        var tabela_box = $('#tabela_box').val();
        var banco = $('#banco').val();
        if(tabela_box!='' && tabela!=''){
          var url = 'ajax_salvar_tabela.php?banco='+banco+'&tabela_box='+tabela_box;
          $.get(url, function() {
            buscar_tabela();
          });
        }
        
      }
    </script>
  </head>
  <body style="font-size: 12px; font-family: 'Arial'">
      Gerador de Classe Banco
      <form action="funcao.php" method="post">
      <div>
        <label>Banco:</label>
        <select name="banco" id="banco" onchange="buscar_tabela()">
          <option value="">Selecione o Banco...</option>
          <?php
          foreach ($arrBanco as $value => $name) {
            echo "<option value='{$value}'>{$name}</option>";
          }
          ?>
        </select>
      </div>
      <div id="load_tabela">
        <label>Tabelas:</label>
        <select name="tabela" id="tabela">
          <option value="">Selecione a tabela</option>
        </select>
      </div>
      <div>
          <input type="submit" value="OK"></input>
      </div>
    </form>
  </body>
</html>