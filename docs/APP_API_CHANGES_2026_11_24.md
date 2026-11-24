# RGM Mart App – API Changes (24 Nov 2026) ✅ Implemented

> Ye file app developer ke liye hai: **backend API me kya badla (ho chuka hai)** aur **app me use kaise karna hai**.
> Kya aur kyun badla, uski poori analysis (labels, design, checklist): `docs/APP_CHANGES_2026_11_24.md`.
>
> Base URL: `{{BASE}}/api/v1` · Header: `Accept: application/json` · Login wale APIs: `Authorization: Bearer {token}`
> Response ka dhancha pehle jaisa hai: `{ "success": true|false, "message": "...", "data": ... , "errors": {...} }`

---

## 0. Summary

| # | Endpoint | Change | Type |
|---|---|---|---|
| 1 | `GET /sub-categories` | **Naya.** Sub categories ki list | 🆕 New |
| 2 | `POST /me/products` | Naya field `sub_category_id`. `main_image` ab **sirf I Have me required** | ✏️ Changed |
| 3 | `POST /me/products/{id}` | Naya field `sub_category_id`. I Want → I Have par image check | ✏️ Changed |
| 4 | `GET /me/products/{id}` | Response me `sub_category_id`, `sub_category`. `main_image` ab `null` ho sakta hai | ➕ Added fields |
| 5 | Product card (`/home`, `/products`, `/categories/{slug}`, `/profiles/{id}`, `/me/products`, related, inquiries) | `image` me want product ke liye **category image fallback**. Naye fields `image_is_category`, `sub_category` | ➕ Added fields |
| 6 | `GET /products/{slug}` | `images` me category image fallback. Naye fields `image_is_category`, `sub_category` | ➕ Added fields |
| 7 | `GET /pages/about` | Poora About content (`stats` pehle jaisa) | ➕ Added fields |

**Breaking change: koi nahi.** Purane fields waise hi hain, sirf naye fields jude hain. Purana APK chalta rahega (section 8).

---

## 1. 🆕 `GET /sub-categories` (public, login nahi chahiye)

Add / Edit Product form ke **Sub Category** dropdown ke liye.

### Option A: saari sub categories ek saath (recommended, form khulte hi ek call)
```
GET /api/v1/sub-categories
```
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "12": [ { "id": 4, "name": "Hybrid Seeds" }, { "id": 5, "name": "Organic Seeds" } ],
    "15": [ { "id": 9, "name": "Tractor" } ]
  }
}
```
- `data` ek **object** hai: key = `category_id` (string), value = us category ki sub categories (naam ke hisaab se A→Z).
- Jis category ki koi sub category nahi hai, uski key **nahi aayegi**. App use khali list maane.
- Koi bhi sub category na ho to `"data": {}` (object, array nahi).

### Option B: sirf ek category ki
```
GET /api/v1/sub-categories?category_id=12
```
```json
{ "success": true, "message": "OK", "data": [ { "id": 4, "name": "Hybrid Seeds" }, { "id": 5, "name": "Organic Seeds" } ] }
```
Category ki koi sub category nahi → `"data": []`.

> Sub categories admin website ke **Master Setting → Sub Category Master** se banti hain. Sirf name hai, image nahi.

---

## 2. ✏️ `POST /me/products` (Add product, `multipart/form-data`)

### Fields (naya / badla hua **bold**)
| Field | Rule | Note |
|---|---|---|
| `listing_type` | required, `have` \| `want` | |
| `title` | required, string ≤ 255 | |
| `category` | required, category id | |
| **`sub_category_id`** | **optional**, sub category id | **Naya.** Ye `category` ki hi sub category honi chahiye, warna 422. Khali / na bhejo → `null` |
| **`main_image`** | **`have` me required, `want` me optional**. jpeg/png/jpg/webp, ≤ 50MB | Pehle hamesha required tha. Want me na bhejo → DB me `null` |
| `sub_images[]` | optional, max 4 | pehle jaisa |
| `brochure` | optional, pdf | pehle jaisa |
| `product_tags`, `description`, `spec_label[]`, `spec_value[]` | optional | pehle jaisa |

> Want me image nahi chuni to **`main_image` part bhejo hi mat**. Khali file part mat bhejo.

### Success `201`
```json
{ "success": true, "message": "Product submitted. It will be live after admin approval.", "data": { "id": 57, "status": "Pending" } }
```

### Errors `422`
```json
{
  "success": false,
  "message": "Please check the highlighted fields and try again.",
  "errors": {
    "main_image": ["Main image is required for I Have listing."],
    "sub_category_id": ["The selected sub category id is invalid."]
  }
}
```
| Kab | Field | Message |
|---|---|---|
| `have` + image nahi | `main_image` | `Main image is required for I Have listing.` |
| Sub category doosri category ki / galat id | `sub_category_id` | `The selected sub category id is invalid.` |

---

## 3. ✏️ `POST /me/products/{id}` (Edit product, `multipart/form-data`)

Fields `POST /me/products` jaise hi hain, aur ye do baatein alag hain:

### `sub_category_id` ka behaviour
| App kya bhejta hai | Result |
|---|---|
| `sub_category_id=4` | Sub category = 4 (category ki honi chahiye, warna 422) |
| `sub_category_id=` (khali string) | Sub category **hat jayegi** (`null`) |
| Field **bheja hi nahi** | Purani value **rahegi**. Par agar `category` badli hai to purani sub category hat jayegi (wo nayi category ki nahi ho sakti) |

> ✅ **Naya app hamesha `sub_category_id` bheje** (khali ho to `""`). "Bheja hi nahi" wala case sirf purane APK ke liye hai.

### `main_image`
- Optional hai. Na bhejo to purani image rahegi.
- **Special case:** product `want` se `have` kiya, purani image `null` hai, aur nayi bhi nahi bheji → `422`:
```json
{ "success": false, "message": "Please check the highlighted fields and try again.",
  "errors": { "main_image": ["Main image is required for I Have listing."] } }
