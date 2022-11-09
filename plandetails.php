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

$reqlpid = optional_param('id', null, PARAM_INT);

if (!$reqlpid) {
    $urltogo = $CFG->wwwroot . '/local/leeloolxpcareers/';
    redirect($urltogo);
}

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("Learning Plan Details");
$PAGE->set_heading("Learning Plan Details");
$PAGE->set_url($CFG->wwwroot . '/local/leeloolxpcareers/plandetails.php');

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
    'id' => $reqlpid
];
$url = $teamniourl . '/admin/sync_moodle_course/get_lpbyid';
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

$lpdata = $response['data']['lpdata'];

$PAGE->set_title($lpdata['name']);
$PAGE->set_heading($lpdata['name']);

echo $OUTPUT->header();

function local_leeloolxpcareers_course_image($course) {
    global $CFG;

    $course = new core_course_list_element($course);
    // Check to see if a file has been set on the course level.
    if ($course->id > 0 && $course->get_course_overviewfiles()) {
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url(
                "$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(),
                !$isimage
            );
            if ($isimage) {
                return $url;
            } else {
                return 'https://leeloolxp.com/modules/mod_acadmic/images/Leeloo-lxp1.png';
            }
        }
    } else {
        // Lets try to find some default images eh?.
        return 'https://leeloolxp.com/modules/mod_acadmic/images/Leeloo-lxp1.png';
    }
    // Where are the default at even?.
    return 'https://leeloolxp.com/modules/mod_acadmic/images/Leeloo-lxp1.png';
}

