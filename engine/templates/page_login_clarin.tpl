{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="container">
	<div class="col-md-12">
		<div class="row">
			<h3 class="text-center">You are logging into inforex for the first time using Clarin federation login.</h3>
			<div class="col-sm-6">
				<div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
					<div class="panel-heading">I have an existing account</div>
					<div class="panel-body scrolling">
						<div class="navbar-collapse collapse">
							<h4>I would you like to continue using my old account, using clarin federation login.</h4>
							<hr>
							<form id="loginFormClarin" method="POST" action="index.php" class="form-horizontal">
								<input type="hidden" name="ajax" value="clarin_new_user"/>
								<input type="hidden" name="mode" value="update"/>
								<div class="form-group">
									<label for="disabledTextInput" class="col-sm-3 control-label">Old login</label>
									<div class="col-sm-9">
										<input name="username" type="text" class="form-control" placeholder="login you've been using so far...">
									</div>
								</div>
								<div class="form-group">
									<label for="disabledSelect" class="col-sm-3 control-label">Old password</label>
									<div class="col-sm-9">
										<input name="password" type="text" class="form-control" placeholder="password you've been using so far...">
									</div>
								</div>
								<button type="submit" class="btn btn-primary btn-block">Update my account</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
					<div class="panel-heading">This is my first time using inforex. I don't have an account.</div>
					<div class="panel-body scrolling">
						<div class="navbar-collapse collapse">
							<h4>I am a new user. I would like to create a brand new account.</h4>
							<hr>
							<form id="newUserForm" class="form-horizontal" method="POST" action="index.php">
								<input type="hidden" name="ajax" value="clarin_new_user"/>
								<input type="hidden" name="mode" value="new"/>
								<div class="form-group">
									<label for="disabledSelect" class="col-sm-3 control-label">Email</label>
									<div class="col-sm-9">
										<input name="email" type="email" class="form-control" value="{$email}" required>
									</div>
								</div>
								<div class="form-group">
									<label for="disabledSelect" class="col-sm-3 control-label">Your screen name</label>
									<div class="col-sm-9">
										<input name="name" type="text" class="form-control" value="{$screenname}" required>
									</div>
								</div>
								<button type="submit" class="btn btn-primary btn-block">Create new account</button>
							</form>
						</div>
					</div>
				</div>
			</div>			
		</div>
	</div>


	<div id="content" style="position:absolute; top:20px; left:0; height: 100%;"></div>
    {literal}
		<script>
            function load_home() {
                $.ajax({
                    async: true,
                    url: 'http://inforex-dev.clarin-pl.eu/clarin_bar',
                    success: function(response) {
						$('#content').html(response);
                    }
                });
            }
            load_home();
		</script>
    {/literal}
</div>

{include file="inc_footer.tpl"}