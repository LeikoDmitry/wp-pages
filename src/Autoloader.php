<?php

namespace Wordpress\Type\Code;

class Autoloader
{
    public function init(): bool
    {
        $autoloader = dirname(__DIR__) . '/vendor/autoload.php';
        if (! is_readable($autoloader)) {
            $this->missingAutoloader();
            return false;
        }
        $autoloader_result = require_once $autoloader;
        if (! $autoloader_result) {
            return false;
        }
        return true;
    }

    /**
     * If the autoloader is missing, add an admin notice
     */
    protected function missingAutoloader(): void
    {
        add_action('admin_notices', function () {
            $link = sprintf('<a href="%s" target="_blank" >', esc_url('https://github.com/LeikoDmitry/wp-parse-users')); 
            ?>
                <div class="notice notice-error">
                    <p>
                        <?php printf(esc_html__(WORDPRESS_TYPE_ADMIN_ERROR_NOTICE), $link, '</a>') ?>
                    </p>
                </div>
            <?php
        });
    }
}
