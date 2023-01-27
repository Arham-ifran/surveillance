<?php
class WoofilterProControllerWpf extends ControllerWpf {
	public function autocompliteSearchText() {
		$res = new ResponseWpf();
		$keyword = ReqWpf::getVar('keyword');
		$filterId = ReqWpf::getVar('filterId');

		$autocomplete =
			FrameWpf::_()->
			getModule('woofilterpro')->
			getModel('autocomplete')->
			init($keyword, $filterId);

		$autocompleteData = $autocomplete->getData();

		$res->addData('autocompleteData', $autocompleteData);
		return $res->ajaxExec();
	}

	public function applyLoader() {
		$res = new ResponseWpf();
		$id = ReqWpf::getVar('id');
		$settings = ReqWpf::getVar('settings');

		$model = FrameWpf::_()->getModule('woofilters')->getModel('woofilters');
		$filters = $model->getFromTbl();
		$cnt = 0;
		foreach ($filters as $filter) {
			$current = unserialize($filter['setting_data']);
			if ( $filter['id'] != $id && isset($current['settings']) ) {
				$filter['settings'] = array_merge($current['settings'], $settings);
				unset($filter['setting_data']);
				$model->save($filter);
				$cnt++;
			}
		}
		$res->addData('message', $cnt . esc_html__(' filters have been changed', 'woo-product-filter'));
		return $res->ajaxExec();
	}
}
