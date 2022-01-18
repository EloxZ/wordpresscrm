<h3><?=__('Chat Agents', 'wp-live-chat-support')?></h3>

<div class='wplc_agent_container'>
<ul>

    <?php foreach ($agents_array as $agent) { ?>
        <li class="wplc_agent_box" id="wplc_agent_li_<?=$agent->ID?>" data-id="<?=$agent->ID?>">
            <p><img src="//www.gravatar.com/avatar/<?= md5($agent->user_email) ?>?s=60&d=<?=urlencode(( (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https' : 'http')."://ui-avatars.com/api//".$agent->display_name."/64/".TCXUtilsHelper::wplc_color_by_string($agent->display_name)."/fff")?>" /></p>
            <?php if ($agent->isOnline) { ?>
                    <span class='wplc_status_box wplc_type_returning'><?=__("Logged In",'wp-live-chat-support')?></span>
            <?php } ?>
                <h3><?= esc_html($agent->display_name) ?></h3>
                <small><?= esc_html($agent->user_email) ?></small>

                <small style='height:30px'>
                    <a href='<?=admin_url('user-edit.php?user_id=') . $agent->ID ?>#wplc-user-fields'><?= __("Edit Profile", 'wp-live-chat-support') ?></a>
                </small>
                <p>
                    <button class='button button-secondary wplc_remove_agent' id='wplc_remove_agent_<?=$agent->ID?>' data-uid='<?=$agent->ID?>'><?=__("Remove",'wp-live-chat-support')?></button>
                </p>
        </li>
    <?php } ?>
    <li style='width:150px;' id='wplc_add_new_agent_box' >
        <p><i class='fa fa-plus-circle fa-4x' style='color:#ccc;' ></i></p>

        <h3><?=__("Add New Agent",'wp-live-chat-support'); ?></h3>
        <select id='wplc_agent_select'>
            <option value=''><?=__("Select",'wp-live-chat-support'); ?></option>
            <?php
            foreach ( $not_agent_users as $user ) {?>
                <option id="wplc_selected_agent_<?= intval( $user->ID ) ?>" data-email="<?= esc_attr( $user->user_email ) ?>"  data-name="<?= esc_attr( $user->display_name ) ?>" value="<?= intval( $user->ID ) ?>"><?= esc_html( $user->display_name ) ?> (<?=__(reset($user->roles),'wp-live-chat-support')?>)</option>
            <?php } ?>
        </select>
        <p><button class='button button-secondary' id='wplc_add_agent' style="display: none;"><?=__("Add Agent",'wp-live-chat-support'); ?></button></p>
    </li>
</ul>
</div>

<hr/>
<p class="description"><?php echo sprintf(__("Should you wish to add a user that has a role less than 'Author', please go to the %s page, select the relevant user, click Edit and scroll to the bottom of the page and enable the 'Chat Agent' checkbox.", 'wp-live-chat-support'), "<a href='/users.php'>". __('Users','wp-live-chat-support'). "</a>"); ?></p>
<p class="description"><?=__("If there are no chat agents online, the chat will show as offline", 'wp-live-chat-support'); ?></p>

<input type="hidden" id="wplc_agents_to_add" name="wplc_agents_to_add"/>
<input type="hidden" id="wplc_agents_to_remove" name="wplc_agents_to_remove"/>

<li class="wplc_agent_box" style="display: none" id="wplc_agent_box_template" >
    <p><img class="wplc_agent_img" src="" /></p>
    <h3 class="wplc_agent_name"></h3>
    <small class="wplc_agent_email"></small>

    <small style='height:30px'>
        <a class="wplc_agent_edit"><?= __("Edit Profile", 'wp-live-chat-support') ?></a>
    </small>
    <p>
        <button class='button button-secondary wplc_remove_agent' ><?=__("Remove",'wp-live-chat-support')?></button>
    </p>
</li>