```

### Success `200`
```json
{ "success": true, "message": "Product updated successfully.", "data": { "id": 57, "status": "Approved" } }
```

---

## 4. ➕ `GET /me/products/{id}` (Edit form pre-fill)

Naye fields **bold**:
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 57,
    "title": "Need 2 ton tomatoes",
    "type": "want",
    "image": "https://…/uploads/categories/category_12.png",
    "image_is_category": true,
    "category": { "id": 12, "name": "Vegetables", "slug": "vegetables", "image": "…", "icon": "…", "tint": "…" },
    "sub_category": { "id": 4, "name": "Hybrid Seeds" },
    "listing_type": "want",
    "category_id": 12,
    "sub_category_id": 4,
    "main_image": null,
    "sub_images": [],
    "brochure": null,
    "…": "baaki fields pehle jaise"
  }
}
```

| Field | Matlab | Form me use |
|---|---|---|
| **`sub_category_id`** | `int` ya `null` | Dropdown pre-select (pehle `category_id` ki list load karo) |
| **`sub_category`** | `{id, name}` ya `null` | Dikhane ke liye |
| `main_image` | **Asli** product image URL ya **`null`** | `null` → upload box dikhao (preview nahi) |
| `image` / `image_is_category` | Card wali image (fallback ke saath) | ⚠️ **Edit form me mat use karo.** Ye category ki image ho sakti hai |

---

## 5. ➕ Product card: category image fallback + sub category

Ye card in sab jagah aata hai: `GET /home` (`want_products`, `have_products`), `GET /products`, `GET /categories/{slug}`,
`GET /profiles/{uniqueId}`, `GET /products/{slug}` → `related`, `GET /me/products`, inquiries ke `product`.

```json
{
  "id": 57,
  "slug": "need-2-ton-tomatoes",
  "title": "Need 2 ton tomatoes",
  "type": "want",
  "image": "https://…/uploads/categories/category_12.png",
  "image_is_category": true,
  "category": { "id": 12, "name": "Vegetables", "slug": "vegetables", "image": "…", "icon": "…", "tint": "…" },
  "sub_category": { "id": 4, "name": "Hybrid Seeds" },
  "owner": { "name": "…", "unique_id": "…", "role": "…", "location": "…", "area": "…" },
  "posted_at": "2026-11-24T10:00:00+05:30"
}
```

