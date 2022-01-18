<h2 class="wplc_wizard_title"><?= __( "Invite agents",'wp-live-chat-support' ) ?></h2>

<div id="myCarousel" class="container-fluid" data-interval="false">
    <div class="row w-100 mx-auto flex-nowrap">
            <div class="new-agent-item col-sm-12 col-lg-4 col-md-4 active">
                <div class="card new-agent-form">
                    <div class="card-header">
                        <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/new_agent_ic.svg' ); ?>">
                        </i><span><?=__("New Agent",'wp-live-chat-support')?></span>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="Username"><?=__("Username",'wp-live-chat-support')?></label>
                                <input name="agentEntry[1][Username]" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="Name"><?=__("Name",'wp-live-chat-support')?></label>
                                <input name="agentEntry[1][Name]" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label" for="Email"><?=__("Email",'wp-live-chat-support')?></label>
                                <input name="agentEntry[1][Email]" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-row">
                            <div style="text-align: center;" class="form-group col-md-4 offset-md-2">
                                <label class="col-form-label" for="AgentCheck"><?=__("Agent",'wp-live-chat-support')?></label>
                                <input type="radio" name="agentEntry[1][AgentRole]" id="agentEntry_1_AgentCheck"
                                       value="agent" checked>
                            </div>
                            <div style="text-align: center;" class="form-group col-md-4">
                                <label class="col-form-label" for="AdminCheck"><?=__("Admin",'wp-live-chat-support')?></label>
                                <input type="radio" name="agentEntry[1][AgentRole]" id="agentEntry_1_AdminCheck"
                                       value="admin">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="new-agent-item col-md-4">
                <div class="card h-100 ">
                    <div class="row h-100">
                        <div class="col-md-12 align-self-center">
                            <div class="card-body">
                                <p class="card-text" style="font-size:70px; text-align:center;"><i
                                            class="add-agent fas fa-plus-circle"></i></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<div class="new-agent-item-template col-sm-12 col-lg-4 col-md-4" style="display: none">
    <div class="card new-agent-form">
        <div class="card-header">
            <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/new_agent_ic.svg' ); ?>">
            <span><?=__("New Agent",'wp-live-chat-support')?></span>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label class="col-form-label" for="Temp0"><?=__("Username",'wp-live-chat-support')?></label>
                    <input class="form-control" disabled type="text" name="Username" id="Temp0"
                           data-array-id="Username">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label class="col-form-label" for="Temp1"><?=__("Name",'wp-live-chat-support')?></label>
                    <input class="form-control" disabled type="text" name="Name" id="Temp1" data-array-id="Name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label class="col-form-label" for="Temp2"><?=__("Email",'wp-live-chat-support')?></label>
                    <input class="form-control" disabled type="text" name="Email" id="Temp2" data-array-id="Email">
                </div>
            </div>
            <div class="form-row">
                <div style="text-align: center;" class="form-group col-md-4 offset-md-2">
                    <label class="col-form-label" for="Temp3"><?=__("Agent",'wp-live-chat-support')?></label>
                    <input type="radio" disabled name="AgentRole" id="Temp3" data-array-id="AgentCheck"
                           data-maintain-name="true" value="agent" checked>
                </div>
                <div style="text-align: center;" class="form-group col-md-4">
                    <label class="col-form-label" for="Temp5"><?=__("Admin",'wp-live-chat-support')?></label>
                    <input type="radio" disabled name="AgentRole" id="Temp5" data-array-id="AdminCheck"
                           data-maintain-name="true" value="admin">
                </div>
            </div>
        </div>
    </div>
</div>