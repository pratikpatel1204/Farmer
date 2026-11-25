
<?php $__env->startSection('title', config('app.name') . ' || Business Dashboard'); ?>
<?php
    $kmIcons = [
        'tag' => '<path d="M3 12V4h8l10 10-8 8L3 12Z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'users' =>
            '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'sprout' =>
            '<path d="M12 21v-9"/><path d="M12 12c0-4-3-7-8-7 0 4 3 7 8 7Zm0 0c0-3 2.5-6 7-6 0 3.5-2.5 6-7 6Z"/>',
    ];

    // I Have / I Want panels: har panel me ek bada total + 3 chhote status numbers
    $splitSections = [
        [
            'title' => 'My Products',
            'sub' => $productStats['total']
                ? number_format($productStats['total']) . ' listings in all | ' . $productStats['approved_rate'] . '% approved'
                : 'Products you have listed on the marketplace',
            'panels' => [
                [
                    'type' => 'have',
                    'label' => 'I Have',
                    'hint' => 'Products you are selling',
                    'icon' => 'tag',
                    'total' => $productStats['have']['total'],
                    'total_label' => 'Products',
                    'stats' => [
                        ['label' => 'Approved', 'value' => $productStats['have']['approved'], 'tone' => 'ok'],
                        ['label' => 'Pending', 'value' => $productStats['have']['pending'], 'tone' => 'wait'],
                        ['label' => 'Rejected', 'value' => $productStats['have']['rejected'], 'tone' => 'bad'],
                    ],
                    'foot' => [],
                ],
                [
                    'type' => 'want',
                    'label' => 'I Want',
                    'hint' => 'Products you want to buy',
                    'icon' => 'search',
                    'total' => $productStats['want']['total'],
                    'total_label' => 'Products',
                    'stats' => [
                        ['label' => 'Approved', 'value' => $productStats['want']['approved'], 'tone' => 'ok'],
                        ['label' => 'Pending', 'value' => $productStats['want']['pending'], 'tone' => 'wait'],
                        ['label' => 'Rejected', 'value' => $productStats['want']['rejected'], 'tone' => 'bad'],
                    ],
                    'foot' => [],
                ],
            ],
        ],
        [
            'title' => 'Product Inquiries',
            'sub' => $inquiryStats['received']
                ? number_format($inquiryStats['received']) . ' received in all | ' . number_format($inquiryStats['pending']) . ' waiting for your reply'
                : 'Inquiries on your products and inquiries you sent',
            'panels' => [
                [
                    'type' => 'have',
                    'label' => 'I Have',
                    'hint' => 'Buyers asking about your products',
                    'icon' => 'tag',
                    'total' => $inquiryStats['have']['received'],
                    'total_label' => 'Received',
                    'stats' => [
                        ['label' => 'Pending', 'value' => $inquiryStats['have']['pending'], 'tone' => 'wait'],
                        ['label' => 'Contacted', 'value' => $inquiryStats['have']['contacted'], 'tone' => 'info'],
                        ['label' => 'Closed', 'value' => $inquiryStats['have']['closed'], 'tone' => 'ok'],
                    ],
                    'foot' => [
                        ['label' => 'This month', 'value' => $inquiryStats['have']['this_month']],
                        ['label' => 'Sent by you', 'value' => $inquiryStats['have']['sent']],
                    ],
                ],
                [
                    'type' => 'want',
                    'label' => 'I Want',
                    'hint' => 'Sellers replying to your requirements',
                    'icon' => 'search',
                    'total' => $inquiryStats['want']['received'],
                    'total_label' => 'Received',
                    'stats' => [
                        ['label' => 'Pending', 'value' => $inquiryStats['want']['pending'], 'tone' => 'wait'],
                        ['label' => 'Contacted', 'value' => $inquiryStats['want']['contacted'], 'tone' => 'info'],
                        ['label' => 'Closed', 'value' => $inquiryStats['want']['closed'], 'tone' => 'ok'],
                    ],
                    'foot' => [
                        ['label' => 'This month', 'value' => $inquiryStats['want']['this_month']],
                        ['label' => 'Sent by you', 'value' => $inquiryStats['want']['sent']],
                    ],
                ],
            ],
        ],
    ];

    $teamCards = [
        ['label' => 'Direct Members', 'value' => $teamStats['direct'], 'note' => $teamStats['direct_active'] . ' active', 'icon' => 'user', 'tone' => 'green'],
        ['label' => 'Below Direct', 'value' => $teamStats['below_direct'], 'note' => 'Level 2 and below', 'icon' => 'sprout', 'tone' => 'leaf'],
        ['label' => 'Total Team', 'value' => $teamStats['total'], 'note' => $teamStats['this_month'] . ' joined this month', 'icon' => 'users', 'tone' => 'sky'],
        ['label' => 'Team Levels', 'value' => $teamStats['levels'], 'note' => 'Deepest level in your team', 'icon' => 'layers', 'tone' => 'soil'],
    ];
