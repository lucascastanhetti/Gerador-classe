<?php
ini_set('display_errors', 0);
ini_set('display_startup_erros', 0);
error_reporting(E_ALL);


$banco = $_POST[banco];

$conexao = pg_connect("host=192.168.0.100 dbname=$banco port=5432 user=postgres password=1234");

$SQL = "SELECT column_name as field FROM information_schema.columns WHERE table_name ='" . $_POST[tabela]."';";
$executa = pg_query($SQL);
$tabela = $_POST[tabela];
$_POST[tabela] = ucfirst($_POST[tabela]);

$result = "class VO_" . $_POST["tabela"] . "{\n\n        //Atributos\n";

$fields = "";
$access_fields = "";
while ($reg = pg_fetch_array($executa)) {
    $array[] = $reg;
    $fields .= "	private $" . $reg["field"] . ";\n";
    $access_fields .= "	 public function get" . ucfirst($reg["field"]) . "(){\n	    return " . '$this->' . $reg["field"] . ";\n         }\n\n";
    $access_fields .= "	 public function set" . ucfirst($reg["field"]) . "($" . $reg["field"] . "){\n	    " . '$this->' . $reg["field"] . " = $" . $reg["field"] . ";\n         }\n\n";
}
$result .= $fields . "\n" . $access_fields;
$result .= "}";

// --------------------Dao-------------------------------
$num = count($array);

$dao = "class Dao_" . $_POST[tabela] . " extends Dao_ConnectionFactory{\n\n       private \$conn = null;\n\n       ";
$dao .= "public function __construct() {
          \$this->conn = Dao_ConnectionFactory::createConn();
       }\n\n       ";

$arrayIns = $array;
$arrayIns[0] = null;
$cont = 1;
foreach ($arrayIns as $k => $ins) {
    if ($ins != null) {
        if ($i == null) {
            $i = $ins[field];
            $c = $ins[field];
            $campoup = $ins[field] . " = ?";
            $interrogacao = "?";
            $i = ucfirst($ins[field]);
            $v = "\$vo->set$i(\$r[\"$ins[field]\"]);";
            $bind = "          \$query->bindValue($cont, \$vo->get$i(), PDO::PARAM_STR);\n";
            $null = "null";
            $cont++;
        } else {
            $i .= "," . $ins[field];
            $c .= "," . $ins[field];
            $campoup .= "," . $ins[field] . " = ?";
            $interrogacao .= ",?";
            $i = ucfirst($ins[field]);

            $v .= "\$vo->set$i(\$r[\"$ins[field]\"]);";
            $null .= ",null";
            $bind .= "          \$query->bindValue($cont, \$vo->get$i(), PDO::PARAM_STR);\n";
            $cont++;
        }
    }
}
$insert = "public function insert(VO_$_POST[tabela] \$vo){\n\n          ";
$insert .= "\$SQL = \"INSERT INTO $tabela(";
$insert .= $c . ")\";\n          \$SQL .= \" VALUES(" . $interrogacao . ")\";\n          \$query = \$this->conn->prepare(\$SQL);\n";
$insert .= $bind;
$insert .= "\n          if(\$query->execute()){\n              return true;\n          } else {\n              return false;\n          }\n       }\n\n       ";
$delete .= "public function delete(VO_$_POST[tabela] \$vo){\n
          \$SQL = \"DELETE FROM $tabela WHERE id = ?\";\n          \$query = \$this->conn->prepare(\$SQL);
          \$query->bindValue(1, \$vo->getId(), PDO::PARAM_INT);
          
          if(\$query->execute()){
            return true;
          } else {
            return false;
          }
       }\n\n       ";
$update = "public function update(VO_$_POST[tabela] \$vo){
                \$SQL = \"UPDATE $tabela SET $campoup\";\$SQL .= \" WHERE id = ?\";        
        \$query = \$this->conn->prepare(\$SQL);        
        $bind
        \$query->bindValue($cont, \$vo->getId(), PDO::PARAM_INT);
        
        if(\$query->execute()){
            return true;
        } else {
            return false;
        }
    }";
$getall = "public function getAll(){
        \$SQL = \"SELECT * FROM $tabela\";
            
        \$query = \$this->conn->prepare(\$SQL);
        \$query->execute();
        
        \$hash[] = array();
        
        while(\$r = \$query->fetch()){
            \$vo = new VO_$_POST[tabela]($null);
            \$vo->setId(\$r[\"id\"]);
            $v
            \$hash[\$vo->getId()] = \$vo;
        }
       
        return \$hash;
    }";
$getid = "public function getById(\$id){
        
        \$SQL = \"SELECT * FROM $tabela WHERE id = ?\";
        
        \$query = \$this->conn->prepare(\$SQL);
        
        \$query->bindValue(1, \$id, PDO::PARAM_INT);
        \$query->execute();
        
        \$vo = new VO_$_POST[tabela]($null);
        
        while(\$r = \$query->fetch()){
            \$vo->setId(\$r[\"id\"]);
            $v
        }
        
        return \$vo;
    }";
$dao = $dao . $insert . $delete . $update . $getall . $getid . "}";


$bll = "class Bll_$_POST[tabela] {
        
        public function insert(VO_$_POST[tabela] \$vo) {
            \$dao = new Dao_$_POST[tabela]();                        
            return \$dao->insert(\$vo);
        }

        public function update(VO_$_POST[tabela] \$vo) {
            //\$vo->setSenha(\$vo->getSenha());
            \$dao = new Dao_$_POST[tabela]();
            return \$dao->update(\$vo);
        }

        public function delete(VO_$_POST[tabela] \$vo) {
            \$dao = new Dao_$_POST[tabela]();
            return \$dao->delete($vo);
        }

        public function getAll() {
            \$dao = new Dao_$_POST[tabela]();
            return \$dao->getAll();
        }

        public function getById(\$id) {
            \$dao = new Dao_$_POST[tabela]();
            return \$dao->getById(\$id);
        }
}";
?>
<table style="size: 100%">
    <tr>
        <td>
            <textarea rows="100%" cols="50%"><?php echo $result; ?></textarea>
        </td>
        <td>
            <textarea rows="100%" cols="50%"><?php echo $dao; ?></textarea>
        </td>
        <td>
            <textarea rows="100%" cols="50%"><?php echo $bll; ?></textarea>
        </td>
    </tr>
</table>