### `image` ka rule (website jaisa, server par hi lagta hai)
| Product | `main_image` | `image` | `image_is_category` |
|---|---|---|---|
| koi bhi | hai | product ki image | `false` |
| **want** | `null` | **category ki image** | **`true`** |
| want | `null`, category ki bhi image nahi | `null` | `false` |
| have | `null` (purana data) | `null` | `false` |

**App me:**
- `image` jaisa aaye waisa dikhao. Fallback ka logic app me **nahi** likhna.
- `image == null` → placeholder icon (`category.icon` / `category.tint` use kar sakte ho). Crash ya broken image nahi aani chahiye.
- Optional: `image_is_category == true` par image ke upar chhota sa label (jaise "Category image") ya halka overlay.
- `sub_category` `null` ho sakta hai. Ho to category ke saath chhota sa dikha sakte ho, jaise "Vegetables · Hybrid Seeds".

---

## 6. ➕ `GET /products/{slug}` (Product detail)

Naye fields **bold**:
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 57,
    "type": "want",
    "images": ["https://…/uploads/categories/category_12.png"],
    "image_is_category": true,
    "category": { "…": "…" },
    "sub_category": { "id": 4, "name": "Hybrid Seeds" },
    "…": "baaki fields pehle jaise"
  }
}
```
- Want product ki koi image nahi hai → `images` me **sirf 1 category image** aur `image_is_category: true`.
- `images: []` ab bhi aa sakta hai (have bina image, ya category ki bhi image nahi). Tab placeholder dikhao.
- Gallery 1 image ke saath theek chalni chahiye (thumbnail strip mat dikhao).

---

## 7. ➕ `GET /pages/about` (About screen)

Pehle sirf `stats` aata tha. Ab **poora About content** aata hai, website ke About page jaisa.
Text server ki `config/about_page.php` file me hai, isliye **text badalne ke liye naya APK nahi chahiye**.

### Response (shortened, poora real response isi shape me hai)
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "stats": { "farmers": 120, "buyers": 80, "products": 340, "categories": 30 },
    "brand": "RGM Mart",
    "hero": {
      "badge": "About us",
      "title": "Much More Than an E-Commerce Portal!",
      "subtitle": "Connecting Farmers, Businesses & Consumers – For Mutual Growth"
    },
    "intro": {
      "eyebrow": "Who we are",
      "title": "Connecting Farmers, Businesses & Consumers",
      "text": "RGM Mart is a nationwide digital networking platform that directly connects …",
      "philosophy_label": "Our philosophy is simple",
      "philosophy": "Connect people directly so they can help each other, save costs, generate business and grow together.",
      "nodes": [
        { "key": "farmer", "label": "Farmers", "icon": "wheat-awn", "color": "green" },
        { "key": "business", "label": "Businesses", "icon": "industry", "color": "brown" },
        { "key": "consumer", "label": "Consumers", "icon": "users", "color": "red" }
      ],
      "center_label": "Mutual Growth"
    },
    "connections_title": { "eyebrow": "How we connect", "title": "Three connections, one network" },
    "connections": [
      {
        "no": "01",
        "title": "Farmer ↔ Business",
        "subtitle": "Connecting Farmers with Companies & Businesses",
        "color": "green",
        "paragraphs": ["Farmers can directly connect with manufacturers, dealers, suppliers and service providers to access everything they need for agriculture:"],
        "chips": ["Seeds", "Saplings", "Fertilizers", "…21 items"],
        "chips_more": "and other agricultural services",
        "examples_intro": "Similarly, companies and industries can directly connect with farmers to procure agricultural produce and raw materials.",
        "examples": [ { "icon": "bottle-droplet", "text": "A ketchup manufacturer can find tomato growers" }, "…" ],
        "highlight": null,
        "cards": [],
        "footer": null,
        "result": "Direct connections between agricultural suppliers, farmers and industries – creating opportunities for both sides."
      },
      { "no": "02", "title": "Farmer ↔ Farmer", "color": "yellow", "…": "…" },
      { "no": "03", "title": "Farmer ↔ Consumer", "color": "red",
        "highlight": "But this relationship goes beyond buying and selling!",
        "cards": [ { "icon": "handshake-angle", "title": "Consumers help farmers", "text": "…" }, { "icon": "children", "title": "Consumers visit farms", "text": "…" } ],
        "…": "…" }
    ],
    "who_can_join": {
      "eyebrow": "Who can join?",
      "title": "Everyone is Welcome!",
      "members": [ { "icon": "wheat-awn", "label": "Farmers" }, { "icon": "people-group", "label": "FPOs" }, "…12 items" ]
    },
    "how_it_works": {
      "eyebrow": "How does it work?",
      "title": "List, post, connect",
      "steps": [
        { "step": 1, "key": "have", "icon": "seedling", "color": "brown", "title": "People Have", "text": "…",
          "action": { "label": "See People Have", "screen": "products", "type": "have" } },
        { "step": 2, "key": "want", "icon": "cart-shopping", "color": "red", "title": "People Want", "text": "…",
          "action": { "label": "See People Want", "screen": "products", "type": "want" } },
        { "step": 3, "key": "connect", "icon": "handshake", "color": "green", "title": "Connect", "text": "…",
          "action": { "label": "Join free", "screen": "register", "type": null } }
      ]
    },
    "vision": {
      "label": "Our Vision",
      "text": "To build India’s largest connected agricultural ecosystem …",
      "words": ["Connect.", "Collaborate.", "Trade.", "Share.", "Grow Together!"],
      "tagline": "One Network. Endless Possibilities. Mutual Growth."
    },
    "cta": {
      "title": "Ready to join RGM Mart?",
      "text": "Farmers, businesses and consumers – everyone is welcome. Register free and start connecting today.",
      "buttons": [ { "label": "Register free", "screen": "register" }, { "label": "Talk to us", "screen": "contact" } ]
    },
    "colors": {
      "green": "#1d7a3a", "green_dark": "#135c2a", "lime": "#8cc63f", "yellow": "#f5b324", "brown": "#7b4a23",
      "red": "#e2552d", "mist": "#f1f8ee", "sun_bg": "#fff8e6", "tomato_bg": "#fff3ef",
      "ink": "#1d2a1f", "ink_soft": "#55665a", "line": "#d9e8d3"
    }
  }
}
```

