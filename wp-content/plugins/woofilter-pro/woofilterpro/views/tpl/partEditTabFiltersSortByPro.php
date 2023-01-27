<div class="row-settings-block">
	<div class="settings-block-label col-xs-4 col-sm-3">
		<?php esc_html_e('Use as default', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Select some sort option as default.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-values-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php HtmlWpf::checkboxToggle('f_default_sortby', array()); ?>
		</div>
		<div class="settings-value settings-w100" data-parent="f_default_sortby">
			<?php
			$options = array();
			$labels  = FrameWpf::_()->getModule('woofilters')->getModel('woofilters')->getFilterLabels('SortBy');
			foreach ($labels as $key => $value) {
				$options[$key] = $value;
			}
			HtmlWpf::selectbox('f_hidden_sortby', array(
				'options' => $options,
				'attrs' => 'class="woobewoo-flat-input"'
			));
			?>
		</div>
		<div class="settings-value settings-w100" data-parent="f_default_sortby">
			<div class="settings-value-label woobewoo-width60">
				<?php esc_html_e('Hide filter', 'woo-product-filter'); ?>
			</div>
			<?php HtmlWpf::checkboxToggle('f_hidden_sort', array('attrs' => 'data-preselect-flag="1"')); ?>
		</div>
	</div>
</div>
