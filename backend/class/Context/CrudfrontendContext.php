<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use noxkiwi\crud\Context\CrudfrontendContext as BaseCrudFrontendContext;
use noxkiwi\spotigame\Auth\SpotigameAuth;

/**
 * I am the frontend Context for the CRUD manager.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 *
 */
final class CrudfrontendContext extends BaseCrudFrontendContext
{
    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        return SpotigameAuth::isAdmin();
    }
}
