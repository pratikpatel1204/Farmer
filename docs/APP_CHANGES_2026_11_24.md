# RGM Mart App – Website changes ko App me lana (24 Nov 2026)

> Website par 24 Nov 2026 ko 6 changes hue. Ye document batata hai ki har change ke liye
> **backend API me kya badalna hai** aur **app (APK) me kya badalna hai**, taaki app bhi website jaisa ho jaye.
>
> ✅ **Status (update):** Website aur **backend API dono ka kaam ho chuka hai**. Section 3, 4, 5 aur 7 ke API changes
> implement aur test ho gaye. Final endpoints, request aur response ke liye **`docs/APP_API_CHANGES_2026_11_24.md`** dekho.
> Is file me "karna baaki hai" ya "suggested code" likha ho, to wo planning ke waqt ka hai. Asli API wahi hai jo nayi file me hai.

---

## 0. Ek nazar me (summary)

| # | Website change | Backend API change chahiye? | App change chahiye? |
|---|---|---|---|
| 1 | Public screens par "I Want / I Have" → **"People Want / People Have"** | ❌ Nahi | ✅ Sirf text (labels) |
| 2 | Admin me **Sub Category Master** | ❌ Nahi (admin sirf website par hai) | ❌ Nahi |
| 3 | Member product form me **Sub Category** (optional) | ✅ Haan: naya endpoint + 1 naya field | ✅ Naya dropdown |
| 4 | **I Want** me main image optional (null), **I Have** me required | ✅ Haan: validation | ✅ Form validation |
| 5 | People Want product me image na ho to **category ki image** | ✅ Haan: `image` field me fallback | ⚠️ Chhota sa (null handle) |
| 6 | Header Category menu **3 column + scroll** | ❌ Nahi | ✅ Category picker UI |
| 7 | **About Us** page ka naya content + design | ✅ Recommended: content API se | ✅ About screen naya |

**Kaam ka order:**
1. Server par DB migration.
2. Backend API changes.
3. Backend deploy.
4. App update aur release.

Purane app version backend change ke baad bhi chalte rahenge; section 8 me iska poora check hai.

---

## 1. "I Want / I Have" → "People Want / People Have"

### Website par kya hua
Sirf **public website** (front) par label badle. **Member panel me "I Have / I Want" hi hai**, kyunki wahan member khud
apni listing bana raha hai.

### API
**Koi change nahi.** API me type ki value pehle jaisi rahegi: `"type": "have"` / `"type": "want"` (aur `listing_type`).
Ye sirf app ka display text hai.

### App me kya karna hai
Sirf **public / browse** screens ke labels badalne hain:

| App screen (website jaisa) | Purana text | Naya text |
|---|---|---|
| Bottom nav / top tabs / drawer menu | I Want / I Have | People Want / People Have |
| Home: product sections ke heading | I Want / I Have | People Want / People Have |
| Listing screen ka title | "I Want: what buyers need" | "People Want: what buyers need" |
| Listing screen ka title | "I Have: ready stock from farmers" | "People Have: ready stock from farmers" |
| Product card badge (want) | I Want | People Want |
| Product detail breadcrumb / type chip | I Want / I Have | People Want / People Have |
| Category screen ke tabs | I Have / I Want | People Have / People Want |
| Public profile: counts + tabs | I Have (n) / I Want (n) | People Have (n) / People Want (n) |
| Search result text | "Results from I Have and I Want listings…" | "Results from People Have and People Want listings…" |
| FAQ (Contact screen me ho to) | "What is the difference between I Want and I Have?" | "What is the difference between People Want and People Have?" |

**Ye NAHI badalna** (website ke member panel jaisa "I Have / I Want" hi rahega):
- My Products list ke filter / badge
- Add / Edit Product ka "Listing Type" selector (`I Have`: "I have this product available to sell", `I Want`: "I am looking to buy this product")
- Member dashboard ke I Have / I Want panels
- My Inquiries me product type
- Register screen ka "I want to buy produce" (ye ek sentence hai, label nahi)

> Agar app me multi-language files (`app_en.arb` / `en.json` …) hain to ye keys **har bhasha ki file** me update karni hongi
> (dekho `docs/APP_LANGUAGE_CHANGE.md`).

---

## 2. Admin: Sub Category Master

