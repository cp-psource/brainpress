<div class="wrap brainpress_wrapper brainpress-import">
	<h2><?php esc_html_e( 'Importieren', 'brainpress' ); ?></h2>
	<p class="description page-tagline">
		<?php esc_html_e( 'Lade Deine exportierten Kurse hoch, um sie hier zu importieren.', 'brainpress' ); ?>
	</p>

	<form method="post" enctype="multipart/form-data" class="has-disabled">
		<?php wp_nonce_field( 'brainpress_import', 'brainpress_import' ); ?>
		<p>
			<input type="file" name="import" class="input-key" />
		</p>
		<h3><?php esc_html_e( 'Import Optionen', 'brainpress' ); ?></h3>
		<div>
			<label>
				<input type="checkbox" name="brainpress[replace]" value="1" />
				<?php esc_html_e( 'Kurs ersetzen, falls vorhanden.', 'brainpress' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'Kurse mit demselben Titel werden automatisch durch neue ersetzt.', 'brainpress' ); ?>
			</p>
		</div><br />
		<div>
			<label>
				<input type="checkbox" name="brainpress[students]" class="input-requiredby" value="1" />
				<?php esc_html_e( 'Kursstudenten einbeziehen', 'brainpress' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'Die Liste der Studenten muss ebenfalls im Export enthalten sein, damit dies funktioniert.', 'brainpress' ); ?>
			</p>
		</div><br />
		<div>
			<label>
				<input type="checkbox" name="brainpress[comments]" data-required-imput="brainpress[students]" disabled="disabled" value="1" />
				<?php esc_html_e( 'Kursthread/Kommentare einschließen', 'brainpress' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'Die Auflistung der Kommentare muss ebenfalls im Export enthalten sein, damit dies funktioniert.', 'brainpress' ); ?>
			</p>
		</div>		
		<div class="cp-submit">
			<?php submit_button( __( 'Datei hochladen und importieren', 'brainpress' ), 'button-primary disabled' ); ?>
		</div>
	</form>
</div>
