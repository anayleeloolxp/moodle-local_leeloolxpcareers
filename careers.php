<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_leeloolxpcareers
 * @copyright   2022 Leeloo LXP <info@leeloolxp.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');

$reqcatid = optional_param('id', null, PARAM_INT);

if (!$reqcatid) {
    $urltogo = $CFG->wwwroot . '/local/leeloolxpcareers/';
    redirect($urltogo);
}

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');


$PAGE->set_url($CFG->wwwroot . '/local/leeloolxpcareers/careers.php');

$PAGE->requires->css('/local/leeloolxpcareers/assets/css/style.css');
$PAGE->requires->js('/local/leeloolxpcareers/assets/js/script.js');

$configleeloolxpapi = get_config('local_leeloolxpapi');

$licensekey = $configleeloolxpapi->gradelicensekey;

$curl = new curl;

$postdata = array('license_key' => $licensekey);
$url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
$options = array(
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_HEADER' => false,
    'CURLOPT_POST' => count($postdata),
);

$output = $curl->post($url, $postdata, $options);

if (!$output = $curl->post($url, $postdata, $options)) {
    $urltogo = $CFG->wwwroot;
    redirect($urltogo, 'Invalid License Key', 1);
    return true;
}

$infoleeloolxp = json_decode($output);

if ($infoleeloolxp->status != 'false') {
    $teamniourl = $infoleeloolxp->data->install_url;
} else {
    $urltogo = $CFG->wwwroot;
    redirect($urltogo, 'Invalid License Key', 1);
    return true;
}

$postdata = [
    'catid' => $reqcatid
];
$url = $teamniourl . '/admin/sync_moodle_course/get_careersbycat';
$curl = new curl;
$options = array(
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_HEADER' => false,
    'CURLOPT_POST' => count($postdata),
    'CURLOPT_HTTPHEADER' => array(
        'Leeloolxptoken: ' . get_config('local_leeloolxpapi')->leelooapitoken . ''
    )
);

if (!$response = $curl->post($url, $postdata, $options)) {
    $urltogo = $CFG->wwwroot;
    redirect($urltogo, 'Invalid License Key', 1);
}

$response = json_decode($response, true);
$PAGE->set_title($response['data']['catdata']['name']);
$PAGE->set_heading($response['data']['catdata']['name']);
echo $OUTPUT->header();
if ($response['data']['catdata']['image_big']) {
    echo '<style>.carrer-main-banner {
        background-size: 100% 100%;
        background-image: url(' . $teamniourl . '/' . $response['data']['catdata']['image_big'] . ');
    }</style>';
}
$careerhomeurl = new moodle_url(
    '/local/leeloolxpcareers/index.php'
);
?>
<div class="row">
    <div class="col-12">
        <div class="main-left-section main-carrer">

            <div class="carrer-main-banner">
                <div class="container">
                    <div class="topMain-inn-banner">
                        <div class="topMain-left-arrow">
                            <a href="<?php echo $careerhomeurl; ?>"><img src="https://vonkelemen.org/online/local/leeloolxpcareers/assets/img/left-aro-img.png" alt=""></a>
                        </div>
                        <div class="topMain-cont-banner">
                            <!-- <h2><?php echo $response['data']['catdata']['name']; ?></h2>
                        <h4><?php echo $response['data']['catdata']['heading']; ?></h4> -->
                            <?php echo $response['data']['catdata']['heading']; ?>
                            <div class="gratuita-btn-right">
                                <div class="gratuita-btn">
                                    <?php echo get_string('createaccount', 'local_leeloolxpcareers'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-menu-bar">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="filter-select">
                                <div class="form-group">
                                    <select class="form-control" id="sel1">
                                        <option value=""><?php echo get_string('nivel', 'local_leeloolxpcareers'); ?></option>
                                        <?php foreach ($response['data']['filterarr']['career_cats'] as $careercat) {
                                            echo '<option value="' . $careercat['id'] . '">' . $careercat['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-select">
                                <div class="form-group">
                                    <select class="form-control" id="sel2">
                                        <option value=""><?php echo get_string('career', 'local_leeloolxpcareers'); ?></option>
                                        <?php foreach ($response['data']['filterarr']['careers'] as $career) {
                                            echo '<option class="hideimportant sel2options parentcat_' . $career['category_id'] . '" value="' . $career['id'] . '">' . $career['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-select">
                                <div class="form-group">
                                    <select class="form-control" id="sel3">
                                        <option value=""><?php echo get_string('grado', 'local_leeloolxpcareers'); ?></option>
                                        <?php foreach ($response['data']['filterarr']['lps'] as $lp) {
                                            $lpurl = new moodle_url(
                                                '/local/leeloolxpcareers/plandetails.php',
                                                ['id' => $lp['id']]
                                            );
                                            echo '<option class="hideimportant sel3options parentcar_' . $lp['parent_id'] . '" value="' . $lpurl . '">' . $lp['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carrer-listing">
                <div class="container">
                    <div class="carrer-list-items">
                        <ul>
                            <?php
                            foreach ($response['data']['careerlisting'] as $career) { ?>
                                <li>
                                    <div class="carrerList-item">
                                        <div class="carrerList-item-img">
                                            <img src="<?php echo $teamniourl . '/' . $career['image_small']; ?>" alt="">
                                        </div>
                                        <div class="carrerList-item-cont">
                                            <div class="carrerList-item-title">
                                                <div class="carrerList-item-name"><?php echo $career['name']; ?></div>
                                                <div class="carrerList-item-des"><?php echo $career['lps']; ?></div>
                                            </div>
                                            <div class="carrerList-item-btn">
                                                <?php
                                                if ($career['lpsintab'] == 1) {
                                                    $careerurl = new moodle_url(
                                                        '/local/leeloolxpcareers/plandetails.php',
                                                        ['id' => $career['lpfirst']]
                                                    );
                                                } else {
                                                    $careerurl = new moodle_url(
                                                        '/local/leeloolxpcareers/learningplans.php',
                                                        ['id' => $career['id']]
                                                    );
                                                }
                                                ?>
                                                <a href="<?php echo $careerurl; ?>" class="btn"><?php echo get_string('viewdetails', 'local_leeloolxpcareers'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<div class="ddstyles">
    <style id="sel1style"></style>
    <style id="sel2style"></style>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>
    $(function() {
        $('select').selectpicker({
            dropupAuto: false
        });
    });
</script>
<?php

echo $OUTPUT->footer();