### Website par kya hua
- Naya table `sub_categories` (`id`, `category_id`, `name`, timestamps). Sirf name hai, **image nahi**.
- Ek category me same naam do baar nahi ban sakta (`unique(category_id, name)`).
- Category delete hogi to uski sub categories bhi delete ho jayengi (cascade).
- Admin ke Category list me "Sub Categories" count column hai.

### API / App
**Koi change nahi.** Admin panel sirf website par hai. Sub categories ka data app ko section 3 ke endpoint se milega.

---

## 3. Product me Sub Category (optional)

### Website par kya hua
- `products` table me naya column **`sub_category_id`** (nullable, FK → `sub_categories.id`, sub category delete ho to `NULL`).
- Member Add / Edit Product form me Category ke baad **Sub Category** dropdown.
- **I Have aur I Want dono me optional.**
- Category badalte hi dropdown me sirf usi category ki sub categories aati hain.
- Jis category ki koi sub category nahi hai, uska dropdown disabled rehta hai.
- Server check karta hai ki sub category **selected category ki hi** ho. Doosri category ki ho to 422 error.

### 3.1 Backend API changes (karna baaki hai)

#### (a) Naya endpoint: sub categories list
```
GET /api/v1/sub-categories                  → saari, category ke hisaab se group
GET /api/v1/sub-categories?category_id=12   → sirf ek category ki
```
Public group me (`throttle:60,1,km-public`), login ki zarurat nahi.

Response (grouped, website ka form bhi yahi shape use karta hai):
```json
{
  "success": true, "message": "OK",
  "data": {
    "12": [ { "id": 4, "name": "Hybrid Seeds" }, { "id": 5, "name": "Organic Seeds" } ],
    "15": [ { "id": 9, "name": "Tractor" } ]
  }
}
```
`?category_id=12` ke saath:
```json
{ "success": true, "message": "OK", "data": [ { "id": 4, "name": "Hybrid Seeds" }, { "id": 5, "name": "Organic Seeds" } ] }
```

Suggested code: naya `app/Http/Controllers/Api/V1/SubCategoryController.php`
```php
public function index(Request $request)
{
    $query = \App\Models\SubCategory::orderBy('name');

    if ($request->filled('category_id')) {
        return $this->ok(
            $query->where('category_id', (int) $request->category_id)->get(['id', 'name'])
        );
    }

    return $this->ok(
        $query->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($items) => $items->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
    );
}
```
`routes/api.php` (public group me):
```php
Route::get('sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories');
```

#### (b) `POST /me/products` aur `POST /me/products/{id}`: naya field `sub_category_id`
File: `app/Http/Controllers/Api/V1/Member/ProductController.php`

`RULES` me add karo (website `Member\ProductMemberController` jaisa):
```php
'sub_category_id' => ['nullable', Rule::exists('sub_categories', 'id')->where('category_id', (int) $request->category)],
```
> `RULES` abhi `private const` hai, aur const me `$request` use nahi ho sakta. Isliye ye rule `store()` / `update()` ke
> andar `self::RULES + [...]` me add karna hoga (jaise `main_image` add hota hai).

Save karte waqt:
```php
// store()
$product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;

// update(): sirf tab badlo jab app ne field bheja ho (purane app version ke liye, section 8 dekho)
if ($request->has('sub_category_id')) {
    $product->sub_category_id = $request->filled('sub_category_id') ? (int) $request->sub_category_id : null;
}
```

#### (c) `GET /me/products/{id}` (edit form) me sub category lautao
```php
'sub_category_id' => $product->sub_category_id,
'sub_category'    => $product->subCategory ? ['id' => $product->subCategory->id, 'name' => $product->subCategory->name] : null,
```
(`Product::with(['categorys', 'subCategory'])` karna, relation `Product::subCategory()` pehle se bana hua hai.)

#### (d) Optional: product card / detail me sub category ka naam
`ApiData::productCard()` aur `GET /products/{slug}` me:
```php
'sub_category' => $product->subCategory ? ['id' => $product->subCategory->id, 'name' => $product->subCategory->name] : null,
```
Isme `ApiData::PRODUCT_WITH` me `'subCategory'` add karna hoga (N+1 se bachne ke liye).
Website abhi public card par sub category **nahi** dikhata, isliye ye optional hai.

### 3.2 App changes
**Add / Edit Product screen:**

```
[Listing Type: I Have | I Want]
[Product Title *]   [Category *]   [Sub Category]   ← naya, optional
```

