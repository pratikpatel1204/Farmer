# RGM Mart App – Category → Sub Category → Products flow ✅ API ready

> Website par category kholne ka flow badla hai. App me bhi wahi flow chahiye. Ye file batati hai ki
> **API me kya badla (ho chuka hai, test kiya hua)** aur **app me screens kaise banani hain**.
>
> Base URL: `{{BASE}}/api/v1` · Header: `Accept: application/json` · Ye sab APIs **public** hain (login nahi chahiye).
> Pichli API changes (sub category field, image fallback, About): `docs/APP_API_CHANGES_2026_11_24.md`.

---

## 1. Website par flow (reference)

```
Category tap (jaise "Vegetables")
 │
 ├─ Category ki sub categories HAIN
 │    ├─ Upar: Sub category cards   [Hybrid Seeds · 4 listings] [Organic · 2 listings] …
 │    └─ Neeche: "Other listings in Vegetables"
 │               = sirf wo products jinki sub category NULL hai
 │
 │    Sub category card tap
 │      └─ Sirf us sub category ke products
 │         (People Have / People Want tabs, search, sort, pagination)
 │
 └─ Category ki koi sub category NAHI
      └─ Seedha saare products (pehle jaisa)
```

Rule:
- Jis product me **sub category select hai**, wo sirf **apni sub category** ki list me dikhega.
- Jis product ki **sub category `null`** hai, wo **main category** page par ("Other listings") dikhega.

Website URLs: `/category/vegetables` aur `/category/vegetables/hybrid-seeds`.

---

## 2. API changes (summary)

| Endpoint | Change |
|---|---|
| `GET /categories` | Har category me naya field **`sub_categories_count`** |
| `GET /categories/{slug}` | Naya query param **`sub_category`** (`none` / id / slug), naya param **`q`** (search). Response me naye keys **`sub_categories`**, **`without_sub_category_count`**, **`sub_category`**, **`filter`** |

**Breaking change: koi nahi.** `sub_category` param na bhejo to pehle jaisa **saare products** aate hain (purane APK ke liye).

---

