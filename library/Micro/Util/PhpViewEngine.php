<?php
namespace Micro\Util;

use Rawebone\ViewModel\ViewModelInterface;
use Rawebone\ViewModel\ViewEngineInterface;

class PhpViewEngine implements ViewEngineInterface
{
    public function render(ViewModelInterface $model)
    {
        ob_start();
        include $model->getTemplateFile();
        return ob_get_clean();
    }
}