| Behaviour | Detail |
|---|---|
| Data kab laao | Form khulte hi `GET /sub-categories` (grouped) ek baar call karo, ya category select hone par `?category_id=` |
| Category badli | Sub Category ki value **khali** karo, nayi list bharo |
| List khali | Dropdown disabled, placeholder "No sub category" |
| Required? | **Nahi.** Label par `*` nahi, khali chhod sakte hain |
| Bhejna | Multipart field `sub_category_id` = id, ya khali string `""` (hatane ke liye) |
| Edit pre-fill | `GET /me/products/{id}` → `sub_category_id` select karo (pehle category ki list load ho) |
| Error 422 | `errors.sub_category_id[0]` dropdown ke neeche dikhao |

> **Edit me hamesha `sub_category_id` bhejo** (khali ho to `""`). Agar field nahi bheja to server purani value rakhega.

---

## 4. Main image: I Have me required, I Want me optional

### Website par kya hua
| Listing type | Main image |
|---|---|
| **I Have** | **Required**. Edit me agar pehle se image hai to dobara upload zaroori nahi |
| **I Want** | **Optional**. Na daali to DB me `main_image = NULL` |

Edit ka special case: koi product **I Want → I Have** me badla gaya aur uski image nahi hai → error:
`"Main image is required for I Have listing."`

Form me: I Want select karne par Main Image ka `*` hat jata hai aur ye hint dikhta hai:
*"Optional for I Want. If empty, the category image will be shown."*

### 4.1 Backend API changes (karna baaki hai)
File: `app/Http/Controllers/Api/V1/Member/ProductController.php`

**`store()`**: abhi `main_image` hamesha required hai, aur file bina check ke save hoti hai:
```php
// abhi
'main_image' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:51200',
...
$product->main_image = $this->storeFile($request->file('main_image'), 'uploads/product_images', 'main');
```
Badal ke:
```php
'main_image' => 'required_if:listing_type,have|nullable|file|image|mimes:jpeg,png,jpg,webp|max:51200',
...
if ($request->hasFile('main_image')) {
    $product->main_image = $this->storeFile($request->file('main_image'), 'uploads/product_images', 'main');
}
```
> `hasFile` check zaroori hai. Iske bina I Want bina image ke bheja to `storeFile(null)` crash karega aur 500 error aayega.

**`update()`**: website jaisa check (validation ke baad, save se pehle):
```php
if ($request->listing_type === 'have' && ! $request->hasFile('main_image') && empty($product->main_image)) {
    return $this->fail('Main image is required for I Have listing.', 422, [
        'main_image' => ['Main image is required for I Have listing.'],
    ]);
}
```
(`ApiController::fail($message, $status, $errors)`: isse 422 aata hai aur `errors.main_image` milta hai, app wahi padhe.)

### 4.2 App changes
| Cheez | Kya karna hai |
|---|---|
| Label | I Have → "Main Image *"; I Want → "Main Image" (bina `*`) + hint "Optional for I Want. If empty, the category image will be shown." |
| Listing type badla | Turant label / hint / validation badlo |
| Client validation (Add) | I Have + image nahi → rok do: "Please select a main image" |
| Client validation (Edit) | I Have + nayi image nahi + purani `main_image` bhi `null` → rok do |
| Multipart | Image nahi chuni to `main_image` part **bhejo hi mat** (khali file part mat bhejo) |
| Server error | 422 par `errors.main_image[0]` dikhao |
| Edit pre-fill | `GET /me/products/{id}` me `main_image` `null` aa sakta hai → preview ki jagah upload box dikhao |

---

## 5. People Want: image nahi hai to category ki image

### Website par kya hua
Image dikhane ka rule (ek hi jagah: `App\Support\Market::productImage()`):
```
1. Product ki main_image hai           → wahi
2. Nahi hai + product "want" hai       → uski category ki image
3. Nahi hai + "have" ya category image bhi nahi → null (icon placeholder)
```
Ye rule website ke product card, product detail gallery, aur admin ke product list / view me laga hai.

### 5.1 Backend API changes (karna baaki hai)
**Recommendation: fallback server par karo**, taaki app me koi logic na ho, aur website + app hamesha same rahe.

`app/Support/ApiData.php` → `productCard()`:
```php
// abhi
'image' => self::url($product->main_image),
// naya (Market::productImage() pehle se full URL deta hai)
'image' => Market::productImage($product),
'image_is_category' => empty($product->main_image) && Market::productImage($product) !== null,
```

