<?php
if (!isset($this) || !isset($moves)) {
    throw new Exception('Data not set properly');
}

$this->layout('layout', ['title' => 'Game']) ?>

<a href="/">Back to landing page</a><br><br>

<table class="layout"><tr>
<?php foreach($moves as $move): ?>
    <td>
    <?php $this->insert('partials/analyzeBoard', ['move' => $move, 'style' => "transform: scale(0.7);", 'color' => 'white']) ?>
    <table class="layout"><tr>
    <?php foreach($move->children as $move2): ?>
        <td>
        <?php $this->insert('partials/analyzeBoard', ['move' => $move2, 'style' => "transform: scale(0.6);", 'color' => 'pink']) ?>
        <table class="layout"><tr>
        <?php foreach($move2->children as $move3): ?>
            <td>
            <?php $this->insert('partials/analyzeBoard', ['move' => $move3, 'style' => "transform: scale(0.5);", 'color' => 'khaki']) ?>
            <table class="layout"><tr>
            <?php foreach($move3->children as $move4): ?>
                <td>
                <?php $this->insert('partials/analyzeBoard', ['move' => $move4, 'style' => "transform: scale(0.4);", 'color' => 'lightblue']) ?>
                <table class="layout"><tr>
                <?php foreach($move4->children as $move5): ?>
                    <td>
                    <?php $this->insert('partials/analyzeBoard', ['move' => $move5, 'style' => "transform: scale(0.3);", 'color' => 'palegreen']) ?>
                    </td>
                <?php endforeach ?>
                </tr></table>
                </td>
            <?php endforeach ?>
            </tr></table>
            </td>
        <?php endforeach ?>
        </tr></table>
        </td>
    <?php endforeach ?>
    </tr></table>
    </td>
<?php endforeach ?>
</tr></table>