<?php
if(isset($_POST['configurator_id'])){
    $cat_id = $_POST['configurator_id'];
}
?>

<?php $components = get_configutor_child_texonomys($cat_id);?>
<?php if(!empty($components)):?>
<div class="sub-cat-list">
    <?php foreach($components as $key=> $component): ?>
        <?php  $image_id = get_term_meta( $component->term_id, 'configurator-taxonomy-image-id', true ); ?>
        <a href="#" class="list-item">
            <div class="icon"><img class="scale-with-grid" src="<?php echo wp_get_attachment_image_url($image_id);?>"></div>
            <h5><?php echo $component->name; ?></h5>
        </a>
    <?php endforeach; ?>
</div>

<?php else:?>

<p>No record found!</p> 
<?php endif;?>