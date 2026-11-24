# KisanMandi App – Language Change (Website jaisa)

> Goal: Jaise website par **EN / हिन्दी / ગુજરાતી / मराठी** dropdown se poori site ki bhasha badal jati hai,
> waisa hi mobile app me bhi ho – pehli baar **Hindi**, user badle to wahi yaad rahe.

---

## 1. Website me abhi kaise chal raha hai (reference)

| Cheez | Website me |
|---|---|
| Engine | **Google Translate widget** (`translate.google.com/translate_a/element.js`) – page ka English text browser me hi translate hota hai |
| File | `public/front/js/translate.js`, `public/member/js/translate.js` |
| Dropdown | `resources/views/front/layout/header.blade.php`, `resources/views/member/layout/header.blade.php` |
| Bhasha yaad rakhna | Cookie `googtrans=/en/hi` (English = koi cookie nahi) |
| Pehli visit | `localStorage.lang_chosen` nahi hai → Hindi lag jati hai |
| Change par | `changeLanguage(code)` → cookie set → `location.reload()` |
| Languages | `en`, `hi`, `gu`, `mr` |

**Important:** Google Translate widget sirf browser (HTML page) me chalta hai. Native app (Flutter / React Native)
ke screens HTML nahi hote, isliye app me yahi widget **use nahi ho sakta**. App me do hisse alag se handle karne honge:

1. **Static text** – buttons, labels, menu, error messages ("Login", "Add Product", "Search"...)
2. **Dynamic text** – API se aane wala content (product naam/description, blog, category, policy, notification...)

---

## 2. Backend me pehle se kya ready hai

`GET /api/v1/app/config` (splash par ek baar call hota hai) already languages bhejta hai –
`config/kisanmandi_app.php` se:

```json
{
  "languages": [
    { "code": "en", "name": "English", "short": "EN" },
    { "code": "hi", "name": "हिन्दी",  "short": "HI" },
    { "code": "gu", "name": "ગુજરાતી", "short": "GU" },
    { "code": "mr", "name": "मराठी",   "short": "MR" }
  ],
  "default_language": "hi"
}
```

Default bhasha `.env` me badal sakte hain: `APP_DEFAULT_LANGUAGE=hi` → phir `php artisan config:clear`.

App ko **dropdown ki list yahin se banani hai** (hardcode nahi), taaki baad me nayi bhasha sirf config se add ho.

---

## 3. App me flow (website jaisa hi)

```
App open
  └─ Local storage me "app_lang" hai?
        ├─ Haan → wahi bhasha lagao
        └─ Nahi (pehli baar) → /app/config ka default_language ("hi") lagao, save karo
                                (website ka lang_chosen jaisa)

User header / Settings me language dropdown kholta hai
  └─ Bhasha chuni
        ├─ "app_lang" save karo (SharedPreferences / AsyncStorage)
        ├─ App ka locale turant badlo (static text)  ← reload ki zarurat nahi
        └─ Current screen ka API data dobara fetch karo (dynamic text)
```

Dropdown UI website jaisa: icon 🌐 + `HI` / `हिन्दी` + ✔ tick current bhasha par.
Bhasha ke naam **apni hi script me** dikhao (हिन्दी, ગુજરાતી, मराठी) – translate mat karo (website me `notranslate` isi liye hai).

---

## 4. Static text (UI labels) – app ke andar translation files

Har bhasha ki ek file, same keys:

```
lib/l10n/app_en.arb      (Flutter)        ya   src/i18n/en.json  (React Native)
lib/l10n/app_hi.arb                           src/i18n/hi.json
lib/l10n/app_gu.arb                           src/i18n/gu.json
lib/l10n/app_mr.arb                           src/i18n/mr.json
```

Example (`hi`):

```json
{
  "login": "लॉग इन",
  "addProduct": "उत्पाद जोड़ें",
  "search": "खोजें",
  "chooseLanguage": "अपनी भाषा चुनें",
  "sendEnquiry": "पूछताछ भेजें"
}
```

> Tip: Website ke Hindi/Gujarati/Marathi version se text copy karke shuru kar sakte hain, phir kisi
> native speaker se ek baar check karwa lena – machine translation farmers ke liye kabhi-kabhi ajeeb lagta hai.

### Flutter example

```yaml
# pubspec.yaml
dependencies:
  flutter_localizations: { sdk: flutter }
  intl: any
  shared_preferences: ^2.2.0
flutter:
  generate: true
```

```dart
// lib/lang/lang_controller.dart
class LangController extends ChangeNotifier {
  static const _key = 'app_lang';
  Locale _locale = const Locale('hi');
  Locale get locale => _locale;
  String get code => _locale.languageCode;

  /// Splash par call karo (defaultLang = /app/config ka default_language)
  Future<void> init(String defaultLang) async {
    final p = await SharedPreferences.getInstance();
    final saved = p.getString(_key);
    if (saved == null) await p.setString(_key, defaultLang); // pehli baar Hindi
    _locale = Locale(saved ?? defaultLang);
    notifyListeners();
  }

  /// Dropdown se call hoga (website ka changeLanguage)
  Future<void> change(String lang) async {
    final p = await SharedPreferences.getInstance();
    await p.setString(_key, lang);
    _locale = Locale(lang);
    notifyListeners(); // poori app turant nayi bhasha me – reload nahi
  }
}
```

