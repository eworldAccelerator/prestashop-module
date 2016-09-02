{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.png" style="margin-top:16px;" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='Your Web Performance Optimization system' mod='eworldaccelerator'}</h2>
			<h4>{l s='is correctly installed on your Prestashop' mod='eworldaccelerator'}</h4>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				{if isset($configured) && $configured}<p>
					<h4>{l s='If you haven\'t do it, you need to' mod='eworldaccelerator'}</h4>
					<ul class="ul-spaced">
						<li>{l s='create an account on' mod='eworldaccelerator'} <a href="http://www.eworld-accelerator.com/" target="_blank">our website</a></li>
						<li>{l s='get eworld Accelerator files on ' mod='eworldaccelerator'} <a href="http://customer.eworld-accelerator.com/" target="_blank">our customer website</a></li>
						<li>{l s='get eworld Accelerator licence on ' mod='eworldaccelerator'} <a href="http://customer.eworld-accelerator.com/" target="_blank">our customer website</a></li>
						<li>{l s='put eworld Accelerator files on this website root directory' mod='eworldaccelerator'}</li>
						<li>{l s='put the license.txt in the eworld-accelerator directory' mod='eworldaccelerator'}</li>
					</ul>
				</p>
				{else}<p>You can click on the <strong>button <i>eworld Accelerator</i> in the left menu</strong> to manage eworld Accelerator cache files.</p>
				{/if}
			</div>
		</div>
	</div>
</div>
