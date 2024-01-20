<?php

class BrainPress_Template_Communication {

	public static function render_notifications() {
		$course_id = BrainPress_Helper_Utility::the_course( true );
		$notifications = BrainPress_Data_Notification::get_notifications( array( $course_id, 'all' ) );

		$content = do_shortcode( '[course_unit_submenu]' );

		if ( empty( $notifications ) ) {
			$content .= sprintf(
				'<p class="message">%s</p>',
				__( 'Dieser Kurs hat keine Benachrichtigungen.', 'brainpress' )
			);
			return $content;
		}

		$content .= '<ul class="notification-archive-list">';
		foreach ( $notifications as $notification ) {
			$content .= '
				<li>';

			$content .= '
					<div class="notification-archive-single-meta">
						<div class="notification-date">
							<span class="month">' . get_the_date( 'M', $notification ) . '</span>
							<span class="day">' . get_the_date( 'd', $notification ) . '</span>
							<span class="year">' . get_the_date( 'Y', $notification ) . '</span>
						</div>
						<div class="notification-time">
							' . get_the_time( 'h:ia', $notification ) . '
						</div>
					</div>
			';

			$author = sprintf( __( 'von <span>%s</span>', 'brainpress' ), BrainPress_Helper_Utility::get_user_name( $notification->post_author ) );
			// $author = get_user_option( 'display_name', $notification->post_author );
			$content .= '
					<div class="notification-archive-single">
						<h3 class="notification-title">' . esc_html( $notification->post_title ) . '</h3>
						<div class="notification_author">' . $author . '</div>
						<div class="notification-content">
							' . BrainPress_Helper_Utility::filter_content( $notification->post_content ) . '
						</div>
					</div>
			';

			$content .= '
				</li>';

		}
		$content .= '</ul>';

		return str_replace( array( "\n", "\r" ), '', $content );

	}

