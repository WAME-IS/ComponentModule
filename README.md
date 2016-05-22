# Ako vytvoriť komponentu
Komponenty vytvárame do zložiek *components*
- vendor/wame/TextBlockModule/components/TextBlockControl

alebo v prípade rozšírovania modulov

- vendor/wame/ArticleModule/vendor/wame/MenuModule/components/MenuControl


Komponentu môžeme vytvoriť niekoľkými spôsobmi

### 1. Cez factory (továrničku)
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

		$this->getTemplateFile(); // vyrieší použitie správneho súboru pre template
		$this->template->render();
	}

}
```

### 2. Cez interface
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

		$this->getTemplateFile(); // vyrieší použitie správneho súboru pre template
		$this->template->render();
	}

}
```

### Template
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

### Config
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


### Registrácia do ComponentManager

*vendor/wame/TextBlockModule/vendor/wame/ComponentModule/config/**config.textBlock.component.neon***
```
services:
	ComponentManager:
		setup:
			- addComponent(Wame\TextBlockModule\Vendor\Wame\ComponentModule\TextBlockComponent())
```
