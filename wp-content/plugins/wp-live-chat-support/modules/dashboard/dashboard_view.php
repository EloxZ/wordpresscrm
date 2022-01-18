<div class="wrap wplc_wrap">
    <div id="wplc_dashboard_panel">
        <div id="wplc_dashboard_main">
            <div class="wplc_dashboard_hello_icon">
                <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/dashboard_hello.svg' ); ?>">
            </div>
            <div class="wplc_dashboard_main_col">
                <div class="wplc_dashboard_hello_msg">Hello <?= $user->display_name ?>,<br><span> have a great day at work!</span>
                </div>
                <div class="wplc_dashboard_section_head">CURRENT ACTIVITY</div>
                <div class="wplc_dashboard_activity">
                    <div class="wplc_dashboard_activity_current">
                        <div class="wplc_dashboard_activity_current_icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="wplc_dashboard_activity_current_data">
                            <span id ="wplc_online_visitors" ><?= $online_visitors ?></span><br/>
                            ACTIVE USERS
                        </div>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <div class="wplc_dashboard_activity_current">
                        <div class="wplc_dashboard_activity_current_icon">
                            <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/new_agent_ic.svg' ); ?>">
                        </div>
                        <div class="wplc_dashboard_activity_current_data">
                            <span id="wplc_online_agents"><?= $online_users ?></span><br/>
                            ACTIVE AGENTS
                        </div>
                        <i class="fa fa-caret-up"></i>
                    </div>
                </div>
                <div class="wplc_dashboard_section_head">RECENT ACTIVITY</div>
                <div class="wplc_material_panel">
                    <div class="wplc-center">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                               class="wplc_dashboard_stats_table">
                            <tbody>
                            <tr>
                                <th width="18%">
                                    <div class="wplc-dashboard-stats-title wplc_right"><?= __( "Chats", 'wp-live-chat-support' ); ?></div>
                                </th>
                                <th width="19%" align="center">
                                    <div class="wplc-dashboard-stats-title"><?= __( "Missed", 'wp-live-chat-support' ); ?></div>
                                </th>
                                <th width="19%" align="center">
                                    <div class="wplc-dashboard-stats-title"><?= __( "Engaged", 'wp-live-chat-support' ); ?></div>
                                </th>
                                <th width="19%" align="center">
                                    <div class="wplc-dashboard-stats-title"><?= __( "Total", 'wp-live-chat-support' ); ?></div>
                                </th>
                            </tr>
							<?php foreach ( $stats as $daysKey => $stat ) { ?>
                                <tr>
                                    <td height="20" align="right">
                                        <div class="wplc-dashboard-stats-side-title"><?= $daysKey == 0 ? __( "Today", 'wp-live-chat-support' ) : sprintf( __( "Last %s days", 'wp-live-chat-support' ), $daysKey ); ?></div>
                                    </td>
                                    <td align="center"><?= $stat['missed'] ?></td>
                                    <td align="center"><?= $stat['count'] ?></td>
                                    <td align="center"><?= $stat['total'] ?></td>
                                </tr>
							<?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>

        <div id="wplc_dashboard_news">
            <div class="wplc_panel">
                <div class="wplc_news_heading">
                    <div><?= __( "Latest News", 'wp-live-chat-support' ); ?></div>
                    <div class="wplc_news_icon"><i class="fas fa-newspaper" aria-hidden="true"></i></div>

                </div>
                <div id="wplc_blog_posts" class="wplc_material_panel">
					<?= $news ?>
                </div>
            </div>
        </div>

    </div>
</div>