<?php

use yii\web\YiiAsset;

?>

<div id="hall-container">

 
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<?php
require_once('img/test.svg');
require_once('img/1table.svg');
?>


</div>

<?= $this->registerJsFile('/js/booking.js', ['depends' => YiiAsset::class]); ?>