	public static function render_discussions() {

		$course = BrainPress_Helper_Utility::the_course( false );
		$course_id = $course->ID;

		$discussion_is_allowed = BrainPress_Helper_Utility::checked( BrainPress_Data_Course::get_setting( $course_id, 'allow_discussion', false ) );

		if ( false == $discussion_is_allowed ) {
			$content = sprintf( '<p class="message">%s</p>', __( 'Für diesen Kurs sind keine Diskussionen verfügbar.', 'brainpress' ) );
			return $content;
		}
		$discussions = BrainPress_Data_Discussion::get_discussions( array( $course_id, 'all' ) );
		$count = BrainPress_Data_Discussion::get_discussions_count( $course_id );
		$number = BrainPress_Data_Discussion::get_number_of_discussions();

		$content = do_shortcode( '[course_unit_submenu]' );

		$slug_new = BrainPress_Core::get_setting( 'slugs/discussions_new', 'add_new_discussion' );

		$new_discussion_link = BrainPress_Core::get_slug( 'course/', true ) . $course->post_name . '/' . BrainPress_Core::get_slug( 'discussions/' ) . $slug_new;
		$content .= '
			<div class="discussion-new">
				<a href="' . esc_url( $new_discussion_link ) . '" class="button">' . esc_html__( 'Starte eine neue Diskussion', 'brainpress' ) . '</a>
			</div>
		';
		if ( 0 === $count ) {
			$content .= sprintf(
				'<p class="message">%s</p>',
				__( 'Dieser Kurs hat keine Diskussionen.', 'brainpress' )
			);
			return $content;
		}

		if ( empty( $discussions ) ) {
			$content .= sprintf(
				'<p class="message">%s</p>',
				__( 'Ups, etwas ist schief gelaufen.', 'brainpress' )
			);
			return $content;
		}

		$content .= '<ul class="discussion-archive-list">';
		foreach ( $discussions as $discussion ) {
			$content .= '
				<li>
				';

			$comments_count = wp_count_comments( $discussion->ID );

			$content .= '
					<div class="discussion-archive-single-meta">
						<div class="discussion-comment"><div class="comment">
						' . $comments_count->approved . '
						</div></div>
					</div>
			';

			$author = BrainPress_Helper_Utility::get_user_name( $discussion->post_author, false, false );
			$attributes = BrainPress_Data_Discussion::attributes( $discussion->ID );

			if ( 'course' == $attributes['unit_id'] ) {
				$applies_to = get_post_field( 'post_title', $course_id );
			} else {
				$applies_to = get_post_field( 'post_title', $attributes['unit_id'] );
			}

			$date = get_the_date( get_option( 'date_format' ), $discussion );

			$discussion_url = BrainPress_Core::get_slug( 'courses/', true ) . $course->post_name . '/';
			$discussion_url = $discussion_url . BrainPress_Core::get_slug( 'discussion/' ) . $discussion->post_name;

			$content .= '
					<div class="discussion-archive-single">
						<h3 class="discussion-title"><a href="' . esc_url_raw( $discussion_url ) . '">' . esc_html( $discussion->post_title ) . '</a></h3>
						<div class="discussion-content">
							' . BrainPress_Helper_Utility::truncate_html( BrainPress_Helper_Utility::filter_content( $discussion->post_content ), 100 ) . '
						</div>
						<hr />
						<div class="meta">' . esc_html( $author ) . ' | ' . esc_html( $date ) . ' | ' . esc_html__( 'Gilt für:', 'brainpress' ) . ' ' . $applies_to . '</div>
					</div>
			';

			$content .= '
				</li>';
		}
		$content .= '</ul>';
		global $wp;
		$current = intval( isset( $wp->query_vars['paged'] )? $wp->query_vars['paged']:0 );
		if ( $count > $number ) {
			$url = BrainPress_Data_Course::get_course_url( $course_id ).BrainPress_Core::get_slug( 'discussion' );
			$i = 0;
			$content .= '<ul class="cp-pager">';
			for ( $i = 0; $i < $count; ) {
				$n = 1 + $i / $number;
				$content .= sprintf(
					'<li class="%s"><a href="%s%s">%d</a></li>',
					esc_attr( $current === $n ? 'current':'' ),
					esc_url( $url ),
					esc_url( 0 < $i? '/page/'.$n:'' ),
					$n
				);
				$i += $number;
			}
			$content .= '</ul>';
		}
		return str_replace( array( "\n" ), '', $content );
	}

	public static function render_discussion() {
		global $wp, $post;

		$course_id = BrainPress_Helper_Utility::the_course( true );
		$post_name = $wp->query_vars['discussion_name'];
		$discussion = get_page_by_path( $post_name, OBJECT, BrainPress_Data_Discussion::get_post_type_name() );
		if ( empty( $discussion ) && isset( $wp->query_vars['type'] ) && isset( $wp->query_vars['item'] ) ) {
			$discussion = get_post( (int) $wp->query_vars['item'] );
		}

		$author = false;
		if ( ! empty( $discussion ) ) {
			$discussion->comment_status = 'open';
			wp_update_post( $discussion );

			$title = $discussion->post_title;
			$post_content = $discussion->post_content;
			$author = $discussion->post_author;
		} else {
			$title = __( 'Unbekannte Diskussion', 'brainpress' );
			$post_content = __( 'Die gesuchte Diskussion konnte nicht gefunden werden.', 'brainpress' );
		}

		$content = do_shortcode( '[course_unit_submenu]' );

		$content .= '<div class="course-discussion-wrapper">';
		$content .= '<div class="course-discussion-page course-discussion-content">';
		$content .= sprintf(
			'<h3 class="title course-discussion-title">%s%s%s</h3>',
			esc_html__( 'Diskussion', 'brainpress' ),
			esc_html_x( ': ', 'separator between "Discussion" text and title', 'brainpress' ),
			esc_html( $title )
		);
		$content .= BrainPress_Helper_Utility::filter_content( $post_content );
		if ( get_current_user_id() == (int) $author ) {
			$edit_discussion_link = BrainPress_Core::get_slug( 'course/', true ) . get_post_field( 'post_name', $course_id ) . '/' . BrainPress_Core::get_slug( 'discussions/' ) . BrainPress_Core::get_slug( 'discussion_new' );
			$edit_discussion_link .= '?id=' . $discussion->ID;
			$content .= '<div class="edit-link">';
			$content .= sprintf( '<a class="button" href="%s">%s</a>', esc_url( $edit_discussion_link ), esc_html__( 'Bearbeiten', 'brainpress' ) );
			$content .= '</div>';
		}
		$content .= '</div>';

		if ( ! empty( $discussion ) ) {
			setup_postdata( $discussion );
			ob_start();
			comments_template();
			$content .= ob_get_clean();
			wp_reset_postdata();
		}

		$content .= '</div>';

		return $content;
	}

