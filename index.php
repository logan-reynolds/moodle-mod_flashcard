<?php

    /** 
    * This page prints a particular instance of a flashcard
    * 
    * @package mod-flashcard
    * @category mod
    * @author Gustav Delius
    * @author Valery Fremaux
    * @author Tomasz Muras
    * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
    */

    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/flashcard/lib.php');

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = $DB->get_record('course', array('id' => $id))) {
        print_error("coursemisconf");
    }
    $context = context_course::instance($course->id);

    require_login($course->id);

    add_to_log($course->id, 'flashcard', 'view all', "index.php?id=$course->id", '');


/// Get all required strings

    $strflashcards = get_string('modulenameplural', 'flashcard');
    $strflashcard  = get_string('modulename', 'flashcard');

/// Print the header

	$PAGE->set_url($CFG->wwwroot.'/mod/flashcard/index.php?id='.$course->id);
	$PAGE->set_context($context);
	$PAGE->set_pagelayout('incourse');
	$PAGE->navbar->add($strflashcards);
	$PAGE->set_heading(format_string($course->fullname));
	$PAGE->set_title(get_string('modulename', 'feedback').' '.get_string('activities'));
	echo $OUTPUT->header();

/// Get all the appropriate data

    if (! $flashcards = get_all_instances_in_course('flashcard', $course)) {
        $OUTPUT->notification(get_string('noflashcards', 'flashcard'), "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic  = get_string('topic');
    
    $table = new html_table();

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname);
        $table->align = array ('center', 'left');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ('center', 'left', 'left', 'left');
    } else {
        $table->head  = array ($strname);
        $table->align = array ('left', 'left', 'left');
    }

    foreach ($flashcards as $flashcard) {
        if (!$flashcard->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id={$flashcard->coursemodule}\">{$flashcard->name}</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id={$flashcard->coursemodule}\">{$flashcard->name}</a>";
        }

        if ($course->format == 'weeks' or $course->format == 'topics') {
            $table->data[] = array ($flashcard->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo '<br/>';

    echo html_writer::table($table);

/// Finish the page

    echo $OUTPUT->footer($course);

?>
