<?php
error_reporting(E_ALL);
require_once("lib/config.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ESTOQUE DE PRODUTOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
<div>
    <table border="1px" width="75%" align="center">
        <tr>
            <th>Excluir - Editar - <?php echo "<a href=lib".DIRECTORY_SEPARATOR."delete.php?acao=truncate>Excluir tudo</a>"; ?></th>
            <th>Titulo</th>
            <th>Valor</th>
            <th>Produto</th>
            <th>Descrição</th>
        </tr>
    <?php
        class showProdutos
        {
            private $db_conn, $stmt, $row;

            public function __construct()
            {
                $this->db_conn = Cnx::conecta();
            }

            public function displayProd()
            {
                $this->stmt = $this->db_conn->prepare("SELECT * FROM produtos");
                $this->stmt->execute();

                if ($this->stmt->rowCount() > 0)
                {
                while ($this->row = $this->stmt->fetch(PDO::FETCH_ASSOC))
                {
                    echo ("

                            <tr>
                               <td><a href=lib".DIRECTORY_SEPARATOR."delete.php?id=".$this->row['id']."&acao=excluir>Excluir</a> - <a href=registra_estoque.php?id=".$this->row['id']."&acao=editar>Editar</a></td>
                               <td>".$this->row['titulo']."</td>
                               <td>R$".$this->row['valor']."</td>
                               <td><img src=".$this->row['dir']." width=100px></td>
                               <td>".$this->row['descricao']."</td>
                            </tr>

                    ");
                }
                } else
                {
                    echo ("<strong>Não há nenhum produto cadastrado, por favor cadastrar: </strong><a href=registra_estoque.php>Registrar produto</a>");
                }
            }
        }
        $Produtos = new showProdutos;
        $Produtos->displayProd();
    ?>
    </table>
    <a href="registra_estoque.php">Cadastrar produto</a>
</div>
</body>
</html>