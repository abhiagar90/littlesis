<?php decorate_with('layout') ?>

<?php include_partial('map/header', array('map' => $map)) ?>

<div id="map_description"><?php echo $map["description"] ?></div>

<?php include_partial('global/map_and_controls', array('id' => $map["id"])) ?>

<script>
var data = <?php echo $map["data"] ?>;
var width = <?php echo $map["width"] ? $map["width"] : sfConfig::get("app_netmap_default_width") ?>;
var height = <?php echo $map["height"] ? $map["height"] : sfConfig::get("app_netmap_default_height") ?>;
var key = '<?php echo sfConfig::get("app_netmap_api_key") ?>';
var netmap = new Netmap(width, height, "#netmap", key);
netmap.set_network_map_id(<?php echo $map["id"] ?>);
netmap.set_user_id(<?php echo $map["user_id"] ?>);
netmap.set_data(data);
netmap.build();
</script>