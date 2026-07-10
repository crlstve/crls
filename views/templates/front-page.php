<?php Functions::partial('header'); ?>
<main class="relative w-full min-h-dvh overflow-x-hidden ">
    <canvas id="tutorial" class="fixed bottom-0 -z-10 opacity-20 dark:opacity-30 backdrop-blur-sm"></canvas>
    <?php 
        Functions::partial('hero');
        
        Functions::partial('experience'); 

    ?>
    <section class="max-w-6xl mx-auto mb-6 lg:py-12 grid grid-cols-12 gap-6 lg:gap-12 lg:grid-flow-dense">
        <?php Functions::partial('projects');  ?>

        <?php Functions::partial('stack');?>
    </section>
    <?php
        Functions::partial('modal'); 
    ?>

</main>
<?php Functions::partial('footer'); ?>