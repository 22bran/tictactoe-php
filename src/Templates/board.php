<?php
if (!isset($this) || !isset($game)) {
    throw new Exception('Data not set properly');
}
$this->layout('layout', ['title' => 'Game']) ?>

<a href="/">Back to landing page</a> | <a href="/restart">Restart</a><br/><br/>
<?php if ($game->started()): ?>
<a href="/undo-move">Undo move</a> | 
<?php endif ?>
Board <?=$game->rows?> x <?=$game->columns?> | <?=$game->stones?> stones to win | <?=$game->remainingMoves()?> remaining moves</br/><br/>
<table class="board">
    <tbody>
        <tr>
            <?php foreach($game->board as $row => $columns): ?>
                <?php foreach($columns as $column => $field): ?>
                    <?php if ($column === array_key_first($columns)): ?>
                        </tr>
                        <tr>
                    <?php endif ?>
                    <td class="boardtd">
                        <?php if ($field->isEmpty()): ?>
                            <?php if ($game->winner === false && $game->draw() === false): ?>
                                <span class="move stone"><a href="move?row=<?=$row?>&column=<?=$column?>"><?=$game->onTheMove->stoneType->toString()?></a></span>
                            <?php endif ?>
                        <?php else: ?>
                            <span class="<?=(count($game->moves) > 0) && $row === $game->moves[count($game->moves) - 1]->row && $column === $game->moves[count($game->moves) - 1]->column ? 'lastStone' : 'stone'?>"><?=$field->value->toString()?></span>   
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            <?php endforeach ?>
        </tr>
    </tbody>
</table>
<?php if ($game->started() && $game->winner === false && $game->draw() === false): ?>
    <br><a href="/analyze" target="_blank">Analyze next moves</a><br>
<?php endif ?>
<?php if ($game->winner !== false): ?>
    <h2>Winner is <?=$game->winner->name ?></h2>
    <p>Play time: <?=$game->getPlayTime() ?> seconds</p>
<?php elseif ($game->draw()): ?>
    <h2>Draw</h2>
    <p>Play time: <?=$game->getPlayTime() ?> seconds</p>
<?php endif ?>