<?php

namespace Wordpress\Type\Code\Base;

use Psr\Log\LoggerInterface;
use Wordpress\Type\Code\HandlerInterface;
use Wordpress\Type\Code\PluginInterface;
use Wordpress\Type\Code\LoaderInterface;
use Wordpress\Type\Code\Exception\Plugin as PluginException;
use Throwable;
use TypeError;

class Plugin implements PluginInterface
{
    private LoaderInterface $loader;
    private HandlerInterface $handler;
    private LoggerInterface $logger;

    public function __construct(LoaderInterface $loader, HandlerInterface $handler, LoggerInterface $logger)
    {
        $this->loader = $loader;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    public function run(): void
    {
        $this->pluginActivate();
        $this->pluginDeactivate();
        $this->addShortCode();
        $this->defineHooks();
    }

    private function pluginActivate(): void
    {
        register_activation_hook(WORDPRESS_TYPE_CODE_FILE, [$this, 'createPage']);
    }

    private function pluginDeactivate(): void
    {
        register_deactivation_hook(WORDPRESS_TYPE_CODE_FILE, [$this, 'removePage']);
    }

    private function addShortCode(): void
    {
        add_shortcode(WORDPRESS_TYPE_CODE_SHORT_CODE, [$this->handler, 'addShortCode']);
    }

    /**
     * @throws Throwable
     */
    public function createPage(): void
    {
        $params = [
            'post_title'    => WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE,
            'post_content'  => sprintf(
                '<!-- wp:shortcode -->%s%s uri=%s %s<!-- /wp:shortcode -->',
                '[',
                WORDPRESS_TYPE_CODE_SHORT_CODE,
                WORDPRESS_TYPE_CODE_BASE_URL,
                ']'
            ),
            'post_status'   => WORDPRESS_TYPE_CODE_PAGE_STATUS,
            'post_author'   => WORDPRESS_TYPE_CODE_SHORT_PAGE_CREATOR_ID,
            'post_type'     => WORDPRESS_TYPE_CODE_SHORT_PAGE_TYPE,
        ];
        try {
            set_transient(WORDPRESS_TYPE_ADMIN_NOTICE_MESSAGE_KEY, true, WORDPRESS_TYPE_ADMIN_NOTICE_EXPIRED);
            $this->tryCreatePage($params);
        } catch (PluginException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function removePage(): void
    {
        try {
            if (! $post = $this->getPageByTitle(WORDPRESS_TYPE_CODE_SHORT_PAGE_TITLE)) {
                $this->throwException(new PluginException(__('Page not exist')));
            }
            if (! $this->tryDeletePage($post->ID)) {
                $this->throwException(new PluginException(__('Page cannot be delete')));
            }
        } catch (PluginException | TypeError $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    private function defineHooks(): void
    {
        $this->loader->addAction(
            'admin_notices',
            [$this->handler, 'adminNotice'],
            HandlerInterface::CALLBACK_PRIORITY,
            HandlerInterface::CALLBACK_ACCEPTED_ARGS
        );
        $this->loader->addAction(
            sprintf('wp_ajax_%s', WORDPRESS_TYPE_CODE_AJAX_ENDPOINT),
            [$this->handler, 'ajax'],
            HandlerInterface::CALLBACK_PRIORITY,
            HandlerInterface::CALLBACK_ACCEPTED_ARGS
        );
        $this->loader->addAction(
            sprintf('wp_ajax_nopriv_%s', WORDPRESS_TYPE_CODE_AJAX_ENDPOINT),
            [$this->handler, 'ajax'],
            HandlerInterface::CALLBACK_PRIORITY,
            HandlerInterface::CALLBACK_ACCEPTED_ARGS
        );
        $this->loader->addAction(
            'wp_enqueue_scripts',
            [$this->handler, 'enqueueScripts'],
            HandlerInterface::CALLBACK_PRIORITY,
            HandlerInterface::CALLBACK_ACCEPTED_ARGS
        );
        $this->loader->run();
    }

    private function throwException(Throwable $throwable): void
    {
        throw new $throwable(__($throwable->getMessage()));
    }

    private function tryCreatePage(array $params): void
    {
        $result = wp_insert_post($params);
        if (is_wp_error($result)) {
            $this->throwException(new PluginException(__($result->get_error_message())));
        }
    }

    private function getPageByTitle(string $title): object
    {
        $post = get_page_by_title($title);
        if (! $post) {
            return new \stdClass();
        }
        return $post;
    }

    private function tryDeletePage(int $postId): bool
    {
        return (bool) wp_delete_post($postId, true);
    }
}