`app/Http/Controllers/Api/V1/ProductController.php` → `show()`:
```php
$images = array_values(array_unique(array_filter(array_merge(
    [ApiData::url($product->main_image)],
    ApiData::urls($product->sub_images)
))));

// People Want me koi image nahi: category ki image
if (! $images && $type === 'want' && ($fallback = Market::productImage($product))) {
    $images = [$fallback];
}
```

Ye productCard har jagah use hota hai, isliye fallback in sab me apne aap aa jayega:
`/home` (want_products), `/products`, `/categories/{slug}`, `/profiles/{id}`, related products, `/me/products`.

> **Edit form ka `GET /me/products/{id}` → `main_image`** me fallback **mat** lagao. Wahan asli value (`null`) hi chahiye,
> warna app category ki image ko product ki image samjhega.

### 5.2 App changes
- Backend ke baad `image` field me want product ke liye category image apne aap aayegi. **Extra logic nahi chahiye.**
- `image` ab bhi `null` ho sakta hai (have product bina image, ya category ki bhi image nahi). Uske liye **placeholder icon**
  ka handling pakka rakho (crash / broken image nahi).
- Optional: `image_is_category == true` ho to image par chhota sa label "Category image" ya halka overlay dikha sakte ho,
  taaki user ko pata chale ye asli product photo nahi hai.
- Product detail: `images` array me sirf 1 category image ho sakti hai. Gallery 1 image ke saath theek chale.

---

## 6. Category menu: 3 column + scroll

### Website par kya hua
Header ka Category dropdown ~30 categories hone se screen se bahar jaata tha. Ab:
- Desktop: **3 column**, max height ~440px, andar scroll.
- Mobile menu: 3 column (phone < 576px par 2, < 380px par 1), max **50% screen height**, andar scroll.
- Lambe naam 2 line me wrap hote hain.

### API
**Koi change nahi.** `GET /categories` wahi hai.

### App changes
Jahan bhi app me category list / picker hai (Category screen, filter bottom sheet, drawer):
- **Grid, 3 column** (`GridView` `crossAxisCount: 3` / `FlatList numColumns={3}`). Bahut chhoti screen (< 360dp width) par 2.
- Bottom sheet / dropdown ho to **max height ~60–70% screen**, andar scroll (sheet screen se bahar na jaye).
- Har tile: category image (round, `image` field) ya `icon` + `tint`, aur naam **max 2 line** (ellipsis).
- Add Product ka Category selector bhi lamba hai. Usme **search box** dena achha rahega (30+ items).

---

## 7. About Us: naya content + design

### Website par kya hua
About page poora naya hai (RGM Mart ka "Short ABOUT RGM" content). Sections:

| # | Section | Content |
|---|---|---|
| 1 | Hero | **Much More Than an E-Commerce Portal!** / Connecting Farmers, Businesses & Consumers – For Mutual Growth |
| 2 | Who we are | RGM Mart intro + "Our philosophy is simple" quote + Farmers / Businesses / Consumers connection graphic |
| 3 | How we connect | 3 blocks: **01 Farmer ↔ Business** (hara), **02 Farmer ↔ Farmer** (peela), **03 Farmer ↔ Consumer** (laal). Har block me "Result" |
| 4 | Who can join? | 12 member types ke tiles + "Everyone is Welcome!" |
| 5 | How does it work? | People Have / People Want / Connect (3 cards) |
| 6 | Our Vision | Vision + "Connect. Collaborate. Trade. Share. Grow Together!" + "One Network. Endless Possibilities. Mutual Growth." |
| 7 | CTA | "Ready to join RGM Mart?" + Register free / Talk to us |

Files: `resources/views/front/pages/about.blade.php`, `public/front/css/about.css`.

### 7.1 Backend API (recommended)
Abhi `GET /api/v1/pages/about` sirf `stats` deta hai. **Recommendation:** content bhi API se bhejo, taaki text badalne par
**naya APK release na karna pade**. `stats` waise hi rahenge (purane app ke liye).

