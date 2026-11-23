<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    <style>
        :root { --leaf:#1d7a3a; --deep:#135c2a; --ink:#24302a; --muted:#5f6b63; --line:#e3ece5; }
        * { box-sizing: border-box; }
        body { margin:0; padding:16px 16px 32px; background:#fff; color:var(--ink);
               font:15px/1.65 "Mukta", -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        h2 { font-size:22px; color:var(--deep); margin:4px 0 12px; line-height:1.3; }
        h3 { font-size:17px; color:var(--leaf); margin:24px 0 8px; padding-top:12px; border-top:1px solid var(--line); }
        p, ul { margin:0 0 12px; }
        ul { padding-left:20px; }
        li { margin-bottom:6px; }
        a { color:var(--leaf); }
        strong { color:#1b2620; }
        img, table { max-width:100%; }
    </style>
</head>
<body>
    {!! $page->content !!}
</body>
</html>
