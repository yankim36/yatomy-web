# YAtomy — Atomy QR 랜딩페이지

애터미(Atomy) 오프라인 홍보용 QR코드 3종에 연결되는 랜딩페이지 3개(EN/KR, 총 6개 URL)를
워드프레스 플러그인 하나로 제공합니다. 테마와 무관하게 동작하며, 페이지는 인라인 CSS/JS만 사용하는
단일 HTML 응답(약 9 KB, 외부 요청 0건)이라 QR 스캔 직후 모바일에서 즉시 뜹니다.

| 구성 | 경로 |
|---|---|
| 워드프레스 플러그인 | `wp-content/plugins/atomy-qr-landing/` |
| 페이지 콘텐츠 (EN/KR 원고) | `wp-content/plugins/atomy-qr-landing/includes/content.php` |
| HTML 렌더러 (디자인/CSS/JS) | `wp-content/plugins/atomy-qr-landing/includes/class-renderer.php` |
| 관리자 설정 화면 | `wp-content/plugins/atomy-qr-landing/includes/class-settings.php` |
| 정적 미리보기 빌드 | `bin/build-static.php` → `dist/` |
| QR ↔ URL 매핑표 | [`docs/qr-mapping.md`](docs/qr-mapping.md) |

## 페이지 및 URL

| 페이지 | QR | EN | KR | 자동 감지 |
|---|---|---|---|---|
| AI Era, New Opportunity | QR 1 통합 | `/en/start/` | `/kr/start/` | `/start/` |
| Good Products, Fair Price | QR 2 소비자 | `/en/products/` | `/kr/products/` | `/products/` |
| Free Lifetime Business | QR 3 사업자 | `/en/business/` | `/kr/business/` | `/business/` |

`/start/`처럼 언어가 없는 URL은 브라우저 언어(Accept-Language)를 보고 `/kr/` 또는 `/en/`으로 302 리다이렉트합니다.
슬러그는 설정 화면에서 변경할 수 있습니다.

각 페이지 구성 (스펙 순서 그대로): Headline → Subheadline → CTA(첫 화면) → 01 문제 → 02 기회 → 03 신뢰/차별점 → CTA(하단).
모바일에서는 첫 화면 CTA와 하단 CTA가 모두 화면 밖일 때만 하단 고정(sticky) CTA가 나타납니다.

## Hostinger 설치

1. 이 저장소의 `wp-content/plugins/atomy-qr-landing` 폴더를 zip으로 압축합니다.
   ```bash
   cd wp-content/plugins && zip -r atomy-qr-landing.zip atomy-qr-landing
   ```
2. 워드프레스 관리자 → 플러그인 → 새로 추가 → 플러그인 업로드 → zip 업로드 후 **활성화**.
   (또는 Hostinger 파일 관리자/FTP로 `wp-content/plugins/`에 폴더째 업로드)
3. 설정 → 고유주소(Permalinks)가 "일반(Plain)"이 아닌지 확인합니다. Plain이면 "글 이름"으로 바꾸고 저장.
4. (선택) 설정 → **Atomy QR Landing** 에서 슬러그, 브랜드명, 분석 스니펫 등을 조정합니다.
   Easy Registration 링크(`https://us.atomy.com/gate/join/easyreg/v2/22457174`)는 플러그인에 기본값으로 들어 있어 별도 입력이 필요 없습니다.
5. `/en/start/`, `/kr/start/` 등 6개 URL을 휴대폰으로 열어 확인한 뒤, `docs/qr-mapping.md`대로 QR 목적지를 교체합니다.

Hostinger LiteSpeed 캐시를 쓰는 경우: 언어별 URL(`/en/...`, `/kr/...`)은 캐시해도 안전합니다.
언어 자동 감지 URL(`/start/` 등)은 `Cache-Control: no-store` + `Vary: Accept-Language`를 보내므로 캐시에 저장되지 않습니다.
플러그인 설정을 바꾼 뒤 변화가 보이지 않으면 LiteSpeed 캐시를 한 번 비워 주세요.

## 설정 항목 (설정 → Atomy QR Landing)

- **Easy Registration URL** — 비워 두면 기본값 `https://us.atomy.com/gate/join/easyreg/v2/22457174`. 다른 링크로 바꾸거나 페이지별로 다르게 하려면 여기에 입력.
- **페이지 슬러그** — 기본값 `start` / `products` / `business`. 화면에 현재 도메인 기준 URL이 표시됨.
- **언어 자동 감지** — 끄면 `/start/`가 항상 `/en/`으로 이동.
- **브랜드명 / 브랜드 링크** — 상단 좌측 로고 텍스트(기본 `YAtomy`)와 링크.
- **푸터 문구 (EN/KR)** — 기본값 "This page is operated by an independent Atomy member." / "본 페이지는 애터미 독립 회원이 운영합니다."
- **og:image** — 카카오톡/페이스북 공유 시 썸네일 (1200×630 권장).
- **Extra `<head>` HTML** — GA4/GTM/픽셀 스니펫. CTA 클릭 시 `dataLayer`에 `atomy_cta_click` 이벤트 전송.

개발자용 필터: `atomy_qrl_cta_url( $url, $page_key )`, `atomy_qrl_render_context( $ctx, $page_key, $lang )`.

## 원고 수정

모든 문구는 `includes/content.php` 한 파일에 있습니다. `**굵게**` 표기만 지원하며 그 외 HTML은 이스케이프됩니다.
제목(`title`)과 메타 설명(`meta_description`)도 여기서 언어별로 관리합니다.

## 로컬 미리보기 (워드프레스 없이)

```bash
php bin/build-static.php                 # dist/ 에 6개 페이지 + index.html 생성
npx serve dist                           # 또는 python3 -m http.server -d dist 8000
```

실제 도메인을 넣어 정적 HTML로 뽑을 수도 있습니다 (정적 호스팅 대체 배포용, `--cta=`로 링크 교체 가능):

```bash
php bin/build-static.php --base=https://yatomy.com
```

## 디자인

- 모바일 우선, 최대 폭 680px, 시스템 폰트(한글은 Apple SD Gothic Neo / Noto Sans KR / 맑은 고딕 순).
- 절제된 블루/화이트: 기본 `#1b4f9c`, 네이비 텍스트 `#0f1f3d`, 연한 배경 `#f4f7fb`. 값은 렌더러의 `:root` 변수에서 한 번에 변경 가능.
- 내비게이션은 브랜드명 + 언어 스위처뿐이며 다른 링크는 없습니다(이탈 최소화).
- SEO: 언어별 `<title>`/description, canonical, `hreflang`(en / ko / x-default), Open Graph.

## Yan 확인 필요 사항

- [x] **Easy Registration 링크** — `https://us.atomy.com/gate/join/easyreg/v2/22457174` (플러그인 기본값으로 반영)
- [x] **최종 슬러그** — `start` / `products` / `business` 확정 (필요 시 설정에서 변경 가능)
- [x] **색상** — 블루/화이트 제안값으로 확정 (추후 변경 시 `:root` 변수만 교체)
- [ ] **도메인 구조** — 현재 서브경로(`/en/`, `/kr/`) 방식. 서브도메인이 필요하면 별도 설정 필요
- [x] **푸터 문구** — 기본 문구("독립 회원이 운영") 유지
- [ ] **og:image** — 공유 썸네일 이미지 (선택, 1200×630)
