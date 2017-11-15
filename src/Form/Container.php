<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 3.0)
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category    docroot
 * @package     docroot
 * @author      Jeroen Bleijenberg <jeroen@jcode.nl>
 *
 * @copyright   Copyright (c) 2017 J!Code (http://www.jcode.nl)
 * @license     http://opensource.org/licenses/GPL-3.0 General Public License (GPL 3.0)
 */
namespace Jcode\Form;

use Jcode\Form;
use \Jcode\Layout\Block\Template;

class Container extends Template
{

    protected $template = 'Jcode_Form::Container.phtml';

    /**
     * @inject \Jcode\Form
     * @var \Jcode\Form
     */
    protected $form;

    protected function prepareForm()
    {

    }

    /**
     * @return Form
     * @internal param null $id
     * @internal param array $options
     */
    public function getForm() :Form
    {
        return $this->form;
    }

    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    public function render()
    {
        $this->prepareForm();

        parent::render();

        return $this;
    }

    public function getPreparedForm()
    {
        return $this->form;
    }

}