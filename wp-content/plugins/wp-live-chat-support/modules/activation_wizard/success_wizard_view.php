<div class="bootstrap-wplc-content">
    <div class="container-fluid">
        <div class="row justify-content-center mt-0">
            <div class="col-9 text-center p-0 mt-3 mb-2">
                <div class="px-0 pt-4 pb-0 mt-3 mb-3">
                    <div id="wplc-wizard-header">
                        <h2><?= __( "Welcome to 3CX Live Chat" ) ?></h2>
                    </div>
                    <div class="row" id="wplc-messagebox">
                        <div class="col-md-10 mx-0">
                            <div id="wplc_wizard">
                                <fieldset data-step-id="success_finish">
                                    <div class="form-card">
                                        <div class="row">
                                            <div class="col-md-4 offset-1   <?= $fully_completed ? 'success' : 'warning' ?>" id="wplc-messagebox-left">
                                                <div id="wplc-messagebox-icon">
                                                    <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/activation_complete.svg' ); ?>">
                                                    <div id="wplc-messagebox-complete">Activation Complete
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-6" id="wplc-messagebox-right">
												<?php foreach ( $activation_result['Agents']['Success'] as $username ) { ?>
                                                    <div class="row wplc-messagebox-result-row">
                                                        <div class="col-md-1">
                                                            <img class="wplc-checklist-check" src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/wizard_checklist.svg' ); ?>">
                                                        </div>
                                                        <div class="col-md-10">
															<?= __( "Agent" ) . ' ' . esc_html($username) . ' ' . __( "added" ) ?>
                                                        </div>
                                                    </div>
												<?php } ?>
												<?php foreach ( $activation_result['Agents']['Error'] as $username => $error ) { ?>
                                                    <div class="row wplc-messagebox-result-row">
                                                        <div class="col-md-1">
                                                            <img class="wplc-checklist-check" src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/wizard_checklist.svg' ); ?>">
                                                        </div>
                                                        <div class="col-md-10">
                                                            Unable to add agent <?= esc_html($username) ?>.<br/> Error: <?= $error ?>
                                                        </div>
                                                    </div>
												<?php }
												?>

												<?php foreach ( $single_settings as $key => $setting ) {
													if ( array_key_exists( $key, $activation_result ) && $activation_result[ $key ] ) {
														?>
                                                        <div class="row wplc-messagebox-result-row">
                                                            <div class="col-md-1">
                                                                <img class="wplc-checklist-check" src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/wizard_checklist.svg' ); ?>">
                                                            </div>
                                                            <div class="col-md-10">
																<?= $setting ?>
                                                            </div>
                                                        </div>
													<?php }
												}
												?>
                                                <input type="button" name="start_now"
                                                       id="button_start_now"
                                                       class="action-button"
                                                       value="<?= __( "Finish" ) ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
