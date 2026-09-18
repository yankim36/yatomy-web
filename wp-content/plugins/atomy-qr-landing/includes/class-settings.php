<?php
/**
 * Settings screen: Settings → Atomy QR Landing.
 *
 * @package Atomy_QR_Landing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomy_QR_Landing_Settings {

	const PAGE = 'atomy-qr-landing';

	/** @var Atomy_QR_Landing */
	private $plugin;

	public function __construct( Atomy_QR_Landing $plugin ) {
		$this->plugin = $plugin;
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( ATOMY_QRL_FILE ), array( $this, 'action_links' ) );
	}

	public function menu() {
		add_options_page(
			__( 'Atomy QR Landing', 'atomy-qr-landing' ),
			__( 'Atomy QR Landing', 'atomy-qr-landing' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render' )
		);
	}

	public function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Settings', 'atomy-qr-landing' ) . '</a>' );
		return $links;
	}

	public function register() {
		register_setting(
			'atomy_qrl',
			ATOMY_QRL_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => Atomy_QR_Landing::defaults(),
			)
		);

		add_settings_section( 'cta', __( 'Call to action (Easy Registration)', 'atomy-qr-landing' ), array( $this, 'section_cta' ), self::PAGE );
		$this->field( 'cta', 'cta_url', __( 'Easy Registration URL (all pages)', 'atomy-qr-landing' ), 'url', sprintf( __( 'The link every CTA button opens. Empty = built-in default <code>%s</code>. Leave the per-page fields empty unless one page needs a different link.', 'atomy-qr-landing' ), esc_html( ATOMY_QRL_DEFAULT_CTA_URL ) ) );
		foreach ( $this->plugin->content()['pages'] as $key => $page ) {
			/* translators: %s: QR label, e.g. "QR 1 — Combined" */
			$this->field( 'cta', 'cta_url_' . $key, sprintf( __( 'Override for %s', 'atomy-qr-landing' ), $page['qr'] ), 'url' );
		}

		add_settings_section( 'urls', __( 'Page slugs', 'atomy-qr-landing' ), array( $this, 'section_urls' ), self::PAGE );
		foreach ( $this->plugin->content()['pages'] as $key => $page ) {
			$this->field( 'urls', 'slug_' . $key, $page['qr'], 'slug', $this->slug_help( $key ) );
		}
		$this->field( 'urls', 'detect_language', __( 'Language auto-detect', 'atomy-qr-landing' ), 'checkbox', __( 'When on, the language-neutral URL (e.g. /start/) sends Korean browsers to /kr/ and everyone else to /en/. When off it always goes to /en/.', 'atomy-qr-landing' ) );

		add_settings_section( 'brand', __( 'Branding & footer', 'atomy-qr-landing' ), '__return_null', self::PAGE );
		$this->field( 'brand', 'site_name', __( 'Brand name', 'atomy-qr-landing' ), 'text', __( 'Shown top-left and appended to the page title.', 'atomy-qr-landing' ) );
		$this->field( 'brand', 'home_url', __( 'Brand link URL', 'atomy-qr-landing' ), 'url', __( 'Where the brand name links to. Empty = this site\'s home page.', 'atomy-qr-landing' ) );
		$this->field( 'brand', 'footer_en', __( 'Footer line (EN)', 'atomy-qr-landing' ), 'text', __( 'Empty = "This page is operated by an independent Atomy member."', 'atomy-qr-landing' ) );
		$this->field( 'brand', 'footer_kr', __( 'Footer line (KR)', 'atomy-qr-landing' ), 'text', __( 'Empty = "본 페이지는 애터미 독립 회원이 운영합니다."', 'atomy-qr-landing' ) );
		$this->field( 'brand', 'og_image', __( 'Share image URL (og:image)', 'atomy-qr-landing' ), 'url', __( 'Optional. 1200×630 recommended. Shown when a link is shared on KakaoTalk, Facebook, etc.', 'atomy-qr-landing' ) );

		add_settings_section( 'advanced', __( 'Tracking / advanced', 'atomy-qr-landing' ), '__return_null', self::PAGE );
		$this->field( 'advanced', 'head_extra', __( 'Extra <head> HTML', 'atomy-qr-landing' ), 'textarea', __( 'Analytics snippets (GA4, GTM, Meta Pixel...). Pasted as-is into every landing page. CTA clicks push an "atomy_cta_click" event to dataLayer when GTM is present.', 'atomy-qr-landing' ) );
	}

	private function slug_help( $key ) {
		$en = $this->plugin->url( $key, 'en' );
		$kr = $this->plugin->url( $key, 'kr' );
		$nt = $this->plugin->neutral_url( $key );
		return sprintf(
			'<a href="%1$s" target="_blank" rel="noopener">%1$s</a><br><a href="%2$s" target="_blank" rel="noopener">%2$s</a><br><a href="%3$s" target="_blank" rel="noopener">%3$s</a> (auto-detect)',
			esc_url( $en ),
			esc_url( $kr ),
			esc_url( $nt )
		);
	}

	private function field( $section, $id, $label, $type, $help = '' ) {
		add_settings_field(
			$id,
			$label,
			array( $this, 'render_field' ),
			self::PAGE,
			$section,
			array(
				'id'        => $id,
				'type'      => $type,
				'help'      => $help,
				'label_for' => 'atomy_qrl_' . $id,
			)
		);
	}

	public function section_cta() {
		echo '<p>' . esc_html__( 'The Atomy Easy Registration link is built in, so nothing needs to be entered here. Fill these fields only to override it (for example with a different sponsor link).', 'atomy-qr-landing' ) . '</p>';
	}

	public function section_urls() {
		echo '<p>' . esc_html__( 'Each page is served at /en/{slug}/ and /kr/{slug}/. Point each QR code at one of these URLs. Changing a slug updates the URLs immediately; update the dynamic QR codes to match.', 'atomy-qr-landing' ) . '</p>';
	}

	public function render_field( $args ) {
		$s     = $this->plugin->settings();
		$id    = $args['id'];
		$name  = ATOMY_QRL_OPTION . '[' . $id . ']';
		$dom   = 'atomy_qrl_' . $id;
		$value = isset( $s[ $id ] ) ? $s[ $id ] : '';

		switch ( $args['type'] ) {
			case 'checkbox':
				printf( '<label><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label>', esc_attr( $dom ), esc_attr( $name ), checked( ! empty( $value ), true, false ), esc_html__( 'Enabled', 'atomy-qr-landing' ) );
				break;
			case 'textarea':
				printf( '<textarea id="%s" name="%s" rows="6" class="large-text code">%s</textarea>', esc_attr( $dom ), esc_attr( $name ), esc_textarea( $value ) );
				break;
			case 'url':
				printf( '<input type="url" id="%s" name="%s" value="%s" class="regular-text code" placeholder="https://">', esc_attr( $dom ), esc_attr( $name ), esc_attr( $value ) );
				break;
			case 'slug':
				printf( '<input type="text" id="%s" name="%s" value="%s" class="regular-text code">', esc_attr( $dom ), esc_attr( $name ), esc_attr( $value ) );
				break;
			default:
				printf( '<input type="text" id="%s" name="%s" value="%s" class="regular-text">', esc_attr( $dom ), esc_attr( $name ), esc_attr( $value ) );
		}
		if ( ! empty( $args['help'] ) ) {
			echo '<p class="description">' . wp_kses( $args['help'], array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ), 'br' => array(), 'code' => array() ) ) . '</p>';
		}
	}

	public function sanitize( $input ) {
		$defaults = Atomy_QR_Landing::defaults();
		$old      = wp_parse_args( get_option( ATOMY_QRL_OPTION, array() ), $defaults );
		$input    = is_array( $input ) ? $input : array();
		$out      = array();

		foreach ( array( 'cta_url', 'cta_url_start', 'cta_url_products', 'cta_url_business', 'home_url', 'og_image' ) as $k ) {
			$out[ $k ] = isset( $input[ $k ] ) ? esc_url_raw( trim( $input[ $k ] ) ) : '';
		}
		foreach ( array( 'site_name', 'footer_en', 'footer_kr' ) as $k ) {
			$out[ $k ] = isset( $input[ $k ] ) ? sanitize_text_field( $input[ $k ] ) : '';
		}

		$used = array();
		foreach ( $this->plugin->content()['pages'] as $key => $page ) {
			$slug = isset( $input[ 'slug_' . $key ] ) ? sanitize_title( $input[ 'slug_' . $key ] ) : '';
			if ( '' === $slug || in_array( $slug, $used, true ) || in_array( $slug, $this->plugin->language_codes(), true ) ) {
				$slug = $page['default_slug'];
				add_settings_error( ATOMY_QRL_OPTION, 'slug_' . $key, sprintf( __( 'Slug for %s was invalid or duplicated and has been reset to its default.', 'atomy-qr-landing' ), $page['qr'] ) );
			}
			$used[]                = $slug;
			$out[ 'slug_' . $key ] = $slug;
		}

		$out['detect_language'] = empty( $input['detect_language'] ) ? 0 : 1;

		$head = isset( $input['head_extra'] ) ? (string) $input['head_extra'] : '';
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$head = wp_kses_post( $head );
		}
		$out['head_extra'] = $head;

		foreach ( $this->plugin->page_keys() as $key ) {
			if ( $out[ 'slug_' . $key ] !== $old[ 'slug_' . $key ] ) {
				update_option( ATOMY_QRL_FLUSH_FLAG, 1 );
				break;
			}
		}

		return $out;
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$pages = $this->plugin->content()['pages'];
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Atomy QR Landing Pages', 'atomy-qr-landing' ); ?></h1>

			<h2><?php esc_html_e( 'QR code → URL mapping', 'atomy-qr-landing' ); ?></h2>
			<p><?php esc_html_e( 'Paste these into your dynamic QR code manager. The auto-detect URL picks EN or KR from the phone\'s language; the fixed URLs always show one language.', 'atomy-qr-landing' ); ?></p>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr>
					<th><?php esc_html_e( 'QR code', 'atomy-qr-landing' ); ?></th>
					<th><?php esc_html_e( 'Auto-detect URL (recommended)', 'atomy-qr-landing' ); ?></th>
					<th>EN</th>
					<th>KR</th>
				</tr></thead>
				<tbody>
				<?php foreach ( $pages as $key => $page ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $page['qr'] ); ?></strong><br><small><?php echo esc_html( $page['en']['title'] ); ?></small></td>
						<td><code><?php echo esc_html( $this->plugin->neutral_url( $key ) ); ?></code></td>
						<td><a href="<?php echo esc_url( $this->plugin->url( $key, 'en' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $this->plugin->url( $key, 'en' ) ); ?></a></td>
						<td><a href="<?php echo esc_url( $this->plugin->url( $key, 'kr' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $this->plugin->url( $key, 'kr' ) ); ?></a></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'atomy_qrl' );
				do_settings_sections( self::PAGE );
				submit_button();
				?>
			</form>
			<p class="description"><?php esc_html_e( 'If a URL shows the theme\'s 404 page after changing slugs, open Settings → Permalinks and press Save once to refresh rewrite rules.', 'atomy-qr-landing' ); ?></p>
		</div>
		<?php
	}
}
