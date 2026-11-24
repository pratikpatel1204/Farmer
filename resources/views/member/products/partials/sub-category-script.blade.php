{{-- Product create/edit: category ke hisaab se sub category dropdown + I Want me main image optional --}}
<script>
    $(function() {
        const subCategories = @json($subCategories ?? (object) []);
        const $category = $('#category');
        const $sub = $('#sub_category_id');

        function fillSubCategories(selected) {
            const list = subCategories[$category.val()] || [];
            $sub.empty().append(new Option('Select Sub Category', ''));
            list.forEach(function(item) {
                $sub.append(new Option(item.name, item.id, false, String(item.id) === String(selected)));
            });
            // Is category ki koi sub category nahi hai to dropdown band
            $sub.prop('disabled', list.length === 0).trigger('change');
        }

        fillSubCategories($sub.data('selected'));
        $category.on('change', function() {
            fillSubCategories('');
            $('#subCategoryError').text('');
        });

        // Main image: I Have me required (*), I Want me optional
        function toggleMainImageRequired() {
            const isWant = $('input[name="listing_type"]:checked').val() === 'want';
            $('.main-image-required').toggle(!isWant);
            $('.main-image-optional-hint').toggle(isWant);
            if (isWant) {
                $('#mainImageError').text('');
            }
        }

        toggleMainImageRequired();
        $('input[name="listing_type"]').on('change', toggleMainImageRequired);
    });
</script>
