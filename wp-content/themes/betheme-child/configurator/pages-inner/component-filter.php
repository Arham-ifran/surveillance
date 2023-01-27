<?php $component = get_term($_POST['comp_id']); ?>
<?php


$manufacturer = group_config_tags_manufacturer($_POST['comp_id'], $_POST);

$lock_filter = 0;

if (isset($_POST['config_link_id']) && !empty($_POST['config_link_id'])) {

    $check_lock_filter = pre_selected_kits($_POST['config_link_id'], 'lock');
    if (isset($check_lock_filter['lock']) && !empty($check_lock_filter['lock']) && isset($check_lock_filter['lock'][$_POST['comp_id']][0]) && !empty($check_lock_filter['lock'][$_POST['comp_id']][0])) {
        $lock_filter = 1;
    }
}
?>
<div class="popup-heading">
        <div class="row">
            <div class="colum-8 pl-3">
                <h3><?php echo $component->name; ?></h3>
                <p class="sub-heading-s"><?php echo $component->description; ?></p>
            </div>
            <div class="colum-4 pr-0" style="max-width: 28%;">
                <button type="button" class="close-button close-component">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <!-- <i class="fa fa-times"></i> -->
    </div>
<div class="configur-wrapper pdb-30" id="custom-fixed-popup">
    <!---img-slider/ descriptions / filters--->
    <div class="row">
        <div class="colum-4" style="width: 28%;">
            <div class="popup-image-wrap">
                <div class="img-wrap">
                    <?php
$image_id = get_term_meta($_POST['comp_id'], 'configurator-taxonomy-image-id', true);
if (!empty($image_id)) {
    ?>
        <img class="scale-with-grid" src="<?php echo wp_get_attachment_image_url($image_id); ?>">
<?php
} else {
    ?>
        <img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/no-image.png" />
<?php
}
?>
                </div>
                <div class="img-thumb-list">
                    <!-- <a href="#" class="thumb-item">
                        <img class="scale-with-grid" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/thumb1.jpg">
                    </a>
                    <a href="#" class="thumb-item">
                        <img class="scale-with-grid" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/thumb2.jpg">
                    </a>
                    <a href="#" class="thumb-item">
                        <img class="scale-with-grid" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/thumb3.jpg">
                    </a>
                    <a href="#" class="thumb-item">
                        <img class="scale-with-grid" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/thumb1.jpg">
                    </a> -->
                </div>
            </div>
        </div>
        <div class="colum-8">
            <div class="p-panel-box pb-2">
                <div class="p-panel-body">
                    <div class="sub-cat-tabs">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="description-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-controls="description" aria-selected="true">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="filters-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-controls="filters" aria-selected="false">Filters</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <?php
                                    echo htmlspecialchars_decode(stripslashes(get_term_meta($component->term_id, 'config-taxonomy-long-desc', true)));
                                ?>
                            </div>
                            <div class="tab-pane fade  " id="filters" role="tabpanel" aria-labelledby="filters-tab">
                                <div class="filters-wrap">
                                    <div class="table-responsive custom-table-s">
                                        <table class="table table-borderd <?php echo $lock_filter == 1 ? 'disabled-block' : ''; ?>">
                                            <?php
if (isset($manufacturer['tags']) && !empty($manufacturer['tags'])):
    $i = 0;
    $store_tag_colors = [];
    foreach ($manufacturer['tags'] as $key => $tags):
        // if check show filter check then show
        if($tags['show_filter'] == 1):
        $color = filter_colors($i);
        $i++;
        $html = '';
        if (!empty($tags['tag'])):
            foreach ($tags['tag'] as $tag):
                $store_tag_colors[$tag->id]['background'] = $color['background'];
                $store_tag_colors[$tag->id]['color'] = $color['color'];
                $flag = 1;
                if (!empty($manufacturer['searchrelation'])) {
                    $flag = 0;
                    if (in_array($tag->id, $manufacturer['searchrelation'])) {
                        $flag = 1;
                    }
                }
                if ($flag) {
                    $html .= '<div data-id="' . $tag->id . '" class="term-filter-d st-label" style="background: ' . $color['background'] . ' ;color: ' . $color['color'] . ';">' . $tag->text . '</div>';
                }
            endforeach; ?>
		<?php endif; ?>
        <?php if ($html): ?>
            <tr>
                <td class="comp_filter_title_name"><?php echo $tags['title']; ?>:</td>
                <td>
                    <?php echo $html; ?>
                </td>
            </tr>
        <?php else: ?>
            <!-- <tr>
                <td colspan="2" class="align-center">
                    No filter Option !
                </td>
            </tr> -->
            <?php
            $html = '';
            foreach ($tags['tag'] as $tag):
                $store_tag_colors[$tag->id]['background'] = $color['background'];
                $store_tag_colors[$tag->id]['color'] = $color['color'];
                $html .= '<div data-id="' . $tag->id . '" class="term-filter-d st-label" style="background: ' . $color['background'] . ' ;color: ' . $color['color'] . ';">' . $tag->text . '</div>';
            endforeach; ?>
            <tr>
                <td><?php echo $tags['title']; ?>:</td>
                <td>
                    <?php echo $html; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="2" class="align-center">
            No filter Option !
        </td>
    </tr>
<?php endif; ?>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---end descriptions / filters--->
    <!---system summary--->
    <div class="p-panel-box summary-box custom-padding-s pb-0">
        <div class="p-panel-head" style="border-bottom: 0;">
            <table class="table custom-table-s" style="margin-bottom: 0;">
                <tr>
                    <th>
                        <div class="record-search <?php echo $lock_filter == 1 ? 'disabled-block' : ''; ?>">
                            <input class="form-control search-posts search-input-s" type="text" placeholder="Search here..">
                            <span class="search-btn"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/search.svg" alt="-" class="scale-with-grid"></span>
                        </div>
                    </th>
                    <th>
                    </th>
                    <th class="<?php echo $lock_filter == 1 ? 'disabled-block' : ''; ?>">
                        <select class="form-control manufacturer-d">
                            <option value="" >Select Manufacturer</option>
                            <?php
if (isset($manufacturer['manufacturer'])):
    foreach ($manufacturer['manufacturer'] as $mft):
    ?>
	                                <option value="<?php echo $mft['id']; ?>"><?php echo $mft['text']; ?></option>
	                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </th>
                </tr>
            </table>
        </div>
        <div class="p-panel-body">
             <div class="pre-loader" style="display: none;text-align:center;"><img  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/loader.gif"/></div>
            <div class="popup-table">
                <div class="table-resposnive custom-table-s">
                    <table class="table component-posts-d <?php echo $lock_filter == 1 ? 'disabled-block' : ''; ?>">
                        <?php include 'component-filter-posts.php'; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!------footer buttons---->
    <div class="popup-buttons">
        <button class="btn-cart btn component-filter-box back-component-id" component-type="back" comp-id="">Back to Last Selection</button>
        <button class="btn btn-ok close-component">Ok & Close</button>
        <button class="btn-cart btn component-filter-box next-component-id" component-type="next" comp-id="">Go to Next Selection</button>
    </div>