<div class="wrap" id="<?php echo esc_attr( self::MENU_SLUG ); ?>">
	
	<h1><?php esc_html_e( 'Projects', 'wer_pk' ); ?></h1>
	<form method="post" id="project_Add_Edit" action="javascript:void(0)" enctype="multipart/form-data">
		<input type="hidden" name="project_id" value="<?php echo !empty($data['editProject']) ? $data['editProject']->id : ""; ?>" />
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<label class="wp-wer_pk-label form-label"><?php _e('Project Name', 'wer_pk' );?></label>
				</td>
				<td>
					<input type="text" value="<?php echo !empty($data['editProject']) ? $data['editProject']->site_name : ""; ?>" name="project_name" class="form-control" required="required" />
				</td>
				<td>
					<label class="wp-wer_pk-label form-label"><?php _e('Project Size', 'wer_pk' );?></label>
				</td>
				<td>
					<input type="number" min="1" max="20" value="<?php echo !empty($data['editProject']) ? $data['editProject']->site_size : "";?>" name="size" class="form-control" required="required"/>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<label class="wp-wer_pk-label form-label"><?php _e('Project Location', 'wer_pk' );?></label>
				</td>
				<td>
					<input type="text" value="<?php  echo !empty($data['editProject']) ? $data['editProject']->site_location : "";?>" name="location" class="form-control" required="required"/>
				</td>
				<td>
					<label class="wp-wer_pk-label form-label"><?php _e('Project Start Date', 'wer_pk' );?></label>
				</td>
				<td>
					<input type="date" value="<?php echo !empty($data['editProject']) ? $data['editProject']->start_date : ""; ?>" name="start_date" class="form-control" required="required"/>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<label class="wp-wer_pk-label form-label"><?php _e('Project Status', 'wer_pk' );?></label>
				</td>
				<td>
					<input 
					class="form-check-input"
					type="checkbox" 
					name="status"
					id="status"
					value="<?php echo !empty($data['editProject']) ? $data['editProject']->status : "0"; ?>"
					<?php 
						echo !empty($data['editProject']) ? $data['editProject']->status == 1 ? "checked" : "" : ""; 
					?>
					/>
					<label for="status"><?php echo __('Hold Project', 'wer_pk') ?></label>

				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="4">
					<?php
						if ( isset($data['editing']) ) {
					?>
						<button 
						type="submit" 
						name="Update" 
						id="UpdateMe" 
						class="btn btn-primary" 
						onClick="wer_pkUpdateForm();"
						>
						<?php echo __('Update Changes', 'wer_pk') ?>
						<span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php
						} else {
					?>
						<button 
						type="submit" 
						name="Save" 
						id="SaveMe" 
						class="btn btn-primary"
						onClick="wer_pkSaveForm();"
						>
						<?php echo __('Save Project', 'wer_pk') ?>
						<span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
						
					<?php
						}
					?>
				</td>
			</tr>

		</table>


		
	</form>


</div><!--end #wrap -->