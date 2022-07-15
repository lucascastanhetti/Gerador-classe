<?php

$banco = $_GET['banco'];

$conexao = pg_connect("host=localhost dbname=$banco port=5432 user=postgres password=1234");

$sql = "SELECT * FROM pg_catalog.pg_tables WHERE schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') 
 ORDER BY schemaname, tablename";
$res = pg_query($sql);
//$num = pg_num_rows($res);

?>
<label>Tabela:</label>
<select name="tabela" id="tabela">
  <?php while ($rowdados = pg_fetch_array($res)){
    echo "<option value='".$rowdados['tablename']."'>".$rowdados['tablename']."</option>";
  }
?>
</select>