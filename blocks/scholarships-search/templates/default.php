<form 
	class="wsu-scholarship-search <?php echo esc_attr( $data['className'] ); ?>"
	method="get" 
	action="<?php echo esc_url( $data['search_page_url'] ); ?>">

	<div class="wsu-scholarship-search__select-wrapper">
		<select id="wsuwp-scholarship-grade-level" class="wsu-scholarship-search__select" name="grade">
			<option value="">- Current grade level -</option>
			<?php if ( ! empty( $data['grade_levels'] ) ) {
				foreach ( $data['grade_levels'] as $grade_level_option ) { ?>
					<option value="<?php echo esc_attr( $grade_level_option->term_id ); ?>"><?php echo esc_html( $grade_level_option->name ); ?></option>
					<?php
				}
			} ?>
		</select>
	</div>

	<div class="wsu-scholarship-search__text-input-wrapper">	
		<input type="text" id="wsu-scholarship-gpa" class="wsu-scholarship-search__text-input" name="gpa" placeholder="G.P.A." value="" maxlength="4"/>
	</div>

	<div class="wsu-scholarship-search__select-wrapper">
		<select id="wsuwp-scholarship-citizenship" class="wsu-scholarship-search__select" name="citizenship">
			<option value="">- Citizenship -</option>
			<?php if ( ! empty( $data['citizenship'] ) ) {
				foreach ( $data['citizenship'] as $citizenship_option ) { ?>
					<option value="<?php echo esc_attr( $citizenship_option->term_id ); ?>"><?php echo esc_html( $citizenship_option->name ); ?></option>
					<?php
				}
			} ?>
		</select>
	</div>

	<div class="wsu-scholarship-search__select-wrapper">
		<select id="wsuwp-scholarship-state" class="wsu-scholarship-search__select" name="state">
			<option value="">- Residency -</option>
			<?php foreach ( $data['states'] as $state_option ) { ?>
				<option value="<?php echo esc_attr( $state_option ); ?>"><?php echo esc_html( $state_option ); ?></option>
				<?php
			} ?>
		</select>
	</div>

	<input class="wsu-button wsu-scholarship-search__submit" type="submit" value="Go">

</form>
