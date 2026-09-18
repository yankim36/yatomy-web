<?php
/**
 * Plugin Name:       Atomy QR Landing Pages
 * Plugin URI:        https://github.com/yankim36/yatomy-web
 * Description:       Three mobile-first, bilingual (EN/KR) funnel landing pages for Atomy QR codes, served at /en/{slug}/ and /kr/{slug}/ with a shared Easy Registration CTA.
 * Version:           1.0.0
 * Author:            YAtomy
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * Text Domain:       atomy-qr-landing
 *
 * @package Atomy_QR_Landing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ATOMY_QRL_VERSION', '1.0.0' );
define( 'ATOMY_QRL_FILE', __FILE__ );
define( 'ATOMY_QRL_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATOMY_QRL_OPTION', 'atomy_qrl_settings' );
define( 'ATOMY_QRL_FLUSH_FLAG', 'atomy_qrl_flush_rewrites' );
define( 'ATOMY_QRL_CTA_PLACEHOLDER', 'https://REPLACE-WITH-ATOMY-EASY-REGISTRATION-LINK' );

require_once ATOMY_QRL_DIR . 'includes/class-renderer.php';
require_once ATOMY_QRL_DIR . 'includes/class-settings.php';

final class Atomy_QR_Landing {

	/** @var Atomy_QR_Landing */
	private static $instance = null;

	/** @var array */
	private $content;

	/** @var array|null */
	private $settings_cache = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->content = require ATOMY_QRL_DIR . 'includes/content.php';

		add_action( 'init', array( $this, 'register_rewrites' ) );
		add_action( 'init', array( $this, 'maybe_flush_rewrites' ), 99 );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'template_redirect', array( $this, 'maybe_render' ), 0 );

		if ( is_admin() ) {
			new Atomy_QR_Landing_Settings( $this );
		}
	}

	/* ---------------------------------------------------------------------
	 * Content / settings accessors
	 * ------------------------------------------------------------------ */

	public function content() {
		return $this->content;
	}

	public function page_keys() {
		return array_keys( $this->content['pages'] );
	}

	public function language_codes() {
		return array_keys( $this->content['languages'] );
	}

	public static function defaults() {
		return array(
			'cta_url'          => '',
			'cta_url_start'    => '',
			'cta_url_products' => '',
			'cta_url_business' => '',
			'slug_start'       => 'start',
			'slug_products'    => 'products',
			'slug_business'    => 'business',
			'site_name'        => 'YAtomy',
			'home_url'         => '',
			'footer_en'        => '',
			'footer_kr'        => '',
			'og_image'         => '',
			'head_extra'       => '',
			'detect_language'  => 1,
		);
	}

	public function settings() {
		if ( null === $this->settings_cache ) {
			$saved                = get_option( ATOMY_QRL_OPTION, array() );
			$this->settings_cache = wp_parse_args( is_array( $saved ) ? $saved : array(), self::defaults() );
		}
		return $this->settings_cache;
	}

	public function slug_for( $page_key ) {
		$s    = $this->settings();
		$slug = isset( $s[ 'slug_' . $page_key ] ) ? sanitize_title( $s[ 'slug_' . $page_key ] ) : '';
		if ( '' === $slug ) {
			$slug = $this->content['pages'][ $page_key ]['default_slug'];
		}
		return $slug;
	}

	public function page_for_slug( $slug ) {
		foreach ( $this->page_keys() as $key ) {
			if ( $this->slug_for( $key ) === $slug ) {
				return $key;
			}
		}
		return null;
	}

	/** Public URL of a page in a language, e.g. https://site/en/start/ */
	public function url( $page_key, $lang ) {
		return home_url( '/' . $lang . '/' . $this->slug_for( $page_key ) . '/' );
	}

	/** Language-neutral URL that auto-redirects, e.g. https://site/start/ */
	public function neutral_url( $page_key ) {
		return home_url( '/' . $this->slug_for( $page_key ) . '/' );
	}

	/** Effective CTA URL for a page: per-page override → global → placeholder. */
	public function cta_url( $page_key ) {
		$s   = $this->settings();
		$url = '';
		if ( ! empty( $s[ 'cta_url_' . $page_key ] ) ) {
			$url = $s[ 'cta_url_' . $page_key ];
		} elseif ( ! empty( $s['cta_url'] ) ) {
			$url = $s['cta_url'];
		}
		if ( '' === $url ) {
			$url = ATOMY_QRL_CTA_PLACEHOLDER;
		}
		/**
		 * Filter the CTA URL per page (e.g. to append tracking parameters).
		 *
		 * @param string $url      Resolved CTA URL.
		 * @param string $page_key start | products | business
		 */
		return apply_filters( 'atomy_qrl_cta_url', $url, $page_key );
	}

	public function cta_is_placeholder() {
		$s = $this->settings();
		if ( ! empty( $s['cta_url'] ) ) {
			return false;
		}
		foreach ( $this->page_keys() as $key ) {
			if ( empty( $s[ 'cta_url_' . $key ] ) ) {
				return true;
			}
		}
		return false;
	}

	/* ---------------------------------------------------------------------
	 * Routing
	 * ------------------------------------------------------------------ */

	public function query_vars( $vars ) {
		$vars[] = 'atomy_page';
		$vars[] = 'atomy_lang';
		return $vars;
	}

	public function register_rewrites() {
		$slugs = array();
		foreach ( $this->page_keys() as $key ) {
			$slugs[] = preg_quote( $this->slug_for( $key ), '#' );
		}
		$slug_re = implode( '|', $slugs );
		$lang_re = implode( '|', array_map( 'preg_quote', $this->language_codes() ) );

		add_rewrite_rule(
			'^(' . $lang_re . ')/(' . $slug_re . ')/?$',
			'index.php?atomy_lang=$matches[1]&atomy_page=$matches[2]',
			'top'
		);
		add_rewrite_rule(
			'^(' . $slug_re . ')/?$',
			'index.php?atomy_page=$matches[1]',
			'top'
		);
	}

	/** Flush rewrite rules once after activation or a slug change. */
	public function maybe_flush_rewrites() {
		if ( get_option( ATOMY_QRL_FLUSH_FLAG ) ) {
			flush_rewrite_rules( false );
			delete_option( ATOMY_QRL_FLUSH_FLAG );
		}
	}

	public static function activate() {
		update_option( ATOMY_QRL_FLUSH_FLAG, 1 );
	}

	public static function deactivate() {
		flush_rewrite_rules( false );
	}

	/** Pick a language for the neutral URL from the browser's Accept-Language. */
	public function detect_language() {
		$s = $this->settings();
		if ( empty( $s['detect_language'] ) ) {
			return 'en';
		}
		$header = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) ) : '';
		if ( '' === $header ) {
			return 'en';
		}
		foreach ( explode( ',', $header ) as $part ) {
			$tag = trim( strtok( $part, ';' ) );
			if ( 'ko' === $tag || 0 === strpos( $tag, 'ko-' ) ) {
				return 'kr';
			}
			if ( 'en' === $tag || 0 === strpos( $tag, 'en-' ) ) {
				return 'en';
			}
		}
		return 'en';
	}

	/** Serve the landing page (bypassing the theme entirely) or redirect the neutral URL. */
	public function maybe_render() {
		$slug = get_query_var( 'atomy_page', '' );
		if ( '' === $slug ) {
			return;
		}
		$page_key = $this->page_for_slug( $slug );
		if ( null === $page_key ) {
			return;
		}

		$lang = get_query_var( 'atomy_lang', '' );
		if ( '' === $lang ) {
			nocache_headers();
			header( 'Vary: Accept-Language' );
			wp_safe_redirect( $this->url( $page_key, $this->detect_language() ), 302 );
			exit;
		}
		if ( ! isset( $this->content['languages'][ $lang ] ) ) {
			return;
		}

		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset', 'UTF-8' ) );

		echo $this->render_page( $page_key, $lang ); // phpcs:ignore WordPress.Security.EscapeOutput -- full document, escaped in renderer.
		exit;
	}

	public function render_page( $page_key, $lang ) {
		$s          = $this->settings();
		$alternates = array();
		foreach ( $this->language_codes() as $code ) {
			$alternates[ $code ] = $this->url( $page_key, $code );
		}

		$notice = '';
		if ( $this->cta_is_placeholder() && current_user_can( 'manage_options' ) ) {
			$notice = '<div class="notice"><div class="wrap">'
				. esc_html__( 'Admin only: the Easy Registration link is not set yet. The CTA buttons point to a placeholder.', 'atomy-qr-landing' )
				. ' <a href="' . esc_url( admin_url( 'options-general.php?page=atomy-qr-landing' ) ) . '">' . esc_html__( 'Set it now', 'atomy-qr-landing' ) . '</a>'
				. '</div></div>';
		}

		$ctx = array(
			'cta_url'     => $this->cta_url( $page_key ),
			'site_name'   => $s['site_name'] !== '' ? $s['site_name'] : 'YAtomy',
			'home_url'    => $s['home_url'] !== '' ? $s['home_url'] : home_url( '/' ),
			'canonical'   => $this->url( $page_key, $lang ),
			'alternates'  => $alternates,
			'og_image'    => $s['og_image'],
			'head_extra'  => (string) $s['head_extra'],
			'footer_text' => (string) $s[ 'footer_' . $lang ],
			'notice'      => $notice,
			'version'     => ATOMY_QRL_VERSION,
		);

		/**
		 * Filter the render context (CTA URL, site name, head HTML...) before output.
		 *
		 * @param array  $ctx
		 * @param string $page_key
		 * @param string $lang
		 */
		$ctx = apply_filters( 'atomy_qrl_render_context', $ctx, $page_key, $lang );

		$renderer = new Atomy_QR_Landing_Renderer( $this->content );
		return $renderer->render( $page_key, $lang, $ctx );
	}
}

register_activation_hook( __FILE__, array( 'Atomy_QR_Landing', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Atomy_QR_Landing', 'deactivate' ) );

Atomy_QR_Landing::instance();
