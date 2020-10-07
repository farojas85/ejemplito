
<?php require_once "clases/Conexion.php"; 
$c= new conectar();
$conexion=$c->conexion();?>
<div class="col-sm-8">
                                            
                                        <select class="form-control input-sm" id="clienteVenta" name="PLATO">
				<option value="A">Selecciona</option>
			
				<?php
				$sql="SELECT Platoid,Platoname from tblplatos";
				$result=mysqli_query($conexion,$sql);
				while ($cliente=mysqli_fetch_row($result)):
					?>
					<option value="<?php echo $cliente[1] ?>"><?php echo $cliente[1] ?></option>
				<?php endwhile; ?>
			</select>
</div>