<?php
/**
 * Pure-PHP HTML renderer for the landing pages.
 *
 * Deliberately has no WordPress dependencies so the same markup can be
 * rendered by the plugin (inside WordPress) and by bin/build-static.php
 * (for previews / static fallback deploys).
 *
 * @package Atomy_QR_Landing
 */

class Atomy_QR_Landing_Renderer {

	/** @var array Content array from includes/content.php */
	private $content;

	public function __construct( array $content ) {
		$this->content = $content;
	}

	/** HTML-escape a string. */
	public static function esc( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
	}

	/** Escape, then convert **text** into <strong>text</strong>. */
	public static function rich( $value ) {
		$escaped = self::esc( $value );
		return preg_replace( '/\*\*(.+?)\*\*/su', '<strong>$1</strong>', $escaped );
	}

	/**
	 * Render one page in one language.
	 *
	 * @param string $page_key start | products | business
	 * @param string $lang     en | kr
	 * @param array  $ctx {
	 *     @type string $cta_url      Easy Registration URL.
	 *     @type string $site_name    Brand shown top-left and in <title>.
	 *     @type string $home_url     Where the brand links to.
	 *     @type string $canonical    Absolute URL of this page ('' to omit).
	 *     @type array  $alternates   lang => absolute or relative URL of the same page.
	 *     @type string $og_image     Optional Open Graph image URL.
	 *     @type string $head_extra   Raw HTML injected before </head> (analytics etc.).
	 *     @type string $footer_text  Footer line ('' uses the default UI string).
	 *     @type string $notice       Raw HTML banner shown to admins ('' for none).
	 *     @type string $version      Cache-busting/version string.
	 * }
	 * @return string Full HTML document.
	 */
	public function render( $page_key, $lang, array $ctx ) {
		$page = $this->content['pages'][ $page_key ];
		$copy = $page[ $lang ];
		$ui   = $this->content['ui'][ $lang ];
		$lng  = $this->content['languages'][ $lang ];

		$ctx = array_merge(
			array(
				'cta_url'     => '#',
				'site_name'   => 'YAtomy',
				'home_url'    => '/',
				'canonical'   => '',
				'alternates'  => array(),
				'og_image'    => '',
				'head_extra'  => '',
				'footer_text' => '',
				'notice'      => '',
				'version'     => '',
			),
			$ctx
		);

		$e = array( __CLASS__, 'esc' );
		$r = array( __CLASS__, 'rich' );

		$title       = $copy['title'] . ' | ' . $ctx['site_name'];
		$footer_text = $ctx['footer_text'] !== '' ? $ctx['footer_text'] : $ui['footer'];
		$cta_url     = $ctx['cta_url'];

		/* ---- <head> pieces ------------------------------------------------ */
		$head = '';
		if ( $ctx['canonical'] !== '' ) {
			$head .= '<link rel="canonical" href="' . $e( $ctx['canonical'] ) . '">' . "\n";
		}
		foreach ( $ctx['alternates'] as $alt_lang => $alt_url ) {
			if ( ! isset( $this->content['languages'][ $alt_lang ] ) ) {
				continue;
			}
			$head .= '<link rel="alternate" hreflang="' . $e( $this->content['languages'][ $alt_lang ]['hreflang'] ) . '" href="' . $e( $alt_url ) . '">' . "\n";
		}
		if ( isset( $ctx['alternates']['en'] ) ) {
			$head .= '<link rel="alternate" hreflang="x-default" href="' . $e( $ctx['alternates']['en'] ) . '">' . "\n";
		}
		if ( $ctx['og_image'] !== '' ) {
			$head .= '<meta property="og:image" content="' . $e( $ctx['og_image'] ) . '">' . "\n";
		}
		if ( $ctx['canonical'] !== '' ) {
			$head .= '<meta property="og:url" content="' . $e( $ctx['canonical'] ) . '">' . "\n";
		}

		/* ---- language switcher ------------------------------------------- */
		$switcher = '';
		foreach ( $this->content['languages'] as $code => $l ) {
			if ( $code === $lang ) {
				$switcher .= '<span class="on" aria-current="page" lang="' . $e( $l['hreflang'] ) . '">' . $e( $l['label'] ) . '</span>';
			} elseif ( isset( $ctx['alternates'][ $code ] ) ) {
				$switcher .= '<a href="' . $e( $ctx['alternates'][ $code ] ) . '" hreflang="' . $e( $l['hreflang'] ) . '" lang="' . $e( $l['hreflang'] ) . '">' . $e( $l['label'] ) . '</a>';
			}
		}

		$other_lang = $lang === 'en' ? 'kr' : 'en';
		$footer_switch = '';
		if ( isset( $ctx['alternates'][ $other_lang ] ) ) {
			$footer_switch = '<p class="foot-switch"><a href="' . $e( $ctx['alternates'][ $other_lang ] ) . '" hreflang="' . $e( $this->content['languages'][ $other_lang ]['hreflang'] ) . '">' . $e( $ui['switch_to'] ) . '</a></p>';
		}

		/* ---- sections ------------------------------------------------------ */
		$sections = '';
		foreach ( $copy['sections'] as $i => $body ) {
			$n     = $i + 1;
			$label = isset( $ui['section_labels'][ $i ] ) ? $ui['section_labels'][ $i ] : '';
			$alt   = ( $n === 2 ) ? ' alt' : '';
			$sections .= '<section class="block' . $alt . '" id="section-' . $n . '" aria-labelledby="label-' . $n . '"><div class="wrap">'
				. '<p class="eyebrow" id="label-' . $n . '"><span class="num">0' . $n . '</span>' . $e( $label ) . '</p>'
				. '<p class="body">' . $r( $body ) . '</p>'
				. '</div></section>' . "\n";
		}

		$css = $this->css();
		$js  = $this->js();

		$html = '<!DOCTYPE html>
<html lang="' . $e( $lng['hreflang'] ) . '">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>' . $e( $title ) . '</title>
<meta name="description" content="' . $e( $copy['meta_description'] ) . '">
<meta name="theme-color" content="#1b4f9c">
<meta property="og:type" content="website">
<meta property="og:site_name" content="' . $e( $ctx['site_name'] ) . '">
<meta property="og:title" content="' . $e( $copy['title'] ) . '">
<meta property="og:description" content="' . $e( $copy['meta_description'] ) . '">
<meta property="og:locale" content="' . $e( $lng['og_locale'] ) . '">
<meta name="twitter:card" content="summary">
' . $head . '<style>' . $css . '</style>
' . $ctx['head_extra'] . '
</head>
<body class="lp lp-' . $e( $page_key ) . ' lang-' . $e( $lang ) . '">
' . $ctx['notice'] . '
<header class="top"><div class="wrap row">
<a class="brand" href="' . $e( $ctx['home_url'] ) . '">' . $e( $ctx['site_name'] ) . '</a>
<nav class="lang" aria-label="' . $e( $ui['language_nav'] ) . '">' . $switcher . '</nav>
</div></header>
<main id="main">
<section class="hero"><div class="wrap">
<h1>' . $r( $copy['headline'] ) . '</h1>
<p class="sub">' . $r( $copy['subheadline'] ) . '</p>
<a id="hero-cta" class="btn" href="' . $e( $cta_url ) . '" data-cta="hero">' . $e( $copy['cta_button'] ) . '</a>
<p class="btn-note">' . $e( $ui['sticky_note'] ) . '</p>
</div></section>
' . $sections . '<section class="cta-final" id="register"><div class="wrap">
<h2>' . $r( $copy['cta_text'] ) . '</h2>
<a id="final-cta" class="btn" href="' . $e( $cta_url ) . '" data-cta="final">' . $e( $copy['cta_button'] ) . '</a>
<p class="btn-note">' . $e( $ui['sticky_note'] ) . '</p>
</div></section>
</main>
<footer class="foot"><div class="wrap">
<p>' . $e( $footer_text ) . '</p>
' . $footer_switch . '
</div></footer>
<div id="sticky-cta" class="sticky" aria-hidden="true"><div class="wrap">
<a class="btn" href="' . $e( $cta_url ) . '" data-cta="sticky" tabindex="-1">' . $e( $copy['cta_button'] ) . '</a>
</div></div>
<script>' . $js . '</script>
</body>
</html>
';
		return $html;
	}