?>
<div class="row">
    <div class="col-12">
        <div class="main-left-section main-carrer">

            <div class="carrer-main-banner">
                <div class="container">
                    <div class="carrer-banner-inn">
                        <div class="carrer-left-video">
                            <?php if ($lpdata['video_1']) {
                                if (strpos($lpdata['video_1'], 'uploads/temp_files/') !== false) {
                                    echo '<video width="640" controls><source src="' . $teamniourl . '/' . $lpdata['video_1'] . '" type="video/mp4">Your browser does not support HTML video.</video>';
                                } else {
                                    echo '<iframe src="https://player.vimeo.com/video/' . $lpdata['video_1'] . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                                }
                            } else if ($lpdata['image_big']) {
                                echo '<img src="' . $teamniourl . '/' . $lpdata['image_big'] . '" alt="">';
                            } ?>

                        </div>
                        <div class="carrer-right-cont">
                            <div class="carrer-head"><?php echo $lpdata['name']; ?></div>
                            <div class="carrer-cont">
                                <div class="carrer-cont-top">
                                    <?php echo $lpdata['summary']; ?>
                                </div>
                                <div class="carrer-cont-btm">
                                    <div class="ahora-btn"><a class="btn" href="#">Matricular ahora</a></div>
                                    <p>Vives en América Latina?</p>
                                    <p><a href="https://vonkelemen.org/leeloo/aplicacion-beca">Aplicar para una beca</a></p>
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

            <div class="carrer-details">
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="carrer-details-left">
                                <h4><?php echo $lpdata['name']; ?></h4>
                                <p><small><?php echo $lpdata['heading']; ?></small></p>
                                <p><?php echo $lpdata['description']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="carrer-details-right">
                                <div class="carrer-top-video">
                                    <?php if ($lpdata['video_2']) {
                                        if (strpos($lpdata['video_2'], 'uploads/temp_files/') !== false) {
                                            echo '<video width="640" controls><source src="' . $teamniourl . '/' . $lpdata['video_2'] . '" type="video/mp4">Your browser does not support HTML video.</video>';
                                        } else {
                                            echo '<iframe src="https://player.vimeo.com/video/' . $lpdata['video_2'] . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                                        }
                                    } else if ($lpdata['image_small']) {
                                        echo '<img src="' . $teamniourl . '/' . $lpdata['image_small'] . '" alt="">';
                                    } ?>
                                </div>
                                <div class="carrer-btm-cont">
                                    <div class="carrer-cont">
                                        <div class="carrer-cont-top">
                                            <?php echo $lpdata['summary']; ?>
                                        </div>
                                        <div class="carrer-cont-btm">
                                            <div class="ahora-btn"><a class="btn" href="#">Matricular ahora</a></div>
                                            <p>Vives en América Latina?</p>
                                            <p><a href="https://vonkelemen.org/leeloo/aplicacion-beca">Aplicar para una beca</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carrer-tab-section">
                <div class="container">
                    <div class="carrer-tab-inn">

                        <?php
                        if ($response['data']['careerlpsintab'] == 1) {
                            $siblings = $response['data']['siblings'];
                        ?>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <?php foreach ($siblings as $key => $sibling) {
                                    if ($reqlpid == $sibling['id']) {
                                        echo '<li class="nav-item active"><a class="nav-link" id="myTab' . $key . '-tab" data-toggle="tab" href="#myTab' . $key . '" role="tab" aria-controls="myTab' . $key . '" aria-selected="true">' . $sibling['name'] . '</a></li>';
                                    } else {
                                        $lpurl = new moodle_url(
                                            '/local/leeloolxpcareers/plandetails.php',
                                            ['id' => $sibling['id']]
                                        );
                                        echo '<li class="nav-item"><a href="' . $lpurl . '" class="nav-link" id="myTab' . $key . '-tab">' . $sibling['name'] . '</a></li>';
                                    }
                                } ?>
                            </ul>
                        <?php }
                        if ($lpdata['per_period'] == 1) {
                            $colmain = 'col-md-3';
                            $colsub = 'col-md-12';
                        } else if ($lpdata['per_period'] == 2) {
                            $colmain = 'col-md-6';
                            $colsub = 'col-md-6';
                        } else if ($lpdata['per_period'] == 3) {
                            $colmain = 'col-md-4';
                            $colsub = 'col-md-12';
                        } else {
                            $colmain = 'col-md-12';
                            $colsub = 'col-md-3';
                        }
                        $lpinstances = $response['data']['lpinstances'];
                        $ordernumbersmax = max(array_column($lpinstances, 'order_number'));

                        ?>
                        <div class="tab-content" id="myTabContent">

                            <div class="tab-pane fade show active" id="myTab" role="tabpanel" aria-labelledby="myTab-tab">
                                <div class="carrer-tab-mod">
                                    <div class="row">
                                        <?php
                                        for ($x = 0; $x <= $ordernumbersmax; $x++) { ?>
                                            <div class="<?php echo $colmain; ?>">
                                                <div class="carrer-mod-in">
                                                    <div class="carrer-mod-head"><?php echo $lpdata['period_name'] . ' ' . ($x + 1); ?></div>
                                                    <div class="carrer-mod-cont">
                                                        <ul>
                                                            <?php
                                                            foreach ($lpinstances as $instance) {
                                                                if ($instance['order_number'] == $x) {
                                                                    if ($instance['type'] == 'courses') {
                                                                        $instanceurl = new moodle_url(
                                                                            '/course/view.php',
                                                                            [
                                                                                'id' => $instance['instanceid'],
                                                                                'newui' => 1
                                                                            ]
                                                                        );

                                                                        if (!$instance['instanceimg']) {
                                                                            $course = new stdClass();
                                                                            $course->id = $instance['instanceid'];
                                                                            $instanceimg = local_leeloolxpcareers_course_image($course);
                                                                        } else {
                                                                            $instanceimg = $teamniourl . '/' . $instance['instanceimg'];
                                                                        }
                                                                    } else {
                                                                        $instanceurl = new moodle_url(
                                                                            '/local/leeloolxpcareers/courseset.php',
                                                                            ['id' => $instance['instanceid']]
                                                                        );
                                                                        $instanceimg = $teamniourl . '/' . $instance['instanceimg'];
                                                                    }
                                                            ?>
                                                                    <li class="<?php echo $colsub; ?>">
                                                                        <div class="carrer-item">
                                                                            <div class="coursecategory-item-img">
                                                                                <img src="<?php echo $instanceimg; ?>" alt="">
                                                                            </div>
                                                                            <div class="carrer-item-cont">
                                                                                <div class="carrer-item-list">
                                                                                    <a href="<?php echo $instanceurl; ?>"><?php echo $instance['instancename']; ?></a>
                                                                                </div>
                                                                                <div class="carrer-item-btn">
                                                                                    <a href="#" class="btn btn-light">Matricular</a>
                                                                                </div>
                                                                                <div class="carrer-item-title">
                                                                                    <div style="visibility: hidden;" class="carrer-item-name">1 de Noviembre 2022</div>
                                                                                    <div class="carrer-item-icon">
                                                                                        <span class="netW netW-1 active"></span>
                                                                                        <span class="netW netW-2 active"></span>
                                                                                        <span class="netW netW-3 active"></span>
                                                                                        <span class="netW netW-4"></span>
                                                                                        <span class="netW netW-5"></span>
                                                                                        <span class="netW netW-6"></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                            <?php }
                                                            }
                                                            ?>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        ?>


                                    </div>
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
