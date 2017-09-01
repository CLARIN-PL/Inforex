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
							<form method="POST" action="index.php" class="form-horizontal">
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
							<form class="form-horizontal" method="POST" action="index.php">
								<input type="hidden" name="ajax" value="clarin_new_user"/>
								<input type="hidden" name="mode" value="new"/>
								<div class="form-group">
									<label for="disabledSelect" class="col-sm-3 control-label">Email</label>
									<div class="col-sm-9">
										<input name="email" type="email" class="form-control" value="{$email}">
									</div>
								</div>
								<div class="form-group">
									<label for="disabledSelect" class="col-sm-3 control-label">Your screen name</label>
									<div class="col-sm-9">
										<input name="name" type="text" class="form-control" value="{$screenname}">
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
</div>


{*<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tokenization&amp;id={$report_id}" enctype="multipart/form-data">*}
	{*Select and upload XCES file:*}
	{*<input class="button" type="file" name="xcesFile" />*}
	{*<input type="hidden" name="action" value="report_set_tokens"/>*}
	{*<input type="hidden" id="report_id" value="{$row.id}"/>*}
	{*<input class="button" type="submit" value="Submit"/>*}
{*</form>*}

{include file="inc_footer.tpl"}