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

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("Careers");
$PAGE->set_heading("Careers");
$PAGE->set_url($CFG->wwwroot . '/local/leeloolxpcareers/index.php');

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

$url = $teamniourl . '/admin/sync_moodle_course/get_career_cats';
$curl = new curl;
$options = array(
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_HEADER' => false,
    'CURLOPT_POST' => 0,
    'CURLOPT_HTTPHEADER' => array(
        'Leeloolxptoken: ' . get_config('local_leeloolxpapi')->leelooapitoken . ''
    )
);

if (!$response = $curl->post($url, [], $options)) {
    $urltogo = $CFG->wwwroot;
    redirect($urltogo, 'Invalid License Key', 1);
}

$response = json_decode($response, true);

echo $OUTPUT->header();

?>
<div class="row">
    <div class="col-12">
        <div class="main-left-section main-carrer">

            <div class="carrer-main-banner">
                <div class="container">
                    <div class="carrermain-cont-banner">
                        <ul>
                            <?php
                            $countcats = count($response['data']['career_cats']);
                            foreach ($response['data']['career_cats'] as $careercat) { ?>
                                <li class="center-carrermain">
                                    <div class="carrermain-item">
                                        <div class="carrermain-head">
                                            <img src="<?php echo $teamniourl . '/' . $careercat['image_small']; ?>" alt="">
                                            <h3><?php echo $careercat['name']; ?></h3>
                                        </div>

                                        <!-- <div class="carrermain-for"><?php echo $careercat['description']; ?></div>
                                        <div class="carrermain-vez"><?php echo $careercat['heading']; ?></div> -->
                                        <div class="carrermain-list">
                                            <?php echo $careercat['summary']; ?>
                                        </div>
                                        <div class="carrermain-ftr">
                                            <div class="carrermain-pri">
                                                <?php echo $careercat['pricing']; ?>
                                            </div>
                                            <div class="carrermain-btn">
                                                <?php
                                                $caturl = new moodle_url(
                                                    '/local/leeloolxpcareers/careers.php',
                                                    ['id' => $careercat['id']]
                                                );
                                                ?>
                                                <a href="<?php echo $caturl; ?>" class="btn">VeR carreras</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
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

        </div>
    </div>

</div>
<?php

echo $OUTPUT->footer();
