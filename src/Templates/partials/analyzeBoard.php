<?php

if (!isset($style) || !isset($move) || !isset($color)) {
    throw new Exception('Data not set properly');
}
?>
<table style="<?=$style?>" class="board">
    <tbody>
        <tr>
            <?php foreach($move->board->get() as $row => $columns): ?>
                <?php foreach($columns as $column => $field): ?>
                    <?php if ($column === array_key_first($columns)): ?>
                        </tr>
                        <tr>
                    <?php endif ?>
                    <td style="background-color: <?=$color?>" class="boardtd" title="<?=$row === $move->move->row && $column === $move->move->column ? $move->move->scoreDetail : ''?>">
                        <?php if (!$field->isEmpty()): ?>
                            <span class="<?=$row === $move->move->row && $column === $move->move->column ? 'lastStone' : 'stone'?>"><?=$field->value->toString()?></span>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            <?php endforeach ?>
        </tr>
        <tr><td colspan=<?=$move->board->columns?>>
            <?=$move->score?>
        <td></tr>
    </tbody>
</table>