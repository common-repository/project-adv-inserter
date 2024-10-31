<?php

namespace Sferica\Plugins\ProjectAdvInserter;

class Admin
{
    private $options;

    public function __construct()
    {
        $this->options = get_option(Config::OPTIONS);
    }

    public function admin_init()
    {
        register_setting('projectadvinserter', Config::OPTIONS);
        add_settings_section('projectadvinserter_settings', 'Settings', '', 'projectadvinserter');
        add_settings_field('projectadvinserter_adstxt_field', 'Custom ads.txt', [$this, 'adstxt_field_callback'], 'projectadvinserter', 'projectadvinserter_settings');
    }

    public function adstxt_field_callback()
    {
        $value = isset($this->options[Config::OPTION_ADSTXT]) ? $this->options[Config::OPTION_ADSTXT] : '';
        ?>
        <textarea id="<?php echo Config::OPTION_ADSTXT ?>" 
            name="<?php echo Config::OPTIONS . '['. Config::OPTION_ADSTXT . ']' ?>" 
            style="width:100%;height:200px"><?php echo esc_html($value); ?></textarea>
        <?php
    }

    public function admin_menu()
    {
        add_menu_page(
            'Project Adv Inserter',
            'Project Adv Inserter',
            'manage_options',
            'project-adv-inserter',
            [$this, 'admin_menu_callback'],
            'dashicons-tag'
        );
    }

    public function admin_menu_callback()
    {
        if (isset($_GET['settings-updated'])) {
            add_settings_error('projectadvinserter_messages', 'projectadvinserter_message', 'Impostazioni salvate', 'updated');
        }

        settings_errors('projectadvinserter_messages');

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Project Adv Inserter</h1>
            <hr class="wp-header-end">
            <form action="options.php" method="post">
			<?php
                settings_fields('projectadvinserter');
                do_settings_sections('projectadvinserter');
                submit_button('Salva');
			?>
		    </form>
        </div>
        <?php
    }

    public function update_option($old_value, $value)
    {
        delete_option(Config::TRANSIENT);
    }
}