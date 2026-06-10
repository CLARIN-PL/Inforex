{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables home-corpora-page oidc-link-page">
	<div class="oidc-link-hero">
		<div class="oidc-link-hero-badge"><i class="fa fa-shield" aria-hidden="true"></i> Federated login</div>
		<h1 class="oidc-link-hero-title">Connect your federated login with Inforex</h1>
		<p class="oidc-link-hero-text">Use federated authentication for sign-in and keep roles, permissions and corpora access in the local Inforex account.</p>
	</div>

	<div class="row home-corpora-grid oidc-link-grid">
		<div class="col-md-6 home-corpora-column">
			<div class="panel scrollingWrapper administration-content-panel home-corpora-panel oidc-link-panel">
				<div class="panel-heading administration-content-heading">
					<span class="administration-content-heading-icon"><i class="fa fa-link" aria-hidden="true"></i></span>
					<span>Link existing account</span>
					<span class="home-corpora-counter">Recommended</span>
				</div>
				<div class="panel-body">
					<div class="home-corpora-empty oidc-link-intro">
						<i class="fa fa-user-circle-o" aria-hidden="true"></i>
						<span>Use your old Inforex credentials once to connect federated login with the local account that already has your roles and permissions.</span>
					</div>
					<form id="loginFormOidc" method="POST" action="index.php?page=login_oidc_link" class="oidc-link-form">
						<input type="hidden" name="action" value="oidc_link_account"/>
						<input type="hidden" name="mode" value="update"/>
						<div class="home-corpora-toolbar oidc-link-toolbar">
							<label>Legacy login</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
								<input name="username" type="text" class="form-control" placeholder="login you've been using so far...">
							</div>
						</div>
						<div class="home-corpora-toolbar oidc-link-toolbar">
							<label>Password</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
								<input name="password" type="password" class="form-control" placeholder="password you've been using so far...">
							</div>
						</div>
						<div class="oidc-link-actions">
							<button type="submit" class="btn btn-primary home-corpora-create-button oidc-link-submit">
								<i class="fa fa-link" aria-hidden="true"></i>
								<span>Link my account</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-6 home-corpora-column">
			<div class="panel scrollingWrapper administration-content-panel home-corpora-panel oidc-link-panel">
				<div class="panel-heading administration-content-heading">
					<span class="administration-content-heading-icon"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
					<span>Create local profile</span>
					<span class="home-corpora-counter">New user</span>
				</div>
				<div class="panel-body">
					<div class="home-corpora-empty oidc-link-intro">
						<i class="fa fa-id-card-o" aria-hidden="true"></i>
						<span>Create a fresh Inforex profile linked to federated login. The local profile will still own your application roles and permissions.</span>
					</div>
					<form id="newUserForm" class="oidc-link-form" method="POST" action="index.php?page=login_oidc_link">
						<input type="hidden" name="action" value="oidc_link_account"/>
						<input type="hidden" name="mode" value="new"/>
						<div class="home-corpora-toolbar oidc-link-toolbar">
							<label>Username</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><i class="fa fa-at" aria-hidden="true"></i></span>
								<input type="text" class="form-control" value="{$username|escape}" disabled>
							</div>
						</div>
						<div class="home-corpora-toolbar oidc-link-toolbar">
							<label>Email</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
								<input name="email" type="email" class="form-control" value="{$email|escape}" required>
							</div>
						</div>
						<div class="home-corpora-toolbar oidc-link-toolbar">
							<label>Screen name</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><i class="fa fa-id-badge" aria-hidden="true"></i></span>
								<input name="name" type="text" class="form-control" value="{$screenname|escape}" required>
							</div>
						</div>
						<div class="oidc-link-actions">
							<button type="submit" class="btn btn-primary home-corpora-create-button oidc-link-submit">
								<i class="fa fa-user-plus" aria-hidden="true"></i>
								<span>Create new account</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
