<?php 
error_reporting(E_ALL);
require_once "lib/config.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registrar produto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        div
        {
            text-align: center;
        }
    </style>
    <script src="main.js"></script>
</head>
<body>
<div>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="f_titulo" placeholder="Insira o titulo aqui" value="<?php echo regEstoque::returnData("titulo"); ?>"><br>
    <input type="number" name="f_valor" value="<?php echo regEstoque::returnData("valor"); ?>"><br>
    <textarea name="f_descricao"><?php echo regEstoque::returnData("descricao"); ?></textarea><br>
    <input type="file" name="f_produto[]" multiple=""><br>
    <input type="submit" name="f_enviar" value="<?php if (isset($_GET['id']) and $_GET['acao'] == 'editar') echo "Alterar"; else echo "Cadastrar"; ?>">
</form>
<br>
<strong>Extensões permitidas: jpg, png e jpeg</strong>
<br>
<?php
class regEstoque
{
    private $stmt, $db_conn, $extensions, $file_array, $path, $ext_err, $errors, $ext_allow, $descricao, $titulo, $valor, $name;
    public static $row, $static_db_conn, $static_stmt;

    public function __construct()
    {
        $this->db_conn = Cnx::conecta();
        $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
/*
    private function pre_r($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
*/
    public static function returnData($campo)
    {
        if (isset($_GET['id']) and $_GET['acao'] == 'editar')
        {
            self::$static_db_conn = Cnx::conecta();

            self::$static_stmt = self::$static_db_conn->prepare("SELECT ".$campo." FROM produtos WHERE id = ".$_GET['id']);
            self::$static_stmt->execute();

            switch (self::$row = self::$static_stmt->fetch(PDO::FETCH_ASSOC)) 
            {
                case $campo == 'titulo':
                    return self::$row['titulo'];
                    break;
                
                case $campo == 'descricao':
                    return self::$row['descricao'];
                    break;

                case $campo == 'valor':
                    return self::$row['valor'];
                    break;

                case $campo == 'dir':
                    return self::$row['dir'];
                    break;
            }
        }
    }

    private function error()
    {
         $this->errors = array(
            0 => 'não houve erro, o upload foi bem sucedido',
            1 => 'O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini',
            2 => 'O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML',
            3 => 'O upload do arquivo foi feito parcialmente',
            4 => 'Nenhum arquivo foi enviado',
            6 => ' Pasta temporária ausênte',
            7 => 'Falha em escrever o arquivo em disco',
            8 => 'Uma extensão do PHP interrompeu o upload do arquivo. O PHP não fornece uma maneira de determinar qual extensão causou a interrupção'
        );
    }

    private function Insert_update($query)
    {
        if (!empty($_POST['f_valor']) and !empty($_POST['f_descricao']))
        {
        $this->stmt = $this->db_conn->prepare($query);

        $this->descricao = $_POST['f_descricao'];
        $this->valor = $_POST['f_valor'];
    
            $this->stmt->execute(array(
                ':n_descricao' => $this->descricao,
                ':n_titulo' => $this->titulo,
                ':n_valor' => doubleval($this->valor),
                ':n_dir' => $this->path
            ));
        
        } else
        {
            echo "<strong>Por favor verifique os campos!</strong>";
            exit();
        }
    }

    public function regProduto($dir)
    {
        if (isset($_FILES['f_produto']))
        {
            $this->reArray($_FILES['f_produto']); // Organzar o array $_FILES para melhor manipulação
            //$this->pre_r($this->file_array);

            for ($i = 0; $i < count($this->file_array); $i++) // Percorre em todos os elementos do array, ou seja, percorre a todos os arquivos enviados
            {
                if ($this->file_array[$i]['error']) 
                {
                    $this->error();
                    echo $this->errors[$this->file_array[$i]['error']];
                } else
                {
                    $this->extensions = array('png', 'jpeg', 'jpg', 'PNG', 'JPG', 'JPEG');

                    $this->ext_allow = explode(".", $this->file_array[$i]['name']);

                    if (empty($_POST['f_titulo']))
                    {
                        $this->titulo = $this->ext_allow[0];
                        $this->titulo = str_replace("-", " ", $this->titulo);
                        $this->titulo = ucwords($this->titulo);
                    } else 
                    {
                        $this->titulo = $_POST['f_titulo'];
                    }

                    $this->ext_allow = end($this->ext_allow);

                    if (!in_array($this->ext_allow, $this->extensions))
                    {
                        echo $this->file_array[$i]['name'].' - extensão inválida!';
                    } else
                    {
                        $this->name = str_replace(" ", "_", $this->file_array[$i]['name']);
                        $this->path = $dir.DIRECTORY_SEPARATOR.$this->name;

                        if (isset($_GET['id']) and $_GET['acao'] == 'editar') 
                        {
                                $this->Insert_update("UPDATE produtos SET descricao = :n_descricao, titulo = :n_titulo, valor = :n_valor, dir = :n_dir WHERE id = ".$_GET['id']);   
                        } else 
                        {
                            $this->Insert_update("INSERT INTO produtos (descricao, titulo, valor, dir) values (:n_descricao, :n_titulo, :n_valor, :n_dir)");
                        }

                        move_uploaded_file($this->file_array[$i]['tmp_name'], $this->path);
                        echo ($this->file_array[$i]['name']." - <strong>foi enviado com sucesso!</strong>");
                    }
                }
            }
        }
    }

    private function reArray($file_ary)
    {
        $this->file_array = array();
        $array_count = count($file_ary['name']);
        $array_key = array_keys($file_ary);

        for ($i = 0; $i < $array_count; $i++)
        {
            foreach ($array_key as $key) 
            {
                $this->file_array[$i][$key] = $file_ary[$key][$i];
            }
        }
        return $this->file_array;
    }
}
$regEstoque = new regEstoque;
$regEstoque->regProduto('img');
?>
<br>
<a href="index.php">Visualizar</a>
</div>
</body>
</html>