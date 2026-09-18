<?php
/**
 * Copy for the three Atomy QR landing pages, in English (en) and Korean (kr).
 *
 * Editing guide
 * - Each page has one entry under 'pages', keyed by an internal page key
 *   (start / products / business). The public slug is configurable in
 *   Settings → Atomy QR Landing; the key never changes.
 * - Every page has an 'en' and a 'kr' block with identical structure.
 * - Inline emphasis: wrap text in **double asterisks** to render <strong>.
 *   Everything else is escaped, so raw HTML is not allowed here.
 *
 * @package Atomy_QR_Landing
 */

return array(

	/*
	 * URL prefix => language settings.
	 * 'code'     is the URL segment (/en/..., /kr/...).
	 * 'hreflang' is the ISO code used in <html lang> and hreflang tags.
	 */
	'languages' => array(
		'en' => array(
			'code'      => 'en',
			'hreflang'  => 'en',
			'og_locale' => 'en_US',
			'label'     => 'EN',
			'name'      => 'English',
		),
		'kr' => array(
			'code'      => 'kr',
			'hreflang'  => 'ko',
			'og_locale' => 'ko_KR',
			'label'     => '한국어',
			'name'      => '한국어',
		),
	),

	/* Small UI strings that are not page-specific. */
	'ui' => array(
		'en' => array(
			'section_labels' => array( 'The problem', 'The opportunity', 'Why Atomy, why now' ),
			'language_nav'   => 'Language',
			'switch_to'      => 'View this page in 한국어',
			'footer'         => 'This page is operated by an independent Atomy member.',
			'sticky_note'    => 'Free · No inventory · No obligation',
		),
		'kr' => array(
			'section_labels' => array( '문제', '기회', '왜 애터미, 왜 지금' ),
			'language_nav'   => '언어',
			'switch_to'      => 'View this page in English',
			'footer'         => '본 페이지는 애터미 독립 회원이 운영합니다.',
			'sticky_note'    => '무료 · 재고 없음 · 의무 없음',
		),
	),

	'pages' => array(

		/* ------------------------------------------------------------------
		 * Page 1 — Combined QR: "AI Era, New Opportunity"
		 * ---------------------------------------------------------------- */
		'start' => array(
			'qr'           => 'QR 1 — Combined (통합)',
			'default_slug' => 'start',
			'en'           => array(
				'title'            => 'AI Is Changing Every Job. Are You Ready for What\'s Next?',
				'meta_description' => 'Automation is reshaping the job market. See how Atomy\'s free, lifetime registration lets you build income you control, starting with zero investment.',
				'headline'         => 'AI Is Changing Every Job. Are You Ready for What\'s Next?',
				'subheadline'      => 'While automation reshapes the job market, thousands are building income they control — starting with zero investment.',
				'sections'         => array(
					'Jobs are disappearing faster than ever. AI is automating tasks that used to guarantee job security. The question isn\'t if your industry will change — it\'s when. The people who thrive in this shift aren\'t waiting for permission. They\'re building something of their own.',
					'Atomy is a global consumer goods company built on one simple promise: **absolute quality, absolute price.** No markups, no gimmicks — just products people actually need, at prices that make sense. And because every member can also become a distributor, using Atomy naturally opens the door to building your own income stream, free of charge, for life.',
					'No franchise fees. No inventory. No pressure. Just a global company already trusted by millions, and a system designed so your effort compounds over time — not your job title.',
				),
				'cta_text'         => 'See it for yourself. Registration is free, for life.',
				'cta_button'       => 'Get Started — Free Registration',
			),
			'kr'           => array(
				'title'            => 'AI가 모든 직업을 바꾸고 있습니다. 다음을 준비하셨나요?',
				'meta_description' => '자동화가 일자리 지형을 바꾸는 지금, 무자본·평생 무료 등록으로 스스로 통제할 수 있는 수입을 만드는 방법을 확인하세요.',
				'headline'         => 'AI가 모든 직업을 바꾸고 있습니다. 다음을 준비하셨나요?',
				'subheadline'      => '자동화가 일자리 지형을 바꾸는 동안, 수많은 사람들이 무자본으로 스스로 통제할 수 있는 수입을 만들고 있습니다.',
				'sections'         => array(
					'일자리는 그 어느 때보다 빠르게 사라지고 있습니다. AI는 한때 안정적이라 여겨지던 업무들을 자동화하고 있죠. 문제는 \'내 업계가 바뀔 것인가\'가 아니라 \'언제 바뀔 것인가\'입니다. 이런 변화 속에서 앞서가는 사람들은 허락을 기다리지 않습니다. 스스로 무언가를 만들어갑니다.',
					'애터미는 하나의 단순한 약속 위에 세워진 글로벌 소비재 기업입니다: **절대품질, 절대가격.** 불필요한 마진도, 눈속임도 없이 사람들에게 꼭 필요한 제품을 합리적인 가격에 제공합니다. 그리고 모든 회원이 사업자가 될 수 있는 구조이기 때문에, 자연스럽게 나만의 평생 무료 수입 구조로 이어질 수 있습니다.',
					'가맹비도, 재고 부담도, 실적 압박도 없습니다. 이미 수백만 명이 신뢰하는 글로벌 기업과, 시간이 지날수록 노력이 복리로 쌓이는 시스템이 있을 뿐입니다.',
				),
				'cta_text'         => '직접 확인해보세요. 등록은 무료이며, 평생 유효합니다.',
				'cta_button'       => '무료로 시작하기',
			),
		),

		/* ------------------------------------------------------------------
		 * Page 2 — Consumer QR: "Good Products, Fair Price"
		 * ---------------------------------------------------------------- */
		'products' => array(
			'qr'           => 'QR 2 — Consumer (소비자용)',
			'default_slug' => 'products',
			'en'           => array(
				'title'            => 'Premium Quality. Honest Prices. No Catch.',
				'meta_description' => 'Atomy cuts out the middlemen: absolute quality, absolute price. Skincare, supplements and everyday essentials without the markup. Registration is free.',
				'headline'         => 'Premium Quality. Honest Prices. No Catch.',
				'subheadline'      => 'Discover the products thousands of families trust every day — without the markup.',
				'sections'         => array(
					'Big brands charge you for the name, not the quality. Somewhere between the factory and the shelf, the price triples — and you\'re the one paying for it.',
					'Atomy cuts out the middlemen. Every product is made to the highest standard and sold at the lowest fair price — that\'s the whole philosophy: **absolute quality, absolute price.** From skincare to health supplements to everyday essentials, it\'s quality you can trust at a price that actually makes sense.',
					'Millions of members worldwide already shop this way. Once you compare the quality and the price side by side, it\'s hard to go back.',
				),
				'cta_text'         => 'See the products for yourself — registration is free.',
				'cta_button'       => 'Browse & Register — Free',
			),
			'kr'           => array(
				'title'            => '프리미엄 품질, 정직한 가격. 숨겨진 마진은 없습니다.',
				'meta_description' => '중간 유통을 없앤 절대품질·절대가격. 스킨케어부터 건강기능식품, 생활필수품까지 불필요한 마진 없이 만나보세요. 등록은 무료입니다.',
				'headline'         => '프리미엄 품질, 정직한 가격. 숨겨진 마진은 없습니다.',
				'subheadline'      => '수많은 가정이 매일 신뢰하는 제품들을, 불필요한 마진 없이 만나보세요.',
				'sections'         => array(
					'대형 브랜드는 품질이 아니라 \'이름값\'을 받습니다. 공장에서 매장까지 오는 사이 가격은 몇 배로 뛰고, 그 부담은 고스란히 소비자의 몫이 됩니다.',
					'애터미는 중간 유통 과정을 없앴습니다. 모든 제품은 최고 수준으로 만들어지고, 공정한 최저가로 제공됩니다 — 이것이 바로 **절대품질, 절대가격**의 철학입니다. 스킨케어부터 건강기능식품, 생활필수품까지, 믿을 수 있는 품질을 납득할 수 있는 가격에 만나보세요.',
					'전 세계 수백만 명의 회원들이 이미 이런 방식으로 소비하고 있습니다. 품질과 가격을 직접 비교해보시면, 다시 예전 방식으로 돌아가기 어려울 겁니다.',
				),
				'cta_text'         => '제품을 직접 확인해보세요 — 등록은 무료입니다.',
				'cta_button'       => '둘러보고 무료 등록하기',
			),
		),

		/* ------------------------------------------------------------------
		 * Page 3 — Business QR: "Free Lifetime Business"
		 * ---------------------------------------------------------------- */
		'business' => array(
			'qr'           => 'QR 3 — Business (사업자용)',
			'default_slug' => 'business',
			'en'           => array(
				'title'            => 'Start a Business With Zero Investment. For Life.',
				'meta_description' => 'No franchise fee, no inventory, no risk. Atomy registration is free for life. Build income backed by a global company with a decades-long track record.',
				'headline'         => 'Start a Business With Zero Investment. For Life.',
				'subheadline'      => 'No franchise fee. No inventory. No risk. Just a proven system — and your effort.',
				'sections'         => array(
					'Starting a business usually means capital, risk, and years before you see a return. Most people never start — not because they lack ambition, but because the cost of entry is too high.',
					'Atomy removes that barrier completely. **Registration is free — for life.** There\'s no inventory to buy, no store to rent, no pressure to hit quotas. You build income by sharing products people already want, backed by a global company with a track record spanning decades.',
					'This isn\'t a side hustle with a ceiling. It\'s a system built so that your income can grow independent of your hours — the harder and smarter you build early, the more it compounds later.',
				),
				'cta_text'         => 'Your business starts with one free registration.',
				'cta_button'       => 'Start Free — Register Now',
			),
			'kr'           => array(
				'title'            => '무자본으로 시작하는 사업. 평생 무료입니다.',
				'meta_description' => '가맹비·재고·리스크 없이 시작하세요. 애터미 등록은 평생 무료입니다. 수십 년 역사의 글로벌 기업과 함께 수입을 만들어가세요.',
				'headline'         => '무자본으로 시작하는 사업. 평생 무료입니다.',
				'subheadline'      => '가맹비도, 재고도, 리스크도 없습니다. 검증된 시스템과 당신의 노력만 있으면 됩니다.',
				'sections'         => array(
					'보통 사업을 시작하려면 자본과 리스크, 그리고 수익을 보기까지의 긴 시간이 필요합니다. 많은 사람들이 시작조차 하지 못하는 이유는 의지가 없어서가 아니라, 진입 장벽이 너무 높기 때문입니다.',
					'애터미는 그 장벽을 완전히 없앴습니다. **등록은 무료이며, 평생 유효합니다.** 재고를 살 필요도, 매장을 임대할 필요도, 실적 압박도 없습니다. 이미 사람들이 원하는 제품을 함께 나누는 것만으로, 수십 년의 역사를 가진 글로벌 기업을 등에 업고 수입을 만들어갈 수 있습니다.',
					'한계가 있는 부업이 아닙니다. 근무 시간과 무관하게 수입이 성장할 수 있도록 설계된 시스템입니다 — 초반에 더 열심히, 더 스마트하게 쌓을수록, 그 결과는 시간이 지날수록 복리로 커집니다.',
				),
				'cta_text'         => '사업의 시작은 단 한 번의 무료 등록입니다.',
				'cta_button'       => '지금 무료로 시작하기',
			),
		),
	),
);
