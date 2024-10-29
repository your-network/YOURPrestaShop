{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
* @author    Simul Digital
* @copyright 2016-2024 Simul Digital
* @license   LICENSE.txt
**}


{if $dashboardHTML!=''}
<div id="modulecontent" class="clearfix">
    <div id="sdyourio-menu">
        <div class="col-lg-12">
            <div class="list-group" v-on:click.prevent>
                <a href="#" class="list-group-item" v-bind:class="{ 'active': isActive('yourDashboardConfiguration') }" v-on:click="makeActive('yourDashboardConfiguration')">
                    <i class="icon-cog"></i>
                    {l s='Dashboard' mod='sd_yourio'}
                </a>
                <a href="#" class="list-group-item" v-bind:class="{ 'active': isActive('designConfiguration') }" v-on:click="makeActive('designConfiguration')">
                    <i class="icon-desktop"></i> {l s='Design' mod='sd_yourio'}
                </a>
            </div>
        </div>
        <div id="yourDashboardConfiguration" class="col-lg-12 sdyourio_menu wk-menu-hide">
            <div class="yourDashboard"></div>
        </div>
        <div id="designConfiguration" class="col-lg-12 sdyourio_menu wk-menu-hide">
            {$designConfiguration}
        </div>
    </div>
</div>
{else}
<div class="your-onbaording-process">
    <div class="alert alert-danger ds-none"></div>
    <div class="alert alert-success ds-none"></div>
    <img src="{$img_dir}/your_logo.svg" alt="YourIO"/>
    <div class="onboarding-process-action">
        <div class="process-actions">
            <h2>{l s='Setup YOUR Account' d='Modules.Admin.AdminOnBoarding'}</h2>
            <button class="btn btn-primary btn-processed">
                {l s='Get Start' d='Modules.Admin.AdminOnBoarding'}
            </button>
        </div>
    </div>
</div>
<span class="loader"></span>
{/if}
