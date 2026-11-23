<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PolicyPageController extends Controller
{
    // GET /privacy-policy
    public function privacy()
    {
        return $this->show('privacy', 'Privacy Policy', 'fa-user-shield', 'How we collect, use and protect your information.');
    }

    // GET /terms-and-conditions
    public function terms()
    {
        return $this->show('terms', 'Terms & Conditions', 'fa-file-contract', 'Rules for using our website and mobile app.');
    }

    private function show(string $type, string $title, string $icon, string $text)
    {
        $policy = Policy::where('type', $type)->latest('id')->first();

        // Editor content escape hoke save hota hai, use wapas HTML banao
        // aur sirf safe tags rakho (script, iframe wagairah hat jayenge)
        $content = $policy ? html_entity_decode((string) $policy->content, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
        $content = strip_tags($content, ['div', 'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'a', 'span', 'blockquote']);

        return view('front.pages.policy', [
            'title'     => $title,
            'icon'      => $icon,
            'text'      => $text,
            'content'   => $content,
            'updatedAt' => $policy?->updated_at,
        ]);
    }
}