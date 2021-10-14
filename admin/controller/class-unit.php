<?php
class BrainPress_Admin_Controller_Unit {
	private static $options = array();

	/**
	 * Render the units UI
	 **/
	public static function render() {
		$content = '';

		foreach ( self::view_templates() as $key => $template ) {
			$content .= $template;
		}

		// Cap checking here...
		$nonce = wp_create_nonce( 'unit_builder' );
		$info_text = __( 'Einheiten Builder lädt...', 'brainpress' );
		$content .= sprintf( '<div id="unit-builder" data-nonce="%s"><div class="loading">%s</div></div>', $nonce, $info_text );

		return $content;
	}

	public static function filter_user_capabilities( $allcaps ) {
		$course_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
		$user_id = get_current_user_id();

		if ( BrainPress_Data_Capabilities::can_change_course_status( $course_id, $user_id ) ) {
			$allcaps['brainpress_change_status_cap'] = true;
		} else {
			if ( ! empty( $allcaps['sbrainpress_change_status_cap'] ) ) {
				unset( $allcaps['brainpress_change_status_cap'] );
			}
		}

		return $allcaps;
	}

	public static function view_templates( $template = false ) {
		$course_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;

		add_filter( 'brainpress_current_user_capabilities', array( __CLASS__, 'filter_user_capabilities' ) );
		$can_create_units = BrainPress_Data_Capabilities::can_create_course_unit( $course_id );

		$templates = array(
			'unit_builder' => '
				<script type="text/template" id="unit-builder-template">
					<div class="tab-container vertical unit-builder-container">
						<div class="tab-tabs unit-builder-tabs">
						<div id="sticky-wrapper" class="sticky-wrapper sticky-wrapper-tabs">
							<div class="tabs"></div>' .
							( $can_create_units ?
							'<div class="sticky-buttons"><div class="button button-add-new-unit"><i class="fa fa-plus-square"></i> ' . __( 'Neue Einheit hinzufügen', 'brainpress' ) . '</div></div>' : '' )
						. '</div>
					</div>
					<div class="tab-content tab-content-vertical unit-builder-content">
						<div class="section static unit-builder-header"></div>
						<div class="section static unit-builder-body"></div>
						<div class="section static unit-builder-no-access" style="display:none;">'. __( 'Du hast nicht genügend ZRechte, um diese Einheit zu bearbeiten!', 'brainpress' ) . '</div>
					</div>
					</div>
				</script>
			',
			'unit_builder_tab' => '
				<script type="text/template" id="unit-builder-tab-template">
					<li class="brainpress-ub-tab <%= unit_live_class %> <%= unit_active_class %>" data-tab="<%= unit_id %>" data-order="<%= unit_order %>" data-cid="<%= unit_cid %>"><span><%= unit_title %></span></li>
				</script>
			',
			'unit_builder_header' => '
				<script type="text/template" id="unit-builder-header-template">
				<div class="unit-detail" data-cid="<%- unit_cid %>">
					<h3><i class="fa fa-cog"></i>' . __( 'Einheit Einstellungen', 'brainpress' ) . '<div class="unit-state">' .
						BrainPress_Helper_UI::toggle_switch(
							'unit-live-toggle',
							'unit-live-toggle',
							array(
								'left' => __( 'Entwurf', 'brainpress' ),
								'right' => __( 'Öffentlich', 'brainpress' ),
							)
						)
					. '</h3>
					<label for="unit_name">' . __( 'Einheit Titel', 'brainpress' ) . '</label>
					<input id="unit_name" class="wide" type="text" value="<%= unit_title %>" name="post_title" spellcheck="true">
					<div class="unit-additional-info">
					<label class="unit-description">' . __( 'Einheit Beschreibung', 'brainpress' ) . '</label>
					<textarea name="unit_description" class="widefat unit-wp-editor" id="unit_description_<%- unit_id %>"><%- unit_content %></textarea>
					' . BrainPress_Helper_UI::browse_media_field(
				'unit_feature_image',
				'unit_feature_image',
				array(
					'placeholder' => __( 'Füge eine Bild-URL hinzu oder suche nach einem Bild', 'brainpress' ),
					'title' => __( 'Ausgewähltes Bild der Einheit', 'brainpress' ),
					'value' => '<%= unit_feature_image %>', // Add _s template
				)
			) . '
					</div>
					<div class="unit-availability">
						<label for="unit_availability">'. __( 'Verfügbarkeit der Einheit', 'brainpress' ) . '</label>
						<select id="unit_availability" class="narrow" name="meta_unit_availability">
							<option value="instant"<%= unit_availability == "instant" ? " selected=\"selected\"" : "" %>>'. __( 'Sofort verfügbar', 'brainpress' ) . '</option>
							<option value="on_date"<%= unit_availability == "on_date" ? " selected=\"selected\"" : "" %>>'. __( 'Verfügbar am', 'brainpress' ) . '</option>
							<option value="after_delay"<%= unit_availability == "after_delay" ? " selected=\"selected\"" : "" %>>'. __( 'Verfügbar nach', 'brainpress' ) . '</option>
						</select>
						<div class="div-inline ua-div div-on_date" style="display:none;">
							<div class="date"><input id="dpinputavailability" class="dateinput" type="text" value="<%= unit_date_availability %>" name="meta_unit_date_availability" placeholder="'. __( 'sofort', 'brainpress' ) . '" spellcheck="true" /></div>
						</div>
						<div class="div-inline ua-div div-after_delay" style="display:none;">
							<input type="number" min="0" max="9999" name="meta_unit_delay_days" value="<%=unit_delay_days%>" placeholder="'. __( 'z.B. 7', 'brainpress' ) . '" /> <span>'. __( 'Tag(e)', 'brainpress' ) . '</span>
						</div>
					</div>
					<div class="progress-next-unit">
						<label>'. esc_html__( 'Fortschritt zur nächsten Einheit', 'brainpress' ) . '</label>
						<label><input id="force_current_unit_completion" type="checkbox" value="on" name="meta_force_current_unit_completion" <%= unit_force_completion_checked %> /><span>'.
				sprintf( '%s <em>%s</em> %s',
					esc_html__( 'Benutzer muss alle erforderlichen Bewertungen', 'brainpress' ),
					esc_html__( 'erfüllen', 'brainpress' ),
					esc_html__( 'und alle Seiten durchgegangen sein, um auf die nächste Einheit zuzugreifen', 'brainpress' )
				) . '</span></label>
						<label><input id="force_current_unit_successful_completion" type="checkbox" value="on" name="meta_force_current_unit_successful_completion" <%= unit_force_successful_completion_checked %>><span>'.
			sprintf( '%s %s <em>%s</em>',
				esc_html__( 'Benutzer muss auch', 'brainpress' ),
				esc_html__( 'alle erforderlichen Bewertungen', 'brainpress' ),
				esc_html__( 'bestehen', 'brainpress' )
			) . '</span></label>
					</div>
				</div>
				<div class="unit-buttons">
					<div class="button unit-save-button disabled">' . __( 'Einheiten speichern', 'brainpress' ) . '</div>
					<a href="#" data-href="'. esc_attr( BrainPress_Data_Course::get_course_url( $course_id ) ) . BrainPress_Core::get_slug( 'units/' ) . '" class="button button-preview" target="_blank">'. __( 'Vorschau', 'brainpress' ) . '</a>
					<div class="button unit-delete-button"><i class="fa fa-trash-o"></i> ' . __( 'Einheit löschen', 'brainpress' ) . '</div></div>
				</script>
			',
			'unit_builder_content_placeholder' => '
				<script type="text/template" id="unit-builder-content-placeholder">
				<div class="loading">
				' . esc_html__( 'Lade Module...', 'brainpress' ) . '
				</div>
				</script>
			',
			'unit_builder_content' => '
				<script type="text/template" id="unit-builder-content-template">
					<div class="section unit-builder-pager"></div>
					<div class="section unit-builder-pager-info"></div>
					<div class="section unit-builder-components"></div>
					<div class="section unit-builder-modules"></div>
					<div class="section unit-builder-footer"></div>
				</script>
			',
			'unit_builder_content_pager' => '
				<script type="text/template" id="unit-builder-pager-template">
					<label>' . esc_html__( 'Einheit Abschnitte', 'brainpress' ) . '</label>
					<ul>
						<% for ( var i = 1; i <= unit_page_count; i++ ) { %>
							<% key = "page_" + i %>
							<% label =  ( key in pages_titles && "" != pages_titles[ key ] ) ? pages_titles[ key ] : i %>
							<% label = ( label.length > 19 ) ? label.substr( 0, 19 ) + "…" : label %>
							<li data-page="<%- i %>" data-key="<%- key %>"><%- label %></li>
						<% }; %>
						<li>+</li>
					</ul>
				</script>
			',
			'unit_builder_content_pager_info' => '
				<script type="text/template" id="unit-builder-pager-info-template">
					<div class="page-info-holder">
					<div class="unit-buttons"><div class="button unit-delete-page-button hidden"><i class="fa fa-trash-o"></i> ' . esc_html__( 'Abschnitt löschen', 'brainpress' ) . '</div></div>
					<label>' . esc_html__( 'Abschnittsüberschrift', 'brainpress' ) . '</label>
					<p class="description">' . esc_html__( 'Die Beschriftung wird auf der Seite Kursübersicht und Einheit angezeigt', 'brainpress' ) . '</p>
					<input type="text" value="<%= page_label_text %>" name="page_title" class="wide" />
					<label class="page-description">' . esc_html__( 'Abschnittsbeschreibung', 'brainpress' ) . '</label>
					<textarea name="page_description" class="page-wp-editor" id="page_description_<%- page_id %>"><%- page_description %></textarea>
					' . BrainPress_Helper_UI::browse_media_field(
				'page_feature_image',
				'page_feature_image',
				array(
					'placeholder' => __( 'Füge eine Bild-URL hinzu oder suchen Sie nach einem Bild', 'brainpress' ),
					'title' => __( 'Abschnittsbild', 'brainpress' ),
					'value' => '<%= page_feature_image %>', // Add _s template
				)
			) . '
					<label><input type="checkbox" value="on" name="show_page_title" <%= page_label_checked %> /><span>' . esc_html__( 'Abschnittsüberschrift als Teil der Einheit anzeigen', 'brainpress' ) . '</span></label>
					</div>
				</script>
			',
			'unit_builder_modules' => '
				<script type="text/template" id="unit-builder-modules-template">
					'. __( 'Module! Diese Vorlage wird nicht verwendet ... sie dient nur zum Testen.', 'brainpress' ) . '
				</script>
			',
			'unit_builder_footer' => '
				<script type="text/template" id="unit-builder-footer-template">
				<div class="button unit-save-button disabled">' . __( 'Einheiten speichern', 'brainpress' ) . '</div>
				<a href="#" data-href="'. esc_attr( BrainPress_Data_Course::get_course_url( $course_id ) ) . BrainPress_Core::get_slug( 'units/' ) . '" class="button button-preview" target="_blank">'. __( 'Vorschau', 'brainpress' ) . '</a>
				<!-- <a class="button button-preview" href="#">'. __( 'Vorschau', 'brainpress' ) . '</a> -->
				' .
					BrainPress_Helper_UI::toggle_switch(
						'unit-live-toggle-2',
						'unit-live-toggle-2',
						array(
							'left' => __( 'Entwurf', 'brainpress' ),
							'right' => __( 'Öffentlich', 'brainpress' ),
						)
					) .
					'</script>',
		);

		$templates['unit_builder_content_components'] = '
				<script type="text/template" id="unit-builder-components-template">
					<label class="bigger">' . esc_html__( 'Module', 'brainpress' ) . '</label>
					<p class="description">' . esc_html__( 'Klicke hier, um der Einheit Modulelemente hinzuzufügen', 'brainpress' ) . '</p>';

		/**
		 * Output elements.
		 */
		$outputs = BrainPress_Helper_UI_Module::get_output_types();
		$templates['unit_builder_content_components'] .= self::get_elements( 'output', $outputs );
		$templates['unit_builder_content_components'] .= '<div class="elements-separator"></div>';
		/**
		 * Input elements.
		 */
		$inputs = BrainPress_Helper_UI_Module::get_input_types();
		$templates['unit_builder_content_components'] .= self::get_elements( 'input', $inputs, 'add-element' );

		$templates['unit_builder_content_components'] .= '
				</script>
			';

		return $templates;
	}


	public static function unit_builder_ajax() {
		$json_data = array();
		$skip_empty = false;
		$task = $_REQUEST['task'];
		$is_valid = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['wp_nonce'] ) && wp_verify_nonce( $_REQUEST['wp_nonce'], 'unit_builder' );

		switch ( $task ) {
			case 'units':
				$course_id = (int) $_REQUEST['course_id'];
				$user_id = get_current_user_id();
				$units = BrainPress_Data_Course::get_units( $course_id, 'any' );

				foreach ( $units as $unit ) {
					$meta = get_post_meta( $unit->ID );
					foreach ( $meta as $key => $value ) {
						$meta[ $key ] = is_array( $value )  ? maybe_unserialize( $value[0] ) : $value;
					}
					// Temp for reordering
					$unit->unit_order = isset( $meta['unit_order'] ) ? $meta['unit_order'] : 0;
					$unit->meta = $meta;
					//	$unit->post_content = format_for_editor( $unit->post_content );

					if ( ! empty( $unit->meta['page_description'] ) ) {
						//	$unit->meta['page_description'] = array_map( 'format_for_editor', $unit->meta['page_description'] );
					}

					// Let's add unit capabilities
					$user_cap = array();
					if ( BrainPress_Data_Capabilities::can_change_course_unit_status( $course_id, $unit->ID, $user_id ) ) {
						$user_cap['brainpress_change_unit_status_cap'] = true;
					}
					if ( BrainPress_Data_Capabilities::can_delete_course_unit( $course_id, $unit->ID, $user_id ) ) {
						$user_cap['brainpress_delete_course_units_cap'] = true;
					}
					if ( BrainPress_Data_Capabilities::can_update_course_unit( $course_id, $unit->ID, $user_id ) ) {
						$user_cap['brainpress_update_course_unit_cap'] = true;
					}
					$unit->user_cap = $user_cap;
				}

				// Reorder units before returning it
				$units = BrainPress_Helper_Utility::sort_on_key( BrainPress_Helper_Utility::object_to_array( $units ), 'unit_order' );

				foreach ( $units as $unit ) {
					$json_data[] = $unit;
				}

				if ( empty( $units ) ) {
					// Give the user something to work on to.
					$unit = array(
						'post_title' => __( 'Einheit ohne Titel', 'brainpress' ),
						'post_type' => BrainPress_Data_Unit::get_post_type_name(),
						'post_status' => 'draft',
						'post_parent' => $course_id,
					);
					$unit_id = wp_insert_post( $unit );
					$unit['ID'] = $unit_id;
					$unit['meta'] = array(
						'unit_order' => 1,
						'page_title' => array(
							'page_1' => '',
						),
						'show_page_title' => array( true ),
					);

					foreach ( $unit['meta'] as $key => $value ) {
						$success = add_post_meta( $unit_id, $key, $value, true );
						if ( ! $success ) {
							update_post_meta( $unit_id, $key, $value );
						}
					}

					/**
					 * Action called after we add new unit.
					 *
					 * @since 2.0.0
					 *
					 * @param integer $unit_id Unit ID.
					 * @param array $meta Unit meta data.
					 */
					do_action( 'brainpress_unit_added', $unit_id, $course_id );

					// Let's add unit capabilities
					$user_cap = array(
						'brainpress_change_unit_status_cap' => true,
						'brainpress_delete_course_units_cap' => true,
						'brainpress_update_course_unit_cap' => true,
					);
					$unit['user_cap'] = $user_cap;
					$units[] = $unit;
					$json_data[] = $unit;
				}

				$skip_empty = empty( $units ) ? true : false;
				break;

			case 'modules':
				$unit_id = (int) $_REQUEST['unit_id'];
				$page = (int) $_REQUEST['page'];
				$modules = BrainPress_Data_Course::get_unit_modules( $unit_id, 'any', false, false, array( 'page' => $page ) );

				foreach ( $modules as $module_id => $module ) {
					$attributes = BrainPress_Data_Module::attributes( $module_id );
					$module_type = $attributes['module_type'];
					$meta = get_post_meta( $module->ID );
					foreach ( $meta as $key => $value ) {
						// Escape questions and answers before rendering
						if ( 'questions' === $key ) {
							$value[0] = maybe_unserialize( $value[0] );
							foreach ( $value[0] as $i => $q ) {
								$value[0][ $i ]['question'] = esc_html( $q['question'] );
								if ( ! empty( $q['options'] ) ) {
									foreach ( $q['options']['answers'] as $ii => $answer ) {
										$value[0][ $i ]['options']['answers'][ $ii ] = esc_html( $answer );
									}
								}
							}
						}
						/**
						 * Escape questions and answers before rendering
						 */
						if ( 'answers' === $key ) {
							$v = maybe_unserialize( $value[0] );
							$value[0] = array();
							foreach ( $v as $i => $q ) {
								$value[0][ $i ] = esc_html( $q );
							}
						}
						$meta[ $key ] = is_array( $value )  ? maybe_unserialize( $value[0] ) : $value;
					}
					// Temp for reordering
					$module->module_order = isset( $meta['module_order'] ) ? $meta['module_order'] : 0;
					$module->post_content = format_for_editor( $module->post_content );
					$module->meta = $meta;
				}

				// Reorder modules before returning it
				$modules = BrainPress_Helper_Utility::sort_on_key( BrainPress_Helper_Utility::object_to_array( $modules ), 'module_order' );

				foreach ( $modules as $module ) {
					$json_data[] = $module;
				}

				$skip_empty = empty( $modules ) ? true : false;
				break;

			case 'units_update':
				if ( true === $is_valid ) {
					$data = json_decode( file_get_contents( 'php://input' ) );
					$data = BrainPress_Helper_Utility::object_to_array( $data );
					$units = array();

					foreach ( $data as $unit ) {
						unset( $unit['post_modified'] );
						unset( $unit['post_modified_gmt'] );
						unset( $unit['guid'] );
						$unit['post_name'] = '';

						$new_unit = false;
						$unit_id = isset( $unit['ID'] ) ? (int) $unit['ID'] : 0;
						if ( 0 === $unit_id ) {
							unset( $unit['ID'] );
							$new_unit = true;
						}

						$update = isset( $unit['flag'] ) && 'dirty' === $unit['flag'];
						unset( $unit['flag'] );

						if ( $update ) {

							$course_id = (int) $_REQUEST['course_id'];
							$unit['post_type'] = BrainPress_Data_Unit::get_post_type_name();
							$unit['post_parent'] = $course_id;
							if ( $new_unit ) {
								$unit['post_status'] = 'draft';
							}

							$meta = ! empty( $unit['meta'] ) ? $unit['meta'] : array();
							unset( $unit['meta'] );

							$id = wp_insert_post( $unit );
							$units[] = $id;

							/**
							 * check new pages
							 */
							if ( ! $new_unit ) {
								BrainPress_Data_Unit::show_new_pages( $id, $meta );
							}

							/**
							 * get deleted page number
							 * update modules with page number greateer then
							 * deleted_page
							 */
							if ( isset( $meta['deleted_page'] ) ) {
								$deleted_page = $meta['deleted_page'];
								unset( $meta['deleted_page'] );
								BrainPress_Data_Module::decrease_page_number( $unit_id, $deleted_page );
							}

							// Have pages been removed?
							foreach ( $meta as $key => $value ) {
								$success = add_post_meta( $id, $key, $value, true );
								if ( ! $success ) {
									update_post_meta( $id, $key, $value );
								}
							}

							/**
							 * Action called after we add new unit.
							 *
							 * @since 2.0.0
							 *
							 * @param integer $unit_id Unit ID.
							 * @param array $meta Unit meta data.
							 */
							if ( $new_unit ) {
								do_action( 'brainpress_unit_added', $id, $course_id, $meta );
							}
							do_action( 'brainpress_unit_updated', $id );

							$json_data['unit_id'] = $id;
						} else {
							if ( ! empty( $unit_id ) ) {
								$units[] = $unit_id;
							}
						}
					}

					// Check for removed units and delete if needed.
					$saved_units = BrainPress_Data_Course::get_unit_ids( (int) $_REQUEST['course_id'], array( 'publish', 'draft' ), false );
					foreach ( $saved_units as $u_id ) {
						if ( ! in_array( $u_id, $units ) ) {
							wp_delete_post( $u_id );
							do_action( 'brainpress_unit_deleted', $u_id );
						}
					}

					$json_data['nonce'] = wp_create_nonce( 'unit_builder' );
				}
				break;

			case 'modules_update':
				if ( true === $is_valid ) {
					$data = json_decode( file_get_contents( 'php://input' ) );
					$data = BrainPress_Helper_Utility::object_to_array( $data );
					$unit_id = (int) $_REQUEST['unit_id'];
					$modules = array();

					$update_student_progress = false;

					foreach ( $data as $module ) {
						if ( empty( $module ) ) {
							continue;
						}
						unset( $module['post_modified'] );
						unset( $module['post_modified_gmt'] );
						unset( $module['post_name'] );
						unset( $module['guid'] );

						$new_module = false;
						$module_id = isset( $module['ID'] ) ? (int) $module['ID'] : 0;

						if ( empty( $module_id ) ) {
							$new_module = true;
							unset( $module['ID'] );
						}

						$update = isset( $module['flag'] ) && 'dirty' === $module['flag'];
						unset( $module['flag'] );

						$module['post_type'] = BrainPress_Data_Module::get_post_type_name();
						$module['post_parent'] = $unit_id;
						$module['post_status'] = 'publish';

						if ( ! empty( $module['meta'] ) && 'discussion' === $module['meta']['module_type'] ) {
							$data['comment_status'] = 'open';
						}

						if ( $update ) {
							$meta = ! empty( $module['meta'] ) ? $module['meta'] : array();
							$meta['legacy_updated'] = true;

							unset( $module['meta'] );

							$id = wp_insert_post( $module );
							$modules[] = $id;

							foreach ( $meta as $key => $value ) {
								$success = add_post_meta( $id, $key, $value, true );
								if ( ! $success ) {
									update_post_meta( $id, $key, $value );
								}
								$update_student_progress = true;
							}

							do_action( 'brainpress_module_updated', $id );
						} else {
							if ( ! empty( $module_id ) ) {
								$modules[] = $module_id;
							}
						}
					}

					// Check for removed modules and delete if needed
					$saved_modules = BrainPress_Data_Course::get_unit_modules(
						(int) $_REQUEST['unit_id'],
						'any',
						true,
						false,
						array( 'page' => (int) $_REQUEST['page'] )
					);

					foreach ( $saved_modules as $mod_id ) {
						if ( ! in_array( $mod_id, $modules ) ) {
							wp_delete_post( $mod_id );
							do_action( 'brainpress_module_deleted', $mod_id );
						}
					}

					$unset_keys_array = array( 'answered', 'passed' );
					/**
					 * update student progress
					 */
					$update_student_progress = apply_filters( 'brainpress_update_student_progress', $update_student_progress );
					if ( $update_student_progress ) {
						$course_id = $_REQUEST['course_id'];
						$students = BrainPress_Data_Course::get_students( $course_id, 0, 0, 'ids' );

						if ( is_array( $students ) && ! empty( $students )  ) {
							foreach ( $students as $student_id ) {
								$student_progress = BrainPress_Data_Student::get_calculated_completion_data( $student_id, $course_id );
								$student_progress = BrainPress_Helper_Utility::set_array_value( $student_progress, 'completion/'.$unit_id.'/progress', 0 );
								foreach ( $unset_keys_array as $unset_key ) {
									$student_progress = BrainPress_Helper_Utility::set_array_value( $student_progress, 'completion/'.$unit_id.'/'.$unset_key, array() );
								}
								/**
								 * re-save
								 */
								if ( isset( $student_progress['units'] ) ) {
									foreach ( $student_progress['units'] as $unit_id => $data ) {
										if ( isset( $data['responses'] ) ) {
											$modules = array_keys( $data['responses'] );
											foreach ( $modules as $module_id ) {
												$module_response = BrainPress_Data_Student::get_response( $student_id, $course_id, $unit_id, $module_id );
												BrainPress_Data_Student::module_response( $student_id, $course_id, $unit_id, $module_id, $module_response['response'], $student_progress, true );
											}
										}
									}
								}

								BrainPress_Data_Student::get_calculated_completion_data( $student_id, $course_id );
							}
						}
					}

					$json_data['nonce'] = wp_create_nonce( 'unit_builder' );
				}
				break;

			case 'unit_toggle':
				$unit_id = (int) $_REQUEST['unit_id'];

				if ( true === $is_valid ) {
					$state = sanitize_text_field( $_REQUEST['state'] );
					$response = wp_update_post( array(
						'ID' => $unit_id,
						'post_status' => $state,
					) );
					do_action( 'brainpress_unit_updated', $unit_id );
					$json_data['nonce'] = wp_create_nonce( 'unit_builder' );
				}

				$post = get_post( $unit_id );
				$json_data['post_status'] = $post->post_status;
				break;

			case 'module_add':
				if ( true === $is_valid ) {
					$data = json_decode( file_get_contents( 'php://input' ) );
					$data = BrainPress_Helper_Utility::object_to_array( $data );
					$new_module = false;
					$meta = array( 'legacy_updated' => true );

					if ( ! empty( $data['meta'] ) ) {
						$meta = $data['meta'];
						unset( $data['meta'] );
					}

					if ( ! (int) $data['ID'] ) {
						$new_module = true;
						unset( $data['ID'] );
					}

					$data['ping_status'] = 'closed';
					$data['comment_status'] = 'closed';
					$data['post_parent'] = (int) $_REQUEST['unit_id'];
					$data['post_type'] = BrainPress_Data_Module::get_post_type_name();
					$data['post_status'] = 'publish';

					$id = wp_insert_post( $data );

					foreach ( $meta as $key => $value ) {
						$success = add_post_meta( $id, $key, $value, true );
						if ( ! $success ) {
							update_post_meta( $id, $key, $value );
						}
					}

					$json_data['nonce'] = wp_create_nonce( 'unit_builder' );

					do_action( 'brainpress_module_added', $id, $data['post_parent'], $meta );
				}
				break;
			case 'modules_update_delete_section':
				if ( $is_valid ) {
					$unit_id = $_REQUEST['unit_id'];
					$page = $_REQUEST['page'];
					BrainPress_Data_Unit::delete_section( $unit_id, $page );
				}
				break;
		}

		if ( ! empty( $json_data ) || $skip_empty ) {
			BrainPress_Helper_Utility::send_bb_json( $json_data );
		} else {
			$json_data['success'] = false;
			BrainPress_Helper_Utility::send_bb_json( $json_data );
		}
	}

	/**
	 * Produce row with icons for output and input elements.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type Type of elements, valid 'output' or 'input'.
	 * @param array $elements Arary of elements.
	 * @param string $a_class String with class for a html element.
	 * @return string Icons with elements.
	 */
	private static function get_elements( $type, $elements, $a_class = '' ) {
		$content = '';
		foreach ( $elements as $key => $element ) {
			$dashicon = '';
			if ( isset( $element['dashicon'] ) ) {
				$dashicon = sprintf( '<span class="dashicons dashicons-%s"></span>', esc_attr( $element['dashicon'] ) );
			}
			$content .= sprintf(
				'<div class="%s-element module-%s" data-type="%s"><a class="%s">%s</a><span class="element-label">%s</span></div>',
				esc_attr( $type ),
				esc_attr( $key ),
				esc_attr( $key ),
				esc_attr( $a_class ),
				$dashicon,
				$element['title']
			);
		}
		return $content;
	}
}
