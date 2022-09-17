<?php
namespace MF\Model;
use App\Connection;

class Container{
    public static function getModel($model){
        $conn = Connection::getDb();

        $class = "\\App\\models\\".ucfirst($model);
        
        return new $class($conn);
    }
}

?>