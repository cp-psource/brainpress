<?php

class BrainPress_View_Admin_Setting_General {

	public static function init() {

		add_filter( 'brainpress_settings_tabs', array( __CLASS__, 'add_tabs' ) );
		add_action( 'brainpress_settings_process_general', array( __CLASS__, 'process_form' ), 10, 2 );
		add_filter( 'brainpress_settings_render_tab_general', array( __CLASS__, 'return_content' ), 10, 3 );
	}


	public static function add_tabs( $tabs ) {

		$tabs['general'] = array(
			'title' => __( 'Allgemeine Einstellungen', 'brainpress' ),
			'description' => __( 'Konfiguriere die allgemeinen Einstellungen für BrainPress.', 'brainpress' ),
			'order' => 0,// first tab
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$my_course_prefix = __( 'mein-kurs', 'brainpress' );
		$my_course_prefix = sanitize_text_field( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) . '/'. $my_course_prefix;
		$page_dropdowns = array();

		$pages_args = array(
			'selected' => BrainPress_Core::get_setting( 'pages/enrollment', 0 ),
			'echo' => 0,
			'show_option_none' => __( 'Virtuelle Seite verwenden', 'brainpress' ),
			'option_none_value' => 0,
			'name' => 'brainpress_settings[pages][enrollment]',
		);
		$page_dropdowns['enrollment'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/login', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][login]';
		$page_dropdowns['login'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/signup', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][signup]';
		$page_dropdowns['signup'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/student_dashboard', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][student_dashboard]';
		$page_dropdowns['student_dashboard'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/student_settings', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][student_settings]';
		$page_dropdowns['student_settings'] = wp_dropdown_pages( $pages_args );

		$content = '
			<input type="hidden" name="page" value="' . esc_attr( $slug ) . '"/>
			<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '"/>
			<input type="hidden" name="action" value="updateoptions"/>
		' . wp_nonce_field( 'update-brainpress-options', '_wpnonce', true, false ) . '
				<!-- SLUGS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Slugs', 'brainpress' ) . '</span></h3>
				<p class="description">' . sprintf( __( 'Ein Slug ist ein paar Wörter, die einen Beitrag oder eine Seite beschreiben. Slugs sind normalerweise eine URL-freundliche Version des Post-Titels (der automatisch von WordPress generiert wurde), aber ein Slug kann beliebig sein. Slugs sollen mit% s verwendet werden, da sie den Inhalt der URL beschreiben. Post Slug ersetzt den Platzhalter %s in einer benutzerdefinierten Permalink-Struktur.', 'brainpress' ), '<a href="options-permalink.php">permalinks</a>', '<strong>"%posttitle%"</strong>' ) . '</p>
				<div class="inside">

					<table class="form-table slug-settings">
						<tbody>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kurse Slug', 'brainpress' ) . '</th>
								<td>' . esc_html( trailingslashit( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][course]" id="course_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) . '" />&nbsp;/
									<p class="description">' . esc_html__( 'Deine Kurs-URL sieht so aus: ', 'brainpress' ) . esc_html( trailingslashit( home_url() ) ) . esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) . esc_html__( '/mein-kurs/', 'brainpress' ) . '</p>
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kurskategorie Slug', 'brainpress' ) . '</th>
								<td>' . esc_html( trailingslashit( home_url() ) . trailingslashit( esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][category]" id="category_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/category', 'course_category' ) ) . '" />&nbsp;/
									<p class="description">' . esc_html__( 'Die URL Ihrer Kurskategorie sieht folgendermaßen aus: ', 'brainpress' ) . trailingslashit( esc_url( home_url() ) ) . esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) . '/' . BrainPress_Core::get_setting( 'slugs/category', 'course_category' ) ) . esc_html__( '/deine-kategorie/', 'brainpress' ) . '</p>
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Einheiten Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][units]" id="units_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/units', 'units' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kursbenachrichtigungen Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][notifications]" id="notifications_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/notifications', 'notifications' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kursdiskussionen Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][discussions]" id="discussions_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/discussions', 'discussion' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Kurs Neue Diskussion Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . trailingslashit( esc_attr( BrainPress_Core::get_setting( 'slugs/discussions', 'discussion' ) ) ) .'
									&nbsp;<input type="text" name="brainpress_settings[slugs][discussions_new]" id="discussions_new_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/discussions_new', 'add_new_discussion' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kurs Bewertungen Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][grades]" id="grades_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/grades', 'grades' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kurs Arbeitsmappe Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][workbook]" id="workbook_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/workbook', 'workbook' ) ) . '" />&nbsp;/
								</td>
							</tr>

							<tr class="hidden" valign="top" class="break">
								<th scope="row">' . esc_html__( 'Einschreibungsprozess Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][enrollment]" id="enrollment_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/enrollment', 'enrollment_process' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr class="hidden" valign="top">
								<th scope="row">' . esc_html__( 'Einschreibungsprozess Seite', 'brainpress' ) . '</th>
								<td>' .  self::add_page_dropdown_description( $page_dropdowns['enrollment'], 'enrollment_process' ).  '</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Login Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][login]" id="login_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/login', 'student-login' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Loginseite', 'brainpress' ) . '</th>
								<td>' .  self::add_page_dropdown_description( $page_dropdowns['login'], 'student_login' ). '</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Anmelde Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][signup]" id="signup_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/signup', 'courses-signup' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Anmeldeseite', 'brainpress' ) . '</th>
								<td>' .  self::add_page_dropdown_description( $page_dropdowns['signup'], 'student_signup' ).  '</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Student Dashboard Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][student_dashboard]" id="student_dashboard_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/student_dashboard', 'courses-dashboard' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Student Dashboard Seite', 'brainpress' ) . '</th>
								<td>' .  self::add_page_dropdown_description( $page_dropdowns['student_dashboard'], 'student_dashboard' ). '</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Student Einstellungen Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][student_settings]" id="student_settings_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/student_settings', 'student-settings' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Student Einstellungsseite', 'brainpress' ) . '</th>
								<td>' .  self::add_page_dropdown_description( $page_dropdowns['student_settings'], 'student_settings' ). '</td>
							</tr>

							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Kursleiter Profil Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][instructor_profile]" id="instructor_profile_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/instructor_profile', 'instructor' ) ) . '" />&nbsp;/
								</td>
							</tr>';

		if ( function_exists( 'messaging_init' ) ) {

			$content .= '
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Messaging: Posteingang Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][inbox]" id="inbox_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/inbox', 'student-inbox' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Gesendete Nachrichten Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][sent_messages]" id="sent_messages" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/sent_messages', 'student-sent-messages' ) ) . '" />&nbsp;/
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">' . esc_html__( 'Neue Nachrichten Slug', 'brainpress' ) . '</th>
								<td>' . trailingslashit( esc_url( home_url() ) ) . '
									&nbsp;<input type="text" name="brainpress_settings[slugs][new_messages]" id="new_messages_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/new_messages', 'student-new-message' ) ) . '" />&nbsp;/
								</td>
							</tr>
			';

		}

		$content .= '
						</tbody>
					</table>


				</div>

				<!-- THEME MENU ITEMS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Themenmenüelemente', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Menüelemente anzeigen', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '<div>Füge dem <strong>Hauptmenü</strong> Standardmenüelemente von BrainPress (Kurse, Studenten-Dashboard, Abmelden) hinzu.</div><div>Elemente können auch über Darstellung> Menüs und das BrainPress-Bedienfeld hinzugefügt werden.</div>', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = cp_is_true( BrainPress_Core::get_setting( 'general/show_brainpress_menu', 1 ) ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="brainpress_settings[general][show_brainpress_menu]" ' . $checked . ' />
									';

		if ( current_user_can( 'manage_options' ) ) {
			$menu_error = true;
			$locations = get_theme_mod( 'nav_menu_locations' );
			if ( is_array( $locations ) ) {
				foreach ( $locations as $location => $value ) {
					if ( $value > 0 ) {
						$menu_error = false; // at least one is defined
					}
				}
			}
			if ( $menu_error ) {

				$content .= '
									<span class="settings-error">
									' . __( 'Bitte füge mindestens ein Menü hinzu und wähle  dessen Themeposition aus, um die Menüpunkte von BrainPress automatisch anzuzeigen.', 'brainpress' ) . '
									</span>
				';

			}
		}

		$content .= '
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<!-- LOGIN FORM -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Login Formular', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Verwende das benutzerdefinierte Anmeldeformular', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Verwendet ein benutzerdefiniertes Anmeldeformular, um die Studenten am Frontend Deiner Website zu halten.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = cp_is_true( BrainPress_Core::get_setting( 'general/use_custom_login', 1 ) ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="brainpress_settings[general][use_custom_login]" ' . $checked . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- WP LOGING REDIRECTION -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'WordPress Login Umleitung', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Nach dem Login umleiten', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Leite die Schüler beim Anmelden über das WP-Anmeldeformular zu ihrem Dashboard weiter.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = cp_is_true( BrainPress_Core::get_setting( 'general/redirect_after_login', 1 ) ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="brainpress_settings[general][redirect_after_login]" ' . $checked . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- PRIVACY -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Privatsphäre', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Kursleiter-Benutzername in URL anzeigen', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Wenn diese Option aktiviert ist, wird der Benutzername des Kursleiters in der URL angezeigt. Andernfalls wird die Hash-Version (MD5) angezeigt.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$checked = cp_is_true( BrainPress_Core::get_setting( 'instructor/show_username', 1 ) ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="brainpress_settings[instructor][show_username]" ' . $checked . ' />
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE DETAILS PAGE -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Kursdetailseite', 'brainpress' ) . '</span></h3>
				<p class="description">' . __( 'Medien, die beim Anzeigen von Kursdetails verwendet werden sollen.', 'brainpress' ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Medientyp', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '"Priorität" - Verwende den folgenden Medientyp, den anderen als Fallback.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_type = BrainPress_Core::get_setting( 'course/details_media_type', 'default' );
		$content .= '
									<select name="brainpress_settings[course][details_media_type]" class="widefat" id="course_details_media_type">
										<option value="default" ' . selected( $selected_type, 'default', false ) .'>' . __( 'Prioritätsmodus (Standard)', 'brainpress' ) . '</option>
										<option value="video" ' . selected( $selected_type, 'video', false ) .'>' . __( 'Ausgewähltes Video', 'brainpress' ) . '</option>
										<option value="image" ' . selected( $selected_type, 'image', false ) .'>' . __( 'Listenbild', 'brainpress' ) . '</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Priorität', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Beispiel: Bei Verwendung von "Video" wird das vorgestellte Video verwendet, sofern verfügbar. Das Auflistungsbild ist ein Fallback.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_priority = BrainPress_Core::get_setting( 'course/details_media_priority', 'default' );
		$content .= '
									<select name="brainpress_settings[course][details_media_priority]" class="widefat" id="course_details_media_priority">
										<option value="video" ' . selected( $selected_priority, 'video', false ) .'>' . __( 'Ausgewähltes Video (Bildrückfall)', 'brainpress' ) . '</option>
										<option value="image" ' . selected( $selected_priority, 'image', false ) .'>' . __( 'Listenbild (Video-Fallback)', 'brainpress' ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE LISTINGS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Kurslisten', 'brainpress' ) . '</span></h3>
				<p class="description">' . __( 'Medien zum Anzeigen von Kurslisten (z. B. Kursseite oder Kursleiterseite).', 'brainpress' ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Medientyp', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '"Priorität" - Verwende den folgenden Medientyp, den anderen als Fallback.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_type = BrainPress_Core::get_setting( 'course/listing_media_type', 'default' );
		$content .= '
									<select name="brainpress_settings[course][listing_media_type]" class="widefat" id="course_listing_media_type">
										<option value="default" ' . selected( $selected_type, 'default', false ) .'>' . __( 'Prioritätsmodus (Standard)', 'brainpress' ) . '</option>
										<option value="video" ' . selected( $selected_type, 'video', false ) .'>' . __( 'Ausgewähltes Video', 'brainpress' ) . '</option>
										<option value="image" ' . selected( $selected_type, 'image', false ) .'>' . __( 'Listenbild', 'brainpress' ) . '</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Priorität', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( 'Beispiel: Bei Verwendung von "Video" wird das vorgestellte Video verwendet, sofern verfügbar. Das Auflistungsbild ist ein Fallback.', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';

		$selected_priority = BrainPress_Core::get_setting( 'course/listing_media_priority', 'default' );
		$content .= '
									<select name="brainpress_settings[course][listing_media_priority]" class="widefat" id="course_listing_media_priority">
										<option value="video" ' . selected( $selected_priority, 'video', false ) .'>' . __( 'Ausgewähltes Video (Bildrückfall)', 'brainpress' ) . '</option>
										<option value="image" ' . selected( $selected_priority, 'image', false ) .'>' . __( 'Listenbild (Video-Fallback)', 'brainpress' ) . '</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<!-- COURSE IMAGES -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Kursbilder', 'brainpress' ) . '</span></h3>
				<p class="description">' . __( 'Größe für (neu hochgeladene) Kursbilder.', 'brainpress' ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Bildbreite', 'brainpress' ) . '
								</th>
								<td>
									<input type="text" name="brainpress_settings[course][image_width]" value="' . esc_attr( BrainPress_Core::get_setting( 'course/image_width', 235 ) ) . '"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Bildhöhe', 'brainpress' ) . '
								</th>
								<td>
									<input type="text" name="brainpress_settings[course][image_height]" value="' . esc_attr( BrainPress_Core::get_setting( 'course/image_height', 225 ) ) . '"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- COURSE ORDER -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Kurssortierung', 'brainpress' ) . '</span></h3>
				<p class="description">' . __( 'Reihenfolge der Kurse in Admin und Frontend.', 'brainpress' ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Sortieren nach', 'brainpress' ) . '
								</th>
								<td>';

		$selected_order = BrainPress_Core::get_setting( 'course/order_by', 'course_start_date' );

		$options = array(
			'post_date' => __( 'Nach Erstelldatum', 'brainpress' ),
			'start_date' => __( 'Kursbeginn', 'brainpress' ),
			'enrollment_start_date' => __( 'Startdatum der Kurseinschreibung', 'brainpress' ),
		);
		$content .= BrainPress_Helper_UI::select(
			'brainpress_settings[course][order_by]',
			$options,
			$selected_order,
			'widefat',
			'course_order_by'
		);

		$content .= '</td></tr>';
		$content .= '<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Ausrichtung', 'brainpress' ) . '
								</th>
								<td>';

		$selected_dir = BrainPress_Core::get_setting( 'course/order_by_direction', 'DESC' );
		$content .= '
									<select name="brainpress_settings[course][order_by_direction]" class="widefat" id="course_order_by_direction">
										<option value="DESC" ' . selected( $selected_dir, 'DESC', false ) .'>' . __( 'Absteigend', 'brainpress' ) . '</option>
										<option value="ASC" ' . selected( $selected_dir, 'ASC', false ) .'>' . __( 'Aufsteigend', 'brainpress' ) . '</option>
									</select>
								</td>
							</tr>