?>
<?php $__env->startSection('content'); ?>
    <main class="zentic-content">
        <?php if($pinnedNotification): ?>
            <div class="km-ann km-ann-pinned km-ann-dash">
                <button class="km-ann-head" type="button" data-bs-toggle="collapse" data-bs-target="#annDash"
                    aria-expanded="true">
                    <span class="km-ann-title">
                        <span class="km-ann-badge">Pinned</span><?php echo e($pinnedNotification->title); ?>

                    </span>
                    <span class="km-ann-date"><?php echo e($pinnedNotification->created_at->format('d M Y')); ?></span>
                </button>
                <div id="annDash" class="collapse show">
                    <div class="km-ann-body"><?php echo $pinnedNotification->description; ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="ai-insight-banner mb-4">
            <div class="ai-banner-icon"><i class="icon-circle-user"></i></div>
            <div class="ai-banner-content">
                <p class="ai-banner-title">Welcome back, <?php echo e(auth()->user()->full_name); ?></p>
                <?php if($profile): ?>
                    <p class="ai-banner-text">
                        From your My Account Dashboard you have the ability to view a snapshot of your
                        recent account activity.
                    </p>
                <?php else: ?>
                    <p class="ai-banner-text text-warning">
                        <b>Please update your business profile.</b>
                    </p>
                <?php endif; ?>
            </div>
            <?php if($profile): ?>
                <a href="<?php echo e(route('business.profile', auth()->user()->unique_id)); ?>" class="ai-banner-action" target="_blank">
                    <i class="icon-store"></i> View Business Profile
                </a>
            <?php else: ?>
                <button class="btn-zentic btn-primary disabled">
                    <i class="icon-store"></i> Business Profile
                </button>
            <?php endif; ?>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="zentic-card h-100">
                    <div class="zentic-card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon purple zentic-bg-purple avatar-48 fs-20">
                                <i class="icon-users"></i>
                            </div>
                            <div>
                                <h2 class="fw-700 mb-0 h6">Refer &amp; Earn</h2>
                                <p class="text-muted text-xs mb-0">Invite businesses and earn rewards</p>
                            </div>
                        </div>
                        <label class="form-label">Your Referral Link</label>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control-zentic"
                                value="<?php echo e(route('refer')); ?>?sponsor_id=<?php echo e($user->unique_id ?? ''); ?>" readonly>
                            <button type="button" class="btn-zentic btn-primary flex-shrink-0 refer-earn"
                                data-refer-url="<?php echo e(route('refer')); ?>?sponsor_id=<?php echo e($user->unique_id ?? ''); ?>">
                                <i class="icon-users me-1"></i>Refer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php $__currentLoopData = $splitSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="km-dash-section mb-4">
                <div class="km-dash-head">
                    <h2 class="fw-700 mb-0 h6"><?php echo e($section['title']); ?></h2>
                    <p class="text-muted text-xs mb-0"><?php echo e($section['sub']); ?></p>
                </div>
                <div class="row g-3 g-lg-4">
                    <?php $__currentLoopData = $section['panels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="zentic-card km-split km-type-<?php echo e($panel['type']); ?> h-100">
                                <div class="zentic-card-body">
                                    <div class="km-split-head">
                                        <span class="km-split-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                                stroke-linecap="round" stroke-linejoin="round"><?php echo $kmIcons[$panel['icon']]; ?></svg>
                                        </span>
                                        <div class="km-split-title">
                                            <span class="km-type-pill"><?php echo e($panel['label']); ?></span>
                                            <p class="text-muted text-xs mb-0"><?php echo e($panel['hint']); ?></p>
                                        </div>
                                        <div class="km-split-total">
                                            <span class="km-split-num"><?php echo e(number_format($panel['total'])); ?></span>
                                            <span class="km-split-cap"><?php echo e($panel['total_label']); ?></span>
                                        </div>
                                    </div>

                                    <div class="km-mini-grid">
                                        <?php $__currentLoopData = $panel['stats']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="km-mini km-mini-<?php echo e($stat['tone']); ?>">
                                                <span class="km-mini-num"><?php echo e(number_format($stat['value'])); ?></span>
                                                <span class="km-mini-lbl"><i></i><?php echo e($stat['label']); ?></span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    <?php if($panel['foot']): ?>
                                        <div class="km-split-foot">
                                            <?php $__currentLoopData = $panel['foot']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span><?php echo e($foot['label']); ?> <b><?php echo e(number_format($foot['value'])); ?></b></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <section class="km-dash-section mb-4">
            <div class="km-dash-head">
                <h2 class="fw-700 mb-0 h6">My Team</h2>
                <p class="text-muted text-xs mb-0">Members who joined with your referral link and their team</p>
            </div>
            <div class="row g-3 g-lg-4">
                <?php $__currentLoopData = $teamCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-xl-3">
                        <div class="zentic-card km-stat km-tone-<?php echo e($card['tone']); ?> h-100">
                            <div class="zentic-card-body">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <p class="km-stat-label mb-0"><?php echo e($card['label']); ?></p>
                                    <span class="km-stat-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round"><?php echo $kmIcons[$card['icon']]; ?></svg>
                                    </span>
                                </div>
                                <p class="km-stat-value mb-1"><?php echo e(number_format($card['value'])); ?></p>
                                <p class="text-muted text-xs mb-0"><?php echo e($card['note']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <style>
        .km-dash-head {
            margin-bottom: .85rem;
        }

        /* ---------- I Have / I Want panels ---------- */
        .km-split {
            --km-type: var(--zc-primary, #1d7a3a);
            position: relative;
            overflow: hidden;
            border-top: 3px solid var(--km-type) !important;
        }

        .km-type-have {
            --km-type: var(--zc-primary, #1d7a3a);
        }

        .km-type-want {
            --km-type: #e2552d;
        }

        .km-split-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .km-split-icon {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            color: var(--km-type);
            background: color-mix(in srgb, var(--km-type) 13%, transparent);
        }

        .km-split-icon svg {
            width: 21px;
            height: 21px;
        }

        .km-split-title {
            min-width: 0;
            flex: 1;
        }

        .km-type-pill {
            display: inline-block;
            padding: .12rem .6rem;
            margin-bottom: .2rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .02em;
            color: #fff;
            background: var(--km-type);
        }

        .km-split-total {
            text-align: right;
            line-height: 1.05;
        }

        .km-split-num {
            display: block;
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--km-type);
            font-variant-numeric: tabular-nums;
        }

        .km-split-cap {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .65;
        }

        .km-mini-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .5rem;
        }

        .km-mini {
            --km-mini: #6b7280;
            padding: .55rem .65rem;
            border-radius: 10px;
            background: color-mix(in srgb, var(--km-mini) 9%, transparent);
        }

        .km-mini-ok { --km-mini: #2f8f3e; }
        .km-mini-wait { --km-mini: #d99a0b; }
        .km-mini-bad { --km-mini: #e2552d; }
        .km-mini-info { --km-mini: #2a86b8; }

        .km-mini-num {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }

        .km-mini-lbl {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .74rem;
            font-weight: 600;
            opacity: .85;
            white-space: nowrap;
        }

        .km-mini-lbl i {
            flex-shrink: 0;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--km-mini);
        }

        .km-split-foot {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem 1.25rem;
            margin-top: .8rem;
            padding-top: .7rem;
            border-top: 1px dashed color-mix(in srgb, currentColor 18%, transparent);
            font-size: .78rem;
            opacity: .85;
        }

        .km-split-foot b {
            margin-left: .2rem;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Team cards ---------- */
        .km-stat {
            --km-tone: var(--zc-primary, #1d7a3a);
            position: relative;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .km-stat::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--km-tone);
        }

        .km-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -14px var(--km-tone);
        }

        .km-tone-green { --km-tone: var(--zc-primary, #1d7a3a); }
        .km-tone-leaf { --km-tone: #4f9d2d; }
        .km-tone-sky { --km-tone: #2a86b8; }
        .km-tone-soil { --km-tone: #9a6334; }

        .km-stat-label {
            font-size: .82rem;
            font-weight: 600;
            opacity: .8;
        }

        .km-stat-icon {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            color: var(--km-tone);
            background: color-mix(in srgb, var(--km-tone) 14%, transparent);
        }

        .km-stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .km-stat-value {
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.1;
            margin-top: .35rem;
            font-variant-numeric: tabular-nums;
        }

        @media (max-width: 575.98px) {
            .km-split-num { font-size: 1.6rem; }
            .km-mini { padding: .5rem; }
            .km-mini-num { font-size: 1.05rem; }
            .km-mini-lbl { font-size: .7rem; }
            .km-stat-value { font-size: 1.5rem; }
            .km-stat-icon { width: 34px; height: 34px; border-radius: 10px; }
            .km-stat-icon svg { width: 17px; height: 17px; }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // WhatsApp Share Profile
            document.querySelectorAll('.share-profile').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const storeUrl = this.dataset.storeUrl;
                    if (!storeUrl || !storeUrl.startsWith('http')) {
                        alert('Unable to share profile: Invalid or missing Store URL.');
                        return;
                    }
                    const message = encodeURIComponent(`Check out my store: ${storeUrl}`);
                    window.open(`https://api.whatsapp.com/send?text=${message}`, '_blank');
                });
            });

            // Refer & Earn
            document.querySelectorAll('.refer-earn').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const referUrl = this.dataset.referUrl;
                    if (!referUrl || !referUrl.startsWith('http')) {
                        alert('Unable to share referral: Invalid or missing Refer URL.');
                        return;
                    }
                    const message = encodeURIComponent(`Join my team: ${referUrl}`);
                    window.open(`https://api.whatsapp.com/send?text=${message}`, '_blank');
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('member.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/member/dashboard.blade.php ENDPATH**/ ?>