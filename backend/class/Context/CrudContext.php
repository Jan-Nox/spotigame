<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use noxkiwi\spotigame\Manipulator;

/**
 * I am the Context object that manages data transfer between Crud Frontend and Crud backend.
 * This overwriting is necessary to add the fitting Manipulator class.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class CrudContext extends \noxkiwi\crud\Context\CrudContext
{
    /**
     * @inheritDoc
     */
    protected function __construct()
    {
        parent::__construct();
        $this->setManipulator(new Manipulator($this->getCrud()));
    }
}
