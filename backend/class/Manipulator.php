<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use JetBrains\PhpStorm\Pure;
use noxkiwi\crud\Frontend\Cell;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\Model\PlayerModel;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SongModel;

/**
 * I am the Manipulator for all CRUD instances on Spotigame.
 *
 * @package      noxkiwi\spotigame
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Manipulator extends \noxkiwi\crud\Manipulator
{
    /**
     * I will manipulate a column.
     *
     * @param string $fieldName
     * @param array  $dataset
     *
     * @return \noxkiwi\crud\Frontend\Cell
     */
    #[Pure] protected function manipulateplayer_avatar(string $fieldName, array $dataset): Cell
    {
        $raw           = $dataset[$fieldName] ?? '';
        $cell          = new Cell();
        $cell->sort    = '';
        $cell->display = <<<HTML
<img src="$raw" width="50%"/>
HTML;
        $cell->filter  = $raw;
        $cell->export  = $raw;

        return $cell;
    }

    /**
     * I will manipulate a column.
     *
     * @param string $fieldName
     * @param array  $dataset
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\crud\Frontend\Cell
     */
    protected function manipulateplayer_id(string $fieldName, array $dataset): Cell
    {
        $raw           = $dataset[$fieldName] ?? '';
        $playerName    = PlayerModel::expect($raw)->getField('player_name');
        $cell          = new Cell();
        $cell->sort    = '';
        $cell->display = <<<HTML
<a href="/?context=crudfrontend&view=list&modelName=Player&q=player:$raw:" target="_top">$playerName</a>
HTML;
        $cell->filter  = "player:$raw:";
        $cell->export  = $raw;

        return $cell;
    }

    /**
     * I will manipulate a column.
     *
     * @param string $fieldName
     * @param array  $dataset
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\crud\Frontend\Cell
     */
    protected function manipulatesitting_id(string $fieldName, array $dataset): Cell
    {
        $raw           = $dataset[$fieldName] ?? '';
        $sittingCode   = SittingModel::expect($raw)->getField('sitting_code');
        $cell          = new Cell();
        $cell->sort    = '';
        $cell->display = <<<HTML
<a href="/?context=crudfrontend&view=list&modelName=Sitting&q=sitting:$raw:" target="_top">$sittingCode</a>
HTML;
        $cell->filter  = "sitting:$raw:";
        $cell->export  = $raw;

        return $cell;
    }

    /**
     * @param string $fieldName
     * @param array  $dataset
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\crud\Frontend\Cell
     */
    protected function manipulatemove_id(string $fieldName, array $dataset): Cell
    {
        $raw           = $dataset[$fieldName] ?? '';
        $sittingCode   = MoveModel::expect($raw)->getField('sitting_id');
        $cell          = new Cell();
        $cell->sort    = '';
        $cell->display = <<<HTML
<a href="/?context=crudfrontend&view=list&modelName=Move&q=move:$raw:" target="_top">$sittingCode</a>
HTML;
        $cell->filter  = "move:$raw:";
        $cell->export  = $raw;

        return $cell;
    }

    /**
     * @param string $fieldName
     * @param array  $dataset
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\crud\Frontend\Cell
     */
    protected function manipulatesong_id(string $fieldName, array $dataset): Cell
    {
        $raw           = $dataset[$fieldName] ?? '';
        $sittingCode   = SongModel::expect($raw)->getField('song_title');
        $cell          = new Cell();
        $cell->sort    = '';
        $cell->display = <<<HTML
<a href="/?context=crudfrontend&view=list&modelName=Song&q=song:$raw:" target="_top">$sittingCode</a>
HTML;
        $cell->filter  = "song:$raw:";
        $cell->export  = $raw;

        return $cell;
    }
}
