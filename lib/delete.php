<?php
require_once "config.php";

class Del
{
    private $db_conn, $stmt;

    public function __construct()
    {
        $this->db_conn = Cnx::conecta();
    }

    private function Query($query)
    {
        $this->stmt = $this->db_conn->prepare($query);
        $this->stmt->execute(array(
            ':id' => (int)$_GET['id']
        ));
    }

    public function deleteProd()
    {
        if (isset($_GET['id']) and $_GET['acao'] == 'excluir')
        {
            $this->Query("DELETE FROM produtos WHERE id = :id");

            if ($this->stmt)
            {
                header("Location: ../index.php");
            } else
            {
                echo "<script>alert('Não foi possível excluir!')</script>";
            }
        } elseif (isset($_GET['acao']) == 'truncate')
        {
            $this->Query("TRUNCATE table produtos");
            
            if ($this->stmt)
            {
                header("Location: ../index.php");
            } else
            {
                echo "<script>alert('Não foi possível excluir tudo!')</script>";
            }
        }
    }
}
$class_del = new Del;
$class_del->deleteProd();
?>