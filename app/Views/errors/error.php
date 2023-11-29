<?= $this->extend('layouts/main'); ?>

<?= $this->section('error'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <?php foreach ($errors as $error) : ?>
                <?= $error ?> <br/>
            <?php endforeach ?>
            <br/>
            <a href="javascript:history.back();">Back</a>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


