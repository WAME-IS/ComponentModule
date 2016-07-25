# Ako vytvoriť komponentu
Komponenty vytvárame do zložiek *components*
- vendor/wame/TextBlockModule/components/TextBlockControl

alebo v prípade rozšírovania modulov

- vendor/wame/ArticleModule/vendor/wame/MenuModule/components/MenuControl


Komponentu môžeme vytvoriť niekoľkými spôsobmi

## 1. Cez factory (továrničku)
Vo factory zaregistrujeme všetky závislosti do `__construct()`
a vo funkcii `create()` ich predáme do danej komponenty

*vendor/wame/TextBlockModule/components/TextBlockControl/**TextBlockControlFactory.php***

```
<?php

namespace Wame\TextBlockModule\Components;

use Wame\TextBlockModule\Repositories\TextBlockRepository;

class TextBlockControlFactory 
{
	/** @var TextBlockRepository */
	private $textBlockRepository;
	
	
	public function __construct(
        TextBlockRepository $textBlockRepository
    ) {
		parent::__construct();
		
		$this->textBlockRepository = $textBlockRepository;
	}
    
    
    /** @return TextBlockControl */
	public function create()
    {
		return new TextBlockControl(
			$this->textBlockRepository
        );
    }

}
```

a tak závislosti z factory predáme komponente
komponenta extenduje (dedí) od 
`\Wame\Core\Components\`***BaseControl***
alebo v prípade komponenty pre AdminModule 
`\Wame\AdminModule\Components\`***BaseControl***

v prípade vizuálnej komponenty využijeme funkciu `render()`
kde predáme premené, filtry...

*vendor/wame/TextBlockModule/components/TextBlockControl/**TextBlockControl.php***

```
<?php 

namespace Wame\TextBlockModule\Components;

use Wame\Core\Components\BaseControl;
use Wame\TextBlockModule\Repositories\TextBlockRepository; 

class TextBlockControl extends BaseControl
{
    /** @var TextBlockRepository */
	private $textBlockRepository;
	
	/** @var string */
	private $lang;
	
	
	public function __construct(
        DI\Container $container, TextBlockRepository $textBlockRepository
    ) {
		parent::__construct(container);
		
		$this->textBlockRepository = $textBlockRepository;
		$this->lang = $textBlockRepository->lang;
	}
    
    
	public function render()
	{
        $textBlock = $this->textBlockRepository->get(['component' => $this->componentInPosition->component]);

        $this->template->position = $this->componentInPosition;
        $this->template->component = $this->componentInPosition->component;
        $this->template->title = $this->componentInPosition->component->langs[$this->lang]->title;
        $this->template->text = $textBlock->langs[$this->lang]->text;
	}

}
```

## 2. Cez interface
* výhodou interface je že si všetky závisloti dokáže zaregistrovať sám
* je kratší takže ho môžeme zapísať rovno do súboru pre komponentu
* v interface je povinná metóda `create()` ktorej nastavíme `@return` na danú komponentu cez anotáciu
* interface označujeme veľkým `I` na začiatku napr. `ITextBlockControlFactory`

*vendor/wame/TextBlockModule/components/TextBlockControl/**TextBlockControl.php***

```
<?php 

namespace Wame\TextBlockModule\Components;

use Wame\Core\Components\BaseControl;
use Wame\TextBlockModule\Repositories\TextBlockRepository;

interface ITextBlockControlFactory
{
	/** @return TextBlockControl */
	public function create();	
}

class TextBlockControl extends BaseControl
{
    /** @var TextBlockRepository */
	private $textBlockRepository;
	