Suggested response (`AppController::about()`, text ek config file jaise `config/about_page.php` me rakh sakte ho):
```json
{
  "success": true, "message": "OK",
  "data": {
    "stats": { "farmers": 0, "buyers": 0, "products": 0, "categories": 0 },
    "hero": {
      "title": "Much More Than an E-Commerce Portal!",
      "subtitle": "Connecting Farmers, Businesses & Consumers – For Mutual Growth"
    },
    "intro": {
      "text": "RGM Mart is a nationwide digital networking platform that directly connects Farmers, Agricultural Businesses and Consumers, creating opportunities to exchange products, services, resources, knowledge and business opportunities.",
      "philosophy": "Connect people directly so they can help each other, save costs, generate business and grow together."
    },
    "connections": [
      {
        "no": "01",
        "title": "Farmer ↔ Business",
        "subtitle": "Connecting Farmers with Companies & Businesses",
        "color": "green",
        "paragraphs": [
          "Farmers can directly connect with manufacturers, dealers, suppliers and service providers to access everything they need for agriculture:",
          "Similarly, companies and industries can directly connect with farmers to procure agricultural produce and raw materials."
        ],
        "chips": ["Seeds","Saplings","Fertilizers","Pesticides","Manure","Machinery","Farming tools","Solar energy","Drip irrigation","Biogas","Modern farming technologies","Equipment repair","Soil testing","Crop consultancy","Insurance","Farm loans","Transportation","Warehousing","Cold storage","Processing","Packaging"],
        "examples": [
          { "icon": "bottle-droplet", "text": "A ketchup manufacturer can find tomato growers" },
          { "icon": "wheat-awn", "text": "A flour mill can connect with wheat farmers" },
          { "icon": "apple-whole", "text": "A food processing company can source fruits, vegetables and other ingredients directly from producers" }
        ],
        "result": "Direct connections between agricultural suppliers, farmers and industries – creating opportunities for both sides."
      },
      {
        "no": "02",
        "title": "Farmer ↔ Farmer",
        "subtitle": "Farmers Helping Farmers",
        "color": "yellow",
        "paragraphs": [
          "Farmers can search and connect directly with other farmers in nearby villages, districts or anywhere across India to support each other in farming and related activities.",
          "They can buy, sell, rent, exchange or share seeds, saplings, manure, crops, machinery, tractors, tools, labour, land, water, storage and other agricultural resources.",
          "Farmers can also share farming techniques, practical experiences, innovative ideas, cost-saving methods, market information and knowledge about cultivation, irrigation, soil health, pest management, harvesting, storage and marketing."
        ],
        "examples": [
          { "icon": "tractor", "text": "Rent another farmer’s tractor" },
          { "icon": "map-location-dot", "text": "Lease available farmland" },
          { "icon": "droplet", "text": "Access water from a nearby farm through mutually agreed arrangements" },
          { "icon": "right-left", "text": "Exchange produce and resources instead of making cash payments" }
        ],
        "result": "Reduced farming costs, better utilisation of resources, knowledge sharing and stronger farmer-to-farmer cooperation."
      },
      {
        "no": "03",
        "title": "Farmer ↔ Consumer",
        "subtitle": "Bringing Farmers Closer to the General Public",
        "color": "red",
        "paragraphs": [
          "Farmers can directly connect with households, families, residential societies and consumers to sell fresh vegetables, fruits, grains, pulses, dairy products, spices, honey and other agricultural produce."
        ],
        "highlight": "But this relationship goes beyond buying and selling!",
        "cards": [
          { "title": "Consumers help farmers", "text": "By providing business leads, connecting them with potential buyers, sharing professional expertise, technical knowledge, marketing skills and business opportunities." },
          { "title": "Consumers visit farms", "text": "Introduce children to farming, learn agricultural practices, organise community visits and events, participate in educational and social programmes, and develop meaningful relationships with farming communities." }
        ],
        "result": "Direct farm-to-consumer connections, new business opportunities, agricultural awareness and mutual support."
      }
    ],
    "who_can_join": {
      "title": "Everyone is Welcome!",
      "members": ["Farmers","FPOs","Manufacturers","Traders","Wholesalers","Retailers","Agricultural service providers","Technology companies","Entrepreneurs","Professionals","Families","Consumers"]
    },
    "how_it_works": [
      { "key": "have", "title": "People Have", "text": "List your available products, produce, machinery, land, services, expertise and resources." },
      { "key": "want", "title": "People Want", "text": "Post your requirements, specifications, quantity, location and other details." },
      { "key": "connect", "title": "Connect", "text": "Discover relevant members, connect directly, discuss requirements and establish mutually beneficial relationships." }
    ],
    "vision": {
      "text": "To build India’s largest connected agricultural ecosystem where Farmers, Businesses and Consumers can find each other, help each other and create opportunities together.",
      "brand": "RGM MART",
      "words": ["Connect.","Collaborate.","Trade.","Share.","Grow Together!"],
      "tagline": "One Network. Endless Possibilities. Mutual Growth."
    }
  }
}
```
> Icon names Font Awesome 6 ke hain (website jaise). App apne icon set (Material / custom) me map kar le, ya icon ignore kare.
> `brand` ke liye `config('app.name')` use karo (abhi `RGM Mart`), hardcode nahi.

