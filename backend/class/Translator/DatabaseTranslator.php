<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Translator;

use Exception;
use noxkiwi\core\ErrorHandler;
use noxkiwi\database\Database;
use noxkiwi\database\Exception\DatabaseException;
use noxkiwi\spotigame\Model\TranslationModel;
use noxkiwi\translator\Translator;
use const E_USER_NOTICE;

/**
 * I am the Translator that uses a Model to translate keys.
 *
 * @package      noxkiwi\spotigame\Translator
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class DatabaseTranslator extends Translator
{
    private const LANGUAGE_MAPPING = [
        Translator::LANGUAGE_DE_DE => 'translation_german',
        Translator::LANGUAGE_EN_US => 'translation_english',
        Translator::LANGUAGE_EN_NZ => 'translation_english'
    ];

    /**
     * @inheritDoc
     */
    public function getKeys(): array
    {
        $ret = [];
        try {
            $database = Database::getInstance();
            $query    = <<<MYSQL
SELECT `translation`.`translation_key` FROM `translation`;
MYSQL;
            $results  = [];
            try {
                $database->read($query);
                $results = $database->getResult();
            } catch (DatabaseException $exception) {
                ErrorHandler::handleException($exception, E_USER_NOTICE);
            }
            foreach ($results as $result) {
                $ret[] = $result['translation_key'];
            }
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception);
        }

        return $ret;
    }

    /**
     * @inheritDoc
     */
    public function getLanguages(): array
    {
        return [
            'DE',
            'EN'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTranslation(string $key): string
    {
        try {
            $translation   = TranslationModel::expect($key);
            $languageField = self::LANGUAGE_MAPPING[self::getLanguage()] ?? self::LANGUAGE_MAPPING[Translator::LANGUAGE_DE_DE];

            return $translation->{$languageField} ?? $key;
        } catch (Exception $exception) {
            return $key;
        }
    }
}