	/** @var string */
	private $lang;
	
	
	public function __construct(
        $textBlockRepository
    ) {
		parent::__construct();
		
		$this->textBlockRepository = $textBlockRepository;
		$this->lang = $textBlockRepository->lang;
	}
    
    
	public function render()
	{
        $textBlock = $this->textBlockRepository->get(['component' => $this->componentInPosition->component]);

        $this->template->position = $this->componentInPosition;
        $this->template->component = $this->componentInPosition->component;
        $this->template->title = $this->componentInPosition->component->langs[$this->lang]->title;
        $this->template->text = $textBlock->langs[$this->lang]->text;
	}

}
```

## Template
* defaultná šablóna `default.latte`
* šablónu môžeme zmeniť cez metódu `setTemplateFile()` 
pri vytváraní componenty cez `createComponent()`
    * zadávame len názov šablóny (nie celú cestu) spolu s extenstion (koncovkou)
    * šablónu vyhľadáva v zložkách v poradí
        * `./app/{module}/components/{component}/{template}`
        * `./templates/{customTemplate}/{module}/components/{component}/{template}`
        * `./vendor/wame/{module}/components/{component}/{template}`
* v šablóne môžeme využívať všetky funckie ktoré predáme v `render()`
do `$this->template`

*vendor/wame/TextBlockModule/components/TextBlockControl/**default.latte***
```
<h3 n:if="$component->getParameter('showTitle')">
    {$title}
</h3>

{$text|noescape}
```

## Config
do configu zaregistrujeme service

*vendor/wame/TextBlockModule/config/**config.textBlock.neon***

```
services:
    - Wame\TextBlockModule\Components\ITextBlockControlFactory
```

alebo ak chceme komponentu rozširovať tak

```
services:
    ITextBlockControlFactory:
        class: Wame\TextBlockModule\Components\ITextBlockControlFactory
```

vtedy môžeme cez `setup` spúšťať funkcie napr. z iných modulov, pluginov

## Componentu pridáme do zoznamu komponent v administrácii

Komponenta implementuje interface `Wame\ComponentModule\Registers\IComponent`
kde sú definované všetky potrbné funkcie pre vytvorenie komponenty

* `addItem()` - vytvorí položku do zoznamu
* `getName()` - názov komponenty ktorý sa použije na identifikáciu komponenty v databáze atď. zapisujeme v camelCase
* `getTitle()` - názov komponenty zapísaný cez translator
* `getDescription()` - popis komponenty zapísaný cez translator
* `getIcon()` - CSS class zápisu ikonky *FontAwesome, Glyphicon...*
* `getLinkCreate()` - odkaz na vytvorenie komponenty (využijeme `Nette\Application\LinkGenerator`)
* `getLinkDetail()` - odkaz na detail komponenty (využijeme `Nette\Application\LinkGenerator`) predávasa `$componentEntity`
* `createComponent()` - funkcia na vytvorenie komponenty predáva sa `$componentInPosition`


*vendor/wame/TextBlockModule/vendor/wame/ComponentModule/components/**TextBlockComponent.php***
```
<?php

namespace Wame\TextBlockModule\Vendor\Wame\ComponentModule;

use Nette\Application\LinkGenerator;
use Wame\ComponentModule\Registers\IComponent;
use Wame\MenuModule\Models\Item;
use Wame\TextBlockModule\Components\ITextBlockControlFactory;

interface ITextBlockComponentFactory
{
	/** @return TextBlockComponent */
	public function create();	
}


class TextBlockComponent implements IComponent
{	
	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var ITextBlockControlFactory */
	private $ITextBlockControlFactory;

	
	public function __construct(
		LinkGenerator $linkGenerator,
		ITextBlockControlFactory $ITextBlockControlFactory
	) {
		$this->linkGenerator = $linkGenerator;
		$this->ITextBlockControlFactory = $ITextBlockControlFactory;
	}
	
	
	public function addItem()
	{
		$item = new Item();
		$item->setName($this->getName());
		$item->setTitle($this->getTitle());
		$item->setDescription($this->getDescription());
		$item->setLink($this->getLinkCreate());
		$item->setIcon($this->getIcon());
		
		return $item->getItem();
	}
	
	
	public function getName()
	{
		return 'textBlock';
	}
	
	
	public function getTitle()
	{
		return _('Text block');
	}
	
	
	public function getDescription()
	{
		return _('Create text block');
	}
	
	
	public function getIcon()
	{
		return 'fa fa-list-alt';
	}
	
	
	public function getLinkCreate()
	{
		return $this->linkGenerator->link('Admin:TextBlock:create');
	}

	
	public function getLinkDetail($componentEntity)
	{
		return $this->linkGenerator->link('Admin:TextBlock:edit', ['id' => $componentEntity->id]);
	}
	
	
	public function createComponent()
	{
		$control = $this->ITextBlockControlFactory->create();
		return $control;
	}
	
}
```

## Registrácia do ComponentRegister

*vendor/wame/TextBlockModule/vendor/wame/ComponentModule/config/**config.textBlock.component.neon***
```
services:
	ComponentRegister:
		setup:
			- add(Wame\TextBlockModule\Vendor\Wame\ComponentModule\TextBlockComponent())
