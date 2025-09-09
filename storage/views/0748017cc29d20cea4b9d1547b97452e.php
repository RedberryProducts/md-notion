<?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo e($column['title']); ?>


<?php echo html_entity_decode($column['content']); ?>

<?php if($index < count($columns) - 1): ?>

---

<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\admin\Desktop\md-notion/resources/views/blocks/column_list.blade.php ENDPATH**/ ?>