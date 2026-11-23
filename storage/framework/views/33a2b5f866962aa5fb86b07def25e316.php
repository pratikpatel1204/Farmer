<div class="table-responsive">
    <table class="table table-bordered align-middle" id="memberTable">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Action</th>
                <th>Name</th>
                <th>Member Details</th>
                <th>Sponsor</th>
                <th>Contact Details</th>
                <th>Address</th>
                <th class="text-center">Total Product</th>
                <th class="text-center">Profile Complete</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $profile = $member->profile; ?>
                <tr>
                    <td><?php echo e($loop->iteration + ($members->currentPage() - 1) * $members->perPage()); ?></td>

                    
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex gap-1">
                                <?php if($profile): ?>
                                    <a href="<?php echo e(route('admin.member.details.show', $member->id)); ?>"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Profile">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.member.details.edit', $member->id)); ?>"
                                    class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('admin.member.details.login', $member->unique_id)); ?>"
                                    class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Login"
                                    target="_blank">
                                    <i class="ti ti-login"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger deleteMemberBtn"
                                    data-id="<?php echo e($member->id); ?>" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </td>

                    
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if($member->profile_image): ?>
                                <img src="<?php echo e(asset($member->profile_image)); ?>" alt="<?php echo e($member->full_name); ?>"
                                    class="member-avatar">
                            <?php else: ?>
                                <span class="member-avatar-placeholder"><i class="ti ti-user"></i></span>
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold"><?php echo e($member->full_name); ?></div>
                                <?php if($member->role === 'Farmer'): ?>
                                    <span class="badge bg-success">Farmer</span>
                                <?php else: ?>
                                    <span class="badge bg-info"><?php echo e($member->role); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    
                    <td>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-fingerprint me-1 text-primary"></i>
                            <?php echo e($member->unique_id); ?>

                        </div>
                        <div class="d-flex align-items-center mb-1" title="Password">
                            <i class="ti ti-key me-1 text-danger"></i>
                            <?php echo e($member->temp_password ?? '-'); ?>

                        </div>
                    </td>

                    
                    <td>
                        <?php if($member->sponsor_id): ?>
                            <div class="fw-semibold"><?php echo e($member->sponsor_name ?? '-'); ?></div>
                            <small class="text-muted"><?php echo e($member->sponsor_id); ?></small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-mail me-2 text-primary"></i>
                            <?php echo e($member->email ?? '-'); ?>

                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-phone me-2 text-success"></i>
                            <span class="me-1"><?php echo e($member->mobile ?? '-'); ?></span>
                            <?php if((int) $member->mobile_verified === 1): ?>
                                <i class="ti ti-circle-check text-primary ms-1" title="Verified"></i>
                            <?php else: ?>
                                <i class="ti ti-circle-x text-danger ms-1" title="Not verified"></i>
                            <?php endif; ?>
                        </div>
                    </td>

                    
                    <?php
                        $cityState = collect([$member->city_name, $member->state_name])->filter()->implode(', ');
                    ?>
                    <td style="white-space: normal; min-width: 180px;">
                        <?php if($cityState): ?>
                            <div class="d-flex align-items-center mb-1 fw-medium">
                                <i class="ti ti-building-community me-1 text-primary"></i>
                                <?php echo e($cityState); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($member->area || $member->pincode): ?>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-map-pin me-1 text-danger"></i>
                                <?php echo e(collect([$member->area, $member->pincode])->filter()->implode(' - ')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($member->address): ?>
                            <small class="text-muted" title="<?php echo e($member->address); ?>">
                                <?php echo e(\Illuminate\Support\Str::limit($member->address, 40)); ?>

                            </small>
                        <?php endif; ?>
                        <?php if(!$cityState && !$member->area && !$member->pincode && !$member->address): ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    
                    <td class="text-center">
                        <span class="badge bg-info"><?php echo e($member->products_count ?? 0); ?></span>
                    </td>

                    
                    <td class="text-center">
                        <?php if($member->profile_complete): ?>
                            <span class="badge bg-success">Yes</span>
                        <?php else: ?>
                            <span class="badge bg-danger">No</span>
                        <?php endif; ?>
                    </td>

                    
                    <td>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input user-status-toggle" type="checkbox" role="switch"
                                id="userStatusSwitch<?php echo e($member->id); ?>" data-user-id="<?php echo e($member->id); ?>"
                                <?php echo e((int) $member->status === 1 ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="userStatusSwitch<?php echo e($member->id); ?>">
                                <?php echo e((int) $member->status === 1 ? 'Active' : 'Inactive'); ?>

                            </label>
                        </div>

                        <?php if(!$profile): ?>
                            <div class="alert alert-warning mt-1 p-1 mb-0" role="alert">
                                Profile <br> not created.
                            </div>
                        <?php else: ?>
                            <div class="form-check form-switch">
                                <input class="form-check-input approval-toggle" type="checkbox" role="switch"
                                    id="approveSwitch<?php echo e($profile->id); ?>" data-profile-id="<?php echo e($profile->id); ?>"
                                    <?php echo e($profile->status === 'Approved' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="approveSwitch<?php echo e($profile->id); ?>">
                                    <?php if($profile->status === 'Approved'): ?>
                                        <span class="text-success">Approved</span>
                                    <?php elseif($profile->status === 'Rejected'): ?>
                                        <span class="text-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="text-warning">Pending</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <?php if($profile->status === 'Rejected' && $profile->reject_reason): ?>
                                <small class="text-danger d-block mt-1" style="white-space: normal; max-width: 180px;">
                                    <i class="ti ti-info-circle"></i> <?php echo e($profile->reject_reason); ?>

                                </small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>

                    <td><?php echo e(optional($member->created_at)->format('d-m-Y') ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11" class="text-center">No members found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing <?php echo e($members->firstItem() ?? 0); ?> to <?php echo e($members->lastItem() ?? 0); ?> of <?php echo e($members->total()); ?>

        entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            <?php if($members->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($members->currentPage() - 1); ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            <?php $__currentLoopData = range(1, max(1, $members->lastPage())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $members->currentPage()): ?>
                    <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                <?php elseif($page == 1 || $page == $members->lastPage() || abs($page - $members->currentPage()) <= 2): ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php elseif($page == $members->currentPage() - 3 || $page == $members->currentPage() + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($members->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($members->currentPage() + 1); ?>">&raquo;</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            <?php endif; ?>
        </ul>
    </nav>
</div><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/member_details/table.blade.php ENDPATH**/ ?>