### 7.2 App: About screen layout (website jaisa, mobile ke liye)
```
┌─────────────────────────────┐
│  [About us]                 │  Hero: hara gradient, bada title + subtitle
│  Much More Than an          │
│  E-Commerce Portal!         │
├─────────────────────────────┤
│  WHO WE ARE                 │  intro text
│  ▌" Our philosophy …"       │  quote box: halka hara bg, peeli left border
│     (F)                     │  Farmers / Businesses / Consumers triangle
│   (B)───(C)   RGM Mart      │  (image / custom paint, optional)
├─────────────────────────────┤
│ [01] FARMER ↔ BUSINESS      │  card, top border hara
│  text + chips (Wrap)        │  chips: white pill, scroll nahi, wrap
│  • examples list            │
│  ✔ Result: …                │  white box, left border accent
│ [02] FARMER ↔ FARMER  (peela)│
│ [03] FARMER ↔ CONSUMER (laal)│  2 mini cards (phone par ek ke neeche ek)
├─────────────────────────────┤
│  Everyone is Welcome!       │  grid: 3 column (chhote phone par 2)
│  [🌾 Farmers] [👥 FPOs] …    │  icon + naam
├─────────────────────────────┤
│  1 People Have   →          │  3 cards (ek ke neeche ek)
│  2 People Want   →          │  tap: People Have / People Want listing / Register
│  3 Connect       →          │
├─────────────────────────────┤
│  OUR VISION (hara band)     │  vision text, RGM MART (peela),
│  Connect. Collaborate. …    │  words wrap, last word lime
├─────────────────────────────┤
│  Ready to join RGM Mart?    │  [Register free] [Talk to us]
└─────────────────────────────┘
```

Rang (website ke tokens):

| Token | Hex | Kahan |
|---|---|---|
| green | `#1d7a3a` | Block 01, Connect card, Result tick |
| green-dark | `#135c2a` | Headings, vision gradient |
| lime | `#8cc63f` | "Grow Together!" |
| yellow | `#f5b324` | Block 02, quote border, Vision label, RGM MART |
| brown | `#7b4a23` | People Have card |
| red | `#e2552d` | Block 03, People Want card |
| mist | `#f1f8ee` | Block 01 bg, quote bg, icon bg |
| block 02 bg | `#fff8e6` | |
| block 03 bg | `#fff3ef` | |
| ink / ink-soft | `#1d2a1f` / `#55665a` | Text |
| line | `#d9e8d3` | Card / chip border |

Fonts: headings **Baloo 2** (800), body **Mukta**.

Taps:
- **People Have** → People Have listing (`type=have`)
- **People Want** → People Want listing (`type=want`)
- **Connect / Register free** → Register
- **Talk to us** → Contact

---

## 8. Purane app version (backward compatibility)

Backend change ke baad bhi jo users ke paas purana APK hai, unke liye:

| Case | Kya hoga | Theek hai? |
|---|---|---|
| Purana app I Want ke saath image bhejta hai | Image save hogi (pehle jaisa) | ✅ |
| Purana app `sub_category_id` nahi bhejta (Add) | `NULL` save | ✅ |
| Purana app product **Edit** karta hai, `sub_category_id` nahi bhejta | Agar `update()` me `$request->has('sub_category_id')` check **nahi** lagaya to website se set ki gayi sub category **mit jayegi** | ⚠️ Isliye section 3.1(b) wala `has()` check zaroori hai |
| Purana app `image` field padhta hai | Want product me ab category image aayegi (pehle null tha) | ✅ Better |
| Purana app `/pages/about` se `stats` padhta hai | `stats` waise hi hain | ✅ |
| Purana app "I Want / I Have" dikhata hai | Kaam karega, bas label purana | ✅ (update ke baad theek) |

Force update chahiye to `.env` me `APP_MIN_VERSION_ANDROID` / `APP_MIN_VERSION_IOS` badha do
(`GET /app/config` → `min_version`).

