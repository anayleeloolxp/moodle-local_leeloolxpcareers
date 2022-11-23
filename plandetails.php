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

$backurl = new moodle_url(
    '/local/leeloolxpcareers/' . $response['data']['parenttype'] . '.php',
    ['id' => $response['data']['parentid']]
);

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
if ($response['data']['lpdata']['image_big']) {
    echo '<style>.carrer-main-banner {
        background-size: 100% 100%;
        background-image: url(' . $teamniourl . '/' . $response['data']['lpdata']['image_big'] . ');
    }</style>';
}

$plancoursematricular = array();
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
                            } ?>

                        </div>
                        <div class="carrer-right-cont">
                            <div class="carrer-head"><?php echo $lpdata['name']; ?></div>
                            <div class="carrer-cont">
                                <div class="carrer-cont-top">
                                    <?php echo $lpdata['summary']; ?>
                                </div>
                                <div class="carrer-cont-btm">
                                    <div class="ahora-btn"><a class="Matricularplan btn" href="#" data-toggle="modal" data-target="#myModal_enrolplan">Matricular ahora</a></div>
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
                    <div class="row">
                        <div class="col-md-2">
                            <div class="search-bar-back">
                                <a class="btn" href="<?php echo $backurl; ?>"><img src="https://vonkelemen.org/online/local/leeloolxpcareers/assets/img/Arrowback.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="filter-select">
                                        <div class="form-group">
                                            <select class="form-control" id="sel1">
                                                <option value="">Nivel educativo</option>
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
                                                <option value="">Carrera</option>
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
                                                <option value="">Grado académico</option>
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
                </div>
            </div>

            <div class="carrer-details">
                <div class="container">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="carrer-details-left">
                                <h4><?php echo $lpdata['name']; ?></h4>
                                <p><small><?php echo $lpdata['code']; ?></small></p>
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
                                    } ?>
                                </div>
                                <div class="carrer-btm-cont">
                                    <div class="carrer-cont">
                                        <div class="carrer-cont-top">
                                            <?php echo $lpdata['summary']; ?>
                                        </div>
                                        <div class="carrer-cont-btm">
                                            <div class="ahora-btn"><a class="Matricularplan btn" href="#" data-toggle="modal" data-target="#myModal_enrolplan">Matricular ahora</a></div>
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
                                        echo '<li class="nav-item active"><a class="nav-link" id="myTab' . $key . '-tab" data-toggle="tab" href="#myTab' . $key . '" role="tab" aria-controls="myTab' . $key . '" aria-selected="true">' . $sibling['shortname'] . '</a></li>';
                                    } else {
                                        $lpurl = new moodle_url(
                                            '/local/leeloolxpcareers/plandetails.php',
                                            ['id' => $sibling['id']]
                                        );
                                        echo '<li class="nav-item"><a href="' . $lpurl . '" class="nav-link" id="myTab' . $key . '-tab">' . $sibling['shortname'] . '</a></li>';
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
                                                                                    <?php
                                                                                    if ($instance['type'] == 'courses') {
                                                                                        $selfenrol = $DB->get_record('enrol', array(
                                                                                            'courseid' => $instance['instanceid'],
                                                                                            'enrol' => 'self',
                                                                                            'status' => '0'
                                                                                        ));

                                                                                        $coursecontext = context_course::instance($instance['instanceid']);

                                                                                        if ($selfenrol && !is_enrolled($coursecontext, $USER->id)) {
                                                                                            $plancoursematricular[] = $instance['instanceid'];
                                                                                            echo $matrihtml = '<a class="myModal_enrol_link enrollicon btn btn-light" data-toggle="modal" data-target="#myModal_enrol' . $instance['instanceid'] . '">Matricular Gratis</a>

                                                                                        <div id="myModal_enrol' . $instance['instanceid'] . '" class="modal fade myModal_enrol" role="dialog">
                                                                                                <div class="modal-dialog">

                                                                                                    <!-- Modal content-->
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header">
                                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                                            <h4 class="modal-title">' . $instance['instancename'] . '</h4>
                                                                                                        </div>
                                                                                                        <div class="modal-body">
                                                                                                        <form autocomplete="off" action="' . $CFG->wwwroot . '/enrol/index.php" method="post" accept-charset="utf-8" class="mform" data-boost-form-errors-enhanced="1">
                                                                                                            <div style="display: none;">
                                                                                                            <input name="id" type="hidden" value="' . $instance['instanceid'] . '">
                                                                                                            <input name="instance" type="hidden" value="' . $selfenrol->id . '">
                                                                                                            <input name="sesskey" type="hidden" value="' . sessKey() . '">
                                                                                                            <input type="password" class="form-control " name="enrolpassword" id="enrolpassword_' . $selfenrol->id . '" value="' . $selfenrol->password . '" autocomplete="off">
                                                                                                            <input name="_qf__' . $selfenrol->id . '_enrol_self_enrol_form" type="hidden" value="1">
                                                                                                            <input name="mform_isexpanded_id_selfheader" type="hidden" value="1">
                                                                                                            </div>
                                                                                                            <input type="submit" class=" btn-primary" name="submitbutton" id="id_submitbutton" value="Matricularme">
                                                                                                        </form>
                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>';
                                                                                        }
                                                                                    } else {
                                                                                        $coursesetcoursematricular = array();

                                                                                        foreach ($instance['subcourses'] as $inscourse) {
                                                                                            $selfenrol = $DB->get_record('enrol', array(
                                                                                                'courseid' => $inscourse['courseid'],
                                                                                                'enrol' => 'self',
                                                                                                'status' => '0'
                                                                                            ));
                                                                                            $coursecontext = context_course::instance($inscourse['courseid']);

                                                                                            if ($selfenrol && !is_enrolled($coursecontext, $USER->id)) {
                                                                                                $coursesetcoursematricular[] = $inscourse['courseid'];
                                                                                                $plancoursematricular[] = $inscourse['courseid'];
                                                                                            }
                                                                                        }

                                                                                        if ($coursesetcoursematricular) {
                                                                                            $inputcourses = '';
                                                                                            foreach ($coursesetcoursematricular as $couretoenrol) {
                                                                                                $inputcourses .= '<input name="courestoenrol[]" type="hidden" value="' . $couretoenrol . '">';
                                                                                            }

                                                                                            echo $matrihtml = '<a class="myModal_enrol_link enrollicon btn btn-light" data-toggle="modal" data-target="#myModal_enrolset' . $instance['instanceid'] . '">Matricular Gratis</a>

                                                                                            <div id="myModal_enrolset' . $instance['instanceid'] . '" class="modal fade myModal_enrol" role="dialog">
                                                                                            <div class="modal-dialog">

                                                                                                <!-- Modal content-->
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                                        <h4 class="modal-title">' . $instance['instancename'] . '</h4>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                    <form autocomplete="off" action="' . $CFG->wwwroot . '/local/leeloolxpcareers/enrollsets.php" method="post" accept-charset="utf-8" class="mform" data-boost-form-errors-enhanced="1">
                                                                                                        <div style="display: none;">
                                                                                                            <input name="type" type="hidden" value="plandetails">
                                                                                                            <input name="instance" type="hidden" value="' . $reqlpid . '">
                                                                                                            <input name="sesskey" type="hidden" value="' . sessKey() . '">
                                                                                                            ' . $inputcourses . '

                                                                                                        </div>
                                                                                                        <input type="submit" class=" btn-primary" name="submitbutton" id="id_submitbutton" value="Matricularme">
                                                                                                    </form>
                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>
                                                                                        </div>';
                                                                                        }
                                                                                    } ?>
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
if ($plancoursematricular) {
    $inputcourses = '';
    foreach ($plancoursematricular as $couretoenrol) {
        $inputcourses .= '<input name="courestoenrol[]" type="hidden" value="' . $couretoenrol . '">';
    }

    echo $planmatrihtml = '

    <div id="myModal_enrolplan" class="modal fade myModal_enrol" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">' . $lpdata['name'] . '</h4>
            </div>
            <div class="modal-body">
            <form autocomplete="off" action="' . $CFG->wwwroot . '/local/leeloolxpcareers/enrollsets.php" method="post" accept-charset="utf-8" class="mform" data-boost-form-errors-enhanced="1">
                <div style="display: none;">
                    <input name="type" type="hidden" value="plandetails">
                    <input name="instance" type="hidden" value="' . $reqlpid . '">
                    <input name="sesskey" type="hidden" value="' . sessKey() . '">
                    ' . $inputcourses . '

                </div>
                <input type="submit" class=" btn-primary" name="submitbutton" id="id_submitbutton" value="Matricularme">
            </form>
            </div>

        </div>

    </div>
</div>';
} else {
    echo '<style>.Matricularplan{ display:none; }</style>';
}
?>

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
