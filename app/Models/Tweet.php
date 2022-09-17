<?php
namespace APP\Models;

use MF\Model\Model;

class Tweet extends Model{
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($attr){
        return $this->$attr;
    }

    public function __set($attr, $value){
        $this->$attr = $value;
    }

    public function salvar(){
        $query = 'insert into tweets(id_usuario, tweet) values (:id_usuario, :tweet)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id_usuario"));
        $stmt->bindValue(':tweet', $this->__get("tweet"));
        $stmt->execute();

        return $this;
    }

    public function getAll(){
        $query = "
        select 
            u.nome, 
            t.id, 
            t.id_usuario, 
            t.data, 
            t.tweet 
            from 
                tweets as t 
            left join 
                usuarios as u 
            on 
                (t.id_usuario = u.id) 
            where 
                t.id_usuario = :id_usuario 
            or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
            order by 
                t.data desc";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id_usuario"));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleta($id_delete){
        $query = 'delete from tweets where id = :id_delete and id_usuario = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get("id_usuario"));
        $stmt->bindValue(':id_delete', $id_delete);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}

?>