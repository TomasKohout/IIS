<?php
// source: /opt/lampp/htdocs/IIS/app/presenters/templates/AddAnimal/default.latte

use Latte\Runtime as LR;

class Template7fde61862f extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>

<nav class="navbar navbar-inverse">


    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("MainPage:default")) ?>">ZooWis</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("MainPage:")) ?>">Home</a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Zvíře<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("AddAnimal:")) ?>">Přidat</a></li>
                    <li><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("DeleteAnimal:")) ?>">Odebrat</a></li>
                    <li><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("UpdateAnimal:")) ?>">Upravit</a></li>
                </ul>
            </li>
            <li><a href="#">Page 2</a></li>
            <li><a href="#">Page 3</a></li>
        </ul>
    </div>
</nav>


<div>
<?php
		/* line 28 */ $_tmp = $this->global->uiControl->getComponent("addAnimal");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
?>
</div>

<?php
	}

}
