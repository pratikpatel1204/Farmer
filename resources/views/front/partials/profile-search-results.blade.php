{{-- resources/views/front/partials/profile-search-results.blade.php
     Search page par products ke neeche matching profiles.
     Variable: $profiles (User collection with profile, states, citys) --}}
@if (isset($profiles) && $profiles->count())
    <div class="search-profiles" id="profiles">
        <div class="search-profiles-head">
            <h2><i class="fa-solid fa-users"></i>Profiles</h2>
            <span class="search-profiles-count">{{ $profiles->count() }} {{ $profiles->count() === 1 ? 'profile' : 'profiles' }} found</span>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
            @foreach ($profiles as $member)
                @php
                    $isFarmer = $member->role === 'Farmer';
                    $initial = mb_strtoupper(mb_substr(trim((string) $member->full_name), 0, 1)) ?: '?';
                    $place = collect([$member->citys->name ?? null, $member->states->name ?? null])->filter()->implode(', ');
                @endphp
                <div class="col">
                    <div class="sp-card {{ $isFarmer ? 'is-farmer' : 'is-nonfarmer' }}">
                        <div class="sp-avatar-wrap">
                            <div class="sp-avatar">
                                @if ($member->profile_image)
                                    <img src="{{ asset($member->profile_image) }}" alt="{{ $member->full_name }}" loading="lazy">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            @if (optional($member->profile)->status === 'Approved')
                                <span class="sp-tick" title="Verified"><i class="fa-solid fa-check"></i></span>
                            @endif
                        </div>

                        <h3 class="sp-name" title="{{ $member->full_name }}">{{ $member->full_name }}</h3>

                        <span class="sp-role">
                            <i class="fa-solid {{ $isFarmer ? 'fa-seedling' : 'fa-store' }}"></i>{{ $isFarmer ? 'Farmer' : 'Non Farmer' }}
                        </span>

                        <p class="sp-location">
                            <i class="fa-solid fa-location-dot"></i>{{ $place ?: 'India' }}
                        </p>

                        <div class="sp-area">
                            <i class="fa-solid fa-map-pin"></i>
                            <span>{{ $member->area ?: '-' }}</span>
                        </div>

                        <a href="{{ url('profile/' . $member->unique_id) }}" class="sp-btn">
                            <i class="fa-solid fa-user"></i>View profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
