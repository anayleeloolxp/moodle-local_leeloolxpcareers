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

$reqcoursesetid = optional_param('id', null, PARAM_INT);

if (!$reqcoursesetid) {
    $urltogo = $CFG->wwwroot . '/local/leeloolxpcareers/';
    redirect($urltogo);
}

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading("Course Set");
$PAGE->set_url($CFG->wwwroot . '/local/leeloolxpcareers/courseset.php');

$PAGE->requires->css('/local/leeloolxpcareers/assets/css/style.css');
$PAGE->requires->css('/theme/adsensitive/css/newui.css');
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
    'id' => $reqcoursesetid
];
$url = $teamniourl . '/admin/sync_moodle_course/get_courseset';
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

$coursesetdata = $response['data']['coursesetdata'];

$courses = $response['data']['courses'];

$PAGE->set_title($coursesetdata['name']);
$PAGE->set_heading($coursesetdata['name']);

echo $OUTPUT->header();

if ($coursesetdata['image_big']) {
    echo '<style>.page-top-main-banner {
        background-size: 100% 100%;
        background-image: url(' . $teamniourl . '/' . $coursesetdata['image_big'] . ');
    }</style>';
}

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

global $DB, $USER;
if ($USER->id && $USER->id != 1) {
    $userloggedin = 1;
}

