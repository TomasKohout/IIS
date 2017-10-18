<?php
// source: /opt/lampp/htdocs/nette/app/presenters/templates/MainPage/default.latte

use Latte\Runtime as LR;

class Template1ff91ca6c7 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		?>Ahoj<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
