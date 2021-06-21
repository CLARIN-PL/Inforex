<div data-role="appbar" class="pos-absolute bg-darkCyan fg-white">

    <a href="#" class="app-bar-item d-block d-none-lg" id="paneToggle"><span class="mif-menu"></span></a>

    <div class="app-bar-container ml-auto">
        <div class="app-bar-container">
            {if $user}
            <a href="#" class="app-bar-item">
                <img src="gfx/default-user.jpg" class="avatar">
                <span class="ml-2 app-bar-name">
                    {if isset($user.login)}{$user.login}{/if}
                    {if isset($element.screename)}{$element.screename}{/if}
                </span>
            </a>
            <div class="user-block shadow-1" data-role="collapse" data-collapsed="true">
                <div class="bg-darkCyan fg-white p-2 text-center">
                    <img src="gfx/default-user.jpg" class="avatar">
                    <div class="h4 mb-0">
                        {if isset($user.login)}{$user.login}{/if}
                        {if isset($element.screename)}{$element.screename}{/if}
                    </div>
                    <div></div>
                </div>
                <div class="bg-white d-flex flex-justify-between flex-equal-items p-2 bg-light">
                    <button class="button mr-1">Profile</button>
                    <button class="button ml-1">Sign out</button>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>
