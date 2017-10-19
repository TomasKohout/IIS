<?php
// source: /opt/lampp/htdocs/nette/app/presenters/templates/MainPage/default.latte

use Latte\Runtime as LR;

class Template1ff91ca6c7 extends Latte\Runtime\Template
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
		if (isset($this->params['one_piece_of_data'])) trigger_error('Variable $one_piece_of_data overwritten in foreach on line 25');
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
                    <li><a href="#">Přidat</a></li>
                    <li><a href="#">Odebrat</a></li>
                    <li><a href="#">Upravit</a></li>
                </ul>
            </li>
            <li><a href="#">Page 2</a></li>
            <li><a href="#">Page 3</a></li>
        </ul>
    </div>
</nav>


<?php
		$iterations = 0;
		foreach ($data as $one_piece_of_data) {
?>
        <div class="row">
            <div class="col-sm-1">Jmeno: </div>
            <div class="col-sm-2"><?php echo LR\Filters::escapeHtmlText($one_piece_of_data->jmeno) /* line 28 */ ?></div>
            <div class="col-sm-3">Poslední krmení: </div>
            <div class="col-lg-4"><?php echo LR\Filters::escapeHtmlText($one_piece_of_data->cas) /* line 30 */ ?></div>
        </div>
<?php
			$iterations++;
		}
?>

<?php
	}

}
