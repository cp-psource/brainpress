<?php
BrainPress_Admin_Notifications::init();
$bulk_nonce = wp_create_nonce( 'bulk_action_nonce' );
?>
<div class="wrap brainpress_wrapper course-notifications">
<h2><?php
echo BrainPress_Admin_Notifications::get_label_by_name( 'name' );
BrainPress_Admin_Notifications::add_button_add_new();
?></h2>
	<hr />
	<form method="post">
        <div class="nonce-holder" data-nonce="<?php echo $bulk_nonce; ?>"></div>
<?php
$this->list_notification->views();
$this->list_notification->display();
?>
	</form>
</div>