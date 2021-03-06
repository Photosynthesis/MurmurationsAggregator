<div class="murmurations-node <?= $data_classes ?>" id="<?= $node->murmurations['url'] ?>">
  <table style="border:0;" class="murmurations-node-table">
    <tr>
      <td style="border:0; width:100px;">
  <div class="murmurations-node-image"><img src="<?= $node->murmurations['logo'] ?>"></div>
</td>
<td style="border:0;">

 <div class="murmurations-node-content">
  <h3 class="murmurations-node-name"><?= $node->murmurations['name'] ?></h3>
  <div class="murmurations-node-tagline"><?= $node->murmurations['tagline'] ?></div>
  <div class="murmurations-node-mission"><?= $node->murmurations['mission'] ?></div>
  <div class="murmurations-node-description"><?= wp_trim_words($node->murmurations['description'],40,"...") ?></div>
  <div class="murmurations-node-org-types"><?= $node->murmurations['nodeTypes'] ?></div>
  <div class="murmurations-node-coordinates">
    <?php

    $place_components = array();

    if($node->murmurations['location']['locality']){
       $place_components[] = $node->murmurations['location']['locality'];
    }

    if($node->murmurations['location']['region']){
       $place_components[] = $node->murmurations['location']['region'];
    }

    echo join(', ',$place_components);

    ?></div>
  <a class="murmurations-node-url" href="<?= $node->murmurations['url'] ?>"><?= $node->murmurations['url'] ?></a>
</div>
</td>
</tr>
</table>
</div>
