<?php

namespace Flame\CMS\FrontModule;

abstract class FrontPresenter extends \Flame\Application\UI\Presenter
{

	/**
	 * @autowire
	 * @var \Flame\CMS\Models\Menu\MenuFacade
	 */
	protected $menuFacade;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\Models\Pages\PageFacade
	 */
	protected $pageFacade;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\SettingBundle\Model\SettingFacade
	 */
	protected $settingFacade;
	
	/**
	 * @autowire
	 * @var \Flame\Components\NavbarBuilder\INavbarBuilderControlFactory
	 */
	protected $navbarBuilderControlFactory;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\TagBundle\Components\TagControlFactory
	 */
	protected $tagControlFactory;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\Components\Newsreel\NewsreelControlFactory
	 */
	protected $newsreelControlFactory;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\Components\Categories\CategoryControlFactory
	 */
	protected $categoryControlFactory;
	
	/**
	 * @autowire
	 * @var \Flame\CMS\Templating\ThemeManager
	 */
	protected $themeManager;

	/**
	 * @autowire
	 * @var \Flame\Addons\FlashMessages\IFlashMessageControlFactory
	 */
	protected $flashMessagesControlFactory;

	public function startup()
	{
		parent::startup();

		if(!$this->getUser()->isAllowed($this->name, $this->view)){
			$this->flashMessage('Access denied', 'error');
			$this->redirect('Homepage:');
		}
	}

	/**
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		$list = parent::formatLayoutTemplateFiles();
		$wwwDir = $this->getContextParameter('wwwDir');
		array_unshift($list, $wwwDir . DIRECTORY_SEPARATOR . $this->themeManager->getTheme() . "/@layout.latte");
		return $list;
	}

	public function beforeRender()
	{
		parent::beforeRender();

		$this->template->theme = $this->themeManager->getTheme();
	}

	/**
	 * @return \Flame\CMS\Components\Newsreel\NewsreelControl
	 */
	protected function createComponentNewsreelControl()
	{
		$this->newsreelControlFactory->setLimit($this->settingFacade->getSettingValue('menu_newsreelCount'));
		return $this->newsreelControlFactory->create();
	}

	/**
	 * @return \Flame\CMS\Components\Categories\CategoryControl
	 */
	protected function createComponentCategoriesControl()
	{
		return $this->categoryControlFactory->create();
	}

	/**
	 * @return \Flame\CMS\TagBundle\Components\TagControl
	 */
	protected function createComponentTagsControl()
	{
		return $this->tagControlFactory->create();
	}

	/**
	 * @return \Flame\Components\NavbarBuilder\NavbarBuilderControl
	 */
	protected function createComponentNavbar()
	{
		$control = $this->navbarBuilderControlFactory->create();

		$navbar = $control->getNavbarControl();

		if(count($items = $this->menuFacade->getLastMenuLinkByPriority())){
			foreach($items as $item){
				$navbar->addItem($item->title, $item->url);
			}
		}

		return $control;
	}

	/**
	 * @return \Flame\Addons\FlashMessages\FlashMessageControl
	 */
	protected function createComponentFlasheMessages()
	{
		return $this->flashMessagesControlFactory->create();
	}


}
