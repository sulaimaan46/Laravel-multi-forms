<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Users List</title>
<!-- CSS files -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

<!-- JavaScript files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
    <div id="element-container"></div>

		<h1>Users List</h1>
        <a href="#" class="btn btn-success" id="add_users" data-toggle="modal" data-target="#myModal">Add Users</a>

        <div class="card">
            <div class="card-body">
                <div class="row">

                <!-- @include('users.form') -->
                </div>
            </div>
        </div>
		<table class="table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Role</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>John Smith</td>
					<td>john@example.com</td>
					<td>Admin</td>
				</tr>
				<tr>
					<td>Jane Doe</td>
					<td>jane@example.com</td>
					<td>User</td>
				</tr>
				<tr>
					<td>Bob Johnson</td>
					<td>bob@example.com</td>
					<td>User</td>
				</tr>
			</tbody>
		</table>
	</div>
    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Modal Title</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form fields go here -->
        <div class="form-input-body">

        </div>
      </div>
      </div>
    </div>
  </div>
</div>
</body>
<script>

    $('#add_users').on('click',function(){
            // Submit form data via AJAX
            $.ajax({
                url: "{{route('users.create')}}", // Replace with your server-side script URL
                type: 'GET',
                success: function(response) {
                // Add new element to container
                console.log(response);
                        if(response.status && response.html){
                            $('.form-input-body').html('')
                            $('.form-input-body').html(response.html)
                        }
                },
                error: function(xhr, status, error) {
                // Handle errors
                $('#element-container').append('<div class="alert alert-danger">' + error + '</div>');
                }
            });
      });

        $(document).on('click','#sumbit_btn',function(){
          var data = $('#user_form :input');

          // not sure if you wanted this, but I thought I'd add it.
          // get an associative array of just the values.
          var values = {};
          data.each(function() {
              values[this.name] = $(this).val();
          });
          console.log(values);
            // Submit form data via AJAX
            $.ajax({
                url: "{{route('users.store')}}", // Replace with your server-side script URL
                type: 'POST',
                data:$('#user_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                
                success: function(response) {
                // Add new element to container
                console.log(response);
                        if(response.status && response.html){
                            $('.form-input-body').html('')
                            $('#myModal').hide()
                        }
                },
                error: function(xhr, status, error) {
                // Handle errors
                $('#element-container').append('<div class="alert alert-danger">' + error + '</div>');
                }
            });
        })
</script>
</html>
