<div class="p-panel-body">
    <div class="sub-cat-list system-info">
        <a href="javascript:void(0);" class="list-item print_data_sheet">
            <div class="icon"><img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri();?>/assets/images/datasheet.svg"></div>
            <h5>Print Datasheet</h5>
        </a>
        <!-- <a href="#" class="list-item">
            <div class="icon"><img class="scale-with-grid" src="<?php //echo get_stylesheet_directory_uri();?>/assets/images/connection.svg"></div>
            <h5>Show Connections</h5>
        </a> -->
        <!-- Check if session has share link id then not insert in db -->
        <a href="javascript:void(0);" class="list-item share_link_data" share-id="<?php echo @$_SESSION['share_link_id']; ?>">
            <div class="icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/share.svg"></div>
            <h5>Share Link</h5>
        </a>
    </div>
    <div class="config-list">
        <a href="#">
            <div class="sm-icon">
                <img class="scale-with-grid" src="<?php echo get_stylesheet_directory_uri();?>/assets/images/grey-check.svg" />
            </div>
            <div class="text">
                <h5>Premium Configuration Check</h5>
            </div>
        </a>
        <a href="#">
            <div class="sm-icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/grey-check.svg" /></div>
            <div class="text">
                <h5>Super Silent Modification</h5>
            </div>
        </a>
        <a href="#">
            <div class="sm-icon"><img class="scale-with-grid" src="<?php  echo get_stylesheet_directory_uri();?>/assets/images/grey-check.svg" /></div>
            <div class="text">
                <h5>Thermal grease Deluxe</h5>
            </div>
        </a>
    </div>
    <div class="info-box-alert">
        Assembled, tested - Ready to Work & Play! Free technical support by e-mail, telephone & ticket and a 6-month pickup & return service in the event of a warranty claim are a matter of course.
    </div>
</div>