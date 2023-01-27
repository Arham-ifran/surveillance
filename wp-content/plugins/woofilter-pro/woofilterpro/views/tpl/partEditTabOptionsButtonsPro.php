<?php
	$hideButton = ( isset($this->settings['settings']['display_hide_button']) ? $this->settings['settings']['display_hide_button'] : 'no' );
	$hideMobileButton = ( isset($this->settings['settings']['display_hide_button_mobile']) ? $this->settings['settings']['display_hide_button_mobile'] : 'no' );
	$hideText = esc_attr__('HIDE FILTERS', 'woo-product-filter');
	$showText = esc_attr__('SHOW FILTERS', 'woo-product-filter');
	$hiddenStyle = 'no' == $hideButton && 'no' == $hideMobileButton ? 'wpfHidden' : '';
	$styleHidden = 'no' == $hideMobileButton ? 'wpfHidden' : '';
?>

<div class="row row-settings-block">
	<div class="settings-block-label col-xs-4 col-sm-3">
		<?php esc_html_e('Display Hide Filters button', 'woo-product-filter'); ?>
	</div>
	<div class="settings-block-values settings-w100 col-xs-8 col-sm-9">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="settings-value settings-w100">
					<div class="settings-value-label woobewoo-width60">
						<?php esc_html_e('desktop', 'woo-product-filter'); ?>
					</div>
					<?php
						HtmlWpf::selectbox('settings[display_hide_button]', array(
							'options' => array('no' => 'No', 'yes_close' => 'Yes, show as close', 'yes_open' => 'Yes, show as opened'),
							'value' => $hideButton,
							'attrs' => 'class="woobewoo-flat-input"'
						));
						?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 pl-sm-0">
				<div class="settings-value settings-w100">
					<div class="settings-value-label woobewoo-width60">
						<?php esc_html_e('mobile', 'woo-product-filter'); ?>
					</div>
					<?php
					HtmlWpf::selectbox('settings[display_hide_button_mobile]', array(
						'options' => array('no' => 'No', 'yes_close' => 'Yes, show as close', 'yes_open' => 'Yes, show as opened'),
						'value' => $hideMobileButton,
						'attrs' => 'class="woobewoo-flat-input"'
					));
					?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="settings-value settings-w100 <?php echo esc_attr($hiddenStyle); ?>">
					<div class="settings-value-label woobewoo-width60">
						<?php esc_html_e('hide', 'woo-product-filter'); ?>
					</div>
					<?php
					HtmlWpf::text('settings[hide_button_hide_text]', array(
						'value' => ( isset($this->settings['settings']['hide_button_hide_text']) ? $this->settings['settings']['hide_button_hide_text'] : $hideText ),
						'attrs' => 'placeholder="' . esc_attr($hideText) . '" class="woobewoo-flat-input woobewoo-width150"'
					));
					?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 pl-sm-0">
				<div class="settings-value settings-w100 <?php echo esc_attr($hiddenStyle); ?>">
					<div class="settings-value-label woobewoo-width60">
						<?php esc_html_e('show', 'woo-product-filter'); ?>
					</div>
					<?php
					HtmlWpf::text('settings[hide_button_show_text]', array(
						'value' => ( isset($this->settings['settings']['hide_button_show_text']) ? $this->settings['settings']['hide_button_show_text'] : $showText ),
						'attrs' => 'placeholder="' . esc_attr($showText) . '" class="woobewoo-flat-input woobewoo-width150"'
					));
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="settings-value settings-w100 <?php echo esc_attr($styleHidden); ?>" data-parent="settings[display_hide_button_mobile]" data-no-values="no">
					<div class="settings-value-label">
						<?php esc_html_e('Floating button on mobile', 'woo-product-filter'); ?>
					</div>
					<?php
					HtmlWpf::checkboxToggle('settings[display_hide_button_floating]', array(
						'checked' => ( isset($this->settings['settings']['display_hide_button_floating']) ? $this->settings['settings']['display_hide_button_floating'] : 0 )
					));
					?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 pl-sm-0">
				<?php
				$styleHidden = isset($this->settings['settings']['display_hide_button_floating']) && $this->settings['settings']['display_hide_button_floating'] ? '' : 'wpfHidden';
				?>
				<div class="settings-value settings-w100 <?php echo esc_attr($styleHidden); ?>" data-parent="settings[display_hide_button_floating]">
					<div class="settings-value settings-w50">
						<div class="settings-value-label">
							<?php esc_html_e('left position', 'woo-product-filter'); ?>
						</div>
					</div>
					<div class="settings-value settings-w50">
						<?php
						HtmlWpf::text('settings[display_hide_button_floating_left]', array(
							'value' => isset($this->settings['settings']['display_hide_button_floating_left']) ? $this->settings['settings']['display_hide_button_floating_left'] : '50',
							'attrs' => 'class="woobewoo-flat-input woobewoo-number woobewoo-width60"'));
						HtmlWpf::selectbox('settings[display_hide_button_floating_left_in]', array(
							'options' => array('%' => '%', 'px' => 'px'),
							'value' => ( isset($this->settings['settings']['display_hide_button_floating_left_in']) ? $this->settings['settings']['display_hide_button_floating_left_in'] : '%' ),
							'attrs' => 'class="woobewoo-flat-input"'
						));
						?>
					</div>
				</div>
				<div class="settings-value settings-w100 <?php echo esc_attr($styleHidden); ?>" data-parent="settings[display_hide_button_floating]">
					<div class="settings-value settings-w50">
						<div class="settings-value-label">
							<?php esc_html_e('bottom position', 'woo-product-filter'); ?>
						</div>
					</div>
					<div class="settings-value settings-w50">
						<?php
						HtmlWpf::text('settings[display_hide_button_floating_bottom]', array(
							'value' => isset($this->settings['settings']['display_hide_button_floating_bottom']) ? $this->settings['settings']['display_hide_button_floating_bottom'] : '20',
							'attrs' => 'class="woobewoo-flat-input woobewoo-number woobewoo-width60"'));
						HtmlWpf::selectbox('settings[display_hide_button_floating_bottom_in]', array(
							'options' => array('%' => '%', 'px' => 'px'),
							'value' => ( isset($this->settings['settings']['display_hide_button_floating_bottom_in']) ? $this->settings['settings']['display_hide_button_floating_bottom_in'] : 'px' ),
							'attrs' => 'class="woobewoo-flat-input"'
						));
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
