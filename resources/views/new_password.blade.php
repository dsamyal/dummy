<center>Verify Successfully
    <br />
    <br /><br />
    <form action="../submit_pass" method="post">
    	{{ csrf_field() }}
    <label>Enter Password</label>
    <input type="password" placeholder="Password" id="password" name="pass" required>
    <label>Confirm Password</label>
    <input type="password" placeholder="Confirm Password" name="conf_pass" id="confirm_password" required>
    <input type="hidden" value="{{ $eid }}" name="id" />
    <input type="submit" name="">
    </form>
</center>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">
	
	var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

	function validatePassword(){
	  if(password.value != confirm_password.value) {
	    confirm_password.setCustomValidity("Passwords Don't Match");
	  } else {
	    confirm_password.setCustomValidity('');
	  }
	}

	password.onchange = validatePassword;
	confirm_password.onkeyup = validatePassword;

</script>