<!-- Default course Enrollment Restrictions -->
							<tr valign="top" class="break">
								<th scope="row">' . esc_html__( 'Einschreibungsbeschränkungen', 'brainpress' ) . '</th>
								<td>';
		$enrollment_types = BrainPress_Data_Course::get_enrollment_types_array();
		$enrollment_type_default = BrainPress_Data_Course::get_enrollment_type_default();
		$selected = BrainPress_Core::get_setting( 'course/enrollment_type_default', $enrollment_type_default );
		$content .= BrainPress_Helper_UI::select( 'brainpress_settings[course][enrollment_type_default]', $enrollment_types, $selected, 'chosen-select medium' );
		$content .= sprintf( '<p class="description">%s</p>', __( 'Wähle die Standardeinschränkungen für den Zugriff auf und die Anmeldung zu diesem Kurs aus.', 'brainpress' ) );
		$content .= '
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- REPORTS -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Berichte', 'brainpress' ) . '</span></h3>
				<p class="description">' . __( 'Wähle die Schriftart aus, die in den PDF-Berichten verwendet werden soll.', 'brainpress' ) . '</p>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Schriftart', 'brainpress' ) . '
								</th>
								<td>';

		$reports_font = BrainPress_Core::get_setting( 'reports/font', 'helvetica' );
		$reports_font = empty( $reports_font ) ? 'helvetica' : $reports_font;
		$fonts = BrainPress_Helper_PDF::fonts();
		$content .= '
									<select name="brainpress_settings[reports][font]" class="widefat" id="course_order_by_direction">
					';

		foreach ( $fonts as $font_php => $font_name ) {
			if ( ! empty( $font_name ) ) {
				$font = str_replace( '.php', '', $font_php );
				$content .= '
										<option value="' . esc_attr( $font ) . '" ' . selected( $reports_font, $font, false ) . '>' . esc_html( $font_name ) . '</option>
				';
			}
		}
		$content .= '
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>';

		$content .= '<!-- schema.org -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'schema.org', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Mikrodatensyntax hinzufügen', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td>';
		$checked = cp_is_true( BrainPress_Core::get_setting( 'general/add_structure_data', 1 ) ) ? 'checked' : '';
		$content .= '
									<input type="checkbox" name="brainpress_settings[general][add_structure_data]" ' . $checked . ' />
				<p class="description">' . esc_html__( 'Hinzufügen von Strukturdaten zu Kursen.', 'brainpress' ) . '</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>';

		/**
		 * Social Sharing
		 */
		$services = BrainPress_Helper_SocialMedia::get_social_sharing_array();
		$content .= '<!-- social-sharing.org -->
				<h3 class="hndle" style="cursor:auto;"><span>' . esc_html__( 'Social Sharing', 'brainpress' ) . '</span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
								' . esc_html__( 'Social Sharing', 'brainpress' ) . '
									<a class="help-icon" href="#"></a>
									<div class="tooltip hidden">
										<div class="tooltip-before"></div>
										<div class="tooltip-button">&times;</div>
										<div class="tooltip-content">
											' . __( '', 'brainpress' ) . '
										</div>
									</div>
								</th>
								<td><ul>';
		foreach ( $services as $key => $label ) {
			$checked = cp_is_true( BrainPress_Core::get_setting( 'general/social_sharing/'.$key, 1 ) );
			$content .= sprintf(
				'<li><label><input type="checkbox" name="brainpress_settings[general][social_sharing][%s]" value="on" /%s /> %s</label></li>',
				esc_attr( $key ),
				checked( $checked, true, false ),
				esc_html( $label )
			);
		}
		$content .= '</ul></td></tr>';
		/**
		 * ebd table settings body
		 */
		$content .= '</tbody></table></div>';
		return $content;
	}

	public static function process_form( $page, $tab ) {

		if ( isset( $_POST['action'] ) && 'updateoptions' === $_POST['action'] && 'general' === $tab && wp_verify_nonce( $_POST['_wpnonce'], 'update-brainpress-options' ) ) {

			$settings = BrainPress_Core::get_setting( false ); // false returns all settings
			$post_settings = (array) $_POST['brainpress_settings'];
			// Now is a good time to make changes to $post_settings, especially to fix up unchecked checkboxes
			$post_settings['general']['show_brainpress_menu'] = isset( $post_settings['general']['show_brainpress_menu'] ) ? $post_settings['general']['show_brainpress_menu'] : 'off';
			$post_settings['general']['use_custom_login'] = isset( $post_settings['general']['use_custom_login'] ) ? $post_settings['general']['use_custom_login'] : 'off';
			$post_settings['general']['redirect_after_login'] = isset( $post_settings['general']['redirect_after_login'] ) ? $post_settings['general']['redirect_after_login'] : 'off';
			$post_settings['instructor']['show_username'] = isset( $post_settings['instructor']['show_username'] ) ? $post_settings['instructor']['show_username'] : false;
			$post_settings['general']['add_structure_data'] = isset( $post_settings['general']['add_structure_data'] ) ? $post_settings['general']['add_structure_data'] : 'off';
			/**
			 * Social Sharing
			 */
			$services = BrainPress_Helper_Socialmedia::get_social_sharing_array();
			foreach ( $services as $key => $label ) {
				if ( isset( $post_settings['general']['social_sharing'][ $key ] ) ) {
					$post_settings['general']['social_sharing'][ $key ] = 'on';
				} else {
					$post_settings['general']['social_sharing'][ $key ] = 'off';
				}
			}
			/**
			 * sanitize
			 */
			$post_settings = BrainPress_Helper_Utility::sanitize_recursive( $post_settings );
			// Don't replace settings if there is nothing to replace
			if ( ! empty( $post_settings ) ) {
				$new_settings = BrainPress_Core::merge_settings( $settings, $post_settings );
				BrainPress_Core::update_setting( false, $new_settings ); // false will replace all settings
				// Flush rewrite rules
				flush_rewrite_rules();
			}
		}
	}

	/**
	 * Small helper to display dropdown for some settings.
	 *
	 * @since 2.0.6
	 *
	 * @param string $dropdown Dropdown with list o pages.
	 * @param string $page Page attr for shortcode cp_pages.
	 * @return string
	 */
	private static function add_page_dropdown_description( $dropdown, $page ) {
		$shortcode = sprintf(
			'<input type="text" readonly="readonly" class="cp-sc-box" value="[cp_pages page=&quot;%s&quot;]" />',
			$page
		);
		if ( empty( $dropdown ) ) {
			return sprintf(
				__( 'Bitte <a href="%1$s">füge eine neue Seite hinzu</a> welche den Shortcode %2$s enthält.', 'brainpress' ),
				esc_url( add_query_arg( 'post_type', 'page', admin_url( 'post-new.php' ) ) ),
				$shortcode
			);
		}
		$content = $dropdown;
		$content .= '<p class="description">';
		$text = __( 'Wähle eine Seite aus, auf der Du den Shortcode %s oder einen anderen Satz von %s hast. Bitte beachte, dass der Slug für die oben festgelegte Seite nicht verwendet wird, wenn "Virtuelle Seite verwenden" nicht ausgewählt ist.', 'brainpress' );
		$url = add_query_arg(
			array(
				'post_type' => BrainPress_Data_Course::get_post_type_name(),
				'page' => 'brainpress_settings',
				'tab' => 'shortcodes',
			),
			'edit.php'
		);
		$link_to_help = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html__( 'shortcodes', 'brainpress' )
		);
		$content .= sprintf( $text, $shortcode, $link_to_help );
		$content .= '</p>';
		return $content;
	}
}
