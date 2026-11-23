<?php $__env->startSection('title', config('app.name') . ' || ' . ($survey->exists ? 'Edit Survey' : 'Add Survey')); ?>

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1"><?php echo e($survey->exists ? 'Edit Survey' : 'Add Survey'); ?></h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('admin/dashboard')); ?>"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.survey.index')); ?>">Surveys</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($survey->exists ? 'Edit' : 'Add'); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    <a href="<?php echo e(route('admin.survey.index')); ?>" class="btn btn-light d-flex align-items-center">
                        <i class="ti ti-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <?php echo $__env->make('admin.survey._alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Survey Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="<?php echo e($survey->exists ? route('admin.survey.update', $survey) : route('admin.survey.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php if($survey->exists): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label" for="name">Survey Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" maxlength="255"
                                    class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('name', $survey->name)); ?>" placeholder="e.g. Crop Price Survey - Rabi 2026">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Short Description <span class="text-muted fs-12">(optional, shown on the survey page)</span></label>
                                <textarea id="description" name="description" rows="3" maxlength="1000"
                                    class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Tell members why you are running this survey"><?php echo e(old('description', $survey->description)); ?></textarea>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="0" <?php if((string) old('status', $survey->status) === '0'): echo 'selected'; endif; ?>>Inactive</option>
                                    <option value="1" <?php if((string) old('status', $survey->status) === '1'): echo 'selected'; endif; ?>>Active</option>
                                </select>
                                <small class="text-muted d-block mt-1">Choosing Active will make all other surveys inactive.</small>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <a href="<?php echo e(route('admin.survey.index')); ?>" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i><?php echo e($survey->exists ? 'Update Survey' : 'Save & Add Questions'); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout.main-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/survey/form.blade.php ENDPATH**/ ?>