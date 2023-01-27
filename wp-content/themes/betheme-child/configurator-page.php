<?php

//  Template Name: Configurator page 

get_header();
?>
<div class="configur-wrapper">
    <div class="pre-loaderr">
        <img  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/loader.gif"/>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="config-type-img">
               <img class="scale-with-grid component-image-change" src="">
            </div>
        </div>
        <div class="col-md-8">
              <div class="main-cat-list">
               
                <div class=" row">
                    <?php
                        $cat_id = 0;
                        $pre_selected_kits = [];
                        if(isset($_GET['config_link_id']) && !empty($_GET['config_link_id']))
                        {
                            $pre_selected_kits = pre_selected_kits($_GET['config_link_id']);
                        }
                    ?>
                    <?php foreach(get_configutor_texonomys() as $key => $configrator): ?>
                        <?php
                            $active = '';
                            $disable ='';
                            if( isset($pre_selected_kits['configurator']) ) {
                                if($pre_selected_kits['configurator'] == $configrator->term_id) {
                                    $active = 'active';
                                    $cat_id = $configrator->term_id;
                                } else {
                                    $disable = 'disabled-block';
                                }
                            } else if($key < 1) {
                                $active = 'active';
                                $cat_id = $configrator->term_id;
                            }
                        ?>
                        <?php  $image_id = get_term_meta( $configrator->term_id, 'configurator-taxonomy-image-id', true ); ?>
                        <div class="col-md-5 show-component-d <?php echo $disable; ?>" config-id="<?php echo $configrator->term_id; ?>">
                            <a href="javascript:void(0)" config-id="<?php echo $configrator->term_id; ?>">
                                <div class="m-list-item <?php echo $active;  ?>">
                                    <div class="icon">
                                        <img class="scale-with-grid d-none" src="<?php  echo wp_get_attachment_image_url($image_id);?>">
                                    </div>
                                    <div class="content">
                                        <?php echo $configrator->name; ?>
                                        <span>Configurator</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-md-5">
                        <a href="javascript:void(0)" class="reset-config-d">
                            <div class="m-list-item restart">  
                                <div class="icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/cat6.svg"></div>
                                <div class="content">Restart <span>Configurator</span></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="col-md-5 col-hide-mobile"> -->
                    <div class="col-md-5">
                          <a href="javascript:void(0)" class="pdf-config">
                            <div class="m-list-item restart">  
                                <div class="icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/guide.svg"></div>
                                <div class="content">Tech Guide<span>for this section</span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-5">
                        <a href="javascript:void(0)" class="calculator-box custom-calculator-box">
                            <div class="m-list-item restart dark-blue">  
                                <div class="icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/payment-method.png"></div>
                                 <div class="content"><span>Total</span><span class="config-price-d">€ 0</span></div>
                                 <div class="content discount_content"></div>
                                <div class="content sub_total_content"></div>
                                
                               
                               
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="gray-bg">
    <!-- html for preloader -->
    <div class="pre-loaderTab" >
        <img  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/loader.gif"/>
    </div>
    <div class="configur-wrapper sub-cat-tabs pb-3">

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="component-tab" data-toggle="tab" href="#component" role="tab" aria-controls="component" aria-selected="true">Component Choice</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" id="accessories-tab" data-toggle="tab" href="#accessories" role="tab" aria-controls="accessories" aria-selected="false">Accessories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab" aria-controls="services" aria-selected="false">Services</a>
            </li> -->
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="component" role="tabpanel" aria-labelledby="component-tab">
                <?php include('configurator/pages-inner/components.php'); ?>
            </div>
            <div class="tab-pane fade" id="accessories" role="tabpanel" aria-labelledby="accessories-tab">
                ices
            </div>
            <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                serv
            </div>
        </div> 
    </div>
</section>
<!-- remove content and change styling -->
<section class="config-system">
    <div class="configur-wrapper">
        <div class="container custom-box-sizing-set">
            <div class="config-system-wrap row">
                <div class="col-md-12 mx-auto">
                    <div class="p-panel-box">
                        <div class="p-panel-head"><h3>Configuration Overview:</h3></div>
                        <div class="p-panel-body" id="configurator-overview">
                            <?php include('configurator/pages-inner/configurator-overview.php'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mx-auto">
                    <div class="p-panel-box">
                        <div class="sub-cat-list system-info pb-0">
                            <a href="javascript:void(0)" class="list-item add-to-cart-d">
                                <div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri();?>/assets/images/addtocart.png"></div>
                                <h5>  Add to Cart</h5>
                            </a>
                         

                            <a href="javascript:void(0);" class="list-item share_link_data" share-id="<?php echo @$_SESSION['share_link_id']; ?>" popup-attribute="share_link_datasheet_page_msg">
                                <div class="icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/share.svg"></div>
                                <h5>Share Link</h5>
                            </a>

                        </div>
                        <!-- <div class="text-center">
                            <button class="btn-cart btn add-to-cart-d add-to-cart-btn" type="button" popup-attribute="add_to_cart_page_msg">
                                Add to Cart
                            </button>
                        </div> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" name="config_product_id" value="" />
<input type="hidden" name="selected_configurator" value="<?php echo $cat_id; ?>" />
<div class="component-filter-block" id="overlay"></div>
<?php get_footer(); ?>