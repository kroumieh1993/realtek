<?php

/**
 * @var $current_tab string
 * @var $tabs array
 */

$subscription = es_get_user_subscription();
$upgrade_plan_url = "<a href='" . es_get_page_url( 'pricing' ) . "'>" . __( 'upgrade your plan', 'es' ) . "</a>" ? : __( 'upgrade your plan', 'es' );
$adding_new_home = "<a href='" . es_get_page_url( 'pricing' ) . "'>" . __( 'adding new home', 'es' ) . "</a>" ? : __( 'adding new home', 'es' ); ?>

<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
	<?php if ( ! empty( $tabs[ $current_tab ]['label'] ) ) : ?>
		<h2 class="heading-font"><?php echo $tabs[ $current_tab ]['label']; ?></h2>
	<?php endif; ?>
</div>

<div class="es-section es-section--current-plan">
    <div class="es-section__content">
	    <?php include es_locate_template( 'front/shortcodes/profile/partials/billing-plan.php' ); ?>
    </div>
</div>

<div class="es-section es-section--payment-details">
    <?php include es_locate_template( 'front/shortcodes/profile/partials/billing-payment-details.php' ); ?>
</div>

<div class="es-section es-section--billing-history">
    <h3 class="es-section__title heading-font"><?php _e( 'Billing History', 'es' ); ?></h3>
    <div class="es-section__content">
        <?php if ( $orders = es_get_user_orders() ) : ?>
            <table class="es-table">
                <thead>
                <tr>
                    <th><?php _e( 'Date', 'es' ); ?></th>
                    <th><?php _e( 'Type', 'es' ); ?></th>
                    <th><?php _e( 'Amount', 'es' ); ?></th>
                    <th><?php _e( 'Status', 'es' ); ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $orders as $_order ) : ?>
                        <tr>
                            <td><?php echo $_order->get_create_date(); ?></td>
                            <td><?php echo $_order->get_payment_type_label(); ?></td>
                            <td><?php echo es_format_value( $_order->amount, 'price' ); ?></td>
                            <td><?php echo $_order->get_status(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php printf( __( 'If you %s or purchase %s, you\'ll see a history of your payments here.', 'es' ), $upgrade_plan_url, $adding_new_home ); ?></p>
        <?php endif; ?>
    </div>
</div>