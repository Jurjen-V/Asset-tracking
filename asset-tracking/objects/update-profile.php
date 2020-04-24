<!-- Update profile modal form -->
<div id="modal2" class="modal add_assets modal2">
	<div class="modal-content">
		<h4 class="standard-color">Update account</h4>
		<form class="col s12 animate" action="" method="post">
		   	<div class="row">
		   		<!-- input email -->
				<div class="input-field col s12" id="name">
					<input id="Email" value="<?=$email?>" class="validate" type="email" required name="email">
	          		<label for="Email">E-mail Address</label>
	          		<span class="helper-text" data-error="Geen correct e-mailadres" data-success="correct">voorbeeld@voorbeeld.nl</span>
				</div>
			</div>
			<div class="row">
				<!-- input password_1 -->
				<div class="input-field col s12" id="password">
					<input id="Password_1" minlength="10" required type="password" class="validate" name="password_1">
			        <label for="Password_1">Password</label>
			        <span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
			<div class="row">
				<!-- input password_2 -->
				<div class="input-field col s12" id="password">
					<input id="Password_2" minlength="10" required type="password" class="validate" name="password_2">
	        		<label for="Password_2">Confrim password</label>
	        		<span class="helper-text" data-error="Wachtwoord is te kort" data-success="correct">10 karakters lang</span>
				</div>
			</div>
		     <div class="input-group">
		     	<!-- Submit button -->
	      		<button id="submit" class="btn waves-effect standard-bgcolor" type="submit" name="update">Update</button>
	    	</div>
	    	<!-- cancel button to close modal -->
	      		<button id="Cancel_add" type="button" class="btn waves-effect  modal-close">Cancel</button>
	    	</div>
		 </form>
	</div>
</div>