### Connection block render karne ka order
Har `connections[]` item ko isi order me dikhao. Jo field `null` / khali ho, wo skip:
```
[no]  title           ← no ka box: colors[color]; color = green | yellow | red
      subtitle
paragraphs[]          ← har ek paragraph
chips[] + chips_more  ← Wrap / chips (chips_more dashed chip)
examples_intro        ← paragraph
examples[]            ← icon + text list (white rows)
highlight             ← bold, accent rang
cards[]               ← 2 cards (phone par ek ke neeche ek)
footer                ← paragraph
✔ Result: result      ← white box, left border accent
```
Block ka background: green → `colors.mist`, yellow → `colors.sun_bg`, red → `colors.tomato_bg`.

### Taps (`action.screen` / `buttons[].screen`)
| `screen` | App screen |
|---|---|
| `products` + `type: "have"` | People Have listing |
| `products` + `type: "want"` | People Want listing |
| `register` | Register |
| `contact` | Contact Us |

### Icons
`icon` me **Font Awesome 6** ke naam hain (bina `fa-`), website ke same icons. App:
- `font_awesome_flutter` / `react-native-vector-icons/FontAwesome6` use kare, **ya**
- apne icon set me map kare. Map me na mile to default icon dikhaye (crash nahi).

Screen ka layout mockup: `docs/APP_CHANGES_2026_11_24.md` → section 7.2.

---

## 8. Purana APK (backward compatibility), test kiya hua

| Purana app kya karta hai | Ab kya hota hai |
|---|---|
| Want ke saath image bhejta hai | Save hoti hai (pehle jaisa) ✅ |
| Add me `sub_category_id` nahi bhejta | `null` save ✅ |
| Edit me `sub_category_id` nahi bhejta | Website se set ki gayi sub category **rehti hai**. Sirf category badalne par hat ti hai ✅ |
| Card ka `image` padhta hai | Want product me category image milti hai (pehle `null`) ✅ |
| `/pages/about` se `stats` padhta hai | `stats` waise hi hain ✅ |
| Naye fields (`sub_category`, `image_is_category`, …) | Purana app inhe ignore karega ✅ |

---

## 9. App developer checklist