	/** Critical CSS, inlined so the page needs no extra requests. */
	public function css() {
		return <<<'CSS'
:root{--blue:#1b4f9c;--blue-dark:#143c78;--blue-soft:#e8effa;--navy:#0f1f3d;--ink:#22304a;--muted:#5b6678;--bg:#fff;--bg-soft:#f4f7fb;--line:#dfe6ef;--radius:12px}
*{box-sizing:border-box}
html{-webkit-text-size-adjust:100%;scroll-behavior:smooth}
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Apple SD Gothic Neo","Noto Sans KR","Malgun Gothic",sans-serif;color:var(--ink);background:var(--bg);line-height:1.65;font-size:17px;word-break:keep-all;overflow-wrap:break-word}
.wrap{max-width:680px;margin:0 auto;padding-left:20px;padding-right:20px}
.row{display:flex;align-items:center;justify-content:space-between;gap:12px}
.top{padding:14px 0;border-bottom:1px solid var(--line)}
.brand{font-weight:800;color:var(--navy);text-decoration:none;font-size:17px;letter-spacing:.01em}
.lang{display:inline-flex;border:1px solid var(--line);border-radius:999px;overflow:hidden;font-size:13px;line-height:1}
.lang a,.lang span{padding:8px 13px;text-decoration:none;color:var(--muted);font-weight:600}
.lang a:hover{background:var(--bg-soft);color:var(--navy)}
.lang .on{background:var(--blue);color:#fff}
.hero{padding:36px 0 34px}
.hero h1{font-size:clamp(28px,7.2vw,40px);line-height:1.22;margin:0 0 14px;color:var(--navy);letter-spacing:-.015em;font-weight:800}
.hero .sub{font-size:19px;line-height:1.55;color:var(--muted);margin:0 0 26px}
.btn{display:flex;justify-content:center;align-items:center;width:100%;min-height:56px;padding:14px 22px;border-radius:var(--radius);background:var(--blue);color:#fff;font-weight:700;font-size:17px;text-decoration:none;text-align:center;box-shadow:0 6px 18px rgba(27,79,156,.22);transition:background .15s,transform .15s}
.btn:hover{background:var(--blue-dark)}
.btn:active{transform:translateY(1px)}
.btn:focus-visible{outline:3px solid #9ec0f0;outline-offset:2px}
.btn-note{font-size:13px;color:var(--muted);margin:10px 0 0;text-align:center}
.block{padding:34px 0}
.block.alt{background:var(--bg-soft);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
.eyebrow{display:flex;align-items:center;gap:10px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--blue);font-weight:700;margin:0 0 12px}
.eyebrow .num{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 6px;border-radius:8px;background:var(--blue-soft);color:var(--blue);font-size:12px;letter-spacing:0}
.body{margin:0;font-size:18px;line-height:1.7}
.body strong{color:var(--navy)}
.cta-final{padding:44px 0 40px;text-align:center;border-top:1px solid var(--line)}
.cta-final h2{font-size:clamp(22px,5.6vw,28px);line-height:1.3;margin:0 0 22px;color:var(--navy);letter-spacing:-.01em}
.foot{padding:24px 0 112px;font-size:13px;color:var(--muted);text-align:center}
.foot p{margin:0 0 8px}
.foot a{color:var(--blue)}
.sticky{position:fixed;left:0;right:0;bottom:0;z-index:50;background:rgba(255,255,255,.96);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border-top:1px solid var(--line);padding:10px 0 calc(10px + env(safe-area-inset-bottom));transform:translateY(110%);transition:transform .25s ease}
.sticky.show{transform:none}
.sticky .btn{min-height:50px;box-shadow:none}
.notice{background:#fff4d6;color:#6b4d00;border-bottom:1px solid #f0d58a;font-size:14px;padding:10px 0}
.notice .wrap{display:block}
.notice a{color:inherit;font-weight:700}
@media (min-width:720px){.hero{padding:56px 0 48px}.btn{width:auto;min-width:300px;margin:0 auto}.hero .btn{margin:0}.block{padding:40px 0}.foot{padding-bottom:40px}.sticky{display:none}}
@media (prefers-reduced-motion:reduce){html{scroll-behavior:auto}.sticky{transition:none}}
CSS;
	}

	/** Tiny inline script: shows the sticky CTA only while no other CTA is on screen. */
	public function js() {
		return <<<'JS'
(function(){var s=document.getElementById('sticky-cta'),h=document.getElementById('hero-cta'),f=document.getElementById('final-cta');if(!s||!h||!f)return;var vis={hero:true,fin:false};function upd(){var show=!vis.hero&&!vis.fin;s.classList.toggle('show',show);s.setAttribute('aria-hidden',show?'false':'true');s.querySelector('a').tabIndex=show?0:-1;}if(!('IntersectionObserver' in window)){vis.hero=false;upd();return;}var io=new IntersectionObserver(function(es){es.forEach(function(en){if(en.target===h)vis.hero=en.isIntersecting;if(en.target===f)vis.fin=en.isIntersecting;});upd();},{threshold:0});io.observe(h);io.observe(f);document.addEventListener('click',function(ev){var a=ev.target.closest&&ev.target.closest('[data-cta]');if(a&&window.dataLayer)window.dataLayer.push({event:'atomy_cta_click',cta:a.getAttribute('data-cta'),page:document.body.className});});})();
JS;
	}
}
