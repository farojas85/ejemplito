<?php
error_reporting(0);
if(isset($_POST['submit']))
{
$fname=$_POST['fname'];
$mnumber=$_POST['mobilenumber'];
$email=$_POST['email'];
$password=md5($_POST['password']);
$direccion=$_POST['direccion'];
$sql="INSERT INTO  tblusers(FullName,MobileNumber,Direccion,EmailId,Password) VALUES(:fname,:mnumber,:direccion,:email,:password)";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mnumber',$mnumber,PDO::PARAM_STR);
$query->bindParam(':direccion',$direccion,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$_SESSION['msg']="Usted ha sido registrado con éxito. Ahora puedes iniciar sesión ";
header('location:thankyou.php');
}
else 
{
$_SESSION['msg']="Algo salió mal. Inténtalo de nuevo.";
header('location:thankyou.php');
}
}
?>
<!--Javascript for check email availabilty-->
<script>
function checkAvailability() {

$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#email").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
						</div>
							<section>
								<div class="modal-body modal-spa">
									<div class="login-grids">
										<div class="login">
											<div class="login-left">
												<ul>
													<li><a class="fb" href="https://www.facebook.com/VIP-WADO-383080992423404/"><i></i>Facebook</a></li>
													<li><a class="goog" href="https://www.facebook.com/VIP-WADO-383080992423404/"><i></i>Google</a></li>
													
												</ul>
											</div>
											<div class="login-right">
												<form name="signup" method="post">
													<h3>Crea tu cuenta </h3>
					

				<input type="text" value="" placeholder="Nombre completo" name="fname" autocomplete="off" required="">
				<input type="text" value="" placeholder="numero celular" maxlength="10" name="mobilenumber" autocomplete="off" required="">
                                <input type="text" value="" placeholder="Dirección del domicilio" maxlength="50" name="direccion" autocomplete="off" required="">
		<input type="text" value="" placeholder="id Email " name="email" id="email" onBlur="checkAvailability()" autocomplete="off"  required="">	
		 <span id="user-availability-status" style="font-size:12px;"></span> 
	<input type="password" value="" placeholder="Password" name="password" required="">	
													<input type="submit" name="submit" id="submit" value="CREAR UNA CUENTA">
												</form>
											</div>
												<div class="clearfix"></div>								
										</div>
									
									</div>
								</div>
							</section>
					</div>
				</div>
			</div>