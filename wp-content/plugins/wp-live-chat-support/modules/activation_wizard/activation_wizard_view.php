<div class="bootstrap-wplc-content">
    <div class="container-fluid">
        <div class="row justify-content-center mt-0">
            <div class="col-9 text-center p-0 mt-3 mb-2">
                <div class="px-0 pt-4 pb-0 mt-3 mb-3">
                    <div id="wplc-wizard-header">
                        <h2><?= __( "Welcome to 3CX Live Chat" ) ?></h2>
                        <p><?= __( "Complete the activation wizard to start using the plugin" ) ?></p>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mx-0">
                            <form id="wplc_wizard" method="post" action="<?= $saveUrl ?>">
                                <ul id="wplc_wizard_progressbar">
									<?php foreach ( $steps as $key => $step ) { ?>
                                        <li <?= $key == 0 ? 'class="active"' : '' ?> id="<?= $step->id ?>"
                                                                                     data-include="<?= $step->id == 'step-channel' ? 'true' : 'false' ?>"
                                                                                     style="display:<?= $step->id == 'step-channel' ? 'inline-block' : 'none' ?>">
                                            <div class="wizard-step">
                                                <span class="wizard-step-icon">
                                                    <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/' . $step->icon ); ?>">

                                                </span>
                                                <span class="wizard-step-text"><strong><?= $step->label ?></strong></span>
                                            </div>
                                        </li>
									<?php } ?>
                                </ul>
								<?php foreach ( $steps as $key => $step ) { ?>
                                    <fieldset data-include="false" data-channels="<?= $step->channels ?>"
                                              data-step-id="<?= $step->id ?>"
                                              data-jsvalidation="<?= property_exists( $step, 'jsvalidation' ) ? $step->jsvalidation : '' ?>">

										<?php if ( property_exists( $step, 'view' ) ) { ?>
                                            <div class="form-card wplc_partial_form" data-substep="false">
												<?php include_once( plugin_dir_path( __FILE__ ) . $step->view ); ?>
                                            </div>
										<?php } else if ( property_exists( $step, 'children' ) ) {
											foreach ( $step->children as $child ) { ?>
                                                <div class="form-card wplc_partial_form" data-substep="true">
													<?php include_once( plugin_dir_path( __FILE__ ) . $child->view ); ?>
                                                </div>
											<?php }
										} ?>

										<?php if ( $key < count( $steps ) - 1 ) { ?>
                                            <div class="wplc-wizard-buttons">
                                                <button name="previous"
                                                        id="button_previous_<?= $step->id ?>"
                                                        class="previous action-button"
                                                        style="display:<?= $key == 0 ? 'none' : 'inline-block' ?>;">
                                                    <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/back_arrow.svg' ); ?>">
													<?= __( "Back" ) ?>
                                                </button>


                                                <button name="next" id="button_next_<?= $step->id ?>"
                                                        class="next action-button">
													<?= __( "Next" ) ?>
                                                    <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/next_arrow.svg' ); ?>">
                                                </button>
                                            </div>
										<?php } ?>
                                    </fieldset>
								<?php } ?>

                                <!-- <input type="submit" value="submit" />-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