	public static function render_new_discussion() {
		$course_id = BrainPress_Helper_Utility::the_course( true );

		$content = do_shortcode( '[course_unit_submenu]' );

		$content .= '<div class="course-discussion-wrapper">';
		$content .= '<div class="course-discussion-page new course-discussion-content">';
		$content .= '<h3 class="title course-discussion-title">' . esc_html__( 'Neue Diskussion', 'brainpress' ) . '</h3>';

		$title = '';
		$body = '';
		$course_section = 'course';

		// Are we editing?
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
		$edit = ! empty( $id );
		if ( $edit ) {
			$post = get_post( $id );
			$title = $post->post_title;
			$body = $post->post_content;
			$course_section = get_post_meta( $id, 'unit_id', true );
		}

		// Form.
		$content .= '
		<form method="POST">';

		// Course Area
		$options_unit = array();
		$options_unit['value'] = $course_section;
		$options_unit['first_option'] = array(
			'text' => sprintf( '%s: %s', __( 'Kurs', 'brainpress' ), get_post_field( 'post_title', $course_id ) ),
			'value' => 'course',
		);
		$content .= '<div class="discussion-section">
				<label><span>' .
				esc_html__( 'In dieser Diskussion geht es um ', 'brainpress' ) .
				BrainPress_Helper_UI::get_unit_dropdown( 'unitID', 'unit_id', $course_id, false, $options_unit ) .
				'</span></label>
			</div>
		';

		// Input area.
		$content .= wp_nonce_field( 'add-new-discussion', '_wpnonce', true, false );
		$cancel_link = BrainPress_Core::get_slug( 'course/', true ) . get_post_field( 'post_name', $course_id ) . '/' . BrainPress_Core::get_slug( 'discussions' );
		if ( $edit ) {
			$content .= '<input type="hidden" name="id" value="' . $id . '" />';
		}

		$add_edit = $edit ? esc_html__( 'Diskussion aktualisieren', 'brainpress' ) : esc_html__( 'Diskussion hinzufügen', 'brainpress' );

		$content .= '<input type="hidden" value="add_new_discussion" name="action" /><input type="hidden" value="' . esc_attr( $course_id ) . '" name="course_id" />
			<input name="discussion_title" type="text" placeholder="' . esc_attr__( 'Titel der Diskussion', 'brainpress' ) . '" value="' . esc_attr( $title ) . '" />
			<textarea name="discussion_content" placeholder="' . esc_attr__( 'Gib hier Deine Diskussion oder Frage ein…', 'brainpress' ) . '">' . BrainPress_Helper_Utility::filter_content( $body ) . '</textarea>
			<div class="button-links">
				<a href="' . esc_url( $cancel_link ) . '" class="button">' . esc_html__( 'Abbrechen', 'brainpress' ) . '</a>
				<button type="submit" class="button submit-discussion">' . esc_html( $add_edit ) . '</button>
			</div>
		</form>
		';

		$content .= '</div>';
		$content .= '</div>';

		wpautop( $content, false );

		return str_replace( "\n", '', $content );
	}
}
