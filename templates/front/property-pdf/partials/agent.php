<?php

/**
 * @var $agent_id int
 * @var $agent Es_Agent_Post
 */

$agent = es_get_agent( $agent_id );

if ( $agent ) : ?>
    <style>
        .agent {
            vertical-align: top;
            font-size: 0;
            letter-spacing: 0;
            margin-top: 20px;
        }

        .agent .agent-field {
            display: block;
            margin: 5px 0;
        }

        .agent .agent-field img {
            margin-right: 5px;
        }

        .agent .email {
            font-weight: 300;
            font-size: 11px;
            color: #212121;
        }

        .tel {
            font-weight: 300;
            font-size: 12px;
            color: #212121;
        }

        .agent__image, .agent__content {
            display: inline-block;
            vertical-align: top;
        }

        .name {
            font-weight: bold;
            font-size: 16px;
            color: #424242;
        }

        .agent__image {
            margin-right: 20px;
        }

        .agency {
            font-weight: 300;
            font-size: 12px;
            letter-spacing: 0.05em;
            color: #6A6A6A;
            display: block;
        }

        .agent-field .fa3, .agent-field .fa {
            color: <?php echo ests( 'main_color' ); ?>;
            font-size: 12px !important;
            margin-right: 5px;
        }

        .field-value {
            display: inline-block;
            position: relative;
            top: -4px;
        }

        .agent__image {
            width: 120px;
            height: 100px;
        }
    </style>

	<table class="agent" cellpadding="0" cellspacing="0">
        <tr>
            <td class="agent__image">
                <img src="<?php echo es_get_the_agent_avatar_url( $agent_id ); ?>" width="100" alt=""/>
            </td>
            <td class="agent__content" style="vertical-align: middle;">
                <table>
                    <?php if ( $name = $agent->post_title ) : ?>
                        <tr>
                            <td>
                                <div class="agent-field themed-sup-line name"><?php echo $name; ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ( $agent->has_agency() ) : ?>
                    <tr>
                        <td>
                        <sup class="agency"><?php echo $agent->get_agency()->post_title; ?></sup>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ( $config = $agent->get_preferred_contact_config() ) : ?>
                    <tr>
                        <td>
                            <?php echo $config['label']; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
	</table>
<?php endif;