$servername = "localhost";
$username = "vkorg_demo";
$password = ",FJn.yEtWQ@t";
$dbname = "vkorg_demo";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$coursesetcoursematricular = array();
?>
<div id="wrapper">

    <div class="page-top-main-banner" id="yui_3_17_2_1_1667300920418_28">
        <div class="page-img-main-banner" id="yui_3_17_2_1_1667300920418_27">
            <div class="page-img-container" id="yui_3_17_2_1_1667300920418_26">
                <div class="page-main-banner-cont" id="yui_3_17_2_1_1667300920418_25">
                    <div class="page-top-main-banner-cont" id="yui_3_17_2_1_1667300920418_24">
                        <div class="container" id="yui_3_17_2_1_1667300920418_23">
                            <div class="page-context-header" id="yui_3_17_2_1_1667300920418_22">
                                <div class="page-header-headings" id="yui_3_17_2_1_1667300920418_21">
                                    <h1 id="yui_3_17_2_1_1667300920418_20"><?php echo $coursesetdata['name']; ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-pl-btn">
                        <div class="container">
                            <div class="row coursesetlist">
                                <?php

                                $activities = 0;
                                $videos = 0;
                                $modules = 0;

                                foreach ($courses as $course) {

                                    $getcourse = $DB->get_record('course', array(
                                        'id' => $course['courseid']
                                    ));
                                    if (!$getcourse) {
                                        continue;
                                    }

                                    $modinfo = get_fast_modinfo($course['courseid']);
                                    foreach ($modinfo->sections as $section) {
                                        $activities += count($section);
                                    }
                                    $modules += count($modinfo->sections);
                                    foreach ($modinfo->cms as $cms) {

                                        if (($cms->modname == 'leeloolxpvimeo' || $cms->modname == 'premiumvideo' || $cms->modname == 'regularvideo') &&  $cms->visible == 1) {
                                            $videos += 1;
                                        }
                                    }

                                    $coursesin = new stdClass();
                                    $coursesin->id = $course['courseid'];
                                    $instanceimg = local_leeloolxpcareers_course_image($coursesin);

                                    $courseurl = new moodle_url(
                                        '/course/view.php',
                                        [
                                            'id' => $course['courseid'],
                                            //'newui' => 1
                                        ]
                                    );
                                ?>
                                    <div class="col-3">
                                        <div class="coursecategory-item">
                                            <div class="coursecategory-item-img">
                                                <img src="<?php echo $instanceimg; ?>" alt="">
                                            </div>
                                            <div class="coursecategory-item-cont">
                                                <div class="coursecategory-item-title">
                                                    <div class="coursecategory-item-name"><?php echo $course['coursename'] ?></div>
                                                    <div class="coursecategory-item-icon">
                                                        <span class="netW netW-1 active"></span>
                                                        <span class="netW netW-2"></span>
                                                        <span class="netW netW-3"></span>
                                                        <span class="netW netW-4"></span>
                                                        <span class="netW netW-5"></span>
                                                        <span class="netW netW-6"></span>
                                                    </div>
                                                </div>
                                                <div class="coursecategory-item-list">
                                                    <a href="<?php echo $courseurl; ?>"><?php echo get_string('viewdetailcontent', 'local_leeloolxpcareers'); ?></a>
                                                </div>
                                                <div class="coursecategory-item-btn">
                                                    <?php

                                                    $selfenrol = $DB->get_record('enrol', array(
                                                        'courseid' => $course['courseid'],
                                                        'enrol' => 'self',
                                                        'status' => '0'
                                                    ));

                                                    $coursecontext = context_course::instance($course['courseid']);

                                                    if ($selfenrol && !is_enrolled($coursecontext, $USER->id)) {
                                                        $coursesetcoursematricular[] = $course['courseid'];
                                                        echo $matrihtml = '<a class="myModal_enrol_link enrollicon btn btn-light" data-toggle="modal" data-target="#myModal_enrol' . $course['courseid'] . '">' . get_string('matriculargratis', 'local_leeloolxpcareers') . '</a>

                                                        <div id="myModal_enrol' . $course['courseid'] . '" class="modal fade myModal_enrol" role="dialog">
                                                                <div class="modal-dialog">

                                                                    <!-- Modal content-->
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title">' . $course['coursename'] . '</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                        <form autocomplete="off" action="' . $CFG->wwwroot . '/enrol/index.php" method="post" accept-charset="utf-8" class="mform" data-boost-form-errors-enhanced="1">
                                                                            <div style="display: none;">
                                                                            <input name="id" type="hidden" value="' . $course['courseid'] . '">
                                                                            <input name="instance" type="hidden" value="' . $selfenrol->id . '">
                                                                            <input name="sesskey" type="hidden" value="' . sessKey() . '">
                                                                            <input type="password" class="form-control " name="enrolpassword" id="enrolpassword_' . $selfenrol->id . '" value="' . $selfenrol->password . '" autocomplete="off">
                                                                            <input name="_qf__' . $selfenrol->id . '_enrol_self_enrol_form" type="hidden" value="1">
                                                                            <input name="mform_isexpanded_id_selfheader" type="hidden" value="1">
                                                                            </div>
                                                                            <input type="submit" class=" btn-primary" name="submitbutton" id="id_submitbutton" value="' . get_string('matricularme', 'local_leeloolxpcareers') . '">
                                                                        </form>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>';
                                                    }

                                                    $sqlproduct = "SELECT product_id, product_alias FROM pkae2_hikashop_product where product_name = '" . $course['coursename'] . "'";
                                                    $conn->query("SET NAMES 'utf8'");
                                                    $resultsql = $conn->query($sqlproduct);
                                                    $rowproduct = $resultsql->fetch_assoc();

                                                    if (!empty($rowproduct) && !is_enrolled($coursecontext, $USER->id)) {

                                                        $pro_alias = $rowproduct['product_alias'];
                                                        if ($pro_alias == '') {
                                                            $product_url = "https://vonkelemen.org/leeloo/?option=com_hikashop&view=product&layout=show&product_id=" . $rowproduct['product_id'];
                                                        } else {
                                                            $product_url = "https://vonkelemen.org/leeloo/store/product/" . $pro_alias;
                                                        }


                                                        echo $certihtml = '<div id="myModal_hika' . $course['courseid'] . '" class="modal fade myModal_hika" role="dialog">
                                                            <div class="modal-dialog">

                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                                                        <h4 class="modal-title">' . $course['coursename'] . '</h4>
                                                                    </div>
                                                                    <div class="modal-body">

                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>

                                                        <button class="btn btn-primary myModal_hika_link" link="' . $product_url . '" data-toggle="modal" data-target="#myModal_hika' . $course['courseid'] . '">' . get_string('certificar', 'local_leeloolxpcareers') . '</button>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>


                            </div>

                        </div>
                    </div>

                    <div class="gamification-right-tab-sec" id="">
                        <div class="gam-urarro" style="background:url(<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/arrow-icon.png'; ?>) no-repeat center center; display: none;"></div>
                        <ul id="">
                            <li>
                                <button type="button" class="btn" data-toggle="modal" data-target="#introModal" id="">
                                    <div class="gami-tab-itm" id="">
                                        <div class="gami-tab-icon" id=""><img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/pl-bk.png'; ?>"></div>
                                        <span><?php echo get_string('intro', 'local_leeloolxpcareers'); ?></span>
                                    </div>
                                </button>
                            </li>
                            <li id="">
                                <button type="button" class="btn" data-toggle="modal" data-target="#overview_skill" id="">
                                    <div class="gami-tab-itm" id="">
                                        <div class="gami-tab-icon" id=""><img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/overview.png'; ?>" id=""></div>
                                        <span><?php echo get_string('overview', 'local_leeloolxpcareers'); ?></span>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="page-bottom-bar" id="">
            <div class="page-bottom-inn" id="">
                <div class="container">
                    <div class="row" id="">
                        <div class="col-md-3"></div>
                        <div class="col-md-6" id="">
                            <div class="bottom-bar-btn text-center" id="">

                                <?php if ($coursesetcoursematricular) {

                                ?>
                                    <div class="bottom-bar-div">
                                        <button class="btn" data-toggle="modal" data-target="#myModal_enrolset"><?php echo get_string('matriculargratis', 'local_leeloolxpcareers'); ?></button>
                                    </div>
                                <?php } ?>
                                <!-- <div class="bottom-bar-div">


                                </div> -->

                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>




    <div id="myModal_enrolset" class="modal fade myModal_enrol" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $coursesetdata['name']; ?></h4>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" action="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/enrollsets.php'; ?>" method="post" accept-charset="utf-8" class="mform" data-boost-form-errors-enhanced="1">
                        <div style="display: none;">
                            <input name="type" type="hidden" value="courseset">
                            <input name="instance" type="hidden" value="<?php echo $reqcoursesetid; ?>">
                            <input name="sesskey" type="hidden" value="<?php echo sessKey(); ?>">
                            <?php foreach ($coursesetcoursematricular as $couretoenrol) {
                                echo '<input name="courestoenrol[]" type="hidden" value="' . $couretoenrol . '">';
                            } ?>

                        </div>
                        <input type="submit" class=" btn-primary" name="submitbutton" id="id_submitbutton" value="<?php echo get_string('matricularme', 'local_leeloolxpcareers'); ?>">
                    </form>
                </div>

            </div>

        </div>
    </div>

    <div class="modal fade overview_skill" id="overview_skill" tabindex="-1" role="dialog" aria-labelledby="overview_skill" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" role="document" id="">
            <div class="modal-content" id="">
                <div class="modal-header" id="">
                    <h4 class="modal-title" id="exampleModalLabel"><?php echo $coursesetdata['name']; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="">
                        <span aria-hidden="true" id="">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="overview_popinn">
                        <div class="left-mod-section">
                            <div class="row">
                                <!-- <div class="col-3 ">
                                    <div class="mod-inn">
                                        <div class="mod-icn">
                                            <span>
                                                <img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/course01.png'; ?>">
                                            </span>
                                        </div>
                                        <div class="mod-cont">
                                            <h3><span>0h00m</span> <small>hours</small></h3>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-4">
                                    <div class="mod-inn">
                                        <div class="mod-icn">
                                            <span>
                                                <img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/course02.png'; ?>">
                                            </span>
                                        </div>
                                        <div class="mod-cont">
                                            <h3><span><?php echo $videos; ?></span> <small><?php echo get_string('videolessons', 'local_leeloolxpcareers'); ?></small></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mod-inn">
                                        <div class="mod-icn">
                                            <span>
                                                <img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/course03.png'; ?>">
                                            </span>
                                        </div>
                                        <div class="mod-cont">
                                            <h3><span><?php echo $modules; ?></span> <small><?php echo get_string('modules', 'local_leeloolxpcareers'); ?></small></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mod-inn">
                                        <div class="mod-icn">
                                            <span>
                                                <img src="<?php echo $CFG->wwwroot . '/local/leeloolxpcareers/assets/img/course04.png'; ?>">
                                            </span>
                                        </div>
                                        <div class="mod-cont">
                                            <h3><span><?php echo $activities; ?></span> <small><?php echo get_string('learningactivities', 'local_leeloolxpcareers'); ?></small></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="left-abt-section">
                            <div class="row">
                                <div class="col-12">
                                    <div class="abt-txt-left">
                                        <div class="abt-txt-head"><?php echo get_string('aboutcourse', 'local_leeloolxpcareers'); ?></div>
                                        <div class="abt-in-txt">
                                            <?php echo $coursesetdata['description']; ?>
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

    <div class="modal fade" id="introModal" tabindex="-1" role="dialog" aria-labelledby="introModal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document" id="yui_3_17_2_1_1667365024663_353">
            <div class="modal-content" id="yui_3_17_2_1_1667365024663_352">
                <div class="modal-header" id="yui_3_17_2_1_1667365024663_351">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="yui_3_17_2_1_1667365024663_350">
                        <span aria-hidden="true" id="yui_3_17_2_1_1667365024663_349">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if ($coursesetdata['video_1']) {
                        if (strpos($coursesetdata['video_1'], 'uploads/temp_files/') !== false) {
                            echo '<video width="640" controls><source src="' . $teamniourl . '/' . $coursesetdata['video_1'] . '" type="video/mp4">Your browser does not support HTML video.</video>';
                        } else {
                            echo '<iframe src="https://player.vimeo.com/video/' . $coursesetdata['video_1'] . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                        }
                    } else if ($coursesetdata['image_big']) {
                        echo '<img src="' . $teamniourl . '/' . $coursesetdata['image_big'] . '" alt="">';
                    } ?>

                </div>
            </div>
        </div>
    </div>


    <div id="goto-top-link">
        <a class="btn btn-light" role="button" href="#" aria-label="Go to top">
            <i class="icon fa fa-arrow-up fa-fw " aria-hidden="true"></i>
        </a>
    </div>


    <div id="stickycontainer"></div>

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
