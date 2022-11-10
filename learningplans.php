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

$reqcareerid = optional_param('id', null, PARAM_INT);

if (!$reqcareerid) {
    $urltogo = $CFG->wwwroot . '/local/leeloolxpcareers/';
    redirect($urltogo);
}

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("Learning Plans Listing");
$PAGE->set_heading("Learning Plans Listing");
$PAGE->set_url($CFG->wwwroot . '/local/leeloolxpcareers/learningplans.php');

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
    'careerid' => $reqcareerid
];
$url = $teamniourl . '/admin/sync_moodle_course/get_lpsbycareer';
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

echo $OUTPUT->header();
if ($response['data']['lpdata']['image_big']) {
    echo '<style>.carrer-main-banner {
        background-size: 100% 100%;
        background-image: url(' . $teamniourl . '/' . $response['data']['lpdata']['image_big'] . ');
    }</style>';
}
?>
<div class="row">
    <div class="col-12">
        <div class="main-left-section main-carrer">

            <div class="carrer-main-banner">
                <div class="container">
                    <div class="topMain-inn-banner">
                        <div class="topMain-left-arrow">
                            <a href="javascript:history.back()"><img src="https://vonkelemen.org/online/local/leeloolxpcareers/assets/img/left-aro-img.png" alt=""></a>
                        </div>
                        <div class="topMain-cont-banner">
                            <!-- <h2><?php echo $response['data']['lpdata']['name']; ?></h2>
                            <h4><?php echo $response['data']['lpdata']['heading']; ?></h4> -->
                            <?php echo $response['data']['lpdata']['heading']; ?>
                            <div class="gratuita-btn-right">
                                <div class="gratuita-btn">
                                    <a href="https://vonkelemen.org/leeloo/login">
                                        <h5>Crear una cuenta gratuita</h5>
                                        <p>para registrar mi progreso y mis certificaciones</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-menu-bar">
                <div class="container">
                    <div class="row" id="yui_3_17_2_1_1667024021769_23">
                        <div class="col-md-4" id="yui_3_17_2_1_1667024021769_22">
                            <div class="filter-select" id="yui_3_17_2_1_1667024021769_21">
                                <div class="form-group" id="yui_3_17_2_1_1667024021769_20">
                                    <select class="form-control" id="sel1">
                                        <option value="">Nivel educativo</option>
                                        <?php foreach ($response['data']['filterarr']['career_cats'] as $careercat) {
                                            echo '<option value="' . $careercat['id'] . '">' . $careercat['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="yui_3_17_2_1_1667024021769_34">
                            <div class="filter-select" id="yui_3_17_2_1_1667024021769_33">
                                <div class="form-group" id="yui_3_17_2_1_1667024021769_32">
                                    <select class="form-control" id="sel2">
                                        <option value="">Carrera</option>
                                        <?php foreach ($response['data']['filterarr']['careers'] as $career) {
                                            echo '<option data-parentid="' . $career['category_id'] . '" value="' . $career['id'] . '">' . $career['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="yui_3_17_2_1_1667024021769_37">
                            <div class="filter-select" id="yui_3_17_2_1_1667024021769_36">
                                <div class="form-group" id="yui_3_17_2_1_1667024021769_35">
                                    <select class="form-control" id="sel3">
                                        <option value="">Grado académico</option>
                                        <?php foreach ($response['data']['filterarr']['lps'] as $lp) {
                                            $lpurl = new moodle_url(
                                                '/local/leeloolxpcareers/plandetails.php',
                                                ['id' => $lp['id']]
                                            );
                                            echo '<option data-parentid="' . $lp['parent_id'] . '" value="' . $lpurl . '">' . $lp['name'] . '</option>';
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
                            foreach ($response['data']['lpslisting'] as $lp) { ?>
                                <li>
                                    <div class="carrerList-item">
                                        <div class="carrerList-item-img">
                                            <img src="<?php echo $teamniourl . '/' . $lp['image_small']; ?>" alt="">
                                        </div>
                                        <div class="carrerList-item-cont">
                                            <div class="carrerList-item-title">
                                                <div class="carrerList-item-name"><?php echo $lp['name']; ?></div>
                                                <div class="carrerList-item-des"><?php echo $lp['heading']; ?></div>
                                            </div>
                                            <div class="carrerList-item-btn">
                                                <?php
                                                $lpurl = new moodle_url(
                                                    '/local/leeloolxpcareers/plandetails.php',
                                                    ['id' => $lp['id']]
                                                );
                                                ?>
                                                <a href="<?php echo $lpurl; ?>" class="btn">VER DETALLES</a>
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
<?php

echo $OUTPUT->footer();