---

## 9. Server deploy checklist

`php artisan migrate` **seedha mat chalana**. Server ka `migrations` table DB se match nahi karta, isliye purani
`create_permission_tables` migration dobara chalne ki koshish karti hai aur fail hoti hai. Sirf ye do chalao, **isi order me**:
```bash
php artisan migrate --force --path=database/migrations/2026_11_24_100000_create_sub_categories_table.php
php artisan migrate --force --path=database/migrations/2026_11_24_110000_add_sub_category_id_to_products_table.php
php artisan optimize:clear
```
Phir:
- [ ] Admin → Master Setting → **Sub Category Master** khulta hai (Categories Master permission wale role ko dikhega)
- [ ] Kuch sub categories bana do (app testing ke liye)
- [ ] Section 3, 4, 5, 7 ke API changes deploy
- [ ] `GET /api/v1/sub-categories` check
- [ ] Browser cache: `about.css`, `header.css` naye hain, users ko Ctrl+F5 ki zarurat pad sakti hai

---

## 10. App testing checklist

**Labels**
- [ ] Public screens par har jagah People Want / People Have
- [ ] My Products / Add Product me I Have / I Want hi hai

**Sub category**
- [ ] Category chuni → sirf usi ki sub categories
- [ ] Category badli → sub category khali ho gayi
- [ ] Bina sub category ki category → dropdown disabled
- [ ] Sub category ke saath save → edit me wahi selected aaye
- [ ] Edit me sub category hata kar save → `null` ho jaye
- [ ] I Have aur I Want dono me bina sub category save ho jaye

**Image**
- [ ] I Want bina image → save ho, list / detail me **category image** dikhe
- [ ] I Have bina image → app rok de; app bypass ho to server 422 de
- [ ] I Want (bina image) → Edit me I Have kiya, image nahi di → 422 "Main image is required for I Have listing."
- [ ] Jis category ki image nahi, uska want product → placeholder icon (crash nahi)
- [ ] Edit form me bina image wala product → upload box dikhe, category image **nahi**

**Category picker**
- [ ] 3 column, 30+ categories par scroll, sheet screen se bahar nahi
- [ ] Lambe naam 2 line me

**About**
- [ ] Saare 7 sections, text website jaisa
- [ ] People Have / People Want / Register / Contact taps sahi screen par
- [ ] Chhote phone (360dp) par horizontal scroll nahi

---

## 11. Reference: website me kya kya badla (files)

| Change | Files |
|---|---|
| People Want / Have labels | `resources/views/front/**` (header, footer, home, hero, product-card, products/index, products/show, category/show, profiles/show, pages/about, pages/contact) |
| Sub Category Master | `app/Http/Controllers/Admin/SubCategoryController.php`, `app/Models/SubCategory.php`, `resources/views/admin/sub_category/*`, `routes/web.php` (`admin/sub-category/*`), `resources/views/admin/layout/Sidebar.blade.php`, `resources/views/admin/category/category-table.blade.php` (count column), `app/Http/Controllers/Admin/Categorycontroller.php` |
| Migrations | `database/migrations/2026_11_24_100000_create_sub_categories_table.php`, `database/migrations/2026_11_24_110000_add_sub_category_id_to_products_table.php` |
| Product sub category + image rule | `app/Http/Controllers/Member/ProductMemberController.php`, `app/Models/Product.php` (`subCategory()` relation, `sub_category_id` fillable), `app/Models/Category.php` (`subCategories()`), `resources/views/member/products/create.blade.php`, `edit.blade.php`, `partials/sub-category-script.blade.php` |
| Category image fallback | `app/Support/Market.php` (`productImage()`), `resources/views/front/partials/product-card.blade.php`, `app/Http/Controllers/Front/ProductController.php` (`show`), `resources/views/admin/product/table.blade.php`, `view.blade.php` |
| Category menu 3 col | `public/front/css/header.css` |
| About page | `resources/views/front/pages/about.blade.php`, `public/front/css/about.css` |

API files jo badalni hain (section 3–7):
`routes/api.php`, `app/Http/Controllers/Api/V1/SubCategoryController.php` (naya), `app/Http/Controllers/Api/V1/Member/ProductController.php`,
`app/Http/Controllers/Api/V1/ProductController.php`, `app/Support/ApiData.php`, `app/Http/Controllers/Api/V1/AppController.php`.