```dart
MaterialApp(
  locale: langController.locale,
  supportedLocales: const [Locale('en'), Locale('hi'), Locale('gu'), Locale('mr')],
  localizationsDelegates: AppLocalizations.localizationsDelegates,
  ...
)
// Use: Text(AppLocalizations.of(context)!.login)
```

### React Native example

```js
// i18n.js  (i18next + react-i18next + AsyncStorage)
i18n.use(initReactI18next).init({
  resources: { en: {translation: en}, hi: {translation: hi}, gu: {translation: gu}, mr: {translation: mr} },
  lng: 'hi', fallbackLng: 'en',
});

export async function initLang(defaultLang) {
  const saved = await AsyncStorage.getItem('app_lang');
  if (!saved) await AsyncStorage.setItem('app_lang', defaultLang);
  await i18n.changeLanguage(saved || defaultLang);
}

export async function changeLanguage(lang) {
  await AsyncStorage.setItem('app_lang', lang);
  await i18n.changeLanguage(lang);
}
// Use: const { t } = useTranslation();  <Text>{t('login')}</Text>
```

---

## 5. Dynamic text (API ka data) – product, blog, category, policy...

Website par Google widget ye bhi translate kar deta hai. App me iske liye **ek** option chunna hoga:

### Option A (Recommended) – App ke andar on-device translation (free)

Google **ML Kit On-Device Translation** – Hindi, Gujarati, Marathi teeno support hain.
Ek baar har bhasha ka model (~30 MB) download hota hai, phir **offline + free** chalta hai.

- Flutter: `google_mlkit_translation`
- React Native: `@react-native-ml-kit/translate-text` (ya native module)

```dart
final translator = OnDeviceTranslator(
  sourceLanguage: TranslateLanguage.english,
  targetLanguage: TranslateLanguage.hindi, // langController.code se map karo
);
final text = await translator.translateText(product.name);
```

Rules:
- Language `en` ho to translate mat karo, original dikhao.
- Translation ko memory/local cache me rakho (`"$lang|$text" → result`) taaki list scroll par baar-baar na chale.
- Bhasha chunte hi model download start karo aur ek chhota loader dikhao ("भाषा तैयार हो रही है...") –
  website ka `page-loader` jaisa.
- **Translate mat karo:** mobile number, price, unique ID, user ka naam, email, URL.

Backend me koi change nahi chahiye.

### Option B – Server translate kare (Google Cloud Translation API)

App har request me header bheje: `Accept-Language: hi`.
Backend ek middleware se locale set kare aur response ke text fields translate karke (DB/cache me save karke) bheje.

- ✅ App halki rehti hai, model download nahi
- ❌ Google Cloud billing chahiye (per character paisa), backend me kaam zyada

Agar ye option lena ho to backend me ye karna hoga:
1. `app/Http/Middleware/ApiSetLocale.php` – `Accept-Language` padhkar `app()->setLocale()` (sirf `en/hi/gu/mr` allow).
2. `routes/api.php` me `v1` group par `ApiForceJson` ke saath ye middleware lagana.
3. Ek `translations` table + helper jo `(text, lang)` ka result cache kare.
4. Product / Blog / Category resources me naam-description ko helper se pass karna.

> Shuruat ke liye **Option A** best hai – free hai, aur backend touch nahi karna padta.

### Policy / About jaise HTML pages (WebView)

`/api/v1/policies` HTML `content` bhejta hai. Agar app ise WebView me dikhata hai to wahan website wala
Google Translate trick hi kaam karega: WebView load karne se pehle cookie set karo
`googtrans=/en/<lang>` aur HTML me `translate.js` + `<div id="google_translate_element">` include karo.
Ya simple tareeka: content ko bhi Option A se translate kar do.

---

## 6. Language dropdown kahan-kahan dikhe

| Jagah | Kyun |
|---|---|
| Pehli baar app khule (optional "Choose your language" screen, Hindi pre-selected) | Farmer khud chun sake |
| Home header (🌐 HI) | Website header jaisa |
| Member dashboard / Settings | Login ke baad bhi badal sake |

Login ho ya na ho, dono jagah same `app_lang` use hoga (website me bhi cookie dono jagah common hai).

---

## 7. Checklist

- [ ] `/app/config` se `languages` + `default_language` lekar dropdown banao
- [ ] `app_lang` local storage me save; pehli baar `hi`
- [ ] 4 translation files (`en`, `hi`, `gu`, `mr`) – saare hardcoded strings unme shift
- [ ] Dropdown: native naam, current par ✔, select par turant locale change
- [ ] Dynamic content: Option A (ML Kit) ya Option B (server) – ek final karo
- [ ] Number, price, ID, naam translate nahi honge
- [ ] Bhasha badalne par current screen ka data refresh
- [ ] App restart ke baad bhi wahi bhasha rahe
- [ ] Gujarati / Marathi font sahi render ho raha hai (Android purane phone par test karo)

---

## 8. Test cases

1. Fresh install → app Hindi me khule.
2. Gujarati chuno → saare buttons + product naam Gujarati me; app band karke kholo → Gujarati hi rahe.
3. English chuno → original text, koi translation call nahi.
4. Internet band (Option A, model downloaded) → translation phir bhi chale.
5. Login / logout karne par bhasha na badle.
