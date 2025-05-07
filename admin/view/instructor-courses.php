<?php
$instructor_id = (int) $_REQUEST['instructor_id'];
$instructor = get_userdata( $instructor_id );
$instructor_name = BrainPress_Helper_Utility::get_user_name( $instructor_id, true );
$assigned_courses = BrainPress_Data_Instructor::get_assigned_courses_ids( $instructor_id );
$assigned_courses = array_filter( $assigned_courses );
$assigned_courses = array_map( 'get_post', $assigned_courses );
?>
<div class="wrap brainpress_wrapper brainpress-intructor-courses">
	<table>
		<tr>
			<td><?php echo get_avatar( $instructor->user_email, 102 ); ?></td>
			<td valign="top">
				<h2><?php echo $instructor_name; ?></h2>
			</td>
		</tr>
	</table>
	<hr />
	<?php if ( ! empty( $assigned_courses ) ) : ?>
	<table class="wp-list-table widefat stripe">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Kurs', 'brainpress' ); ?></th>
				<th><?php esc_html_e( 'Kursstatus', 'brainpress' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $assigned_courses as $course ) :
				$permalink = BrainPress_Data_Course::get_course_url( $course->ID );
			?>
			<tr>
				<td><a href="<?php echo esc_url( $permalink ); ?>" target="_blank"><?php echo $course->post_title; ?></a></td>
				<td><?php
				$status = get_post_status_object( $course->post_status );
				echo $status->label;
?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
	<br />
	<p class="description"><?php esc_html_e( 'Keine Kurse gefunden!', 'brainpress' ); ?></p>
	<?php endif; ?>
</div>
