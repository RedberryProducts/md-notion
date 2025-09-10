<?php echo $current_page['title']; ?>


<?php if($current_page['hasContent']): ?>

<?php echo $current_page['content']; ?>


<?php endif; ?>
<?php if($hasChildDatabases): ?>

## Child Databases

<?php $__currentLoopData = $child_databases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $database): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo $database['title']; ?>


<?php if($database['hasTableContent']): ?>
<?php echo $database['table_content']; ?>


<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php if($hasChildPages): ?>

## Child Pages

<?php $__currentLoopData = $child_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo $childPage['title']; ?>


<?php if($childPage['hasContent']): ?>
<?php echo $childPage['content']; ?>


<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\Users\admin\Desktop\md-notion/resources/views/page-md.blade.php ENDPATH**/ ?>