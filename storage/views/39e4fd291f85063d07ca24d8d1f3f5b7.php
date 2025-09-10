
<?php echo $current_page['title']; ?>


<?php if($current_page['hasContent']): ?>

<?php echo $current_page['content']; ?>


<?php endif; ?>

<?php if($hasChildDatabases): ?>

---

# Child Databases

    <?php $__currentLoopData = $child_databases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $database): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php echo $database['title']; ?>


        <?php if($database['hasTableContent']): ?>

<?php echo $database['table_content']; ?>


        <?php endif; ?>
## Database Items
        <?php $__currentLoopData = $database['child_pages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php echo $__env->make('md-notion::full-md', $itemPage, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<?php if($hasChildPages): ?>

---

# Child Pages

    <?php $__currentLoopData = $child_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->make('md-notion::full-md', $childPage, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\Users\admin\Desktop\md-notion/resources/views/full-md.blade.php ENDPATH**/ ?>