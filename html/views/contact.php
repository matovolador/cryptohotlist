<div class="panel-body">
	<h3>Contact</h3>
	<form id="form">
		<div class="form-group">
			<label for="email">Email address</label>
			<input type="email" class="form-control" id="email" placeholder="Enter email" required>
		</div>
		<div class="form-group">
		    <label for="message">Message</label>
		    <textarea class="form-control" id="message" rows="3" placeholder="Enter Message" required></textarea>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
	<script type="text/javascript">
		$("#form").validate();
	</script>
</div>