## 3. `GET /categories`: `sub_categories_count`

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 12,
      "name": "Vegetables",
      "slug": "vegetables",
      "image": "https://…/uploads/categories/veg.png",
      "icon": "fa-carrot",
      "tint": "green",
      "counts": { "all": 6, "have": 4, "want": 2 },
      "sub_categories_count": 2
    }
  ]
}
```
- **`sub_categories_count`** = is category me kitni sub categories hain (product ho ya na ho).
- App iska use kar sakta hai: `> 0` ho to category tap par **Category screen (sub category cards ke saath)**,
  `0` ho to bhi wahi screen chalegi (bas cards nahi honge). Alag screen ki zarurat nahi, section 5 dekho.

---

## 4. `GET /categories/{slug}`

### Query params
| Param | Values | Default | Matlab |
|---|---|---|---|
| **`sub_category`** | *(na bhejo)* | | Category ke **saare** products (purana behaviour) |
| | **`none`** | | Sirf jin products ki **sub category `null`** hai → **Category screen** |
| | **`{id}`** ya **`{slug}`** (jaise `4` ya `hybrid-seeds`) | | Sirf **us sub category** ke products → **Sub Category screen** |
| `type` | `have` / `want` | sab | People Have / People Want tab |
| `sort` | `latest` / `oldest` / `name` | `latest` | |
| **`q`** | text | | **Naya.** Title / tags me search |
| `page`, `per_page` | number | | Pagination (pehle jaisa) |

> `{slug}` (category) ki jagah category ki `id` bhi chalti hai (pehle jaisa).

### Response
```json
{
  "success": true,
  "message": "OK",
  "data": [ { "id": 57, "title": "…", "type": "want", "image": "…", "sub_category": null, "…": "product card" } ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 12, "total": 3 },

  "category": { "id": 12, "name": "Vegetables", "slug": "vegetables", "image": "…", "icon": "fa-carrot", "tint": "green",
                "counts": { "all": 6, "have": 4, "want": 2 } },

  "sub_categories": [
    { "id": 4, "name": "Hybrid Seeds",  "slug": "hybrid-seeds",  "products_count": 2 },
    { "id": 5, "name": "Organic Seeds", "slug": "organic-seeds", "products_count": 1 }
  ],
  "without_sub_category_count": 3,
  "sub_category": null,
  "filter": "without_sub_category"
}
```

| Key | Type | Matlab |
|---|---|---|
| `data` | product card[] | Products (product card ka shape pehle jaisa, `docs/APP_API_CHANGES_2026_11_24.md` section 5) |
| `meta.total` | int | Is list me kitne products (pagination ke liye) |
| `category` | object | Category (pehle jaisa). `counts` = poori category ke (sub category ka farq nahi) |
| **`sub_categories`** | array | Is category ki **saari** sub categories, naam ke hisaab se A→Z. **Har response me aata hai** (chahe koi bhi `sub_category` param ho) |
| `sub_categories[].products_count` | int | Us sub category me kitne products. **`type` param ke hisaab se** (People Want tab par sirf want gine) |
| `sub_categories[].slug` | string | URL / param ke liye |
| **`without_sub_category_count`** | int | Bina sub category wale products kitne (`type` ke hisaab se) |
| **`sub_category`** | object \| null | Jo sub category select hai (`{id, name, slug}`), warna `null` |
| **`filter`** | string | `all` (param nahi) / `without_sub_category` (`none`) / `sub_category` (id / slug) |

### Errors
| Kab | Status | Body |
|---|---|---|
| Category nahi mili | `404` | `{ "success": false, "message": "Category not found." }` |
| Sub category nahi mili (ya kisi aur category ki hai) | `404` | `{ "success": false, "message": "Sub category not found." }` |

---

## 5. App screens

### 5.1 Category screen (category tap)
**Call:** `GET /categories/{slug}?sub_category=none&type={tab}&sort=…&q=…&page=1`

```
┌──────────────────────────────────┐
│ ← Vegetables                     │  title = category.name
│ [All] [People Have] [People Want]│  type tab
├──────────────────────────────────┤
│ Sub categories in Vegetables   2 │  ← sirf jab sub_categories.length > 0
│ ┌──────────────┐ ┌─────────────┐ │
│ │🥕 Hybrid     │ │🥕 Organic   │ │  grid: 2 column (tablet 3)
│ │  Seeds       │ │  Seeds      │ │  icon = category.icon, rang = category.tint
│ │  2 listings ›│ │  1 listing ›│ │  products_count == 0 → halka (opacity), par tap ho
│ └──────────────┘ └─────────────┘ │
├──────────────────────────────────┤
│ Other listings in Vegetables     │  ← heading sirf jab sub_categories.length > 0
│ 3 listings found                 │  meta.total
│ [product] [product] [product]    │  data (product cards), infinite scroll / pages
└──────────────────────────────────┘
```

| Halat | App kya dikhaye |
|---|---|
| `sub_categories` khali | Cards aur "Other listings" heading **mat** dikhao. Sirf product list (saare products, kyunki sab `null` hain) |
| `sub_categories` hain, `data` bhi hai | Cards + "Other listings in {category}" + products |
| `sub_categories` hain, `data` khali (`meta.total == 0`) | Cards + chhota message: **"Pick a sub category"** / "Listings in {category} are grouped in the sub categories above." (empty state "Nothing here yet" **nahi**) |
| `sub_categories` khali, `data` khali | Empty state: "Nothing here yet" + "Post a listing" button (pehle jaisa) |

- **Tab badla** (All / Have / Want) → same call `type` badal ke. `sub_categories[].products_count` bhi us tab ke hisaab se badlega.
- **Page 2+** (scroll) par `sub_categories` dobara aata hai. App sirf **page 1** wala use kare, cards dobara mat banao.

### 5.2 Sub Category screen (card tap)
**Call:** `GET /categories/{slug}?sub_category={sub.id}&type={tab}&sort=…&q=…&page=1`

```
┌──────────────────────────────────┐
│ ← Hybrid Seeds                   │  title = sub_category.name
│   Vegetables                     │  subtitle = category.name (breadcrumb)
│ [All] [People Have] [People Want]│  Category screen ka tab saath le jao
│ [Hybrid Seeds ✓] [Organic Seeds] │  (optional) chips: sub_categories, tap = switch
│ 2 listings found                 │
│ [product] [product]              │
└──────────────────────────────────┘
```
- `sub_category` ke liye **`id` bhejo** (slug bhi chalega, par id pakki hai).
- Empty (`meta.total == 0`): "Nothing here yet. No listings in {sub category} right now." + "Post a listing".
- Back → Category screen.
- (Optional) upar horizontal chips me `sub_categories` dikhao, taaki user bina back gaye doosri sub category par ja sake (website ke sidebar jaisa).

### 5.3 Search / sort / filter (dono screens)
`q`, `sort`, `type` dono screens par same chalte hain. Search box ka placeholder: "Search in {category / sub category name}".

---

## 6. Deep link / share (optional)

Website URL se app screen:

| Website URL | App screen | API call |
|---|---|---|
| `/category/vegetables` | Category screen | `GET /categories/vegetables?sub_category=none` |
| `/category/vegetables/hybrid-seeds` | Sub Category screen | `GET /categories/vegetables?sub_category=hybrid-seeds` |

Share karte waqt bhi yahi URL banao: `{web_url}/category/{category.slug}/{sub_category.slug}`
(`web_url` `GET /app/config` se milta hai).

---

## 7. Purana APK (backward compatibility)

| Purana app | Ab kya hota hai |
|---|---|
| `GET /categories/{slug}` **bina** `sub_category` | **Saare** products (pehle jaisa, sub category wale bhi) ✅ |
| Naye keys (`sub_categories`, `filter`, …) | Ignore ho jayenge ✅ |
| `GET /categories` ka naya `sub_categories_count` | Ignore ✅ |

> Isliye naye app ko Category screen par **`sub_category=none` bhejna zaroori hai**, warna sub category wale products
> category page par bhi dikh jayenge (website se alag).

---

## 8. App developer checklist

- [ ] Category tap → Category screen → `?sub_category=none`
- [ ] `sub_categories` hain → cards grid (2 col phone, 3 tablet) + "Other listings in {category}" heading
- [ ] `sub_categories` khali → sirf products (koi heading / cards nahi)
- [ ] Card par `name` + `products_count` listing(s), 0 wala halka par tap-able
- [ ] Card tap → Sub Category screen → `?sub_category={id}`, current `type` tab saath
- [ ] Sub Category screen: title = sub category, subtitle = category, back = category
- [ ] Tab (`type`), `sort`, `q` dono screens par
- [ ] Cards sirf page 1 se, pagination par dobara nahi
- [ ] Empty states: "Pick a sub category" (category, cards hain) / "Nothing here yet" (baaki)
- [ ] 404 "Sub category not found." → toast + back
- [ ] (optional) Sub category chips, deep link, share URL

---

## 9. Backend: kya badla

| File | Change |
|---|---|
| `app/Http/Controllers/Api/V1/CategoryController.php` | `index()`: `sub_categories_count`. `show()`: `sub_category` + `q` params, `sub_categories`, `without_sub_category_count`, `sub_category`, `filter` keys |
| `app/Support/Market.php` | `subSlugOf()`, `subCategoryUrl()` helpers (website aur API dono ke liye) |

Website (same rule): `app/Http/Controllers/Front/CategoryController.php`, `resources/views/front/category/show.blade.php`,
`public/front/css/category-sub.css`, route `category/{slug}/{subSlug}` (`routes/web.php`).

**Deploy:** naya migration nahi hai (sub category wali migrations pehle hi chal chuki hain). Code upload karke `php artisan optimize:clear` chalao.

**Data:** jab tak admin me sub categories nahi banengi (Master Setting → Sub Category Master), har category me
`sub_categories: []` aayega aur app pehle jaisa saare products dikhayega.

---

## 10. Test result (local, 24 Nov 2026)

Real API requests, DB transaction me (rollback, koi test data nahi bacha):

| Test | Result |
|---|---|
| `GET /categories` → `sub_categories_count` sahi | ✅ |
| `GET /categories/{slug}` (bina param) → saare products, `filter: all` | ✅ |
| `sub_categories` me id, name, slug, products_count | ✅ |
| `without_sub_category_count` sahi | ✅ |
| `?sub_category=none` → sirf `null` wale, sub category wala product nahi | ✅ |
| `?sub_category={id}` → sirf us sub category ka product | ✅ |
| `?sub_category={slug}&type=want&sort=name` | ✅ 200 |
| Khali sub category → `total: 0` | ✅ |
| Galat sub category → `404` | ✅ |
| `?q=` search | ✅ 200 |
