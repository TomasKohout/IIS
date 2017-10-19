<?php
// source: /opt/lampp/htdocs/IIS/app/presenters/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Templated33c40dd9e extends Latte\Runtime\Template
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

    <div class="jumbotron text-center center-block color-mtf">
        <div class="row center-block">
            <h1>Vítejte do ZooWisu</h1>
        </div>
        <div class="row center-block">
        <div class="col-sm-5"></div>
        <div class="col-sm-2">
<?php
		/* line 10 */ $_tmp = $this->global->uiControl->getComponent("signInForm");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
?>
        </div>
        </div>
    </div>
<?php
	}

}
