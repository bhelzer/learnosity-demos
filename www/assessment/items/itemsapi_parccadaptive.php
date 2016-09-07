<?php

include_once '../../config.php';
include_once 'includes/header.php';

use LearnositySdk\Request\Init;
use LearnositySdk\Utils\Uuid;

$security = array(
    'consumer_key' => $consumer_key,
    'domain'       => $domain
);

$request = array(
    'activity_id'    => 'itemadaptivedemo',
    'name'           => 'Items API demo - PARCC adaptive activity',
    'rendering_type' => 'assess',
    'state'          => 'initial',
    'session_id'     => Uuid::generate(),
    'user_id'        => $studentid,
    'assess_inline'  => true,
    'adaptive'       => array(
        'type'                      => 'parccadaptive',
        'required_tags' => array(
            array('type' => 'adaptive-item-set', 'name' => 'ELA Decoding')
        )
    ),
    'config' => array(
        'title' => 'Adaptive Assessment – PARCC ELA algorithm',
        'administration' => array(
            'pwd' => '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8' // `password`
        ),
        'navigation' => array(
            'intro_item'             => 'adaptive-intro',
            'show_prev'              => false,
            'show_progress'          => false,
            'show_fullscreencontrol' => false,
            'show_acknowledgements'  => true,
            'toc'                    => false
        ),
        'time' => array(
            'max_time' => 1800
        ),
        'assessApiVersion'    => "latest",
        'configuration'       => array(
            'onsubmit_redirect_url' => 'itemsapi_parccadaptive.php',
            'onsave_redirect_url'   => 'itemsapi_parccadaptive.php'
        )
    )
);

if (isset($_POST['adaptive'])) {
    foreach ($_POST['adaptive'] as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $childKey => $childValue) {
                if (strlen($childValue)) {
                    $request['adaptive'][$key][$childKey] = (float) $childValue;
                } else {
                    unset($request['adaptive'][$key][$childKey]);
                }
            }
        } else {
            if (strlen($value)) {
                $request['adaptive'][$key] = (float) $value;
            } else {
                unset($request['adaptive'][$key]);
            }
        }
    }
}

$Init = new Init('items', $security, $consumer_secret, $request);
$signedRequest = $Init->generate();

?>

<div class="jumbotron section">
    <div class="pull-right toolbar">
        <ul class="list-inline">
            <li data-toggle="tooltip" data-original-title="Customise API Settings"><a href="#" class="text-muted" data-toggle="modal" data-target="#settings"><span class="glyphicon glyphicon-list-alt"></span></a></li>
            <li data-toggle="tooltip" data-original-title="Preview API Initialisation Object"><a href="#"  data-toggle="modal" data-target="#initialisation-preview"><span class="glyphicon glyphicon-search"></span></a></li>
            <li data-toggle="tooltip" data-original-title="Visit the documentation"><a href="http://docs.learnosity.com/assessment/items/knowledgebase/adaptiveassessment" title="Documentation"><span class="glyphicon glyphicon-book"></span></a></li>
        </ul>
    </div>
    <h1>Items API – Adaptive Assessment – PARCC ELA algorithm</h1>
    <p>A dynamic assessment that adapts to the user's ability in real time.<p>
</div>

<div class="section">
    <!-- Container for the items api to load into -->
    <div id="learnosity_assess"></div>
</div>
<script src="<?php echo $url_items; ?>"></script>
<script>
    var itemsApp = LearnosityItems.init(<?php echo $signedRequest; ?>);
</script>

<?php
    include_once '../../../src/views/modals/settings-items-adaptive.php';
    include_once '../../../src/views/modals/initialisation-preview.php';
    include_once '../../../src/includes/footer.php';
