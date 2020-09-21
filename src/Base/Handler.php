<?php

namespace Wordpress\Type\Code\Base;

use Psr\Log\LoggerInterface;
use Wordpress\Type\Code\HandlerInterface;
use Wordpress\Type\Code\Exception\Handler as HandlerException;

class Handler implements HandlerInterface
{
    private const BASE_ENDPOINT = 'users';
    private const BASE_TEMPLATE_DIR = 'template';
    private const BASE_TEMPLATE_EXTENSION = '.php';
    private const FRONTEND_NONCE_FIELD = 'type-code-nonce';
    private const FRONTEND_NONCE_REQUEST = 'front-code-nonce';
    private const ADMIN_AJAX_HANDLER = 'admin-ajax.php';
    private const MAIN_BOOTSTRAP_CSS_NAMESPACE = 'code-type-bootstrap-css-file';
    private const MAIN_BOOTSTRAP_JS_NAMESPACE = 'code-type-boostrap-js-file';
    private const MAIN_JS_NAMESPACE = 'code-type-main-js-file';
    private const MAIN_JS_NAMESPACE_KEY = 'frontend';
    private const MAIN_JS_USER_ID_KEY = 'userId';
    private const CACHE_EXPIRED_SECONDS = 600;
    private const CACHE_KEY = 'code-type-users';
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function ajax(): string
    {
        try {
            check_ajax_referer(static::FRONTEND_NONCE_FIELD, static::FRONTEND_NONCE_REQUEST);
            $userId = intval(esc_attr($_POST[static::MAIN_JS_USER_ID_KEY])) ?? 0;
            $body = $this->getResourceData(WORDPRESS_TYPE_CODE_BASE_URL, $userId);
            return $this->sendJson($body);
        } catch (HandlerException $exception) {
            $this->logger->error($exception->getMessage());
            return $this->sendJson('');
        }
    }

    public function adminNotice(): void
    {
        if (get_transient(WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE_KEY)) {
            $message = sprintf(
                '%s - <a href="%s">%s</a>',
                WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE,
                get_permalink(get_page_by_path(WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE)),
                WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE
            )
            ?>
                <div class="notice-info notice is-dismissible">
                    <p>
                        <?php echo __($message) ?>
                    </p>
                </div>
            <?php
            delete_transient(WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE_KEY);
        }
    }

    public function addShortCode(array $attr): string
    {
        try {
            $attr = shortcode_atts([
                'uri' => WORDPRESS_TYPE_CODE_BASE_URL,
            ], $attr);
            $data = $this->getResourceData($attr['uri']);
            return $this->loadTemplate('users', json_decode($data));
        } catch (HandlerException $exception) {
            $this->logger->error($exception->getMessage());
            return '';
        }
    }

    private function getResourceData(string $uri, string $arg = ''): string
    {
        switch ($arg) {
            case '':
                $url = sprintf('%s/%s', $uri, static::BASE_ENDPOINT);
                break;
            default:
                $url = sprintf('%s/%s/%s', $uri, static::BASE_ENDPOINT, $arg);
        }
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            throw new HandlerException($response->get_error_message());
        }
        return wp_remote_retrieve_body($response);
    }

    private function loadTemplate(string $templateName, array $data = []): string
    {
        set_query_var(WORDPRESS_TYPE_CODE_QUERY_VAR, $this->getFromCache(static::CACHE_KEY, $data));
        ob_start();
        include sprintf(
            '%s/%s/%s%s',
            WORDPRESS_TYPE_CODE_ROOT_DIR,
            static::BASE_TEMPLATE_DIR,
            $templateName,
            static::BASE_TEMPLATE_EXTENSION
        );
        return ob_get_clean();
    }

    public function enqueueScripts(): void
    {
        $assetsPath = sprintf('%s%s', plugin_dir_url(WORDPRESS_TYPE_CODE_FILE), 'assets');
        wp_enqueue_script(
            static::MAIN_BOOTSTRAP_JS_NAMESPACE,
            sprintf('%s/%s', $assetsPath, 'js/bootstrap.min.js'),
            '',
            WORDPRESS_TYPE_CODE_VERSION,
            true
        );
        wp_enqueue_script(
            static::MAIN_JS_NAMESPACE,
            sprintf('%s/%s', $assetsPath, 'main.js'),
            '',
            WORDPRESS_TYPE_CODE_VERSION,
            true
        );
        wp_localize_script(static::MAIN_JS_NAMESPACE, static::MAIN_JS_NAMESPACE_KEY, [
            'ajaxurl'  => admin_url(static::ADMIN_AJAX_HANDLER),
            'nonce'    => wp_create_nonce(static::FRONTEND_NONCE_FIELD),
            'action'   => WORDPRESS_TYPE_CODE_AJAX_ENDPOINT,
            'nonceKey' => static::FRONTEND_NONCE_REQUEST,
            'userId'   => self::MAIN_JS_USER_ID_KEY,
        ]);
        wp_register_style(
            static::MAIN_BOOTSTRAP_CSS_NAMESPACE,
            sprintf('%s/%s', $assetsPath, 'css/bootstrap.min.css'),
            [],
            WORDPRESS_TYPE_CODE_VERSION
        );
        wp_enqueue_style(static::MAIN_BOOTSTRAP_CSS_NAMESPACE);
    }

    private function sendJson(string $data): string
    {
        return wp_send_json([
            'userDetail' => json_decode($data)
        ]);
    }

    private function setCache(string $key, array $data): void
    {
        set_transient($key, $data, static::CACHE_EXPIRED_SECONDS);
    }

    private function getFromCache(string $key, array $data): array
    {
        $result = get_transient($key);
        if (false === $result) {
            $this->setCache($key, $data);
            return $data;
        }
        return $result;
    }
}
