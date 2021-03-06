<?php
/**
 * Created by EBelair.
 * User: manu
 * Date: 01/03/14
 * Time: 18:35
 */

namespace SuchTable\View\Helper;


use SuchTable\Element;
use SuchTable\Fieldset\ElementFieldset;
use SuchTable\TableInterface;
use SuchTable\TableRow As TableRowElement;
use SuchTable\View\Helper\TableRow;
use Zend\Form\View\Helper\FormText;

class TableHead extends AbstractHelper
{
    protected $tag = 'thead';

    /**
     * @param TableInterface $table
     * @return TableHead|string
     */
    public function __invoke(TableInterface $table = null)
    {
        if (!$table) {
            return $this;
        }

        return $this->render($table);
    }

    /**
     * @param TableInterface $table
     * @return string
     */
    public function render(TableInterface $table)
    {
        /** @var TableRow $tr */
        $tr = $this->getView()->plugin('tr');
        /** @var TableHeaderCell $th */
        $th = $this->getView()->plugin('th');
        /** @var TableCell $td */
        $td = $this->getView()->plugin('td');

        // @todo use other form element helpers
        /** @var FormText $formText */
        $formText = $this->getView()->plugin('formText');

        $content = '';
        $firstRowIsRendered = false;
        foreach ($table as $key => $element) {
            if ($element instanceof TableRowElement && false === $firstRowIsRendered) {
                foreach ($element as $el) {
                    $content .= $th->render($el);
                }
                $firstRowIsRendered = true;
            }
        }
        if ($content) {
            $content = $tr->openTag() . $content . $tr->closeTag();

            // Form?
            if (true !== $table->getOption('disableForm')
                && false !== $table->getOption('headForm')) {
                $formContent = '';
                /** @var ElementFieldset $fieldset */
                $fieldset = $table->getForm()->get($table->getElementsKey());
                $fieldset->prepareElement($table->getForm());
                /** @var Element $element */
                foreach ($table as $element) {
                    $name = $element->getName();
                    $formContent .= $td->openTag();
                    if ($element->getOption('disableForm') !== true && $fieldset->has($name)) {
                        $formContent .= $formText->__invoke($fieldset->get($name));
                    }
                    $formContent .= $td->closeTag();
                }
                if ($formContent) {
                    $content .= $tr->openTag() . $formContent . $tr->closeTag();
                }
            }

            return $this->openTag() . $content . $this->closeTag();
        }
    }

    /**
     * @todo attributes ?
     * @return string
     */
    public function openTag()
    {
        return '<thead>';
    }

    /**
     * @return string
     */
    public function closeTag()
    {
        return '</thead>';
    }
}