```

## Registrácia do MenuManager
Keď chceme komponentu pridať ako položku menu tak ju zaregistrujeme do MenuManager

Implementuje interface `Wame\MenuModule\Models\DatabaseMenuProvider\IMenuItem`
kde sú definované všetky potrbné funkcie pre vytvorenie položky menu

* `addItem()` - vytvorí položku do zoznamu
* `getName()` - názov komponenty ktorý sa použije na identifikáciu komponenty v databáze atď. zapisujeme v camelCase
* `getTitle()` - názov komponenty zapísaný cez translator
* `getDescription()` - popis komponenty zapísaný cez translator
* `getIcon()` - CSS class zápisu ikonky *FontAwesome, Glyphicon...*
* `getLinkCreate()` - odkaz na vytvorenie komponenty (využijeme `Nette\Application\LinkGenerator`) predáva sa `$menuId`
* `getLinkUpdate()` - odkaz na detail komponenty (využijeme `Nette\Application\LinkGenerator`) predáva sa `$menuEntity`
* `getLink()` - odkaz kam bude smerovať po kliknutí na položku menu (využijeme `Nette\Application\LinkGenerator`) predáva sa `$menuEntity`

*vendor/wame/ArticleModule/vendor/wame/MenuModule/components/MenuControl/MenuManager/**ArticleMenuItem.php***

```
<?php

namespace Wame\ArticleModule\Vendor\Wame\MenuModule\Components\MenuManager;

use Nette\Application\LinkGenerator;
use Wame\MenuModule\Models\Item;
use Wame\MenuModule\Models\DatabaseMenuProvider\IMenuItem;
use Wame\MenuModule\Repositories\MenuRepository;

interface IArticleMenuItem
{
	/** @return ArticleMenuItem */
	public function create();
}


class ArticleMenuItem implements IMenuItem
{	
    /** @var LinkGenerator */
	private $linkGenerator;
	
    /** @var string */
	private $lang;
	
	
	public function __construct(
		LinkGenerator $linkGenerator,
		MenuRepository $menuRepository
	) {
		$this->linkGenerator = $linkGenerator;
		$this->lang = $menuRepository->lang;
	}

	
	public function addItem()
	{
		$item = new Item();
		$item->setName($this->getName());
		$item->setTitle($this->getTitle());
		$item->setDescription($this->getDescription());
		$item->setLink($this->getLinkCreate());
		$item->setIcon($this->getIcon());
		
		return $item->getItem();
	}

	
	public function getName()
	{
		return 'article';
	}
	
	
	public function getTitle()
	{
		return _('Article');
	}
	
	
	public function getDescription()
	{
		return _('Insert link to the article');
	}
	
	
	public function getIcon()
	{
		return 'fa fa-file-text';
	}
	
	
	public function getLinkCreate($menuId = null)
	{
		return $this->linkGenerator->link('Admin:Article:menuItem', ['m' => $menuId]);
	}
	
	
	public function getLinkUpdate($menuEntity)
	{
		return $this->linkGenerator->link('Admin:Article:menuItem', ['id' => $menuEntity->id, 'm' => $menuEntity->component->id]);
	}
	
	
	public function getLink($menuEntity)
	{
		return $this->linkGenerator->link('Article:Article:show', ['id' => $menuEntity->langs[$this->lang]->slug, 'lang' => $this->lang]);
	}
	
}
```

položku menu predáme do `MenuManager` cez config

*vendor/wame/ArticleModule/vendor/wame/MenuModule/config/**config.article.menu.neon***

```
services:
	MenuManager:
		setup:
			- addMenuItemType(Wame\ArticleModule\Vendor\Wame\MenuModule\Components\MenuManager\ArticleMenuItem(), 'article')
