<meta name="csrf-token" content="{{ csrf_token() }}" />

<form action="submit.php" id="user_form" method="POST">
                    <x-form.text label="Name" name="name" class="form-input" value="Sulaimaan" />
                    <x-form.email label="Email" name="email" class="form-input" value="" />
<br>
                    <label for="name">password:</label>

                    <input type="password" id="password" name="password">
                    <br>
                    <input type="button" id="sumbit_btn" class="btn btn-danger" value="Submit">
                </form>