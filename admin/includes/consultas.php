<?php
class Consultas
{
    public $tabla;     
    public $campos;        
    public $condicion;
    public $orden;
    public $valores;
    public $inicio;
    public $limite;
    public $cantidad_registros;

    function obtenerRegistrosTodos($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetchAll();
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function obtenerRegistrosTodosOrden($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla." ".$this->orden;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetchAll();
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function obtenerRegistroUnoOrden($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla." ".$this->orden;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetch(PDO::FETCH_OBJ);
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function obtenerRegistrosTodosOrdenPaginacion($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla." ".$this->orden.
                    " LIMIT ".$this->inicio.", ".$this->limite ;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetchAll();
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function obtenerUnoRegistroCondicion($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla." WHERE ".$this->condicion;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetch(PDO::FETCH_OBJ);
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function obtenerRegistrosCondicion($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "SELECT ".$this->campos." FROM ".$this->tabla." WHERE ".$this->condicion;
            
            $statment = $dbh->prepare($sql);
            $statment->execute();

            $this->cantidad_registros =$statment->rowCount();

            return $statment->fetchAll();
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
    }

    function insertarDatos($dbh,$datos)
    {
        $mensaje ="";
        try 
		{
            $sql = "INSERT INTO ".$this->tabla."(".$this->campos.") VALUES (".$this->valores.")";
            $statment = $dbh->prepare($sql);
            $statment->execute($datos);
            return $dbh->lastInsertId();
        } 
        catch (Exception $e) 
		{
			return $e->getMessage();
        }
        
    }

    function actualizarDatos($dbh,$datos)
    {
        $mensaje ="";
        try 
		{
            $sql = "UPDATE ".$this->tabla."(".$this->campos.") VALUES (".$this->valores.")";
            $statment = $dbh->prepare($sql);
            $statment->execute($datos);
            return $dbh->lastInsertId();
        } catch (Exception $e) 
		{
			return $e->getMessage();
        }
        
    }


    function eliminarRegistro($dbh)
    {
        $mensaje ="";
        try 
		{
            $sql = "DELETE FROM ".$this->tabla." WHERE ".$this->condicion;
            $statment = $dbh->prepare($sql);
            return $statment->execute();
        } catch (Exception $e) 
		{
			return $e->getMessage();
        }
        
    }

}