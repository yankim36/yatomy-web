# QR 코드 → 랜딩페이지 URL 매핑표

다이나믹 QR 관리 도구에서 각 QR의 목적지 URL만 아래 값으로 교체하면 됩니다.
`{도메인}`은 워드프레스 사이트 주소로 바꿔 넣으세요 (예: `https://yatomy.com`).

## 권장 매핑 (언어 자동 감지)

언어 없는 URL로 연결하면 스캔한 휴대폰의 언어 설정에 따라 EN / KR 페이지로 자동 이동합니다
(한국어 브라우저 → `/kr/`, 그 외 → `/en/`). 명함·티셔츠처럼 누가 스캔할지 모르는 매체에 적합합니다.

| QR | 용도 | 메시지 | 목적지 URL |
|---|---|---|---|
| QR 1 | 통합 (Combined) | AI Era, New Opportunity | `{도메인}/start/` |
| QR 2 | 소비자용 (Consumer) | Good Products, Fair Price | `{도메인}/products/` |
| QR 3 | 사업자용 (Business) | Free Lifetime Business | `{도메인}/business/` |

## 언어 고정 매핑

특정 언어권 대상 자료(예: 한인 커뮤니티용 전단)라면 언어를 고정한 URL을 사용하세요.

| QR | EN | KR |
|---|---|---|
| QR 1 통합 | `{도메인}/en/start/` | `{도메인}/kr/start/` |
| QR 2 소비자 | `{도메인}/en/products/` | `{도메인}/kr/products/` |
| QR 3 사업자 | `{도메인}/en/business/` | `{도메인}/kr/business/` |

각 페이지 상단의 언어 스위처(EN ↔ 한국어)로 언제든 다른 언어로 전환할 수 있습니다.

## 슬러그 변경 시

워드프레스 관리자 → 설정 → **Atomy QR Landing** 에서 슬러그를 바꾸면 위 URL도 함께 바뀝니다.
같은 화면 상단에 현재 도메인이 반영된 매핑표가 자동으로 표시되니, 그 값을 QR 도구에 복사하면 됩니다.

## 유입 추적 (선택)

QR별 유입을 구분하고 싶다면 QR 목적지 URL 뒤에 UTM 파라미터를 붙여도 페이지는 정상 동작합니다.

```
{도메인}/start/?utm_source=qr&utm_medium=offline&utm_campaign=business-card
{도메인}/start/?utm_source=qr&utm_medium=offline&utm_campaign=tshirt
```

GA4/GTM 스니펫은 설정 화면의 "Extra <head> HTML" 칸에 붙여 넣으면 됩니다.
CTA 버튼 클릭 시 `dataLayer`에 `atomy_cta_click` 이벤트(`cta`: hero / final / sticky)가 전송됩니다.
