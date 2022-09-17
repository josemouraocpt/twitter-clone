<?php
namespace APP\Models;

use MF\Model\Model;

class Usuario extends Model{
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($attr){
        return $this->$attr;
    }

    public function __set($attr, $value){
        $this->$attr = $value;
    }

    //salvar
    public function salvar(){
        $query = "insert into usuarios(nome, email, senha) values(:nome, :email,:senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get("nome"));
        $stmt->bindValue(':email', $this->__get("email"));
        $stmt->bindValue(':senha', $this->__get("senha")); //criptografar com MD5
        $stmt->execute();

        return $this;
    }


    //validar cadastro
    public function validarCadastro(){
        $valido = true;

        if(strlen($this->__get("nome")) < 3 ){
            $valido = false;
        }
        if(strlen($this->__get("email")) < 3 ){
            $valido = false; 
        }
        if(strpos($this->__get("email"), "@") == false){
            $valido = false;
        }
        if(strpos($this->__get("email"), ".com") == false){
            $valido = false;
        }
        if(strlen($this->__get("senha")) < 3 ){
            $valido = false;
        }
        return $valido;
    }

    //recuperar usuario
    public function getUsuarioPorEmail(){
        $query = "select nome, email from usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get("email"));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar(){
        $query = "select id, nome, email from usuarios where email = :email and senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get("email"));
        $stmt->bindValue(':senha', md5($this->__get("senha")));
        $stmt->execute();
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if($usuario['id'] != "" && $usuario['email'] != ""){
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }
        return $this;
    }

    //recuperar todos os usuarios
    public function getAll(){
        $query = "
        select 
            u.id, 
            u.nome, 
            u.email, 
            (
                select 
                    count(*)
                from
                    usuarios_seguidores as us
                where
                    us.id_usuario = :id and us.id_usuario_seguindo = u.id
            ) as seguindo_sn 
        from 
            usuarios as u 
        where 
            u.nome like :nome 
        and 
            u.id != :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get("nome").'%');
        $stmt->bindValue(':id', $this->__get("id"));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //seguir
    public function seguir($id_usuario_seguindo){
        $query = "insert into usuarios_seguidores (id_usuario, id_usuario_seguindo) values (:id, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get("id"));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //deixar de seguir
    public function deixarDeSeguir($id_usuario_seguindo){
        $query = "delete from usuarios_seguidores where id_usuario = :id and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get("id"));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getInfoUsuario(){
        $query = "select nome from usuarios where id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id"));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalTweets(){
        $query = "select count(*) as total_tweet from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id"));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalSeguindo(){
        $query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id"));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalSeguidores(){
        $query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id"));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}  

?>