**Add / Edit Product**
- [ ] Form khulte hi `GET /sub-categories` → map `{category_id: [...]}` memory me rakho
- [ ] Category ke baad **Sub Category** dropdown (optional, `*` nahi)
- [ ] Category badli → sub category reset. List khali → dropdown disabled
- [ ] Submit me hamesha `sub_category_id` bhejo (khali = `""`)
- [ ] Edit pre-fill: `sub_category_id`, aur `main_image` (`null` ho sakta hai)
- [ ] Listing type `have` → Main Image `*` + required. `want` → optional + hint *"Optional for I Want. If empty, the category image will be shown."*
- [ ] Want me image nahi → `main_image` part mat bhejo
- [ ] 422 me `errors.main_image[0]` / `errors.sub_category_id[0]` field ke neeche dikhao

**Lists / detail**
- [ ] `image == null` → placeholder (crash nahi)
- [ ] (optional) `image_is_category` label
- [ ] (optional) `sub_category.name` card / detail par
- [ ] Detail gallery 1 image ke saath theek

**About**
- [ ] `GET /pages/about` se poori screen bano (text hardcode nahi)
- [ ] `action.screen` taps sahi screen par

**Labels** (API se nahi, app ke andar): public screens par "People Want / People Have". Member screens par "I Want / I Have" hi rahega.
List: `docs/APP_CHANGES_2026_11_24.md` → section 1.

---

## 10. Backend: kya badla (files) + deploy

| File | Change |
|---|---|
| `routes/api.php` | `GET sub-categories` route (public group) |
| `app/Http/Controllers/Api/V1/SubCategoryController.php` | **Naya** |
| `app/Http/Controllers/Api/V1/Member/ProductController.php` | `sub_category_id` validation + save. `main_image` `required_if:listing_type,have`, `hasFile` check. Want→Have image check. `show()` me `sub_category_id`, `sub_category` |
| `app/Http/Controllers/Api/V1/ProductController.php` | Detail me category image fallback, `image_is_category`, `sub_category` |
| `app/Support/ApiData.php` | `productCard()`: `image` fallback (`Market::productImage()`), `image_is_category`, `sub_category`. Naya `subCategory()` helper. `PRODUCT_WITH` me `subCategory` |
| `app/Http/Controllers/Api/V1/AppController.php` | `about()`: content + `brand` (`:brand` → `config('app.name')`) |
| `config/about_page.php` | **Naya.** About ka text |

**Server deploy (order se):**
```bash
# 1) DB (seedha "php artisan migrate" mat chalana, server ka migrations table out of sync hai)
php artisan migrate --force --path=database/migrations/2026_11_24_100000_create_sub_categories_table.php
php artisan migrate --force --path=database/migrations/2026_11_24_110000_add_sub_category_id_to_products_table.php

# 2) Code upload, phir
php artisan optimize:clear      # naya config/about_page.php + routes load hone ke liye
```

**About ka text badalna ho to:** `config/about_page.php` edit karo, phir `php artisan config:clear`.
⚠️ Website ka About page (`resources/views/front/pages/about.blade.php`) abhi apna text khud rakhta hai.
Text badlo to **dono jagah** badalna, taaki website aur app same rahe.

---

## 11. Test result (local, 24 Nov 2026)

Saare tests real API requests se chale, DB transaction me (rollback, koi data nahi bacha):

| Test | Result |
|---|---|
| `GET /sub-categories` (grouped) | ✅ 200 |
| `GET /sub-categories?category_id=` | ✅ 200 |
| Want, bina image + sub category → save, `main_image = null` | ✅ 201 |
| Have, bina image | ✅ 422 `main_image` |
| Doosri category ki sub category | ✅ 422 `sub_category_id` |
| Have, image ke saath | ✅ 201 |
| `GET /me/products/{id}`: `main_image: null`, `sub_category` | ✅ |
| Card `image` = category image, `image_is_category: true` | ✅ |
| Edit, field nahi bheja → sub category rahi | ✅ |
| Edit, `sub_category_id=""` → hat gayi | ✅ |
| Edit, category badli + field nahi → hat gayi | ✅ |
| Edit, want → have bina image | ✅ 422 |
| `GET /products/{slug}`: 1 category image + `sub_category` | ✅ |
| `GET /products`, `/me/products`, `/home` | ✅ 200 |
| `GET /pages/about`: 3 connections, "RGM Mart" brand | ✅ |
