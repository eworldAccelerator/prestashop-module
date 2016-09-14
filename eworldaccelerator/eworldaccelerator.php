<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
	exit;
}
ini_set('display_errors', 1);
require_once 'eworldAcceleratorHandler.php';

class Eworldaccelerator extends Module {
	protected $config_form = false;
	/** @var EworldAcceleratorHandler $eworldAcceleratorHandler */
	private $eworldAcceleratorHandler;

	public function __construct() {
		$this->name = 'eworldaccelerator';
		$this->tab = 'administration';
		$this->version = '0.2.0';
		$this->author = 'eworld Accelerator';
		$this->need_instance = 0;
		$this->eworldAcceleratorHandler = null;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('eworld Accelerator');
		$this->description = $this->l('Delete automatically the eworld Accelerator\'s cache when a modification is made on the back office.');

		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install() {
		// Install Tab
		$tab = new Tab();
		$tab->name[$this->context->language->id] = 'eworld Accelerator';
		$tab->class_name = 'AdminEworldAccelerator';
		$tab->id_parent = 0; // Home tab
		$tab->module = $this->name;
		$tab->add();

		return parent::install() &&
		$this->registerHook('header') &&
		$this->registerHook('backOfficeHeader') &&
		$this->registerHook('actionOrderStatusPostUpdate') &&
		$this->registerHook('actionCategoryDelete') &&
		$this->registerHook('actionCategoryUpdate') &&
		$this->registerHook('actionProductDelete') &&
		$this->registerHook('actionProductSave') &&
		$this->registerHook('actionUpdateQuantity') &&
		$this->registerHook('actionObjectCmsDeleteAfter') &&
		$this->registerHook('actionObjectCmsUpdateAfter') &&
		$this->registerHook('actionProductUpdate');
	}

	public function uninstall() {
		Configuration::deleteByName('EWORLDACCELERATOR_DIRECTORY');

		// Uninstall Tabs
		$moduleTabs = Tab::getCollectionFromModule($this->name);
		if (!empty($moduleTabs)) {
			foreach ($moduleTabs as $moduleTab) {
				$moduleTab->delete();
			}
		}
		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent() {
		/**
		 * If values have been submitted in the form, process.
		 */
		if (((bool)Tools::isSubmit('submitEworldacceleratorModule')) == true) {
			$this->postProcess();
		}

		$this->context->smarty->assign('module_dir', $this->_path);

		$output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

		if (isset($this->errors) && is_array($this->errors) && sizeof($this->errors) > 0) {
			$output .= join("\r\n", $this->errors);
		}

		return $output . $this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm() {
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitEworldacceleratorModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			. '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'configured' => trim(Tools::getValue('EWORLDACCELERATOR_DIRECTORY')) != '',
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm() {
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('eworld Accelerator Directory'),
						'name' => 'EWORLDACCELERATOR_DIRECTORY',
						'desc' => 'absolute path to eworldAccelerator\'s files',
						'size' => 100,
						'required' => true
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right'
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues() {
		if (trim(Tools::getValue('EWORLDACCELERATOR_DIRECTORY')) != '') {
			return array(
				'EWORLDACCELERATOR_DIRECTORY' => Configuration::get('EWORLDACCELERATOR_DIRECTORY'),
			);
		}
		else {
			return array(
				'EWORLDACCELERATOR_DIRECTORY' => _PS_ROOT_DIR_ . '/eworld-accelerator/',
			);
		}
	}

	/**
	 * Save form data.
	 */
	protected function postProcess() {
		$directory = trim(Tools::getValue('EWORLDACCELERATOR_DIRECTORY'));

		if ($directory == '') {
			$this->errors[] = $this->displayError($this->l('Directory field is empty'));
		} else {
			// Add final slash
			if (Tools::substr($directory, -1) != '/' && Tools::substr($directory, -1) != '\\') {
				$directory .= '/';
			}
			if (!is_dir($directory)) {
				$this->errors[] = $this->displayError($this->l('The directory entered is not a valid directory'));
			} else if (!file_exists($directory . 'run_cache.php')) {
				$this->errors[] = $this->displayError($this->l('The directory entered is not the eworld Accelerator\'s directory'));
			} else if (!file_exists($directory . 'license.txt')) {
				$this->errors[] = $this->displayError($this->l('The license.txt file is missing. Connect to your account to get yours'));
			} else {
				Configuration::updateValue('EWORLDACCELERATOR_DIRECTORY', $directory);
				$this->errors[] = $this->displayConfirmation($this->l('Directory updated'));
			}
		}
	}

	public function setExtraMessage($str) {
		$this->context->cookie->extraMsg = $str;
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookBackOfficeHeader() {
		if (isset($this->context->cookie->cacheDeleted) && $this->context->cookie->cacheDeleted == 1) {
			if (!empty($this->context->cookie->extraMsg)) {
				$this->context->controller->confirmations[] = 'Cache deleted ('.$this->context->cookie->extraMsg.')';
				unset($this->context->cookie->extraMsg);
			}
			else {
				$this->context->controller->confirmations[] = 'Cache deleted';
			}
			unset($this->context->cookie->cacheDeleted);
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader() {
		/*$this->context->controller->addJS($this->_path.'/views/js/front.js');
		$this->context->controller->addCSS($this->_path.'/views/css/front.css');*/
	}

	public function hookActionCategoryDelete() {
		$this->deleteCacheFromCategoryId((int)Tools::getValue('id_category'));
		$this->setExtraMessage('Category deleted');
	}

	public function hookActionCategoryUpdate() {
		$this->deleteCacheFromCategoryId((int)Tools::getValue('id_category'));
		$this->setExtraMessage('Category updated');
	}

	public function hookActionProductDelete() {
		$this->deleteProductCache(Tools::getValue('id_product'));
		$this->setExtraMessage('Product deleted');
	}

	public function hookActionProductSave() {
		$this->deleteProductCache(Tools::getValue('id_product'));
		$this->setExtraMessage('Product saved');
	}

	public function hookActionProductUpdate() {
		$this->deleteProductCache(Tools::getValue('id_product'));
		$this->setExtraMessage('Product updated');
	}

	public function hookActionObjectCmsDeleteAfter($params) {
		$this->hookActionObjectCmsUpdateAfter($params);
	}

	public function hookActionOrderStatusPostUpdate($params) {
		if (is_array($params) && isset($params['cart'])) {
			$cart = $params['cart'];
			$details = $cart->getSummaryDetails();
			$productList = $details['products'];
			if (is_array($productList) && sizeof($productList) > 0) {
				foreach ($productList as $curProductInfos) {
					$this->deleteProductCache((int)$curProductInfos['id_product']);
				}
				$this->setExtraMessage('Order status updated');
			}
		}
	}

	public function hookActionUpdateQuantity($params) {
		if (is_array($params) && isset($params['id_product'])) {
			$this->deleteProductCache((int)$params['id_product']);
			$this->setExtraMessage('Product qty updated');
		}
	}

	public function hookActionObjectCmsUpdateAfter($params) {
		/** @var CMSCore $objectCMS */
		$objectCMS = $params['object'];
		$permalink = $this->context->link->getCMSLink($objectCMS);
		if ($this->getEworldAcceleratorHandler()) {
			$this->eworldAcceleratorHandler->deleteURL($permalink);
			$this->context->cookie->cacheDeleted = 1;
		}
	}

	/**
	 * @param int $id
	 */
	private function deleteProductCache($id) {
		$this->deleteCacheFromProductId((int)$id);

		// Categories
		$categories_ids = Product::getProductCategories((int)$id);
		if (is_array($categories_ids) && sizeof($categories_ids) > 0) {
			foreach ($categories_ids as $currentCategoryId) {
				$this->deleteCacheFromCategoryId($currentCategoryId);
			}
		}
	}

	/**
	 * @param int $productId
	 */
	private function deleteCacheFromProductId($productId) {
		$permalink = $this->context->link->getProductLink($productId);
		if ($this->getEworldAcceleratorHandler()) {
			$this->eworldAcceleratorHandler->deleteURL($permalink);
			$this->context->cookie->cacheDeleted = 1;
		}
	}

	/**
	 * @param int $categoryId
	 */
	private function deleteCacheFromCategoryId($categoryId) {
		$permalink = $this->context->link->getCategoryLink($categoryId);
		if ($this->getEworldAcceleratorHandler()) {
			$this->eworldAcceleratorHandler->deleteURL($permalink);
			$this->context->cookie->cacheDeleted = 1;
		}
		// Parent Category
		$currentCategory = new Category($categoryId);
		if (isset($currentCategory->id_parent) && intval($currentCategory->id_parent) > 0) {
			$this->deleteCacheFromCategoryId($currentCategory->id_parent);
		}
	}

	/**
	 * @return bool
	 */
	private function getEworldAcceleratorHandler() {
		if (is_object($this->eworldAcceleratorHandler)) {
			return true;
		}
		else {
			$this->eworldAcceleratorHandler = new EworldAcceleratorHandler(trim(Configuration::get('EWORLDACCELERATOR_DIRECTORY', '')));
			if (is_object($this->eworldAcceleratorHandler)) {
				return true;
			}
		}
		return false;
	}
}