```

## Formulár pre vytvorenie položky menu
základný formulár *vendor/wame/MenuModule/forms/**MenuItemForm.php*** obsahuje základný formulár s nadradenou položkou, CSS class, ikonka a zobrazovanie len prihláseným, len odhláseným alebo všetkým.

My tento formulár rozšírime len o polia ktoré potrebujeme pre danú položku menu.
extendujeme `Wame\DynamicObject\Forms\BaseFormContainer`
kde sú definované funkcie ktoré sú potrebné pre vytvorenie položky menu
* `render()` - môžeme nastaviť template ak by sme jednotlivé containery formulára vykresľovali ručne
* `configure()` - zadefinujeme formulárové prvky (formulár si vyžiadame cez `$this->getForm()`)
* `setDefaultValues()` - ak potrebujeme prvkom nastaviť defaultné hodnoty tak využijeme túto metódu kde sú prístupné všetky parametre z hlavného formulára 
(formulár si vyžiadame cez `$this->getForm()` a parametre sa nachádzajú v prvom parametri `$object`)

*vendor/wame/ArticleModule/vendor/wame/MenuModule/components/MenuManager/forms/article/**ArticleFormContainer.php***

```
<?php

namespace Wame\ArticleModule\Vendor\Wame\MenuModule\Components\MenuManager\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;

interface IArticleFormContainerFactory
{
	/** @return ArticleFormContainer */
	public function create();
}


class ArticleFormContainer extends BaseFormContainer
{
    public function render() 
	{
        $this->template->_form = $this->getForm();
        $this->template->render(__DIR__ . '/default.latte');
    }

	
    public function configure() 
	{
		$form = $this->getForm();

		$form->addAutocomplete('value', _('Article'), '/api/v1/article-search', [
			'columns' => ['langs.title'],
			'select' => 'a.id, langs.title'
		]);
		
		$form->addText('alternative_title', _('Alternative title'));
    }
	
	
	public function setDefaultValues($object)
	{
		$form = $this->getForm();

		$form['value']->setDefaultValue($object->menuEntity->value);
		$form['alternative_title']->setDefaultValue($object->menuEntity->langs[$object->lang]->alternativeTitle);
	}

}
```

### Šablóna formulárového kontainera
Základná šablóna sa volá `default.latte` a nachádza sa hneď pri formulárovom kontaineri

*vendor/wame/ArticleModule/vendor/wame/MenuModule/components/MenuManager/forms/article/**default.latte***

### Vytvorenie formulára
Formulár vytvoríme v prezentri

* injectneme si `Wame\MenuModule\Forms\MenuItemForm`
* formuláru nastavíme action (je to názov formulára)
je to kôli správnemu presmerovaniu po odoslaní formulára kedy sa strácali ďalšie parametre z URL
* nastavíme typ položky ktorý sa uloží do databázy
* predáme ID ak by sa jednalo o úpravu položky
* a pridáme všetky dodatočné formulárove kontainery

*vendor/wame/ArticleModule/vendor/wame/AdminModule/presenters/**ArticlePresenter.php***

```
<?php

namespace App\AdminModule\Presenters;

use Wame\MenuModule\Forms\MenuItemForm;

class ArticlePresenter extends \App\AdminModule\Presenters\BasePresenter
{
	/** @var MenuItemForm @inject */
	public $menuItemForm;

    /**
	 * Menu item form
	 * 
	 * @return MenuItemForm
	 */
	protected function createComponentArticleMenuItemForm()
	{
		$form = $this->menuItemForm
						->setActionForm('articleMenuItemForm')
						->setType('article')
						->setId($this->id)
						->addFormContainer(new \Wame\ArticleModule\Vendor\Wame\MenuModule\Components\MenuManager\Forms\ArticleFormContainer(), 'ArticleFormContainer', 50)
						->build();

		return $form;
	}
    
	public function renderMenuItem()
	{
		if ($this->id) {
			$this->template->siteTitle = _('Edit article item in menu');
		} else {
			$this->template->siteTitle = _('Add article item to menu');
		}
	}
```

### Zavolanie formulára v template
Formulár zavoláme ako komponentu to nám zaručí že sa formulár celý vygeneruje sám.

*vendor/wame/ArticleModule/vendor/wame/AdminModule/presenters/templates/Article/**menuItem.latte***

```
{block content}
	<div class="page-header">
		<h1>{$siteTitle}</h1>
	</div>
	
	{control articleMenuItemForm}
```

