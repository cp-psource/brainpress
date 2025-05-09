---
layout: psource-theme
title: "BrainPress PHP Functions and Definitions"
---

<h2 align="center" style="color:#38c2bb;">📚 BrainPress PHP Functions and Definitions</h2>

<div class="menu">
  <a href="https://github.com/cp-psource/brainpress/discussions" style="color:#38c2bb;">💬 Forum</a>
  <a href="https://github.com/cp-psource/brainpress/releases" style="color:#38c2bb;">⬇️ Download</a>
  <a href="functions.html" style="color:#38c2bb;">🎨 Dev: Funktionen</a>
  <a href="classes.html" style="color:#38c2bb;">🌐 Dev: Klassen</a>
</div>


***inc/functions/utility.php***
-

####brainpress_alert_message( `string` $content, `string` $type )
Create alert messages.

**Parameters:**
* $content - Content for alert message.
* $type - Alert type. Default: `info`

####brainpress_is_admin()
Check if the current page is one of CP admin pages.

####brainpress_get_enrollment_types()
Returns the list of enrollment types use for enrollment restriction option.

####brainpress_get_categories()
Returns the list of categories CP have.

####brainpress_get_setting( `string` $key, `mixed` $default )
Get CP global setting.

####brainpress_render( `string` $filename, `array` $args, `bool` $echo = true )
Get or include CP file.

**Parameters:**
* $filename - The absolute path location.
* $args - An array of optional arguments to set as variables.
* $echo - Whether to include the file or return as string.

####brainpress_get_template( `string` $name, `string` $slug )
Get brainpress template or load current theme's custom brainpress template.

**Parameters:**
* $name - The key template name. Example: `course`-overview.php
* $slug - The slug portion of the template part. Example: course-`overview`.php

####brainpress_get_array_val( `array` $array, `string` $key, `mixed` $default )
Helper function to get the value of an dimensional array base on key/path.

####brainpress_set_array_val( `array` $array, `string` $key, `mixed` $value)
Helper function to set an array value base on path.

####brainpress_get_option( `string` $key, `mixed` $default )
Helper function to get global option in either single or multi site.

####brainpress_get_url()
Get BrainPress courses main url.

####brainpress_user_have_comments( `int` $user_id, `int` $post_id )
Check if the given user have comments on the given course, unit or step ID.

####brainpress_progress_wheel( `array` $args )
Get HTML progress wheel block.

####brainpress_breadcrumb()
Returns CP breadcrumb HTML block

####brainpress_create_html( `string` $tag, `array` $attributes, `string` $content = '' )
Helper function to generate HTML block.


`inc/functions/user.php`
-

####brainpress_get_user_option( `int` $user_id, `string` $key )
Helper function to get user option.

####brainpress_get_user( `int` $user_id = 0 )
Returns an instance of BrainPress_User object on success of null.

**Parameter:**
* $user_id - Optional. If omitted, will use current user ID.

####brainpress_add_course_instructor( `int` $user_id, `int` $course_id )
Add user as instructor to a course.

####brainpress_delete_course_instructor( `int` $user_id, `int` $course_id )
Remove user as instructor from a course.

####brainpress_get_user_instructed_courses( `int` $user_id )
Returns an array of courses where user is an instructor at.

####brainpress_get_user_instructor_profile_url( `int` $user_id )
Returns user instructor profile link if user is an instructor of any course, otherwise return's false.

####brainpress_add_student( `int` $user_id, `int` $course_id )
Add user as student to a course.

####brainpress_delete_student( `int` $user_id, `int` $course_id )
Remove user as student from a course.

####brainpress_get_enrolled_courses( `int` $user_id )
Returns an array of courses where user is enrolled at.

####brainpress_add_course_facilitator( `int` $user_id, `int` $course_id )
Add user as facilitator to a course.

####brainpress_delete_course_facilitator( `int` $user_id, `int` $course_id )
Remove user as facilitator from the course.

####brainpress_get_user_facilitated_courses( `int` $user_id )
Returns an array of courses where user is a facilitator.

####brainpress_get_accessible_courses( `int` $user_id )
Returns an array of courses where user have access. User must be either an instructor or facilitator of the course.


`inc/functions/course.php`
-

####brainpress_get_course( `int` $course_id = 0 )
Returns an instance of BrainPress_Course object on success or WP_Error.

**Parameter:**
* $course_id - Optional. If omitted, will assume current $post ID.

####brainpress_get_courses( `array` $args )
Returns an array of courses base on the given `$args`. Arguments pattern is similar to `get_posts` arguments.

####brainpress_get_the_course_title( `int` $course_id = 0 )
Helper function to get course or current course title.

####brainpress_get_course_summary( `int` $course_id = 0, `int` $length = 140 )
Returns the course summary.

####brainpress_get_course_description( `int` $course_id = 0 )
Returns the course description.

####brainpress_get_course_media( `int` $course_id, `int` $width = 235, `int` $height = 235 )
Return's course media base on set settings.

####brainpress_get_course_availability_dates( `int` $course_id, `string` $separator = ' - ' )
Returns course start and end date, separated by the given separator.

####brainpress_get_course_enrollment_dates( `int` $course_id, `string` $separator = ' - ' )
Returns the course enrollment start and end date, separated by the given separator.

####brainpress_get_course_enrollment_button( `int` $course_id )
Returns course enrollment button, filtered by course status and current user accessibility.

####brainpress_get_course_instructors_link( `int` $course_id, `string` $separator )
Returns instructors links.

####brainpress_get_course_structure( `int` $course_id, `bool` $show_details = false )
Returns course structure.

####brainpress_get_course_permalink( `int` $course_id )

####brainpress_get_course_submenu( `int` $course_id )

####brainpress_get_course_units_archive_url( `int` $course_id )

####brainpress_get_current_course_cycle()
Returns unit, module, step, or iterated contents base on the current serve course.

####brainpress_get_previous_course_cycle_link( `string` $label = 'Previous' )

####brainpress_get_next_course_cycle_link( `string` $label = 'Next' )


`inc/functions/unit.php`
-

####brainpress_get_unit( `int` $unit_id = 0 )
Returns an instance of BrainPress_Unit on success or WP_Error.

**Parameter:**
* $unit_id - Optional. If omitted, will use the current course unit serve.

####brainpress_get_unit_title( `int` $unit_id )

####brainpress_get_unit_description( `int` $unit_id )

####brainpress_get_unit_structure( `int` $course_id, `int` $unit_id, `bool` $items_only = true, `bool` $show_details = false )