<?php $__env->startSection('title', config('app.name') . ' || Surveys'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Surveys</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('admin/dashboard')); ?>"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item">Master</li>
                        <li class="breadcrumb-item active" aria-current="page">Surveys</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    <a href="<?php echo e(route('admin.survey.create')); ?>" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-circle-plus me-2"></i>Add Survey
                    </a>
                </div>
            </div>
        </div>

        <?php echo $__env->make('admin.survey._alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="alert alert-info d-flex align-items-start" role="note">
            <i class="ti ti-info-circle fs-5 me-2"></i>
            <div>Only <strong>one survey</strong> can be active at a time. When you activate a survey, its questions are shown on the website home page and all other surveys become inactive. If no survey is active, the website shows the default "I Want / I Have" banners.</div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <h5 class="mb-0">Survey List</h5>
                <form method="GET" action="<?php echo e(route('admin.survey.index')); ?>" class="d-flex align-items-center gap-2">
                    <div class="input-icon position-relative">
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search survey">
                    </div>
                    <button type="submit" class="btn btn-light"><i class="ti ti-search"></i></button>
                    <?php if(request()->filled('search')): ?>
                        <a href="<?php echo e(route('admin.survey.index')); ?>" class="btn btn-light">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Survey Name</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Responses</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $surveys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($surveys->firstItem() + $loop->index); ?></td>
                                    <td>
                                        <h6 class="fw-medium mb-0">
                                            <a href="<?php echo e(route('admin.survey.questions.index', $survey)); ?>"><?php echo e($survey->name); ?></a>
                                        </h6>
                                        <?php if($survey->description): ?>
                                            <span class="fs-12 text-muted"><?php echo e(\Illuminate\Support\Str::limit($survey->description, 70)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('admin.survey.questions.index', $survey)); ?>" class="badge bg-light text-dark border"><?php echo e($survey->questions_count); ?></a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('admin.survey.results', $survey)); ?>" class="badge bg-light text-dark border"><?php echo e($survey->responses_count); ?></a>
                                    </td>
                                    <td>
                                        <form method="POST" action="<?php echo e(route('admin.survey.status', $survey)); ?>" class="d-flex align-items-center gap-2">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()"
                                                    title="<?php echo e($survey->is_active ? 'Click to make inactive' : 'Click to make active'); ?>"
                                                    <?php if($survey->is_active): echo 'checked'; endif; ?>>
                                            </div>
                                            <?php if($survey->is_active): ?>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td><?php echo e($survey->created_at?->format('d M Y')); ?></td>
                                    <td class="text-end">
                                        <div class="action-icon d-inline-flex align-items-center">
                                            <a href="<?php echo e(route('admin.survey.questions.index', $survey)); ?>" class="me-2" title="Questions"><i class="ti ti-list-details"></i></a>
                                            <a href="<?php echo e(route('admin.survey.results', $survey)); ?>" class="me-2" title="Results"><i class="ti ti-chart-bar"></i></a>
                                            <a href="<?php echo e(route('admin.survey.edit', $survey)); ?>" class="me-2" title="Edit"><i class="ti ti-edit"></i></a>
                                            <form method="POST" action="<?php echo e(route('admin.survey.destroy', $survey)); ?>" class="d-inline"
                                                onsubmit="return confirm('Delete this survey? All its questions and member answers will also be deleted.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-link p-0 text-danger" title="Delete"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="ti ti-clipboard-list fs-1 d-block mb-2"></i>
                                        No survey found. Click "Add Survey" to create one.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($surveys->hasPages()): ?>
                <div class="card-footer"><?php echo e($surveys->links('pagination::bootstrap-5')); ?></div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/survey/index.blade.php ENDPATH**/ ?>