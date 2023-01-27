<?php


if( ! class_exists( 'Configurator_Taxonomy_Images' ) ) {
    class Configurator_Taxonomy_Images 
    {
    
        public function __construct() {
        //
        }

        /**
         * Initialize the class and start calling our hooks and filters
         */
        public function init() 
        {
            // Image actions
            add_action( 'configurator-category_add_form_fields', array( $this, 'add_category_image' ), 10, 2 );
            add_action( 'created_configurator-category', array( $this, 'save_category_image' ), 10, 2 );
            add_action( 'configurator-category_edit_form_fields', array( $this, 'update_category_image' ), 10, 2 );
            add_action( 'edited_configurator-category', array( $this, 'updated_category_image' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
            add_action( 'admin_footer', array( $this, 'add_script' ) );

            add_filter ('manage_configurator-category_custom_column', array( $this, 'manage_category_custom_fields' ), 10,3);
            add_filter('manage_edit-configurator-category_columns',array( $this, 'manage_my_category_columns' ) );
        }

        public function load_media()
        {
            if( ! isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'configurator-category' )
            {
                return;
            }
            wp_enqueue_media();
        }
    
        /**
        * Add a form field in the new category page
        * @since 1.0.0
        */
    
        public function add_category_image( $taxonomy ) {
            ?>
            
            <div class="form-field term-group">
                <label for="configurator-taxonomy-image-id">
                    <?php _e( 'Image', 'showcase' ); ?>
                </label>
                <input type="hidden" id="configurator-taxonomy-image-id" name="configurator-taxonomy-image-id" class="custom_media_url" value="">
                <div id="category-image-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary showcase_tax_media_button" id="showcase_tax_media_button" name="showcase_tax_media_button" value="<?php _e( 'Add Image', 'showcase' ); ?>" />
                    <input type="button" class="button button-secondary showcase_tax_media_remove" id="showcase_tax_media_remove" name="showcase_tax_media_remove" value="<?php _e( 'Remove Image', 'showcase' ); ?>" />
                </p>
                <p>
                <label for="config-taxonomy-validation">
                    <?php _e( 'Validation', 'showcase'); ?>
                </label>
                <select class="form-control" id="config-taxonomy-validation" name="config-taxonomy-validation">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>                
                </select>
                </p>
                <p>
                <label for="multi-component-selection">
                    <?php _e( 'Multiple Selection', 'showcase'); ?>
                </label>
                <select class="form-control" id="multi-component-selection" name="multi-component-selection">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>                
                </select>
                </p>
                 <p>
                    <label for="pdf-guide">
                        <?php _e( 'PDF Guide', 'showcase'); ?>
                    </label>
                    <input type="file" class="pdf_guide" name="pdf-guide" accept="application/pdf">
                </p>
                <p>
                    <label for="config-taxonomy-long-desc">
                        <?php _e( 'Long Description', 'showcase'); ?>
                    </label>
                    <?php wp_editor('', 'config-taxonomy-long-desc', array('media_buttons' => false ,'editor_height' => 5)); ?>
                    <script>
                        jQuery(window).ready(function(){
                            jQuery('label[for=config-taxonomy-long-desc]').parent().parent().remove();
                        });
                    </script>
                </p>
            </div>
            <?php 
        }

        /**
        * Save the form field
        * @since 1.0.0
        */
        public function save_category_image( $term_id, $tt_id ) 
        {
           
            //save image
            if( isset( $_POST['configurator-taxonomy-image-id'] ) && '' !== $_POST['configurator-taxonomy-image-id'] )
            {
                add_term_meta( $term_id, 'configurator-taxonomy-image-id', absint( $_POST['configurator-taxonomy-image-id'] ), true );
            }

            //save validation type
            if( isset( $_POST['config-taxonomy-validation'] ) && '' !== $_POST['config-taxonomy-validation'] )
            {
                add_term_meta( $term_id, 'config-taxonomy-validation', absint( $_POST['config-taxonomy-validation'] ), true );
            }
             //save multiselection
            if( isset( $_POST['multi-component-selection'] ) && '' !== $_POST['multi-component-selection'] )
            {
                add_term_meta( $term_id, 'multi-component-selection', absint( $_POST['multi-component-selection'] ), true );
            }
            if(!empty($_FILES['pdf-guide']['name'])) {


                $supported_types = array('application/pdf');
                $arr_file_type = wp_check_filetype(basename($_FILES['pdf-guide']['name']));
                $uploaded_type = $arr_file_type['type'];
                if(in_array($uploaded_type, $supported_types)) {
                    $upload = wp_upload_bits($_FILES['pdf-guide']['name'], null, file_get_contents($_FILES['pdf-guide']['tmp_name']));
                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                        update_term_meta($term_id, 'pdf-guide', $upload);     
                    } 
                } else {
                    wp_die("The file type that you've uploaded is not a PDF.");
                } 

            } 

            //save validation type
            if( isset( $_POST['config-taxonomy-long-desc'] ) && '' !== $_POST['config-taxonomy-long-desc'] )
            {
                add_term_meta( $term_id, 'config-taxonomy-long-desc',  $_POST['config-taxonomy-long-desc'] , true );
            }

            
        }

        /**
         * Edit the form field
         * @since 1.0.0
         */
        public function update_category_image( $term, $taxonomy )
        {
           
            ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="configurator-taxonomy-image-id">
                        <?php _e( 'Image', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <?php $image_id = get_term_meta( $term->term_id, 'configurator-taxonomy-image-id', true ); ?>
                    <input type="hidden" id="configurator-taxonomy-image-id" name="configurator-taxonomy-image-id" value="<?php echo esc_attr( $image_id ); ?>">
                    <div id="category-image-wrapper">
                        <?php if( $image_id ) { ?>
                        <?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
                        <?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary showcase_tax_media_button" id="showcase_tax_media_button" name="showcase_tax_media_button" value="<?php _e( 'Add Image', 'showcase' ); ?>" />
                        <input type="button" class="button button-secondary showcase_tax_media_remove" id="showcase_tax_media_remove" name="showcase_tax_media_remove" value="<?php _e( 'Remove Image', 'showcase' ); ?>" />
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="config-taxonomy-validation">
                        <?php _e( 'Validation', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <?php $validation = get_term_meta( $term->term_id, 'config-taxonomy-validation', true ); ?>
                    <p>
                        <select class="form-control" id="config-taxonomy-validation" name="config-taxonomy-validation">
                            <option value="yes" <?php if($validation == 'yes') {echo 'selected';} ?>>Yes</option>
                            <option value="no" <?php if($validation == 'no') {echo 'selected';} ?>>No</option>
                        </select>  
                    </p>
                </td>

            </tr>
            <tr>
                <th scope="row">
                    <label for="multi-component-selection">
                        <?php _e( 'Multiple Selection', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <?php $multi_selection = get_term_meta( $term->term_id, 'multi-component-selection', true ); ?>
                    <p>
                        <select class="form-control" id="multi-component-selection" name="multi-component-selection">
                            <option value="yes" <?php if($multi_selection == 'yes') {echo 'selected';} ?>>Yes</option>
                            <option value="no" <?php if($multi_selection == 'no') {echo 'selected';} ?>>No</option>
                        </select>  
                    </p>
                </td>
                
            </tr>
            <tr>
                  <th scope="row">
                    <label for="pdf-guide">
                        <?php _e( 'PDF Guide', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <?php $get_attach = get_term_meta( $term->term_id, 'pdf-guide', true ); ?>
                    <p>
                       <input type="file" class="pdf_guide" name="pdf-guide"  accept="application/pdf">
                    </p>
                    <p><?php 
                    if(isset($get_attach['url']) && !empty($get_attach['url'])) {
                        echo "<a href=".$get_attach['url']." target='_blank'>View Attachment</a><br>";
                    }

                ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="config-taxonomy-long-desc">
                        <?php _e( 'Long Description', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <?php $desc = get_term_meta( $term->term_id, 'config-taxonomy-long-desc', true ); ?>
                    <p>
                        <?php wp_editor($desc, 'config-taxonomy-long-desc', array('media_buttons' => false ,'editor_height' => 5 )); ?>
                    </p>
                </td>
            </tr>

            <?php 
                $post_title_tags = get_term_meta($term->term_id,'post_title_tags',true);
                $tags_counter = 1;
                if(!empty($post_title_tags))
                {
                    $post_title_tags = json_decode($post_title_tags,true);
                    //print_r($post_title_tags);
                    $tags_counter = count($post_title_tags);
                }
            ?>

            <input type="hidden" id="current_term_filter_tags" value="<?php echo $tags_counter; ?>" />
            <tr class="component-tags-block">
                <th scope="row">
                    <label>
                        <?php _e( 'Filters', 'showcase' ); ?>
                    </label>
                </th>
                <td></td>
            </tr>
            <tr class="component-relation-block">
                <th scope="row">
                    <label>
                        <?php _e( 'Relation', 'showcase' ); ?>
                    </label>
                </th>
                <td>
                    <a href="javascript:void(0)" class="show-relation-block">Component Relations</a>
                    <div class="filter-relation-block-d row" style="display: none;">
                        <!-- <div class="custom-relation-head-s clearfix source-compo-div-d">
                            <p class="config_filter_field_s">
                                <label><?php //_e( 'Component:' ); ?></label>
                                <select style="width: 90%;"  class="source_selected_component" name="" disabled></select>
                            </p>
                            <p class="config_filter_field_select_s">
                                <label><?php //_e( 'Filters:' ); ?></label>
                                <select style="width: 100%;"  multiple="multiple" class="source_selected_tags" name="source_selected_tags[0][]"></select>
                            </p>
                            <a class="create-relation-block add-filter-dest-s" href="javascript:void(0)" style="background:red;"><span>+</span></a>
                        </div> -->
                        <div class="row filter-dest-relation filter-dest-relation-s clearfix">
                        </div>
                    </div>
                </td>
            </tr>


            <?php
        }
        /**
        * Update the form field value
        * @since 1.0.0
        */
        public function updated_category_image( $term_id, $tt_id ) {
            // echo 'hello1';
            

            if( isset( $_POST['configurator-taxonomy-image-id'] ) && '' !== $_POST['configurator-taxonomy-image-id'] )
            {
                update_term_meta( $term_id, 'configurator-taxonomy-image-id', absint( $_POST['configurator-taxonomy-image-id'] ) );
            }
            else
            {
                update_term_meta( $term_id, 'configurator-taxonomy-image-id', '' );
            }

            if( isset( $_POST['config-taxonomy-validation'] ) && '' !== $_POST['config-taxonomy-validation'] )
            {
                update_term_meta( $term_id, 'config-taxonomy-validation',  $_POST['config-taxonomy-validation'] );
            }
            else
            {
                update_term_meta( $term_id, 'config-taxonomy-validation', '' );
            }
            if( isset( $_POST['multi-component-selection'] ) && '' !== $_POST['multi-component-selection'] )
            {
                update_term_meta( $term_id, 'multi-component-selection',  $_POST['multi-component-selection'] );
            }
            else
            {
                update_term_meta( $term_id, 'multi-component-selection', '' );
            }
            if(!empty($_FILES['pdf-guide']['name'])) {

  
                $supported_types = array('application/pdf');

   
                $arr_file_type = wp_check_filetype(basename($_FILES['pdf-guide']['name']));
                $uploaded_type = $arr_file_type['type'];

 
                if(in_array($uploaded_type, $supported_types)) {


                    $upload = wp_upload_bits($_FILES['pdf-guide']['name'], null, file_get_contents($_FILES['pdf-guide']['tmp_name']));

                    if(isset($upload['error']) && $upload['error'] != 0) {
                        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                    } else {
                         update_term_meta($term_id, 'pdf-guide', $upload);   
                    } 

                } else {
                    wp_die("The file type that you've uploaded is not a PDF.");
                } 

            } 

            if( isset( $_POST['config-taxonomy-long-desc'] ) && '' !== $_POST['config-taxonomy-long-desc'] )
            {
                update_term_meta( $term_id, 'config-taxonomy-long-desc',  $_POST['config-taxonomy-long-desc'] );
            }
            else
            {
                update_term_meta( $term_id, 'config-taxonomy-long-desc', '' );
            }
            
            if(isset($_POST['post_title_tags'][0]['title'][0]) && !empty($_POST['post_title_tags'][0]['title'][0]) && isset($_POST['post_title_tags'][0]['filter_show'][0]))
            {
                update_term_meta( $term_id, 'post_title_tags', wp_slash(json_encode($_POST['post_title_tags'])) );
            }
            else
            {
                update_term_meta( $term_id, 'post_title_tags', '' );
            }
            if(isset($_POST['source_selected_tags']) && !empty($_POST['source_selected_tags']))
            {
                update_term_meta( $term_id, 'source_selected_tags', wp_slash(json_encode($_POST['source_selected_tags'])) );
            }

            if(isset($_POST['post_relation']) && !empty($_POST['post_relation']))
            {
                update_term_meta( $term_id, 'post_relation', wp_slash(json_encode($_POST['post_relation'])) );
            }
            
        }
     
        /**
        * Enqueue styles and scripts
        * @since 1.0.0
        */
        public function add_script() {

            if( !isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'configurator-category' ) {
                return;
            }
        ?>
        <script>
            jQuery(document).ready( function($) {
                _wpMediaViewsL10n.insertIntoPost = '<?php _e( "Insert", "showcase" ); ?>';
                function ct_media_upload(button_class) {
                    var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;
                    $('body').on('click', button_class, function(e) {
                        var button_id = '#'+$(this).attr('id');
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(button_id);
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment){
                            if( _custom_media ) {
                                $('#configurator-taxonomy-image-id').val(attachment.id);
                                $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                $( '#category-image-wrapper .custom_media_image' ).attr( 'src',attachment.url ).css( 'display','block' );
                            } else {
                                return _orig_send_attachment.apply( button_id, [props, attachment] );
                            }
                        }
                        wp.media.editor.open(button); return false;
                    });
                }
                ct_media_upload('.showcase_tax_media_button.button');
                $('body').on('click','.showcase_tax_media_remove',function() {
                    $('#configurator-taxonomy-image-id').val('');
                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                });
                // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
                $(document).ajaxComplete(function(event, xhr, settings) {
                    var queryStringArr = settings.data.split('&');
                    if( $.inArray('action=add-tag', queryStringArr) !== -1 ) {
                        var xml = xhr.responseXML;
                        $response = $(xml).find('term_id').text();
                        if($response!="") {
                            // Clear the thumb image
                            $('#category-image-wrapper').html('');
                        }
                    }
                });
            });
        </script>
        <?php 

        }

        /*
         * show the custom field columns
        */
        function manage_my_category_columns($columns)
        {
            $columns['configurator-taxonomy-image-id'] = 'Image';
            $columns['config-taxonomy-validation'] = 'Validation';
            // add column in configurator-category taxonomy
            $columns['multi-component-selection'] = 'Multiple Selection';

            $columns['pdf-guide'] = 'PDF Guide';
            
            return $columns;
        }

        /*
         * show the custom field value
        */
        function manage_category_custom_fields($deprecated,$column_name,$term_id)
        {
            $image_id = get_term_meta( $term_id, 'configurator-taxonomy-image-id', true );
            if ($column_name == 'configurator-taxonomy-image-id') 
            {
                echo wp_get_attachment_image( $image_id ,[50,50]);
            }

            $validation = get_term_meta( $term_id, 'config-taxonomy-validation', true );
            if ($column_name == 'config-taxonomy-validation') 
            {
                echo ucfirst($validation);
            }
            $multi_selection = get_term_meta( $term_id, 'multi-component-selection', true );
            if ($column_name == 'multi-component-selection') 
            {
                echo ucfirst($multi_selection);
            }
            
        }
    }
    $Configurator_Taxonomy_Images = new Configurator_Taxonomy_Images();
    $Configurator_Taxonomy_Images->init(); 
}